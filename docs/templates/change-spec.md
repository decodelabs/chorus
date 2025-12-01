# Change Specification Template

> **Package:** `decodelabs/<package>`  
> **Change ID:** `<YYYY-MM-DD>-<short-slug>`  
> **Version:** `<x.y.z>`  
> **SemVer Impact:** `patch | minor | major`  
> **Status:** `draft | approved | implemented | migrated | deprecated | removed`  
> **Author:** <!-- name or handle -->  
> **Created:** <!-- YYYY-MM-DD -->

This document describes a **behavioural or public API change** for a DecodeLabs package.

It is the **canonical source of truth** for:

- what is changing,  
- why,  
- how it affects other repositories,  
- and how to migrate safely.

It is written to be consumed by both **human maintainers** and **AI agents**.

---

## 1. Summary

A concise description of the change:

- What is changing in the package  
- Whether it adds, removes, or modifies behaviour  
- The primary effect on library users  

Keep this to a short paragraph.

---

## 2. Motivation

Explain why this change is needed:

- What problem does it solve?  
- Why is the current behaviour insufficient or incorrect?  
- How does this change improve developer experience, correctness, performance, or architecture?  

If applicable, reference:

- Bug reports  
- Design discussions  
- Feature specs (e.g. `docs/meta/features/...`)

---

## 3. SemVer & Backwards Compatibility

### 3.1 SemVer Classification

State explicitly:

- `patch` — internal fixes that do not change public behaviour  
- `minor` — additive, backwards compatible public APIs  
- `major` — breaking public API or behavioural changes  

### 3.2 Backwards Compatibility Behaviour

Describe the intended BC story:

- Which APIs are being:
  - added  
  - deprecated  
  - changed  
  - removed  
- For deprecations:
  - How long will they remain?
  - In which future version will removal be considered?

Be explicit about **expected user impact**.

---

## 4. Library-Level Changes (decodelabs/<package>)

Describe the changes required in the library itself.

### 4.1 Public API Changes

List:

- New classes / interfaces / traits / methods  
- Changed signatures  
- Changed return types (including nullability / `try*` patterns)  
- Removed APIs (if any)  

For each, briefly describe:

- Purpose  
- Behaviour  
- Error-handling strategy (exceptions vs `null`)  

### 4.2 Internal Changes

Outline any significant internal restructuring that might matter for:

- Performance  
- Error semantics  
- Extensibility / hooks  
- Integration with other packages  

This section guides library implementation work.

---

## 5. Legacy Framework Impact

If this package is used by one or more legacy frameworks, describe:

- Which framework(s) are affected (e.g. “Legacy Framework A”, “Legacy Framework B”).  
- How they currently use the changing APIs.  
- What needs to change there, at a high level.

You do **not** need per-project detail here — just the framework-level implications:

- New integration points  
- Refactors or adapter layers required  
- Any sequencing rules (e.g. “Framework A must be updated before Clients X, Y, Z can upgrade this package”)  

---

## 6. Client Impact

Describe the expected impact on client projects:

- Common usage patterns likely affected  
- Typical changes client code will need (e.g. renaming methods, adjusting types, handling new exceptions)  
- Any breaking behaviour changes that may alter user-visible behaviour  

This section is a guide for **client migration planning**, not an exhaustive codemod spec.

---

## 7. Migration Plan

This is the operational heart of the spec.

Break it down by phase:

### 7.1 Phase 1 — Library Implementation

Steps to implement the change in the library, for example:

- [ ] Add new APIs  
- [ ] Implement deprecations / shims  
- [ ] Update `docs/meta/spec.md`  
- [ ] Update README (if needed)  
- [ ] Update tests and add coverage for new behaviour  
- [ ] Bump version in `composer.json` and `CHANGELOG`  

### 7.2 Phase 2 — Legacy Framework Migration

For each framework:

- [ ] Identify all usage sites  
- [ ] Define codemod patterns (renames, signature updates)  
- [ ] Update adapters, base classes, integration layers  
- [ ] Update framework docs if necessary  
- [ ] Run framework tests  

### 7.3 Phase 3 — Client Project Migration

High-level plan for clients:

- [ ] Define search patterns for affected APIs  
- [ ] Decide on order of client migrations  
- [ ] Apply codemods on each client project (one at a time)  
- [ ] Run tests & smoke checks per client  

### 7.4 Phase 4 — Deprecation Removal (Optional / Future)

If deprecations are introduced:

- [ ] Specify the target version for removal  
- [ ] Outline removal tasks (code, docs, specs)  

---

## 8. Risks & Open Questions

List known risks, for example:

- Behaviour changes that may be subtle but user-visible  
- Performance regressions  
- Security concerns  
- Edge cases that are not fully understood  

Also list open questions, e.g.:

- “Unclear how this interacts with feature X in package Y”  
- “Need confirmation on whether null should be allowed in case Z”  

AI agents must treat this section as **areas where they should not guess**.

---

## 9. Validation Strategy

Describe how we will gain confidence in the change:

- Unit tests  
- Integration tests  
- End-to-end tests  
- Manual QA steps  
- Any temporary feature flags or toggles (if applicable)  

Include:

- Which repos must have tests run  
- Any special environments or datasets needed  

---

## 10. Implementation Notes (For AI Agents & Contributors)

This section is explicitly for agents and human implementers.

Include guidance such as:

- Files or modules most likely to be affected  
- Coding patterns to follow or avoid  
- Relevant documentation in Chorus and the target package  
- Important invariants to preserve  
- Typical pitfalls (e.g. “do not try to change X until Y is updated”)  

Make it practical and concrete.

---

## 11. Status & Tracking

Keep track of progress over time.

### 11.1 Status Checklist

- [ ] Spec drafted  
- [ ] Spec approved  
- [ ] Library implementation complete  
- [ ] Legacy frameworks migrated  
- [ ] Client projects migrated  
- [ ] Deprecations removed (if applicable)  

### 11.2 Related Links

- Library repo PR(s):  
- Framework repo PR(s):  
- Client repo PR(s):  
- Related feature specs:  
- Related issues / tickets:  

---

<!--
Notes for Template Authors:

- Do not include package-specific text here.
- Keep this fully generic so it can be copied for any change.
- AI agents should be instructed to fill this out BEFORE touching code.
-->
