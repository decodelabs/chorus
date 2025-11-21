---
title: Generate Package Spec (Stable Package)
description: Analyse this Decode Labs package and write a detailed spec at docs/meta/spec.md using the Chorus package-spec template.
---

You are working on a **single, finished or stable Decode Labs package**.

Your task is to:

1. **Examine the existing package** (code + docs).
2. **Load the shared spec template** from Chorus.
3. **Write or update** a detailed package spec in this repo at `docs/meta/spec.md`.

Do **not** modify code or tests in this command. This command is for documentation only.

---

## Step 1 — Understand This Package

In this repository, read:

- `composer.json`
- `README.md`
- `AGENTS.md` (if present)
- `docs/meta/spec.md` (if it already exists)
- The `src/` directory (focus on public-facing types and main entry points)
- Any other relevant docs under `docs/`

From this, build an understanding of:

- What this package does and does **not** do.
- Its public API surface (classes, interfaces, traits, factories).
- How it fits into the wider Decode Labs ecosystem (HTTP, runtime, data, logic, etc.).
- Error-handling patterns (especially use of `decodelabs/exceptional`).
- Configuration and extension points.

Do **not** make changes yet; just build a mental model.

---

## Step 2 — Load the Chorus Template

Chorus is the meta/architecture repo for Decode Labs.

Look for the **package spec template** in this order:

1. As a sibling repository:

   ```text
   ../chorus/docs/templates/package-spec.md
   ```

2. As a Composer dev dependency (if present):

   ```text
   vendor/decodelabs/chorus/docs/templates/package-spec.md
   ```

If neither is available locally, ask me for the path or, as a last resort, assume the structure from memory but keep it as close as possible to the existing Decode Labs spec style. Prefer a real file over guessing.

Read the template carefully; it defines:

- The section structure.
- The level of detail expected.
- The tone and style (factual, precise, no fluff).

---

## Step 3 — Generate `docs/meta/spec.md` in This Repo

Create (or update) the file:

```text
docs/meta/spec.md
```

using the template from Chorus as your **basis and structure**.

Rules:

- **Follow the template sections in order.**  
  Do not reorder or drop major sections unless the template explicitly allows it.

- Populate sections based on **this package’s actual code and docs**:
  - Overview (purpose, non-goals)
  - Role in the ecosystem (cluster, context, typical usage)
  - Public surface (key classes, interfaces, traits, entry points)
  - Dependencies (Decode Labs + external)
  - Behaviour and contracts (invariants, inputs/outputs)
  - Error handling
  - Configuration & extensibility
  - Interactions with other packages
  - Usage examples
  - Implementation notes (for contributors)
  - Testing & quality
  - Roadmap & references

- Only describe what really exists:
  - Do **not** invent APIs, configuration options, or behaviours.
  - If the README contradicts the code, prefer the **code**.
  - If something is unclear or seems inconsistent, add a note like:
    ```markdown
    <!-- TODO: clarify behaviour of X in Y scenario -->
    ```

- Keep language consistent with existing Decode Labs specs:
  - Method names as verbs.
  - Clear statements of invariants only when they are actually enforced in code.
  - Mention relevant coding standards and patterns where helpful, but keep the focus on this package.

- Do **not** modify old report or log files if any exist in this repo; this command is for the main spec only.

---

## Step 4 — Scope and Safety

- Do **not** change any other repository.  
  Work only in the current package; do not edit Chorus from here.

- Do **not** refactor or rename code.  
  The package is considered “finished or stable” for this command.

- If you discover issues (e.g. unclear behaviour, missing tests, potential smells), you may:
  - Mention them briefly in the spec (e.g. under Implementation Notes or Roadmap), or
  - Add a `TODO` comment in the spec for human review.
  - But do **not** fix or refactor code as part of this command.

---

## Step 5 — Output

When you are done:

- Provide the full updated contents of `docs/meta/spec.md` in your response.
- Keep it as a single coherent Markdown document that could be dropped into the repo as-is.
- Do not include extra commentary outside the file contents unless explicitly asked.

Now:

1. Locate and read the Chorus package-spec template.
2. Analyse this package.
3. Generate or update `docs/meta/spec.md` accordingly, following all rules above.
