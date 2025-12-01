---
title: Create Change Spec
description: Create a new timestamped change-spec document in Chorus for a DecodeLabs package.
promptVars:
  - name: packageName
    description: The short name of the DecodeLabs package (e.g. remnant, lucid, exceptional)
  - name: changeSummary
    description: A short freeform description of the change (e.g. 'Refactor validation pipeline', 'Remove legacy router API')
---

# Create a New Change Specification

You are creating a **new Change Spec** for the package:

```
decodelabs/{{input:packageName}}
```

The user intends to describe a behavioural or public API change:

> **{{input:changeSummary}}**

Your job:

1. **Start with an interactive exploration**  
   Before generating any files, begin a conversation with the user.  
   Ask clarifying questions about:
   - What is changing  
   - Why  
   - Scope  
   - Behavioural impact  
   - Risks  
   - SemVer implications  
   - Legacy framework impact  
   - Client project impact  
   - Migration strategy  

   Continue refining until the user explicitly instructs:  
   **“OK, generate the spec”** or similar.

2. **Locate the Change Spec Template**  
   Look for the template file in this order:

   1. `../chorus/docs/templates/change-spec.md`
   2. `vendor/decodelabs/chorus/docs/templates/change-spec.md`

   If neither is available, ask for instructions.  
   **Do not guess the template structure.**

3. **Generate a Filename**  
   When asked to generate the spec, create a filename using:

   - Current UTC date: `YYYY-MM-DD`
   - Kebab-case slug generated from `{{input:changeSummary}}`

   Example:

   ```
   2025-01-20-refactor-validation-pipeline.md
   ```

4. **Write the File to Chorus**  
   The final path MUST be:

   ```
   ../chorus/docs/meta/releases/{{input:packageName}}/<timestamp-and-slug>.md
   ```

   Ensure the folder exists; create it if necessary.

5. **Populate Using the Template**  
   - Insert:
     - package name  
     - timestamp  
     - SemVer (ask the user or infer from the conversation but *never guess silently*)  
     - a Change ID matching the filename  
   - Fill in sections using:
     - user-provided details  
     - your own analysis  
     - code insights  
   - Preserve the template structure exactly.  
   - Add `<!-- TODO: ... -->` comments where information is missing.

6. **Write the file to disk**  
   Write the file to disk using the filename generated in step 3 and open the file in the editor.

7. **Once the file is written**  
   - Confirm the file path  
   - Remind the user that:
     - Library changes must follow Phase 1  
     - Framework migrations follow Phase 2  
     - Client migrations follow Phase 3  
     - No multi-repo changes should be performed by Cursor without explicit instructions

---

### Begin the process now

Start by asking the user detailed clarifying questions about the change  
described in: **{{input:changeSummary}}**  
and ensure you fully understand the intended behaviour, impact, and constraints before generating the formal Change Spec.
