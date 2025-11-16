#!/usr/bin/env php
<?php

/**
 * update-packages.php
 *
 * Created with AI.
 *
 * This script fetches the DecodeLabs packages spreadsheet (CSV), stores it as
 * JSON at config/packages.json within the Chorus repository, and recalculates
 * the "Dependencies" column for each package by inspecting the local sibling
 * repositories' composer.json (runtime "require" only) for decodelabs/*
 * packages. Missing repositories or composer.json files are skipped gracefully.
 */

declare(strict_types=1);

final class ChorusPackageUpdater
{
    private string $csvUrl;
    private string $siblingsRoot;
    private string $outputJsonPath;
    private string $userAgent = 'DecodeLabs-Chorus/1.0 (+https://decodelabs.com)';

    public function __construct(
        string $csvUrl,
        string $chorusRoot
    ) {
        $this->csvUrl = $csvUrl;
        $this->siblingsRoot = dirname($chorusRoot);
        $configDir = $chorusRoot . '/config';
        if (!is_dir($configDir)) {
            if (!mkdir($configDir, 0775, true) && !is_dir($configDir)) {
                throw new RuntimeException('Failed to create config directory: ' . $configDir);
            }
        }
        $this->outputJsonPath = $configDir . '/packages.json';
    }

    public function run(): void
    {
        $csv = $this->fetchUrl($this->csvUrl);

        if ($csv === '') {
            throw new RuntimeException('Failed to fetch CSV from URL: ' . $this->csvUrl);
        }

        $rows = $this->parseCsv($csv);
        $outputMap = [];

        foreach ($rows as $row) {
            $rowNorm = $this->normalizeRowKeys($row);
            $name = isset($rowNorm['name']) ? trim((string)$rowNorm['name']) : '';

            if ($name === '') {
                continue;
            }

            $slug = $this->nameToRepoSlug($name);
            $repoPath = $this->siblingsRoot . '/' . $slug;

            $deps = [];

            if (is_dir($repoPath)) {
                $deps = $this->readDecodelabsDependencies($repoPath);
            }

            // Expand dependencies to full package names "decodelabs/<name>"
            $deps = array_values(array_map(
                static function (string $dep): string {
                    return str_starts_with($dep, 'decodelabs/') ? $dep : ('decodelabs/' . $dep);
                },
                $deps,
            ));

            // Extract description from composer.json if available
            $description = null;
            if (is_dir($repoPath)) {
                $description = $this->readDescriptionFromComposer($repoPath);
            }

            // Normalize language to match schema enum
            $language = isset($rowNorm['language']) ? strtolower(trim((string)$rowNorm['language'])) : '';
            $language = $this->normalizeLanguage($language);

            // Normalize milestone to match schema pattern
            $milestone = isset($rowNorm['milestone']) ? trim((string)$rowNorm['milestone']) : '';
            $milestone = $this->normalizeMilestone($milestone);

            // Build scores object with all required fields (defaulting to 0.0)
            $scores = [
                'code' => $this->coerceScore($rowNorm['code'] ?? 0.0),
                'readme' => $this->coerceScore($rowNorm['readme'] ?? 0.0),
                'docs' => $this->coerceScore($rowNorm['refDocs'] ?? $rowNorm['docs'] ?? 0.0),
                'tests' => $this->coerceScore($rowNorm['tests'] ?? 0.0),
            ];

            // Determine GitHub key and location
            [$repoKeyName, $exists] = $this->determineRepoKeyName(
                $repoPath,
                $slug,
                $language,
            );
            $githubFullName = 'decodelabs/' . $repoKeyName;

            // Build output object conforming to schema
            $output = [
                'name' => $name,
                'description' => $description,
                'language' => $language,
                'role' => isset($rowNorm['role']) ? trim((string)$rowNorm['role']) : '',
                'milestone' => $milestone !== '' ? $milestone : null,
                'scores' => $scores,
                'dependencies' => $deps,
                'location' => $exists ? ('https://github.com/' . $githubFullName) : null,
                'notes' => isset($rowNorm['notes']) ? trim((string)$rowNorm['notes']) : null,
            ];

            // Convert empty strings to null for nullable fields
            if ($output['description'] === '') {
                $output['description'] = null;
            }
            if ($output['notes'] === '') {
                $output['notes'] = null;
            }

            $outputMap[$githubFullName] = $output;
        }

        $jsonOut = json_encode($outputMap, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
        if (!is_string($jsonOut)) {
            throw new RuntimeException('Failed to encode JSON output.');
        }

        $bytes = file_put_contents($this->outputJsonPath, $jsonOut . "\n");
        if ($bytes === false) {
            throw new RuntimeException('Failed to write output file: ' . $this->outputJsonPath);
        }

        fwrite(STDOUT, "Wrote updated packages to: {$this->outputJsonPath}\n");
    }

    private function nameToRepoSlug(
        string $name,
    ): string {
        $base = trim($name);
        $special = [
            'Zest vite plugin' => 'vite-plugin-zest',
            'Castaway vite plugin' => 'vite-plugin-castaway',
            'PHPStan DecodeLabs' => 'phpstan-decodelabs',
            'Zest Vite plugin' => 'vite-plugin-zest',
            'Castaway Vite plugin' => 'vite-plugin-castaway',
        ];

        if (isset($special[$base])) {
            return $special[$base];
        }

        $slug = strtolower($base);
        $slug = str_replace('&', 'and', $slug);
        $slug = preg_replace('/\s+/', '-', $slug) ?? $slug;
        $slug = preg_replace('/[^a-z0-9\-]/', '', $slug) ?? $slug;
        $slug = preg_replace('/-+/', '-', $slug) ?? $slug;
        return trim($slug, '-');
    }

    /**
     * @return array<int,array<string,string>>
     */
    private function parseCsv(
        string $csv,
    ): array {
        $rows = [];
        $fp = fopen('php://memory', 'r+');

        if ($fp === false) {
            throw new RuntimeException('Unable to open memory stream for CSV parsing.');
        }

        if (fwrite($fp, $csv) === false) {
            fclose($fp);
            throw new RuntimeException('Failed to write CSV to memory stream.');
        }

        rewind($fp);

        $headers = null;

        while (($data = fgetcsv($fp, 0, ',', '"', '\\')) !== false) {

            if ($headers === null) {
                $headers = $data;
                continue;
            }

            $row = [];
            foreach ($headers as $idx => $key) {
                $row[(string)$key] = isset($data[$idx]) ? (string)$data[$idx] : '';
            }
            $rows[] = $row;
        }

        fclose($fp);
        return $rows;
    }

    private function toCamelCaseKey(
        string $key,
    ): string {
        $key = trim($key);

        if ($key == '') {
            return $key;
        }

        $parts = preg_split('/[^A-Za-z0-9]+/', $key, -1, PREG_SPLIT_NO_EMPTY);

        if ($parts === false || empty($parts)) {
            return $key;
        }

        $normalized = '';
        foreach ($parts as $i => $part) {

            if ($i === 0) {
                $normalized .= strtolower($part);
            } else {
                $normalized .= ucfirst(strtolower($part));
            }
        }
        return $normalized;
    }

    /**
     * @param array<string,mixed> $row
     * @return array<string,mixed>
     */
    private function normalizeRowKeys(
        array $row,
    ): array {
        $out = [];
        foreach ($row as $k => $v) {
            $out[$this->toCamelCaseKey((string)$k)] = $v;
        }
        return $out;
    }

    /**
     * Convert empty strings to null for text fields, preserving arrays, booleans, numbers, and null values.
     *
     * @param array<string,mixed> $row
     * @return array<string,mixed>
     */
    private function normalizeEmptyStrings(
        array $row,
    ): array {
        $out = [];
        foreach ($row as $k => $v) {
            if (is_string($v) && $v === '') {
                $out[$k] = null;
            } else {
                $out[$k] = $v;
            }
        }
        return $out;
    }

    private function coerceScore(
        mixed $value,
    ): float {
        if (is_numeric($value)) {
            $score = (float)$value;
        } else {
            $trim = trim((string)$value);
            $score = is_numeric($trim) ? (float)$trim : 0.0;
        }

        if ($score < 0.0) {
            $score = 0.0;
        } elseif ($score > 5.0) {
            $score = 5.0;
        }
        return $score;
    }

    private function coerceV1NullableBool(
        mixed $value,
    ): ?bool {
        $trim = strtolower(trim((string)$value));

        if ($trim === '') {
            return null;
        }

        if ($trim === 'yes' || $trim === 'y' || $trim === 'true' || $trim === '1') {
            return true;
        }

        if ($trim === 'no' || $trim === 'n' || $trim === 'false' || $trim === '0') {
            return false;
        }
        return null;
    }

    private function fetchUrl(
        string $url,
        int $timeoutSec = 30,
    ): string {
        // Try cURL PHP extension
        if (function_exists('curl_init')) {
            $ch = curl_init($url);

            if ($ch === false) {
                throw new RuntimeException('Failed to initialize cURL.');
            }

            curl_setopt_array($ch, [
                CURLOPT_RETURNTRANSFER => true,
                CURLOPT_FOLLOWLOCATION => true,
                CURLOPT_MAXREDIRS => 5,
                CURLOPT_CONNECTTIMEOUT => $timeoutSec,
                CURLOPT_TIMEOUT => $timeoutSec,
                CURLOPT_USERAGENT => $this->userAgent,
                CURLOPT_SSL_VERIFYPEER => true,
                CURLOPT_SSL_VERIFYHOST => 2,
            ]);
            $body = curl_exec($ch);
            $errno = curl_errno($ch);
            $error = curl_error($ch);
            $code = (int)curl_getinfo($ch, CURLINFO_RESPONSE_CODE);
            curl_close($ch);

            if ($errno === 0 && is_string($body) && $body !== '' && $code >= 200 && $code < 300) {
                return $body;
            }
        }

        // Try stream with context
        $context = stream_context_create([
            'http' => [
                'method' => 'GET',
                'timeout' => $timeoutSec,
                'header' => "User-Agent: {$this->userAgent}\r\n",
                'ignore_errors' => true,
            ],
            'https' => [
                'method' => 'GET',
                'timeout' => $timeoutSec,
                'header' => "User-Agent: {$this->userAgent}\r\n",
                'ignore_errors' => true,
            ],
        ]);
        $body = file_get_contents($url, false, $context);

        if (is_string($body) && $body !== '') {
            return $body;
        }

        // Try shell curl if available
        $curlBin = trim((string)shell_exec('command -v curl'));

        if ($curlBin !== '') {
            $escapedUrl = escapeshellarg($url);
            $cmd = "{$curlBin} -fsSL --max-redirs 5 --connect-timeout {$timeoutSec} --retry 2 -A " . escapeshellarg($this->userAgent) . " {$escapedUrl}";
            $body = shell_exec($cmd);

            if (is_string($body) && $body !== '') {
                return $body;
            }

            // Insecure fallback (only when prior methods failed; useful in sandboxed CI)
            $cmdInsecure = "{$curlBin} -fsSL -k --max-redirs 5 --connect-timeout {$timeoutSec} --retry 2 -A " . escapeshellarg($this->userAgent) . " {$escapedUrl}";
            $body = shell_exec($cmdInsecure);

            if (is_string($body) && $body !== '') {
                return $body;
            }
        }

        return '';
    }

    /**
     * Read description from composer.json if available.
     */
    private function readDescriptionFromComposer(
        string $repoPath,
    ): ?string {
        $composerPath = $repoPath . '/composer.json';

        if (!is_file($composerPath)) {
            return null;
        }

        $json = file_get_contents($composerPath);

        if (!is_string($json) || $json === '') {
            return null;
        }

        $data = json_decode($json, true);

        if (!is_array($data) || !isset($data['description']) || !is_string($data['description'])) {
            return null;
        }

        $desc = trim($data['description']);
        return $desc !== '' ? $desc : null;
    }

    /**
     * Normalize language to match schema enum: "php", "ts", "javascript", "typescript"
     */
    private function normalizeLanguage(
        string $language,
    ): string {
        $lang = strtolower(trim($language));

        // Map common variations to schema enum values
        if ($lang === '' || $lang === 'php') {
            return 'php';
        }

        if ($lang === 'ts' || $lang === 'typescript') {
            return 'typescript';
        }

        if ($lang === 'js' || $lang === 'javascript') {
            return 'javascript';
        }

        // Default to php if unknown
        return 'php';
    }

    /**
     * Normalize milestone to match schema pattern "^m[1-6]$" or return empty string.
     */
    private function normalizeMilestone(
        string $milestone,
    ): string {
        $mil = strtolower(trim($milestone));

        if ($mil === '') {
            return '';
        }

        // Match pattern m1-m6
        if (preg_match('/^m([1-6])$/', $mil, $matches) === 1) {
            return 'm' . $matches[1];
        }

        // Try to extract number if format is different
        if (preg_match('/([1-6])/', $mil, $matches) === 1) {
            return 'm' . $matches[1];
        }

        return '';
    }

    /**
     * @return array<int,string>
     */
    private function readDecodelabsDependencies(
        string $repoPath,
    ): array {
        $composerPath = $repoPath . '/composer.json';

        if (!is_file($composerPath)) {
            return [];
        }

        $json = file_get_contents($composerPath);

        if (!is_string($json) || $json === '') {
            return [];
        }

        $data = json_decode($json, true);

        if (!is_array($data)) {
            return [];
        }

        $require = isset($data['require']) && is_array($data['require']) ? $data['require'] : [];
        $deps = [];

        foreach ($require as $pkg => $_ver) {
            if (is_string($pkg) && str_starts_with($pkg, 'decodelabs/')) {
                $parts = explode('/', $pkg, 2);

                if (isset($parts[1]) && $parts[1] !== '') {
                    $deps[] = $parts[1];
                }
            }
        }

        $deps = array_values(array_unique($deps));
        sort($deps, SORT_NATURAL | SORT_FLAG_CASE);
        return $deps;
    }

    /**
     * Determine repo key name (the part after decodelabs/) and whether the package exists locally.
     *
     * @return array{0:string,1:bool}
     */
    private function determineRepoKeyName(
        string $repoPath,
        string $slug,
        string $language,
    ): array {
        $exists = is_dir($repoPath);

        if ($language === 'ts' || $language === 'typescript' || $language === 'node' || $language === 'javascript' || $language === 'js') {
            $pkg = $this->readPackageNameFromPackageJson($repoPath);
            if ($pkg !== null) {
                return [$pkg, $exists];
            }
        }

        $comp = $this->readPackageNameFromComposer($repoPath);
        if ($comp !== null) {
            return [$comp, $exists];
        }

        if ($language === '') {
            $pkg = $this->readPackageNameFromPackageJson($repoPath);
            if ($pkg !== null) {
                return [$pkg, $exists];
            }
        }

        return [$slug, $exists];
    }

    private function readPackageNameFromComposer(
        string $repoPath,
    ): ?string {
        $composerPath = $repoPath . '/composer.json';
        if (!is_file($composerPath)) {
            return null;
        }
        $json = file_get_contents($composerPath);
        if (!is_string($json) || $json === '') {
            return null;
        }
        $data = json_decode($json, true);
        if (!is_array($data) || !isset($data['name']) || !is_string($data['name'])) {
            return null;
        }
        $name = trim($data['name']);
        if (str_starts_with($name, 'decodelabs/')) {
            $parts = explode('/', $name, 2);
            return isset($parts[1]) ? $parts[1] : null;
        }
        return null;
    }

    private function readPackageNameFromPackageJson(
        string $repoPath,
    ): ?string {
        $pkgPath = $repoPath . '/package.json';
        if (!is_file($pkgPath)) {
            return null;
        }
        $json = file_get_contents($pkgPath);
        if (!is_string($json) || $json === '') {
            return null;
        }
        $data = json_decode($json, true);
        if (!is_array($data) || !isset($data['name']) || !is_string($data['name'])) {
            return null;
        }
        $name = trim($data['name']);
        if (str_starts_with($name, '@decodelabs/')) {
            return substr($name, strlen('@decodelabs/'));
        }
        if (preg_match('/^decodelabs[-\\/](.+)$/', $name, $m) === 1) {
            return $m[1];
        }
        return $name !== '' ? $name : null;
    }
}

// Bootstrap
$csvUrl = 'https://docs.google.com/spreadsheets/d/e/2PACX-1vQaFAnachOWszavqGcgIuXt6rxgauuUGtLzeG1Z1FCg_eum_1BsagTrrHx-Z7kPvoBokwyOfCrDkYZ8/pub?gid=0&single=true&output=csv';
$chorusRoot = dirname(__DIR__);

try {
    $updater = new ChorusPackageUpdater($csvUrl, $chorusRoot);
    $updater->run();
    exit(0);
} catch (Throwable $e) {
    fwrite(STDERR, '[chorus] ' . $e->getMessage() . "\n");
    exit(1);
}
