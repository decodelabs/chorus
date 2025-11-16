# Decode Labs — Chorus

**Chorus** is the meta-layer for the Decode Labs ecosystem.
Where the individual packages contain user-facing code and documentation, Chorus provides the **architectural map**, **cross-cutting decisions**, and **package index** that define how the entire suite fits together.

Chorus is not a runtime library.
It is the **reference point for humans and AI assistants** when designing, documenting, and evolving Decode Labs packages and projects built on top of them.

---

## Purpose

Chorus exists to:

- Describe the **overall architecture** of the Decode Labs ecosystem.
- Maintain a **canonical index** of all packages, including their roles, milestones, and dependencies.
- Host **cross-package documentation**, such as coding standards, naming rules, error-handling strategy, and architectural principles.
- Record **Decision Documents (ADRs)** that explain the rationale behind major choices.
- Provide a stable place for **high-level specifications** and design discussions that span multiple repositories.

Package-specific documentation remains in each package’s own repository.
Chorus focuses only on the parts that *bind those packages together*.

---

## What Chorus Contains

### 1. Package Index
A machine-readable description of every Decode Labs package:
- Name
- Repository location
- Role and responsibility
- Language
- Dependencies
- Milestone/stability
- Documentation references

### 2. Architecture Documentation
System-level guides explaining:
- How packages relate to one another
- Intended dependency directions
- Architectural “clusters”
- Core concepts shared across repositories

### 3. Decision Records (ADRs)
A curated log of cross-cutting decisions:
- Error model
- Logging and observability
- Naming conventions and coding standards
- Discovery mechanisms (e.g. Archetype)
- Deprecation and backwards-compatibility strategy

ADRs help ensure long-term consistency as the ecosystem grows.

### 4. Workflow & Standards
Guidelines that define how Decode Labs packages should:
- Be structured
- Expose public APIs
- Document behaviour
- Introduce or evolve features
- Interact with AI assistants, Cursor, and code generators

---

## What Chorus Does *Not* Contain

- **Runtime code**
- **User-facing package documentation**
- **Tests**
- **Implementations**

Those belong in their respective package repositories.
Chorus is purely an **architectural and organisational layer**.

---

## Contributing

Contributions to Chorus should focus on:
- Improving architectural clarity
- Adding or refining decision documents
- Updating the package index
- Enhancing cross-package documentation
- Recording discussions and design rationale

Changes that affect a specific package’s behaviour should be made in that package’s repository, with Chorus updated only if the change has architectural or ecosystem-wide significance.

---

## Licence

Chorus is licensed under the MIT License. See [LICENSE](./LICENSE) for the full license text.
