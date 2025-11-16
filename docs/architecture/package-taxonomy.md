# Package Taxonomy & Clusters

This document defines high-level **clusters** used to categorise Decode Labs packages.

The goals of this taxonomy are to:

- Make the ecosystem easier to understand at a glance.
- Provide a simple `cluster` field for the internal spreadsheet and `config/packages.json`.
- Help maintainers and tools reason about where new packages should live conceptually.
- Avoid hard coupling: clusters are for **explanation and analysis**, not strict runtime boundaries.

Packages remain **standalone**, but they share a common vocabulary.

---

## Cluster Overview

Each package belongs to a **single primary cluster**, represented as a lowercase slug.

The clusters currently in use are:

- `cli`
- `content`
- `core`
- `data`
- `frontend`
- `http`
- `integration`
- `io`
- `language`
- `logic`
- `observability`
- `runtime`
- `tooling`

The **authoritative cluster assignment** for each package is stored in:

- The internal Decode Labs package spreadsheet, and
- `config/packages.json` as a string field:

```json
"cluster": "core"
```

Some packages could conceptually sit in more than one cluster; in those situations, the chosen cluster should reflect the package’s **primary responsibility**.

This taxonomy is expected to evolve as the ecosystem grows.

---

## `core` — Foundational Utilities

**Purpose**
Low-level, highly reusable libraries that are safe to use anywhere in the stack.

**Typical responsibilities**

- Exceptions and error modelling
- Time and date utilities
- IP/URL parsing and related helpers
- Internationalisation primitives
- UUID and identifier utilities
- Basic type/format helpers (e.g. MIME types, colours)

**Characteristics**

- Minimal dependencies
- No HTTP, CLI, or app-specific knowledge
- Suitable for reuse by almost every other cluster

---

## `language` — Language-Level Extensions

**Purpose**
Packages that extend or refine how you write PHP itself, without being tied to any particular domain.

**Typical responsibilities**

- Type coercion and casting helpers
- Enum helpers/traits
- Fluid interface helpers

**Characteristics**

- Very close to the language/runtime
- Often used pervasively by other packages
- Focused on expressive, ergonomic code

---

## `runtime` — Application Lifecycle & Orchestration

**Purpose**
Libraries that describe how an application is **wired, configured, and started**.

**Typical responsibilities**

- Application containment and environment modelling
- Bootstrapping
- Dependency injection and containers
- Class resolution and discovery
- Queue/daemon management
- High-level entry points (e.g. framework starting point)
- Hook/event systems
- Entity lookup and façade/frontage-style patterns

**Characteristics**

- Know about the *shape* of an app
- Typically depend on `core` and `language`
- Provide the scaffolding used by HTTP, CLI, and other stacks

---

## `http` — HTTP Transport & Routing

**Purpose**
Everything centred on HTTP: servers, clients, and routing.

**Typical responsibilities**

- HTTP dispatching and middleware orchestration
- HTTP client abstraction and request building
- Routing and route resolution

**Characteristics**

- Understand HTTP requests, responses, headers, status codes, etc.
- Often depend on `runtime` and `core`
- Form the backbone of web-facing applications

---

## `cli` — Command-Line Runtime

**Purpose**
Packages for building, running, and interacting with CLI applications.

**Typical responsibilities**

- CLI input/output
- CLI dispatchers and runtime
- CLI entry points and command orchestration

**Characteristics**

- Focused on terminal interactions
- Often mirror some of the patterns used in `http` but for CLI contexts

---

## `io` — Files, Caches, Events & Low-Level Persistence

**Purpose**
Libraries that deal with **system-level IO** and data at rest or in transit at a low level.

**Typical responsibilities**

- Filesystem abstraction and file IO
- Caching and cache backends
- Code cache repositories
- IO event loops
- Process management

**Characteristics**

- IO-bound rather than domain-bound
- Used by various higher-level clusters (`runtime`, `data`, `content`, etc.)

---

## `data` — Modelling, Configuration, Access

**Purpose**
Packages that shape and move **data as data**, not directly concerned with IO details.

**Typical responsibilities**

- Data structures and collections
- Data modelling and schemas
- Data transfer interfaces
- Configuration representation and loading
- Query abstractions and RDBMS adapters
- High-level abstractions over data sources

**Characteristics**

- Sit between `core`/`language` and `io`/`runtime`
- Define what data *represents* and how it’s organised
- Often used by domain-specific libraries and services

---

## `logic` — Validation, Policies & Behavioural Rules

**Purpose**
Libraries that implement **behavioural logic** and rules around data and operations.

**Typical responsibilities**

- Validation and sanitisation
- Security policies (e.g. CSP)
- Captcha and similar challenge/response checks
- Identifier/reference parsing where semantic rules apply
- Scheduling, rules, or policy-style interactions
- AI-related behaviours and assistants, where treated as “smart logic”

**Characteristics**

- Use `core`, `language`, and often `data` heavily
- Do not typically own IO or storage concerns
- Provide reusable “logic blocks” that can be applied across domains

---

## `frontend` — HTML, Components, Assets, SSR

**Purpose**
Front-end and presentation-layer libraries for HTML, components, SSR, and asset pipelines.

**Typical responsibilities**

- HTML markup and component libraries
- Layout and document composition
- Vite/asset integration and config
- Component SSR and client bootstrapping

**Characteristics**

- Concerned with **presentation**, not business rules
- Often integrate with `http` and `runtime`
- May be combined with `content` for full rendering stacks

---

## `content` — Content & Representation

**Purpose**
Packages that transform, generate, or interpret content (text, markup, documents, media).

**Typical responsibilities**

- Content transformation pipelines
- HTML-to-PDF generation
- Parsing external content formats (e.g. tweets, tagged content)
- Content block management
- Translation and localisation content

**Characteristics**

- Often layered atop `frontend`, `io`, and `data`
- Focused on representation and transformation rather than app lifecycle

---

## `integration` — External Systems & Bridges

**Purpose**
Glue code between Decode Labs and external tools, frameworks, or services.

**Typical responsibilities**

- Composer integration tools
- Node/JS bridges
- Email and mailing list handling
- ORM / third-party tool integration
- External service adapters

**Characteristics**

- Depend on external ecosystems as well as Decode Labs libraries
- Typically thin wrappers or adapters
- Keep external concerns from leaking directly into `core` or `runtime`

---

## `observability` — Diagnostics & Insight

**Purpose**
Libraries that give insight into runtime behaviour, errors, and state.

**Typical responsibilities**

- Stack trace handling and enhanced error views
- Dump inspectors
- Source highlighting and debug tools
- Error handling layers oriented toward visibility

**Characteristics**

- Focused on **understanding** behaviour, not changing it
- Can be used alongside almost any other cluster

---

## `tooling` — Developer Tools & Utilities

**Purpose**
Packages that primarily improve the **development experience**, rather than application runtime behaviour.

**Typical responsibilities**

- Code generation and scaffolding
- Release management
- Developer playgrounds and example apps
- CLI tools that support development workflows
- Static analysis helpers

**Characteristics**

- Mostly used during development, CI, and release workflows
- May depend on a wide range of other clusters as needed

---

## Next Steps

- Keep the `cluster` assignment for each package up to date in:
  - The internal spreadsheet, and
  - `config/packages.json`.
- When a package’s responsibilities change significantly:
  - Revisit its cluster assignment,
  - Update the taxonomy (if examples or descriptions no longer fit).
- Use clusters for:
  - Architectural overviews,
  - Dependency analysis,
  - Deciding where new packages should live,
  - Orienting new contributors.

This taxonomy is intentionally **pragmatic** rather than rigid.
It should evolve as the codebase and ecosystem grow.
