# {PackageName} — Package Specification

> **Cluster:** `{cluster}`
> **Language:** `{language}`
> **Milestone:** `{milestone}`
> **Repo:** `https://github.com/decodelabs/{repo}`
> **Role:** {short description}

This document describes the purpose, contracts, and design of **{PackageName}** within the Decode Labs ecosystem.

It is aimed at:

- Developers **using** {PackageName} in their own applications or libraries.
- Contributors **maintaining or extending** {PackageName}.
- Tools and AI assistants that need to reason about its behaviour.

---

## 1. Overview

### 1.1 Purpose

{2-4 sentences describing what the package does and why it exists. Focus on the core value proposition.}

### 1.2 Non-Goals

{PackageName} does **not**:

- {List what the package intentionally does not do}
- {Avoid scope creep and clarify boundaries}

---

## 2. Role in the Ecosystem

### 2.1 Cluster & Positioning

- **Cluster:** `{cluster}` (see Chorus taxonomy)
- {1-2 sentences about where this package fits in the ecosystem and its dependency level}

### 2.2 Typical Usage Contexts

Typical places {PackageName} appears:

- {Context 1: e.g., "HTTP request handling"}
- {Context 2: e.g., "CLI commands"}
- {Context 3: e.g., "Configuration processing"}

{PackageName} is intended to be used whenever {describe when/why to use this package}.

---

## 3. Public Surface

> This section focuses on the conceptual API, not every symbol.

### 3.1 Key Types

The primary public types are:

- `{Namespace}\{Type}`
  {Brief description of what this type represents and its role.}

{List other key public types with brief descriptions.}

### 3.2 Main Entry Points

The main usage pattern is {describe the primary way to use this package}:

```php
{Example code showing the main entry point}
```

{Explain key concepts or patterns used by the package.}

---

## 4. Dependencies

### 4.1 Direct Decode Labs Dependencies

From `composer.json`:

- `decodelabs/{dependency}`
  {Brief description of why this dependency is needed.}

{List other Decode Labs dependencies.}

**Optional integration:**

- `decodelabs/{optional-package}` (optional)
  {Describe how this optional integration works, e.g., "detected at runtime if installed"}

### 4.2 External Dependencies

{List external dependencies and why they're needed, or state "None required for runtime operation" if applicable.}

See `composer.json` for supported PHP versions.

---

## 5. Behaviour & Contracts

### 5.1 Invariants

- {Invariant 1: e.g., "All methods must never mutate input parameters"}
- {Invariant 2: e.g., "Return values are always of the specified type"}
- {List demonstrable invariants that exist in the code}

### 5.2 Input & Output Contracts

{Describe the contracts for key methods or operations. Include:}

- {What inputs are accepted}
- {What outputs are guaranteed}
- {Any constraints or preconditions}

---

## 6. Error Handling

### 6.1 Exception Types

{PackageName} throws {describe exception types used}:

- `{ExceptionType}`: {When this is thrown}
- {List other exception types}

### 6.2 Error Strategy

{Describe the error handling approach: e.g., "fail-fast", "graceful degradation", "uses Exceptional pattern", etc.}

---

## 7. Configuration & Extensibility

### 7.1 Configuration

{Describe how the package is configured, or state "No runtime configuration is required" if applicable.}

### 7.2 Extension Points

{PackageName} supports extension via:

- {Extension point 1: e.g., "Custom filter implementations"}
- {Extension point 2: e.g., "Trait mix-ins"}

{Or state "This package is not designed to be extended" if applicable.}

---

## 8. Interactions with Other Packages

{PackageName} is designed to be used by / uses other packages:

- **`decodelabs/{related-package}`**
  {Describe the relationship}

{List other package interactions.}

Design assumptions:

- {Assumption 1: e.g., "Available early in the stack"}
- {Assumption 2: e.g., "Safe to use from any layer"}

---

## 9. Usage Examples

### 9.1 {Example Name}

```php
{Example code showing a common use case}
```

### 9.2 {Another Example}

```php
{Another example demonstrating different functionality}
```

{Add more examples as needed.}

---

## 10. Implementation Notes (For Contributors)

### 10.1 Internal Architecture

At a high level, {PackageName}:

- {Architectural point 1}
- {Architectural point 2}
- {Describe key implementation details}

Contributors should:

- {Guidance 1: e.g., "Preserve separation of concerns"}
- {Guidance 2: e.g., "Avoid introducing framework dependencies"}

### 10.2 Performance Considerations

- {Performance consideration 1}
- {Performance consideration 2}

### 10.3 Gotchas & Historical Decisions

- {Gotcha 1: e.g., "Why X was chosen over Y"}
- {Historical decision: e.g., "Legacy support for Z"}

---

## 11. Testing & Quality

### 11.1 Testing Strategy

Tests should cover:

- {Test area 1: e.g., "Core functionality"}
- {Test area 2: e.g., "Edge cases"}
- {List what should be tested}

### 11.2 Quality Signals

From the Decode Labs package index (at time of writing):

- **Code:** {score or "Tracked centrally in Chorus"}
- **Readme:** {score or "Tracked centrally in Chorus"}
- **Docs:** {score or "Tracked centrally in Chorus"}
- **Tests:** {score or "Tracked centrally in Chorus"}

{PackageName} is {describe quality/stability level}.

---

## 12. Roadmap & Future Ideas

Non-binding ideas:

- {Future idea 1}
- {Future idea 2}
- {List potential improvements or features}

---

## 13. References

- **Chorus docs:**
  - Architecture principles
  - Package taxonomy & clusters
  - Backwards compatibility strategy (once published)

- **Related packages:**
  - `decodelabs/{related-package}` ({relationship})

- **Repository:**
  - `https://github.com/decodelabs/{repo}`

---

> This spec is intended to stay in sync with the **actual behaviour** of the package.
> When you make significant changes to the public surface or semantics, please update this document and, where applicable, add or update ADRs in Chorus.

