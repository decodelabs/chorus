# DecodeLabs AI-Integration Workflow  
**Authoritative Process for Safe, Versioned, AI-Assisted Development**

This document defines the **canonical workflow** for introducing AI into the DecodeLabs development ecosystem.  
It outlines how behavioural changes propagate from **library → legacy frameworks → client projects**, ensuring stability, correctness, and full traceability.

This workflow MUST be followed by all AI agents and human contributors.

---

# 1. Overview

DecodeLabs libraries form the backbone of multiple large, long-running client systems.  
They replace two legacy frameworks gradually and must remain:

- **stable**
- **predictable**
- **backwards compatible where practical**
- **safe for continuous deployment**

Many changes ripple across multiple repositories.  
This workflow ensures every change is:

1. **Designed and versioned first**
2. **Implemented in libraries in isolation**
3. **Propagated deliberately into legacy frameworks**
4. **Migrated carefully into client projects**
5. **Fully testable at every layer**

AI is a tool in this process — **never the source of truth**.

The single source of truth is:  
**Chorus documentation + versioned change specs**.

---

# 2. Roles of Repositories

### 2.1 Chorus (Meta / Architecture)
- The "air traffic control tower".
- Holds:
  - Architecture principles  
  - Package taxonomy  
  - Coding standards  
  - Templates (for README, package-spec, feature-spec, change-spec, AGENTS)  
  - AGENTS workflow descriptions  
  - **Change specs** (meta-level, migration-focused documents)  
  - Release notes & migration strategy documents  
- **Chorus is meta-only**: it does NOT store package-specific implementation specs.
- All changes originate here, not in code.

### 2.2 DecodeLabs Libraries
- Self-contained, high-quality libraries.
- Each library repository contains:
  - Implementation code
  - **Package specs** (`docs/meta/spec.md`) — detailed package specification
  - **Feature specs** (`docs/meta/features/*.md`) — detailed feature designs
  - README and other package-specific documentation
- No cross-repo edits allowed.
- Updated only according to a documented change spec.

### 2.3 Legacy Frameworks
- Transitional layers between old systems and DecodeLabs.
- Migrate AFTER library changes, BEFORE client projects.

### 2.4 Client Projects
- Production, revenue-critical applications.
- Must stay fully operational and testable at all times.
- Updated **last**, under strict gating.

---

# 3. Four-Phase Change Pipeline

Every behavioural or public API change follows this pipeline.

---

## Phase 0 — Design & Versioning in Chorus  
*(AI allowed: analysis, drafting, cross-repo reasoning; NO code changes anywhere)*

1. Create a **Change Spec** inside Chorus:  
   `docs/meta/releases/<package>/<YYYY-MM-DD>-<slug>.md`  
   
   **Note:** Change Specs live in Chorus because they describe ecosystem-wide migration plans and behavioural changes that span multiple repositories.
   
   For detailed feature designs that are package-specific, create a **Feature Spec** in the target package repository:  
   `<package-repo>/docs/meta/features/<feature-name>.md`

2. The change spec MUST include:
   - Problem being solved  
   - Impact on public API  
   - Behavioural differences  
   - SemVer classification (patch / minor / major)  
   - Migration strategy:
     - Library-level changes
     - Required framework updates
     - Required client migrations  
   - Deprecation plan  
   - Cross-repo sequencing constraints  
   - Risk areas & validation strategy  

3. AI may:
   - Inspect code
   - Identify usages across repos
   - Suggest migration steps
   - Propose API shapes

4. But YOU decide:
   - final API shape
   - the version bump
   - migration ordering

The change spec is now the **canonical instruction set** for all later phases.

---

## Phase 1 — Implement the Change in the Library  
*(AI allowed: code edits, tests, docs; restricted to ONE repo)*

Rules:

- Work only in the `decodelabs/<package>` repo.
- Follow **coding standards** & **package spec**.
- Update:
  - Code
  - Tests
  - `docs/meta/spec.md` (package specification in the package repo)
  - Feature specs in `docs/meta/features/` (if applicable, in the package repo)
  - README (if needed)
  - `CHANGELOG.md`
  - Composer version bump per SemVer
- Implement:
  - new behaviour
  - deprecations for old behaviour
  - backward-compatible shims (if viable)

AI must:

- Begin by reading the change spec in Chorus.
- Confirm all changes match that spec.
- Make no behavioural changes outside the spec.
- Never modify sibling repos.

Goal: Library is ready for consumption by frameworks, with no breaking surprises.

---

## Phase 2 — Migrate Legacy Frameworks  
*(AI allowed: codemods, refactors, fixes; restricted to ONE framework repo)*

Frameworks depend heavily on library internals.

Workflow:

1. AI analyses the framework's usage of the modified library APIs.
2. AI categorises changes:  
   - trivial rename  
   - signature shifts  
   - changed return types  
   - behaviour divergence  
3. AI drafts **migration steps**:
   - search-replace patterns  
   - Rector/php-cs-fixer rules  
   - bespoke refactor diffs  
4. Apply changes in a **framework-only** branch.
5. Run full test suite and manual smoke tests.

**Never** modify client repos in this phase.

Goal: Framework becomes fully compatible with the new library release.

---

## Phase 3 — Migrate Client Projects  
*(AI allowed: codemods & refactors; restricted to ONE client repo)*

This phase requires maximum caution.

1. Lock:
   - library versions (composer.json)
   - framework versions  
2. Generate a **client-specific migration plan** based on the change spec.
3. AI identifies all usage patterns in the client project.
4. AI proposes small, reviewable patches:
   - API renames  
   - type/pattern changes  
   - constructor injections  
   - routing / service layer updates  
   - test adjustments  
5. You review, test, and merge incrementally.

Rules:

- One client project at a time.
- One change spec (or carefully scoped set) at a time.
- Client must remain deployable and testable at all times.

Goal: Clients fully adopt the new library behaviour without regressions.

---

## Phase 4 — Deprecation Removal (Optional)
Only once:
- all frameworks are migrated,
- all client projects are migrated,
- deprecation window has passed,

…AI or human developers may remove deprecated paths in the library.

This triggers another SemVer bump and is handled like any other change spec.

---

# 4. Safe Use of AI in the Workflow

AI **may**:

- Analyse multiple repos
- Summarise usage patterns
- Draft migration plans
- Generate codemods
- Implement code within a single-repo boundary
- Update documentation & specs
- Validate consistency against Chorus rules

AI **may not**:

- Modify more than one repo at a time  
- Make undocumented cross-repo changes  
- Invent APIs or behaviours  
- Remove features without an explicit spec  
- Perform sweeping refactors across the ecosystem  
- Deviate from SemVer rules  
- Guess about ambiguous behaviour (must ask or add TODO)

---

# 5. Documentation as the Source of Truth

All behavioural changes and migration decisions must be recorded in:

- **Change Specs in Chorus** (`docs/meta/releases/<package>/...`) — meta-level migration plans  
- **Package Specs in package repos** (`<package-repo>/docs/meta/spec.md`) — detailed package specifications  
- **Feature Specs in package repos** (`<package-repo>/docs/meta/features/*.md`) — detailed feature designs  
- **Release notes** (in package repos or Chorus as appropriate)

**Important distinction:**
- **Change Specs** live in Chorus because they coordinate ecosystem-wide migrations and behavioural changes across multiple repositories.
- **Package Specs** and **Feature Specs** live in their respective package repositories because they describe implementation details specific to each package.

This ensures:

- reproducibility  
- clarity  
- stable future use by other agents  
- deterministic refactoring across repos  

If ever the process becomes unclear, agents MUST fall back to this document.

---

# 6. Summary Checklist for Any Change

**Before touching code**  
☐ Create a Change Spec in Chorus  
☐ Define SemVer impact  
☐ Define migration steps (library → frameworks → clients)

**Implement in library**  
☐ Follow coding standards  
☐ Add deprecations  
☐ Update package spec (`docs/meta/spec.md` in package repo)  
☐ Update feature specs if applicable (`docs/meta/features/` in package repo)  
☐ Write tests  
☐ Bump version

**Migrate frameworks**  
☐ Apply codemods  
☐ Update integrations  
☐ Run tests

**Migrate clients**  
☐ Apply codemods  
☐ Update tests  
☐ Smoke test deployments

**Finalize**  
☐ Remove deprecations in a later version  
☐ Close out documentation

---

# 7. When in Doubt

If an AI agent is ever uncertain:

- ask for clarification  
OR  
- write:

```markdown
<!-- TODO: clarify behaviour or migration requirement -->
```

Never guess.

---

# 8. Purpose of This Document

This workflow is the **ultimate fallback** for AI agents.  
If other docs are incomplete or missing, agents must use this to reconstruct:

- how to handle library changes  
- how to keep frameworks consistent  
- how to safely update clients  
- how to use SemVer as a guardrail  
- how to minimise risk in live systems  

This document ensures consistency across the entire ecosystem — even if other artefacts fall out of sync.
