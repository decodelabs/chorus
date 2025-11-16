# {{PACKAGE_NAME}} — Package Specification

> **Cluster:** `{{cluster}}`
> **Language:** `{{language}}`
> **Milestone:** `{{milestone}}`
> **Repo:** `https://github.com/decodelabs/{{repo_slug}}`

This document describes the purpose, contracts, and design of **{{PACKAGE_NAME}}** within the Decode Labs ecosystem.

It is aimed at:

- Developers **using** this package.
- Contributors **maintaining or extending** it.
- Tools and AI assistants that need to reason about its behaviour.

---

## 1. Overview

### 1.1 Purpose

- **What does this package do?**
- **What problem does it solve?**
- **In a sentence or two, why does it exist?**

Example prompts:

- “{{PACKAGE_NAME}} provides…”
- “Use this package when you need…”

### 1.2 Non-Goals

List things this package **deliberately does not do**, to avoid scope creep and confusion.

- It does **not** …
- It intentionally avoids …

---

## 2. Role in the Ecosystem

### 2.1 Cluster & Positioning

- **Cluster:** `{{cluster}}` (see Chorus taxonomy)
- How this package fits within that cluster:
  - Is it foundational, mid-level, or a leaf?
  - Does it define a pattern other packages follow?

### 2.2 Typical Usage Contexts

- Where in an application is this package normally used?
- Does it usually appear:
  - in HTTP request handling?
  - in CLI commands?
  - during bootstrapping?
  - in data pipelines?
  - in background jobs?

---

## 3. Public Surface

> Focus on *conceptual* API, not every symbol.

### 3.1 Key Types

List the main classes/interfaces/traits/enums that form the public API:

- `DecodeLabs\{{Namespace}}\Foo` — purpose in one line.
- `DecodeLabs\{{Namespace}}\BarInterface` — what it represents.
- Traits, value objects, exceptions that are part of the public contract.

### 3.2 Main Entry Points

How a user normally engages with this package:

- Static factories (e.g. `Foo::fromString()`).
- Service objects (e.g. `FooHandler->handle()`).
- Helper functions, if any.

If there is a “primary” way to use the package, highlight it.

---

## 4. Dependencies

### 4.1 Direct Decode Labs Dependencies

List key dependencies from `packages.json` and describe why they are needed:

- `decodelabs/xyz` — used for…
- `decodelabs/abc` — provides…

If a dependency is **semantically important** (not just incidental), call that out.

### 4.2 External Dependencies

- Any important external libraries or services.
- Notes on version expectations or compatibility constraints.

---

## 5. Behaviour & Contracts

### 5.1 Invariants

Document invariants that must always hold, for example:

- “Instances of `Foo` are immutable.”
- “`Bar::doThing()` must always return a sorted collection.”
- “This package must never throw raw `\Exception`; only specific exception types.”

### 5.2 Input & Output Contracts

For important operations:

- What kinds of inputs are accepted?
- What is returned?
- Under what conditions do operations fail or throw?

Focus on **behavioural promises** rather than implementation details.

---

## 6. Error Handling

### 6.1 Exception Types

- Which exceptions can be thrown as part of the public API?
- Which package(s) provide those exception types (e.g. `exceptional`)?

### 6.2 Error Strategy

- Does this package:
  - Prefer throwing exceptions immediately?
  - Aggregate errors?
  - Expose error objects or result wrappers?

Mention how this aligns with the overall Decode Labs error strategy (see Chorus docs for details).

---

## 7. Configuration & Extensibility

### 7.1 Configuration

- How is this package configured?
  - Environment/config files?
  - DI container (e.g. Monarch)?
  - Programmatic configuration?

- What are the **key configuration options** and what do they affect?

### 7.2 Extension Points

- Interfaces intended for user implementation.
- Hooks, events, callbacks, or extension mechanisms.
- Any conventions used for registering/extending behaviour (e.g. via Archetype).

---

## 8. Interactions with Other Packages

Describe important interactions with other Decode Labs packages, for example:

- “Typically used together with `decodelabs/harvest` to…”
- “Provides data structures consumed by `decodelabs/supermodel`.”
- “Used by `decodelabs/fabric` during bootstrapping.”

Highlight any **tight couplings** or **design assumptions**.

---

## 9. Usage Examples

Provide a couple of representative examples showing:

- **Basic usage** — simplest sensible case.
- **Typical integration** — how it’s used alongside neighbouring packages.
- Any **edge cases** that are common or non-obvious.

```php
use DecodeLabs\{{Namespace}}\{{PrimaryClass}};

$foo = {{PrimaryClass}}::fromString('example');
// …
```

Keep examples idiomatic with Decode Labs conventions:
- verbs for methods,
- properties for simple state where appropriate,
- clear composition.

---

## 10. Implementation Notes (For Contributors)

This section is for maintainers and contributors.

### 10.1 Internal Architecture

- Rough internal structure (modules, folders, layers).
- Notable patterns used (e.g. pipeline, adapter, visitor, etc.).
- Any internal boundaries that should not be broken.

### 10.2 Performance Considerations

- Known performance characteristics or trade-offs.
- Situations where this package might be a bottleneck.
- Any “don’t do this” style notes for heavy usage.

### 10.3 Gotchas & Historical Decisions

- Known tricky areas of the codebase.
- Decisions that were debated and resolved (with links to Chorus ADRs if relevant).
- Things that look odd but are **deliberate** and should not be “simplified” casually.

---

## 11. Testing & Quality

### 11.1 Testing Strategy

- What level of tests exist:
  - Unit, integration, functional, etc.
- Any special harnesses or fixtures.
- Notable things that are hard to test and how they are handled.

### 11.2 Quality Signals

Optionally reference:

- The `code`, `readme`, `docs`, `tests` scores from `packages.json`.
- Any known gaps:
  - “Tests are good, docs are thin.”
  - “API is stable, but error messages need work.”

---

## 12. Roadmap & Future Ideas

This is *non-binding* but useful context.

- Short-term improvements under consideration.
- Nice-to-have features that depend on other packages reaching certain milestones.
- Potential breaking changes that may be considered in a future major version.

Link to relevant GitHub issues or Chorus ADRs where appropriate.

---

## 13. References

- **Chorus docs:**
  - Architecture principles
  - Taxonomy & clusters
  - Any relevant ADRs
- **Other packages:**
  - Links to specs or READMEs of closely related packages.

```text
https://github.com/decodelabs/{{repo_slug}}
https://github.com/decodelabs/chorus
```

---

> This spec is intended to stay in sync with the **actual behaviour** of the package.
> When you make significant changes to the public surface or semantics, please update this document and, where applicable, add or update ADRs in Chorus.
