# Chorus Conversation Backlog

This document tracks the major design and documentation conversations needed to define the Decode Labs ecosystem and the role of Chorus.

It is primarily for maintainers and collaborators, but may also be useful context for advanced users.

---

## A. Positioning & Language: “What Is Decode Labs?”

**Goal:** Decide how to describe Decode Labs and Fabric without getting stuck on the word “framework”.

**Key topics:**

- Decode Labs as a vendor and ecosystem of libraries which, when combined, function as a framework.
- How to present Fabric as the conventional starting point for new applications.
- Preferred terminology for public docs (e.g. “ecosystem”, “stack”, “suite”, “framework system”).

**Planned outputs:**

- Positioning section in `docs/architecture/overview.md`.
- Short 2–3 sentence blurb for READMEs and external docs.

---

## B. Core Design Principles & Conventions

**Goal:** Capture the shared philosophy that applies to all Decode Labs packages.

**Key topics:**

- Independent packages, shared conventions and philosophy.
- Methods named as **verbs**; minimal use of `getX()` / `setX()` instance methods.
- Preference for properties over traditional getters/setters where appropriate.
- Static methods for “fetch”, “create”, “fromX” style operations.
- Use of Archetype for discovery and what that implies.
- General stance on composition, inheritance, configuration, and IO.

**Planned outputs:**

- `docs/architecture/principles.md`.
- Shortened version for future CONTRIBUTING guidelines.

---

## C. Ecosystem Shape & Layering Rules

**Goal:** Define obvious and sensible constraints on dependencies between packages.

**Key topics:**

- Informal strata:
  - Core utilities (e.g. exceptions, collections, coercion, datasets).
  - Infrastructure/services (HTTP, queues, storage, backups, filesystem, etc.).
  - High-level composition/entry points (e.g. Fabric).
- Initial rules of thumb, for later refinement:
  - Core utilities do not depend on HTTP or application-level concerns.
  - No package depends on Fabric.
  - Higher-level packages depend “downwards” only.

**Planned outputs:**

- Section in `docs/architecture/overview.md`.
- `docs/architecture/layers.md` describing intended dependency direction.

---

## D. Fabric & Bootstrapping

**Goal:** Explain how an application is assembled using Fabric and its related packages, without overloading Fabric conceptually.

**Key topics:**

- What Fabric actually does:
  - Pulls in a sensible baseline of dependencies.
  - Connects bootstrapping via Genesis.
  - Creates a Kingdom with Monarch (configuration and DI).
  - Loads Harvest (HTTP stack).
  - Loads Greenleaf (routing).
- What Fabric intentionally does *not* do.
- How users can start with Fabric or instead assemble their own stack directly from the underlying packages.

**Planned outputs:**

- `docs/architecture/fabric-and-bootstrapping.md`.
- Short summary paragraph for the architecture overview (once individual package specs exist).

---

## E. Development Strategy & Backwards Compatibility

**Goal:** Document the development model and approach to backwards compatibility.

**Key topics:**

- Milestone model (`m1–m6`):
  - Heavy decision-making and evolution before v1.
  - Post-v1 stability, with BC breaks only for very strong reasons.
- Relationship between milestones and BC expectations.
- How semantic versioning is applied across packages.

**Planned outputs:**

- BC strategy document or ADR, e.g. `docs/decisions/bc-strategy.md` or `docs/decisions/adr-0001-bc-strategy.md`.
- Brief reference in the overview.

---

## F. Package Taxonomy & Roles

**Goal:** Turn `config/packages.json` into a clear mental model for humans.

**Key topics:**

- How to talk about:
  - Core packages (e.g. Exceptional, Remnant, Wellspring, Coercion).
  - Infrastructure/services (HTTP, queues, backups, filesystem, etc.).
  - High-level composition (Fabric and related entry points).
- Whether to adopt “clusters” purely for explanatory purposes.

**Planned outputs:**

- `docs/architecture/packages-overview.md`.
- Optional “cluster” pages once individual specs exist.

---

## G. Per-Package Spec Template & Process

**Goal:** Standardise how individual package specs are written in their respective repositories.

**Key topics:**

- Template for `<package>/docs/meta/spec.md`, including:
  - Purpose and non-goals.
  - Public surface (key classes, interfaces, and patterns).
  - Invariants and constraints.
  - Dependencies and their rationale.
  - Internal architecture notes.
  - Cross-package implications.
- Prioritisation of packages (e.g. lowest-dependency, low-milestone packages first).

**Planned outputs:**

- `docs/meta/package-spec-template.md`.
- Agreed ordering for which packages to spec first.

---

## H. Anchor Package Deep Dives

**Goal:** Produce high-quality specs for a small number of foundational packages to act as reference points.

**Candidate packages:**

- Exceptional (error model).
- Remnant (immutable containers / collections).
- Wellspring (data hydration / datasets).
- Genesis (bootstrapping).
- Harvest (HTTP stack).
- Greenleaf (routing).
- Kingdom + Monarch (configuration / DI).

**Planned outputs (per package):**

- `<package>/docs/meta/spec.md` in the package repository.
- Optional short summary pages in Chorus linking to these specs.

---

## I. AI, Cursor, and Codex Workflow

**Goal:** Define how AI assistants and tools should be used in the development process.

**Key topics:**

- Roles:
  - Human maintainer as lead architect.
  - ChatGPT as high-level architect/spec writer/reviewer.
  - Cursor and Codex as implementation engines.
- Preferred workflow:
  - Spec first, implementation second.
  - When to update specs vs when to write ADRs vs when to touch code.
- Guardrails around automated changes to public APIs.

**Planned outputs:**

- `docs/workflows/ai-assistance.md`.

---

## J. Documentation Information Architecture

**Goal:** Prevent documentation sprawl by clarifying the purpose of each docs area.

**Key topics:**

- Roles of:
  - `docs/architecture/`
  - `docs/decisions/`
  - `docs/workflows/`
  - `docs/meta/`
- Relationship between Chorus docs and package-local docs.
- Expectations for keeping `packages.json` and documentation aligned conceptually.

**Planned outputs:**

- `docs/meta/docs-structure.md`.
- Possible additions to the Chorus `README.md`.
