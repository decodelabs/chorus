# AI Workflow: Generating Package Specs (`docs/meta/spec.md`)

This document describes how to use AI (via Cursor and/or Codex CLI) to generate and maintain `docs/meta/spec.md` for each Decode Labs package.

The goals are to:

- Produce **consistent, high-quality package specifications** across all repos.
- Keep specs **close to the code** (in each package’s own repo).
- Use Chorus (`config/packages.json`) as the **index**, not the home of package-level specs.

---

## When to Use This Workflow

Use this workflow when:

- A package **does not yet have** `docs/meta/spec.md`.
- A package’s spec is **out of date** and needs a refresh after major changes.
- You want to bring a **cluster** of packages (e.g. all `core` or all `runtime`) up to the same documentation standard.

Suggested approach:

- Use **Cursor** interactively for:
  - The first few “anchor” packages.
  - Any package with complex or subtle semantics.
- Use **Codex CLI** or batch scripts once you’re happy with:
  - The template,
  - The tone,
  - The quality of a few initial specs.

---

## Inputs for Each Package

For each package, the AI needs:

1. The package repo itself, including:
   - `composer.json`
   - `README.md`
   - `src/` (at least top-level namespaces and key classes)

2. Metadata from Chorus `config/packages.json`:
   - `cluster`
   - `language`
   - `milestone`
   - short role/description
   - quality scores (optional)

3. The **spec structure** (this is implied in the prompt but summarised here):

   - Overview  
     - Purpose  
     - Non-Goals  
   - Role in the Ecosystem  
     - Cluster & Positioning  
     - Usage Contexts  
   - Public Surface  
     - Key Types  
     - Main Entry Points  
   - Dependencies  
     - Decode Labs  
     - External  
   - Behaviour & Contracts  
     - Invariants  
     - Input & Output Contracts  
   - Error Handling  
   - Configuration & Extensibility  
   - Interactions with Other Packages  
   - Usage Examples  
   - Implementation Notes (for Contributors)  
   - Testing & Quality  
   - Roadmap & Future Ideas  
   - References  

This structure should be consistent across all packages.

---

## Cursor Usage (Interactive)

When working in **Cursor**:

1. Open the package repo.
2. Ensure `composer.json`, `README.md`, and `src/` are visible to the agent.
3. Grab the relevant entry from `config/packages.json` (in Chorus) and paste it into the chat.
4. Paste the **Package Spec Generation Prompt** (see below).
5. Let the agent generate or update `docs/meta/spec.md`.
6. Manually review and edit as needed.
7. Commit the spec with a message such as `docs: add package spec` or `docs: update package spec`.

---

## Codex CLI Usage (Batch)

When using **Codex CLI** or scripts:

- Use the same prompt as below, but:
  - Feed `composer.json`, `README.md` and selected `src/` files as input.
  - Provide the `packages.json` entry for that package as part of the prompt.
  - Capture the output to `docs/meta/spec.md`.

This is best used once you’re happy with the results for a few representative packages.

---

## Package Spec Generation Prompt

This is the canonical prompt to use when generating `docs/meta/spec.md` for a package.

> **Important:**  
> - Do NOT edit the structure/order of headings it describes.  
> - Do NOT let agents “simplify” this prompt in their own words.  
> - If you tweak this prompt, update this document so it remains the single source of truth.

```text
You are generating the file `docs/meta/spec.md` for THIS Decode Labs PHP package.

Your job is to produce a **complete, accurate package specification** following the Decode Labs spec template. Describe ONLY what is actually implemented in this repository. Do NOT invent APIs, classes, behaviours, or configuration options that do not exist.

Before writing the spec:

1. Read:
   - `composer.json`
   - `README.md`
   - The `src/` folder (at least top-level namespaces and key classes)

2. I will give you this package’s metadata from Chorus `config/packages.json`, including:
   - `cluster`
   - `language`
   - `milestone`
   - description / role
   - quality scores (optional)

3. Use the **official spec template structure** exactly:

   - Overview  
     - Purpose  
     - Non-Goals  
   - Role in the Ecosystem  
     - Cluster & Positioning  
     - Usage Contexts  
   - Public Surface  
     - Key Types  
     - Main Entry Points  
   - Dependencies  
     - Decode Labs  
     - External  
   - Behaviour & Contracts  
     - Invariants  
     - Input & Output Contracts  
   - Error Handling  
   - Configuration & Extensibility  
   - Interactions with Other Packages  
   - Usage Examples  
   - Implementation Notes (for Contributors)  
   - Testing & Quality  
     - Roadmap & Future Ideas  
   - References  

FOLLOW THIS ORGANISATION EXACTLY. Do not rearrange headings.

Guidelines & rules:

- **Do not guess**. If something is not present in the code, do not describe it.  
- If the README contradicts the code, prefer the code.  
- If unsure about behaviour or design rationale, add a comment like:  
  `<!-- TODO: clarify X -->`

- Do NOT explicitly state a PHP version requirement. Instead write:  
  “See `composer.json` for supported PHP versions.”

- When describing quality scores:  
  - Either use the EXACT values I provide from `packages.json`,  
  - Or describe them qualitatively (e.g. “Tracked centrally in Chorus”).  
  Never invent numbers.

- For optional integrations (like Monarch or Genesis):  
  - Do NOT describe them as dependencies.  
  - Use phrasing such as:  
    “This package detects X at runtime (if installed) and uses it for Y.”

- When listing public types:  
  - Only include real classes/interfaces/traits in `src/`.  
  - Summaries must reflect their real purpose.  
  - Do NOT document internal/private helpers.

- When writing examples:  
  - Use real APIs from this package.  
  - Prefer minimal but idiomatic examples.  
  - Follow Decode Labs conventions (method names as verbs, modern PHP features, static factories when present).

- When describing invariants:
  - Only include invariants that demonstrably exist in the code.

Output:

- The full `docs/meta/spec.md` file in Markdown.
- The top block must match EXACTLY the following format:

  ```
  # {PackageName} — Package Specification

  > **Cluster:** `{cluster}`
  > **Language:** `{language}`
  > **Milestone:** `{milestone}`
  > **Repo:** `https://github.com/decodelabs/{repo}`
  > **Role:** {short description}
  ```

Do not include anything else before this header.

After reading the repo and metadata, produce the completed `docs/meta/spec.md`.
```

---

## Human Review Checklist

After the AI generates a spec:

- [ ] Does the **Overview** accurately reflect what the package really does?  
- [ ] Are **non-goals** clearly stated and correct?  
- [ ] Does the **Public Surface** list only real, intended-for-use classes/interfaces?  
- [ ] Are any **APIs or behaviours invented** that don’t exist in code? (If yes, remove.)  
- [ ] Are **dependencies** described correctly (and optional integrations clearly marked as such)?  
- [ ] Do **examples** compile in principle and match the real API?  
- [ ] Are there any TODO comments that need quick clarification?

For especially important packages (e.g. Exceptional, Remnant, Dovetail, Lucid, Harvest), expect to spend a little extra time polishing the spec manually.

---

## Notes

- This workflow is intentionally conservative: it prefers **accuracy over creativity**.  
- As the ecosystem evolves, update:
  - This workflow document,
  - The spec template (if needed),
  - And the metadata in `config/packages.json`.

Specs should evolve alongside the code, not drift away from it.
