# Decode Labs Coding Standards (PHP)

This document captures cross-cutting coding standards for Decode Labs PHP libraries.

It complements, but does not replace:

- The **architecture principles** (overall design philosophy).
- Per-package specs (detailed behaviour and public contracts).

These rules apply to all Decode Labs PHP code unless a package explicitly documents an exception.

---

## 1. Baseline: php-cs-fixer Ruleset

Decode Labs uses **php-cs-fixer** with the following base ruleset:

```text
"@PSR12,-method_argument_space,array_syntax"
```

That means:

- `@PSR12`  
  Start from the **PSR-12** coding standard preset (the official PHP coding style recommendations).  
  This covers things like:
  - basic file structure,
  - indentation,
  - spacing around operators and control structures,
  - visibility/ordering rules for properties and methods,
  - brace placement rules, etc.

- `-method_argument_space`  
  **Disable** the `method_argument_space` rule from PSR-12.

  PSR-12 normally enforces specific spacing for method arguments (e.g. `foo($a, $b)` with strict rules about spaces after commas and around variadics).  
  Decode Labs diverges from this because we want **tighter control** over multi-line parameter formatting (see below) that doesn’t always align with the default rule’s behaviour.

- `array_syntax`  
  **Override** the array syntax rule:
  - Enforces the use of **short array syntax** (`[]`) instead of `array()`.

In short:

> Use PSR-12 as a base,  
> disable its method-argument spacing rule,  
> and always use `[]` for arrays.

More detailed fixer configuration (including extra rules) may live in repository-local `.php-cs-fixer.php` files, but should not contradict these core standards.

---

## 2. Naming Conventions: camelCase

Decode Labs uses **camelCase** for method names and variable names (including parameters and properties).

### 2.1 Methods and Variables

- **Methods**: `handleRequest()`, `buildResponse()`, `processData()`
- **Variables**: `$userName`, `$requestCount`, `$isValid`
- **Parameters**: `function processData(string $dataSource, int $maxItems)`
- **Properties**: `public string $displayName`, `private bool $isActive`

### 2.2 Why camelCase?

- **Consistency with PSR-12**: PSR-12 recommends camelCase for methods and variables, aligning with PHP community standards.
- **PHP ecosystem alignment**: Most PHP frameworks and libraries (Symfony, Laravel, Doctrine) use camelCase, making Decode Labs code familiar to PHP developers.
- **Tooling support**: PHP's reflection APIs, IDEs, and static analysis tools expect camelCase conventions.

### 2.3 Exceptions

- **Constants** and **Enum case names**: Use `PascalCase` for class constants and enum case names (e.g., `public const MaxRetries = 3`) - we don't need to keep shouting any more!!
- **Class names**: Use `PascalCase` (e.g., `UserProfile`, `RequestHandler`).

---

## 3. Method Signature Formatting

### 3.1 Parameters on New Lines

If a method **has parameters**, they must be written with:

- The opening parenthesis on the same line as the function name.
- **Every parameter on its own line.**
- The closing parenthesis and return type on their own line.
- The opening brace on the same line after the return type.

Example:

```php
public function doThing(
    string $name,
    int $count,
    ?DateTimeImmutable $when,
): void {
    // ...
}
```

Notes:

- Always include a trailing comma after the last parameter in multi-line parameter lists.
- This applies equally to:
  - functions,
  - methods,
  - constructors,
  - closures (where practical).

### 3.2 No-Parameter Methods

If a method has **no parameters**, do **not** add extra blank lines in the parameter list. The brace must still be on a new line after the return type:

```php
public function reset(): void
{
    // ...
}
```

This keeps signatures dense when simple, and vertically aligned when complex.

---

## 4. Nullable Return Types and `try*` Naming

For methods that can “optionally” return a value, the default convention is:

- The **nullable-returning** variant is named `trySomething()` and returns `?Type`.
- The **non-nullable** variant is named `something()` and throws an exception when a value cannot be returned.

Example:

```php
public function tryResolve(
    string $id,
): ?Resource {
    // Returns a Resource or null if not found
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

Guidelines:

- **Nullable return types** that represent “value may or may not exist” should generally follow this `try*` + non-`try*` pair pattern.
- The non-`try*` variant is free to perform additional work (e.g. logging) before throwing, but its primary role is a **strict accessor**.
- Not every nullable return is required to follow this pattern:
  - For example, where `null` is a natural and unambiguous domain value (not just “not found”), or where an exception would be inappropriate.
- Use **common sense** when applying this rule:
  - Prefer clarity about intent over mechanical renaming.
  - When in doubt, document the reasoning in the package spec or in a short docblock.

---

## 5. Properties over Getters/Setters

Where appropriate:

- Prefer **properties** for simple data, especially on value objects and DTO-like classes.
- Avoid traditional Java-style `getSomething()` / `setSomething()` methods for instance state when:
  - no additional logic is needed, and
  - the value conceptually belongs directly to the object.

Example:

```php
final class UserProfile
{
    public function __construct(
        public readonly string $id,
        public string $displayName,
    ) {}
}
```

Instead of:

```php
final class UserProfile
{
    private string $id;
    private string $displayName;

    public function getId(): string { /* ... */ }
    public function getDisplayName(): string { /* ... */ }
    public function setDisplayName(string $name): void { /* ... */ }
}
```

Notes:

- **Static** methods like `getSchema()` / `getDefaultConfig()` are still acceptable where they clearly act as **factories or information providers**.  
  If they perform work or IO, prefer verb names like `buildSchema()`, `fetchConfig()`, etc.
- Future revisions of this document may formalise additional rules around properties vs accessors.

---

## 6. Method Names as Verbs

Method names should be **verbs** or verb phrases that describe the action being performed:

- `handleRequest()`, `buildResponse()`, `loadConfig()`, `createFromArray()`
- Avoid bare nouns like `request()`, `response()`, `config()` when they represent behaviour.

This aligns documentation, specs, and code and makes intent clearer to both humans and AI tools.

---

## 7. Interfaces and Traits

### 7.1 Interface-Trait Pairing Pattern

When an interface requires implementation, use a **trait** with the naming convention:

- Interface: `Collection`
- Trait: `CollectionTrait`

The trait name is the interface name followed by `Trait`.

Example:

```php
interface Collection
{
    public function isEmpty(): bool;
    public function push(mixed ...$values): static;
}

trait CollectionTrait
{
    protected array $items = [];

    public function isEmpty(): bool
    {
        return empty($this->items);
    }

    public function push(mixed ...$values): static
    {
        // Implementation
    }
}

class Sequence implements Collection
{
    use CollectionTrait;
}
```

### 7.2 Trait Requirements

Traits should use PHPStan annotations to document interface requirements:

```php
/**
 * @phpstan-require-implements Collection
 */
trait CollectionTrait
{
    // Implementation
}
```

This helps static analysis tools understand the contract.

### 7.3 When to Use Interfaces vs Abstract Classes

- **Interfaces** for:
  - Public contracts that multiple implementations may satisfy
  - Type hints and dependency injection
  - Defining the "shape" of an API

- **Abstract classes** for:
  - Shared implementation that cannot be expressed in traits
  - When you need to control constructor behaviour
  - When you need protected members that traits cannot provide

- **Traits** for:
  - Reusable implementation that can be mixed into multiple classes
  - Implementation of interface contracts
  - Cross-cutting concerns

---

## 8. Properties and Property Hooks (PHP 8.4+)

### 8.1 Property Hooks

Decode Labs uses PHP 8.4 property hooks extensively for:

- Read-only properties with computed values
- Properties with getters/setters that maintain invariants
- Properties that need lazy initialization

Example patterns:

```php
interface Node
{
    public string $path { get; }
    public string $name { get; }
}

trait NodeTrait
{
    public string $name {
        get => basename($this->path);
    }
}
```

### 8.2 Readonly Properties

Use `readonly` for immutable data:

```php
class Frame
{
    public function __construct(
        public readonly FunctionIdentifier $function,
        public readonly ArgumentList $arguments,
        public readonly ?Location $callSite = null,
    ) {}
}
```

### 8.3 Protected Set Access

Use `protected(set)` for properties that should be readable publicly but only settable internally:

```php
class Config
{
    public protected(set) Config $config;
    public protected(set) bool $local = false;
}
```

### 8.4 Property Visibility

- **Public properties**: For simple data that doesn't need encapsulation
- **Protected properties**: For internal state that subclasses may need
- **Private properties**: For truly internal implementation details

---

## 9. Static Factory Methods

Static factory methods are preferred over constructors when:

- The constructor would require complex setup
- You need to return different types based on input
- You want to provide named, self-documenting creation methods

Naming conventions:

- `create()` — Simple factory
- `fromDebugBacktrace()` — Factory from specific source
- `fromString()` — Factory from string representation
- `fromArray()` — Factory from array data

Example:

```php
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

---

## 10. PHPStan Configuration

### 10.1 Standard Configuration

All packages should use PHPStan with:

- **Level**: `max` (preferred) or `9` (minimum)
- **Paths**: Include both `src/` and `tests/` when tests exist
- **Extensions**: Include `phpstan-extension.neon` if the package provides one

Example `phpstan.neon`:

```yaml
includes:
    - ./phpstan-extension.neon

parameters:
    paths:
        - src/
        - tests/
    level: max
```

### 10.2 Generic Type Annotations

Use PHPStan generics extensively for type safety:

```php
/**
 * @template TKey
 * @template TValue
 * @template TIterate = TValue
 * @phpstan-require-implements Collection<TKey,TValue,TIterate>
 */
trait CollectionTrait
{
    /**
     * @var array<TKey,TValue>
     */
    protected array $items = [];
}
```

### 10.3 PHPStan Suppressions

Use `@phpstan-ignore-next-line` sparingly and only when:

- PHPStan has a known bug
- The code is correct but PHPStan cannot infer the type
- Always document why the suppression is needed

---

## 11. File Structure and Headers

### 11.1 File Header Format

Every PHP file should start with:

```php
<?php

/**
 * {PackageName}
 * @license https://opensource.org/licenses/MIT
 */

declare(strict_types=1);

namespace DecodeLabs\{PackageNamespace};
```

### 11.2 Directory Structure

- Follow PSR-4 autoloading conventions
- Namespace structure should match directory structure
- Use subdirectories for logical grouping (e.g., `Constraint/Array/`, `Processor/`)

Example:

```
src/
  Collections/
    Collection.php          → DecodeLabs\Collections\Collection
    CollectionTrait.php    → DecodeLabs\Collections\CollectionTrait
    Sequence.php           → DecodeLabs\Collections\Sequence
    SequenceTrait.php      → DecodeLabs\Collections\SequenceTrait
```

---

## 12. Error Handling Patterns

### 12.1 Exception Factory Pattern

Use the `Exceptional` factory for creating exceptions:

```php
use DecodeLabs\Exceptional;

throw Exceptional::NotFound(
    message: 'Resource not found',
    data: ['id' => $id]
);

throw Exceptional::UnexpectedValue(
    message: 'Invalid value provided'
);
```

### 12.2 Exception Types

- Use specific exception types from the `exceptional` package
- Avoid generic `Exception` or `RuntimeException` unless appropriate
- Document which exceptions are part of the public API in package specs

---

## 13. ECS (Easy Coding Standard) Configuration

### 13.1 Standard Configuration

Most packages use ECS with:

```php
return ECSConfig::configure()
    ->withPaths([
        __DIR__ . '/src',
        __DIR__ . '/tests'
    ])
    ->withPreparedSets(
        cleanCode: true,
        psr12: true
    );
```

### 13.2 Common Skips

Some packages skip `ProtectedToPrivateFixer`:

```php
->withSkip([
    ProtectedToPrivateFixer::class
]);
```

This is acceptable when protected visibility is intentionally used for extension points.

---

## 14. Testing Structure

### 14.1 Test Organization

- Tests should mirror the `src/` directory structure
- Use PSR-4 autoloading for test classes
- Test classes should be in a `Tests` namespace

Example:

```
tests/
  Collections/
    CollectionTest.php  → DecodeLabs\Collections\Tests\CollectionTest
```

### 14.2 Test Coverage

- Aim for high coverage of public APIs
- Test edge cases and error conditions
- Document test strategy in package specs

---

## 15. Evolution of These Standards

These coding standards are expected to evolve as:

- More packages reach maturity,
- More patterns solidify,
- Tooling (php-cs-fixer, Psalm/PHPStan, etc.) is refined around the Decode Labs ecosystem.

Any **substantial changes** should be recorded in:

- This document (`docs/architecture/coding-standards.md`), and
- Where relevant, in architecture decision records (ADRs) in Chorus.

When in doubt:

- Prefer **clarity and consistency**.
- Align with PSR-12 unless this document (or a package spec) explicitly says otherwise.
- Look at existing packages for examples of established patterns.
