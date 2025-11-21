---
title: New Feature Specification
description: Explore a new feature idea for a DecodeLabs package, refine it collaboratively, and write a detailed feature spec into docs/meta/features/.
promptVars:
  - name: packageName
    description: The name of the target package repository (e.g. remnant, lucid, exceptional)
  - name: featureSummary
    description: A freeform description of the new feature or idea you want to explore
---

# DecodeLabs Feature Specification Workflow

You are working with a new feature or idea for a specific DecodeLabs package.

The target package repository is located at:

```
../{{input:packageName}}
```

unless otherwise instructed.

The feature to be explored is:

> **{{input:featureSummary}}**

Your task is to help the user analyse the feature, examine technical and conceptual implications, discuss concerns, identify architectural constraints, and collaboratively refine the idea *before* producing a formal feature specification.

Follow the workflow below.

---

## Step 1 — Interactive Exploration (Conversation Phase)

Before writing any files, begin an **interactive discussion** with the user.

Goal: understand the intention, scope, edge cases, possible interactions with other DecodeLabs packages, naming, behaviour, constraints, and challenges.

During this phase, you must:

- Ask clarifying questions.
- Propose interpretations and alternative approaches.
- Identify any architectural conflicts or ecosystem-level considerations.
- Reference DecodeLabs principles from the Chorus repository:
  - `docs/architecture/principles.md`
  - `docs/architecture/package-taxonomy.md`
  - `docs/architecture/coding-standards.md`
  - Any relevant template under `docs/templates/`
- Highlight missing details or ambiguities.
- Confirm the final agreed-upon scope before proceeding.

**Do not generate or write any files yet.**
**Do not modify code.**
This phase ends only when the user explicitly says something like:
- “OK, proceed with the spec”
- “All right, generate the spec now”
- “That’s clear — write it up”

---

## Step 2 — Load the Chorus Feature Specification Template (If Present)

Look for a template in the Chorus repository in this order:

1. `../chorus/docs/templates/feature-spec.md`
2. `vendor/decodelabs/chorus/docs/templates/feature-spec.md`

If no template exists:

- Follow the structure of existing specs under `docs/meta/features/` (if any exist).
- Otherwise, use the DecodeLabs documentation style:
  - Clear, precise, strongly structured,
  - User-focused and contributor-focused,
  - No invented behaviour beyond the agreed-upon feature definition.

---

## Step 3 — Write the Feature Specification File

Once the design is agreed upon:

Create a new file in the target package at:

```
docs/meta/features/YYYY-MM-DD-HHMMSS-file-name.md
```

Where:

- The timestamp is in UTC.
- `file-name.md` is a kebab-case summary of the feature (derived from the user’s text unless explicitly provided).

Use the refined understanding from the discussion and the Chorus documentation to produce a **high-quality, complete, DecodeLabs-style feature spec**.

The spec must include:

### 1. Summary
A brief, clear statement of what the feature is and why it exists.

### 2. Motivation & Use Cases
Explain:
- The problem the feature solves  
- Examples of real-world use  
- Why this feature fits the DecodeLabs ecosystem  

### 3. Scope
Define:
- What the feature includes  
- What it explicitly does *not* include (non-goals)  

### 4. Behaviour Specification
Detailed description of:
- Public API additions  
- Interactions with existing types  
- Error-handling model  
- Edge cases  
- Optional vs required inputs  

All behaviour must be explicit and unambiguous.

### 5. Architectural Considerations
Explain how the feature fits the ecosystem:
- Relevant package cluster  
- How it interacts with neighbours  
- Constraints imposed by ecosystem standards  
- Risks or trade-offs identified during discussions  

### 6. Implementation Notes (For Agents & Contributors)
This section is written for future AI agents and human developers.

Include:
- Outline of recommended class & interface structures  
- Naming conventions  
- Method design expectations  
- Notes on where to be careful (security, validation, lifecycle, IO, performance)  
- Steps that must be taken before implementation (tests, invariants, decisions)  

Keep it practical and aligned with DecodeLabs style.

### 7. Testing Considerations
Describe:
- Required test coverage  
- Mocking requirements  
- Boundary and failure tests  
- Usual test directory structure  
- How to demonstrate correctness cleanly  

### 8. Future Extensions / Optional Ideas
Document any follow-on ideas that are out-of-scope now.

### 9. References
Link to:
- Chorus docs used
- Any relevant package specs
- Related features or RFCs

---

## Step 4 — Safety & Scope Rules

While performing this command:

- **Do not modify code** in any repository.
- **Do not make cross-repo changes**.
- Only create the feature spec file.
- If anything is unclear, ask the user instead of assuming.
- If the spec touches on multiple packages, document that *but do not alter other repos*.

---

## Step 5 — Output Instructions

Once all details are agreed and you’ve generated the feature spec:

- Output only the **full file contents** of the markdown file.
- Ask for confirmation before writing it to disk.
- After confirmation, write to:

```
../{{input:packageName}}/docs/meta/features/YYYY-MM-DD-HHMMSS-file-name.md
```

Then report success.

---

Begin with Step 1:  
Ask clarifying questions about the feature described in **{{input:featureSummary}}**.
