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
        $updated = [];

        foreach ($rows as $row) {
            $rowNorm = $this->normalizeRowKeys($row);
            $name = isset($rowNorm['name']) ? trim((string)$rowNorm['name']) : '';

            if ($name === '') {
                $updated[] = $rowNorm;
                continue;
            }

            $slug = $this->nameToRepoSlug($name);
            $repoPath = $this->siblingsRoot . '/' . $slug;

            $deps = [];

            if (is_dir($repoPath)) {
                $deps = $this->readDecodelabsDependencies($repoPath);
            }

            $rowNorm['dependencies'] = array_values($deps);

            if (array_key_exists('code', $rowNorm)) {
                $rowNorm['code'] = $this->coerceScore($rowNorm['code']);
            }

            if (array_key_exists('readme', $rowNorm)) {
                $rowNorm['readme'] = $this->coerceScore($rowNorm['readme']);
            }

            if (array_key_exists('refDocs', $rowNorm)) {
                $rowNorm['refDocs'] = $this->coerceScore($rowNorm['refDocs']);
            }

            if (array_key_exists('tests', $rowNorm)) {
                $rowNorm['tests'] = $this->coerceScore($rowNorm['tests']);
            }

            if (array_key_exists('v1', $rowNorm)) {
                $rowNorm['v1'] = $this->coerceV1NullableBool($rowNorm['v1']);
            }

            $updated[] = $rowNorm;
        }

        $jsonOut = json_encode($updated, JSON_PRETTY_PRINT | JSON_UNESCAPED_SLASHES);
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
