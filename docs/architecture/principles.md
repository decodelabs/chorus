# Decode Labs Architecture Principles & Conventions

This document describes the **shared design philosophy** and **coding conventions** that apply across the Decode Labs ecosystem.

It is written for:

- Developers **using** Decode Labs packages.
- Contributors **maintaining** or **building** new packages.
- Tools and AI assistants that need to reason about the system consistently.

Individual packages remain **independent** and **standalone**, but these principles make them feel like a coherent whole.

---

## 1. Ecosystem Philosophy

### 1.1 Independent Packages, Shared Philosophy

- Each package is a **standalone library**, versioned and released independently.
- There is no single monolithic “framework” package.
- When combined, these libraries form a **cohesive, flexible application stack** that can be adapted to many different requirements.
- Users are free to:
  - Install a single library in isolation, **or**
  - Use higher-level composition packages (such as Fabric) as a **starting point** for typical applications.

### 1.2 Explicit Composition over Hidden Magic

- Decode Labs favours **explicit composition** over hidden framework magic.
- Behaviour should be discoverable from:
  - Public APIs,
  - Configuration,
  - Clear conventions (such as Archetype-based discovery).
- Avoid:
  - Global state,
  - Hidden side effects,
  - Implicit behaviours that require extensive codebase knowledge to understand.

---

## 2. API Design Conventions

### 2.1 Methods Are Verbs

- Public methods should be named as **verbs**, clearly describing the action performed.
  - ✅ `handleRequest()`, `buildResponse()`, `loadConfig()`
  - ❌ `request()`, `response()`, `config()`
- Methods that **return** something should still describe an action:
  - `createFromArray()`, `fetchById()`, `resolveAlias()`.

### 2.2 Instance Properties vs Getter/Setter Methods

- Instance state should generally be exposed via **properties** rather than traditional getters/setters, where this is idiomatic and safe.
- Prefer properties when a value is:
  - Simple,
  - Readonly or set by the constructor,
  - Not requiring additional invariants on change.
- Use methods when:
  - The value is **computed**, **expensive**, or **context-dependent**,
  - Additional validation or invariants are needed,
  - Mutation triggers behaviour.

In short:

> **Data** → properties where appropriate.
> **Behaviour** → verbs.

#### Examples from the Codebase

**Properties for simple data:**
```php
// From remnant/src/Frame.php
class Frame
{
    public readonly FunctionIdentifier $function;
    public readonly ArgumentList $arguments;
    public readonly ?Location $callSite;
    public readonly ?Location $location;
}
```

**Property hooks for computed values:**
```php
// From atlas/src/Atlas/NodeTrait.php
trait NodeTrait
{
    public string $name {
        get => basename($this->path);
    }
}
```

**Methods for behaviour:**
```php
// From collections/src/Collections/Collection.php
interface Collection
{
    public function isEmpty(): bool;
    public function push(mixed ...$values): static;
    public function filter(?callable $callback = null): static;
}
```

### 2.3 Static Methods & Factory Patterns

- Static methods should be used for **factories**, **lookups**, and **creation helpers**, not as pseudo-instance getters.
- Good static method patterns:
  - `fromString()`, `createDefault()`, `fetchFromConfig()`, `create()`
- Avoid:
  - Static `getSomething()` methods that act like misplaced globals.

#### Examples from the Codebase

**Factory methods:**
```php
// From remnant/src/Frame.php
class Frame
{
    public static function create(
        int $rewind = 0
    ): Frame {
        // Factory implementation
    }

    public static function fromDebugBacktrace(
        array $frame
    ): self {
        // Factory from specific source
    }
}
```

**Static lookups (when appropriate):**
```php
// From dovetail/src/Dovetail/ConfigTrait.php
trait ConfigTrait
{
    public static function getRepositoryName(): string
    {
        return new ReflectionClass(static::class)->getShortName();
    }
}
```

### 2.4 Clear Responsibility per Class

- A class or interface should have a **single, clear responsibility**.
- If a class becomes a catch-all, break it down into:
  - Smaller services,
  - Dedicated value objects,
  - Or individual packages, when justified.

---

## 3. Discovery, Configuration, and Wiring

### 3.1 Archetype-Based Discovery

- Decode Labs uses **Archetype-based discovery** instead of central registries.
- This relies on:
  - Interfaces,
  - Namespace and folder conventions,
  - Predictable patterns rather than hard-coded lists.
- Discovery must be:
  - Documented,
  - Predictable,
  - Testable.

### 3.2 Separation of Concerns in Wiring

- Low-level packages should not know about application-level wiring or runtime composition.
- Higher-level packages (including Fabric and its bootstrapping dependencies) handle:
  - Dependency injection and configuration,
  - Selection of stack components,
  - Pipeline assembly (HTTP, routing, etc.).
- This separation keeps core packages reusable in many different contexts.

---

## 4. Dependencies and Layering (High-Level Guidance)

> **Note:** Detailed layering rules will be refined in a dedicated document.
> This section captures the initial intent.

- **Core utilities** (exceptions, collections, coercion, datasets, etc.) should:
  - Have **minimal dependencies**,
  - Avoid HTTP or application-specific concerns,
  - Be safe to use anywhere in the stack.
- **Infrastructure/service** packages (HTTP, queues, backups, filesystem, storage) may depend on core utilities but should avoid depending on high-level orchestrators.
- **Composition/entry-point** packages (e.g. Fabric) may depend on:
  - Core utilities,
  - Infrastructure packages,
  - Bootstrapping and DI/config packages.
- No package should **depend on Fabric**.

General rule of thumb:

> Dependencies should point **downwards** (toward more general, reusable code),
> not upwards into more specialised layers.

---

## 5. Error Handling and Exceptions (High-Level)

> Detailed behaviour will be covered in the `exceptional` package documentation.

- Exceptions are the preferred mechanism for error communication.
- Favour **specific exception types** over catch-all patterns.
- Core libraries should document which exceptions form part of their public contract.
- Avoid swallowing exceptions silently without explicit intent.

### 5.1 Exception Factory Pattern

Use the `Exceptional` factory for creating exceptions:

```php
use DecodeLabs\Exceptional;

// From collections/src/Collections/CollectionTrait.php
if (!static::Mutable) {
    throw Exceptional::DomainException(
        message: 'Cannot modify immutable collection'
    );
}

// From collections/src/Collections/CollectionTrait.php
throw Exceptional::UnexpectedValue(
    message: 'Combine failed - key count does not match item count'
);
```

### 5.2 Try* Pattern for Optional Returns

Methods that may not return a value should use the `try*` naming pattern:

```php
// Pattern: trySomething() returns ?Type, something() throws
public function tryResolve(
    string $id,
): ?Resource {
    // Returns Resource or null
}

public function resolve(
    string $id,
): Resource {
    return $this->tryResolve($id) ?? throw Exceptional::NotFound(
        message: 'Resource not found',
        data: ['id' => $id]
    );
}
```

This pattern makes the intent clear: `try*` methods are "safe" and return null, while non-`try*` methods guarantee a value or throw.

---

## 6. Development Strategy, Milestones, and Sequencing

### 6.1 What Milestones Mean

- Each package is assigned a **milestone** (`m1`–`m6`) which indicates **when it will be worked on** during the Decode Labs development process.
- Milestones are **not** maturity or stability levels.
- Instead, milestones reflect:
  - The **dependency graph** (foundational packages first),
  - Logical **work batches** that can be completed in development stints.

### 6.2 How Milestones Shape Development

- Work proceeds **bottom-up**, ensuring solid foundations:
  - `m1`: Lowest-dependency foundational packages
  - `m2`: Packages depending on `m1`
  - `m3`: Packages depending on `m1` and `m2`
  - …and so forth.
- This structure keeps:
  - Major architectural decisions early,
  - Higher-level packages from outpacing the stability of core layers.

### 6.3 What Milestones Do *Not* Mean

Milestones are not indicators of:

- API stability
- Production readiness
- Documentation completeness
- Test coverage
- Quality

These are tracked separately via the `scores` fields in `packages.json`.

---

## 7. Backwards Compatibility Philosophy

> A dedicated Backwards Compatibility Strategy will be written separately.

- The approach to BC is tied to the **v1 boundary**:
  - Before v1: APIs may evolve rapidly, decisions are intentionally front-loaded.
  - After v1: BC becomes the default; breaks require strong justification and ADR documentation.
- BC-impacting changes must:
  - Be documented through ADRs,
  - Provide migration guidance where feasible,
  - Be communicated clearly in release notes.

---

## 8. How to Use This Document

For **users**:

- Provides an understanding of how Decode Labs libraries are designed.
- Helps set expectations for API style, naming, and composition patterns.

For **contributors**:

- This is the baseline contract for new work and refactoring.
- When in doubt, align with these principles or start a discussion and file an ADR.

For **AI assistants and tools**:

- Use these principles as **constraints** when generating code, documentation, or architectural suggestions.
- Avoid proposing patterns that violate the design philosophy unless explicitly requested.

This document is **living** and should evolve as the ecosystem becomes more mature and cohesive.
