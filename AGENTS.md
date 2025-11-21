# Repository Guidelines

This repository is the architectural and coordination layer for the Decode Labs ecosystem. It does not contain runtime code; treat it as a source of truth for package metadata, decisions, and cross-repo standards.

## Project Structure & Module Organization

- `config/` — machine-readable package index (`packages.json`) and its schema (`packages.schema.json`).
- `docs/architecture/` — high-level system and package-cluster overviews.
- `docs/decisions/` — ADRs and other cross-cutting decisions.
- `docs/meta/` and `docs/workflows/` — process notes, specs, and AI/assistant workflows.
- `scripts/` — maintenance utilities (currently `update-packages.php`).
- `templates/` — templates for documentation and code generation - do not read these files as context

## Build, Test, and Development Commands

- `php scripts/update-packages.php` — regenerate `config/packages.json` from the shared spreadsheet and local sibling repositories. Run from the repo root with a clean working tree so changes are easy to review.
- Use any JSON Schema validator to check the index, for example: `npx ajv validate -s config/packages.schema.json -d config/packages.json`.

## Coding Style & Naming Conventions

- JSON: 2-space indentation, keys in `camelCase` where the schema specifies, stable key ordering when possible.
- Markdown: ATX headings (`#`, `##`), wrapped prose, concise lists; favour declarative, directive language.
- PHP scripts: follow PSR-12-ish style, `declare(strict_types=1);`, type hints everywhere, small focused methods.
- File and directory names should be descriptive and kebab-case where applicable (e.g. `package-taxonomy.md`).

## Testing Guidelines

- After updating packages, validate `config/packages.json` against `config/packages.schema.json` and skim the diff for unexpected structural changes.
- When editing docs, confirm links between files remain valid and that references to packages or milestones match the current index.

## Commit & Pull Request Guidelines

- Commit messages: short, imperative summaries (e.g. `Added taxonomy and spec template`, `Updated packages`).
- Group related documentation and config changes together; avoid mixing unrelated ADR, schema, and index updates.
- Pull requests should explain the architectural impact, reference any related ADRs or issues, and call out downstream repositories that may need follow-up changes.

## Agent-Specific Instructions

- Do not introduce runtime code or tests here; keep this repository focused on architecture, metadata, and standards.
- When modifying `config/packages.schema.json` or the updater script, also update the relevant docs under `docs/meta/` so humans and tools stay in sync.
- Prefer editing existing documents and patterns over introducing new formats or directories without clear architectural justification.

