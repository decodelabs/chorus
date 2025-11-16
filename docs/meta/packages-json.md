# Purpose of `packages.json`

The `packages.json` file is the canonical, machine-readable index of all Decode Labs packages.
It provides a structured overview of the entire ecosystem for both humans and tooling.

---

## Why It Exists

- Acts as a **single source of truth** for metadata about every Decode Labs package.
- Powers **architectural documentation** and **decision-making** within Chorus.
- Enables **automated tooling**, validation, and dependency analysis.
- Provides **AI assistants** with an accurate map of the package ecosystem for design, review, and planning.

---

## What It Contains

Each package entry includes:

- **name** – Human-readable name
- **description** – Short summary of what the package does
- **language** – `php`, `ts`, etc.
- **role** – The package’s responsibility within the ecosystem
- **milestone** – Development stage (`m1`–`m6`)
- **stability** *(optional)* – stable, beta, experimental, deprecated
- **scores** – Quality metrics:
  - code
  - readme
  - docs
  - tests
- **dependencies** – List of Composer-style package names
- **location** – Absolute repo URL
- **notes** – Optional meta commentary

---

## How It Is Used

Chorus uses `packages.json` to:

- Generate **architecture overviews**
- Validate **dependency structures**
- Support **cross-package workflows**
- Provide context for **AI-assisted design and refactoring**

Future tools may use this index to:

- Generate reports
- Identify weak documentation or test coverage
- Flag structural issues across packages
- Suggest architectural improvements

---

## How It Is Maintained

`packages.json` is **generated automatically** from the Decode Labs internal spreadsheet using the script in:

```
/scripts/
```

### Important
> Do **not** edit `packages.json` directly.
> Update the spreadsheet instead and regenerate the file.

---

## Schema

The structure of `packages.json` is validated by:

```
config/packages.schema.json
```

This ensures consistency and prevents drift or malformed entries.

---

## Location

`packages.json` lives in:

```
config/packages.json
```

This keeps machine-readable configuration clearly separated from documentation, implementation, and scripts.

---

## Notes

- Package-specific specs and docs remain inside **each package repository**.
- Chorus contains only **cross-package architecture**, decisions, and indexing.
- `packages.json` is designed to be stable, predictable, and easily consumable.
