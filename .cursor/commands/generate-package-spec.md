---
title: Generate Package Spec
description: Generate docs/meta/spec.md for a Decode Labs package in a sibling repo.
promptVars:
  - name: packageName
    description: The name of the sibling package repo (e.g. remnant, exceptional, lucid)
---

You are generating the file `docs/meta/spec.md` for the Decode Labs PHP package `{{input:packageName}}`.

The package repository is located as a sibling to Chorus.  
Please load it via the relative path:

```
../{{input:packageName}}
```

Before writing the spec:

1. Load the package repo:
   - Read `composer.json`
   - Read `README.md`
   - Inspect the `src/` directory (top-level namespaces + key classes)

2. Look up the **exact metadata** for this package from Chorus:
   `chorus/config/packages.json`, under the key:
   
   ```
   decodelabs/{{input:packageName}}
   ```

3. Use the **Decode Labs spec template structure** exactly:

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

- **Do not guess** about code not present in the repo.
- If the README contradicts the code, prefer the code.
- If unsure about design intent, add a comment such as `<!-- TODO: clarify X -->`.

- Do NOT mention specific PHP versions; instead say:
  “See `composer.json` for supported PHP versions.”

- Quality scores **must** match the metadata I provide, or be described qualitatively.

- Optional integrations (e.g. Monarch) must be described as:
  “Detected at runtime if installed, used for X.”

- Only list classes/interfaces/traits actually present in `src/`.

- Examples MUST reflect real APIs.

Output:

Produce the full `docs/meta/spec.md` file in Markdown.

The header must match exactly:

```
# {PackageName} — Package Specification

> **Cluster:** `{cluster}`
> **Language:** `{language}`
> **Milestone:** `{milestone}`
> **Repo:** `https://github.com/decodelabs/{repo}`
> **Role:** {short description}
```

Do not include anything before this header.

Once the metadata is pasted, read the repository, and generate the complete spec.
