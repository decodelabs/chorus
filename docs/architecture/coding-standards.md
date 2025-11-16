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

- **Constants**: Use `PascalCase` for class constants (e.g., `public const MaxRetries = 3`) - we don't need to keep shouting any more!!
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

## 7. Evolution of These Standards

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
