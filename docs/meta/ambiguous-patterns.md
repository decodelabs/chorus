# Ambiguous Patterns Requiring Clarification

This document lists patterns observed across the Decode Labs ecosystem that are **inconsistent or unclear** and need architectural clarification or standardization.

These patterns were identified during ecosystem analysis and should be addressed through:
- ADRs (Architecture Decision Records) in `docs/decisions/`
- Updates to coding standards in `docs/architecture/coding-standards.md`
- Package-specific documentation where the pattern is intentional

---

## 1. Property Visibility Modifiers

### 1.1 `public protected(set)` Pattern

**Observed in:**
- `Exceptional\ExceptionTrait`: `public protected(set) Parameters $parameters;`
- `Effigy\*` classes (likely, based on similar patterns)

**Question:**
- Is `public protected(set)` a PHPStan/Psalm annotation or actual PHP syntax?
- If it's an annotation, what's the exact syntax and where is it documented?
- Should this pattern be standardized across the ecosystem, or is it package-specific?

**Recommendation:**
- Document the exact syntax and tooling requirements.
- Clarify when this pattern should be used vs. `readonly` properties or traditional getters/setters.

---

## 2. Interface Property Accessors

### 2.1 Property Accessors in Interfaces

**Observed in:**
- `Exceptional\Exception`: `public Parameters $parameters { get; }`
- `Lucid\Processor`: `public array $outputTypes { get; }`, `public string $name { get; }`
- `Monarch\Environment`: `public string $name { get; }`, `public EnvironmentMode $mode { get; }`
- `Atlas\Node`: `public string $path { get; }`, `public string $name { get; }`

**Question:**
- Are these property accessors (`{ get; }`, `{ get; set; }`) a PHP 8.4+ feature or a custom annotation?
- If they're annotations, which tooling supports them (PHPStan/Psalm)?
- Should interfaces use property accessors, or should they use method signatures instead?

**Recommendation:**
- Clarify whether this is a PHP language feature or tooling annotation.
- Document the standard pattern for interface property definitions.
- Consider whether this conflicts with the "properties over getters" principle.

---

## 3. Trait Naming Conventions

### 3.1 Trait Name Matching Interface Name

**Observed pattern:**
- `Collection` interface → `CollectionTrait`
- `Processor` interface → `ProcessorTrait`
- `Node` interface → `NodeTrait`
- `Config` interface → `ConfigTrait`

**Question:**
- Is this a **required** convention, or just a common pattern?
- Should all traits that implement interfaces follow this naming?
- What about traits that don't implement interfaces (e.g., `SortableTrait`, `ExceptionTrait`)?

**Recommendation:**
- Document the naming convention explicitly in coding standards.
- Clarify when to use `{InterfaceName}Trait` vs. other naming patterns.

---

## 4. Static Factory Method Naming

### 4.1 Factory Method Variations

**Observed patterns:**
- `create()` - most common
- `fromDebugBacktrace()` - specific source
- `fromException()` - specific source
- `from*()` - pattern for conversion factories

**Question:**
- When should factories be named `create()` vs. `from*()`?
- Should `create()` always be the primary factory, with `from*()` for conversions?
- Are there other factory naming patterns that should be standardized?

**Recommendation:**
- Document factory naming conventions in coding standards.
- Provide guidance on when to use each pattern.

---

## 5. PHPStan Configuration Levels

### 5.1 Inconsistent PHPStan Levels

**Observed:**
- `exceptional`: `level: max`
- `remnant`: `level: max`
- `coercion`: `level: 9`

**Question:**
- Should all packages target `level: max`, or is `level: 9` acceptable?
- Are there specific reasons for different levels (e.g., package complexity, dependency constraints)?
- Should this be standardized across the ecosystem?

**Recommendation:**
- Document the target PHPStan level for Decode Labs packages.
- Clarify when exceptions to the standard are acceptable.

---

## 6. ECS Configuration

### 6.1 ECS vs. PHP-CS-Fixer

**Observed:**
- Some packages use `ecs.php` (Easy Coding Standard)
- Some packages use `php-cs-fixer` configuration
- Chorus coding standards mention `php-cs-fixer` with `@PSR12,-method_argument_space,array_syntax`

**Question:**
- Which tool should be the standard: ECS or PHP-CS-Fixer?
- Are both acceptable, or should one be preferred?
- How do ECS rules map to the `@PSR12,-method_argument_space,array_syntax` baseline?

**Recommendation:**
- Standardize on one tool or document when to use each.
- Ensure both tools produce equivalent results for the baseline rules.

---

## 7. Test Structure

### 7.1 Test Directory and Framework

**Observed:**
- Most packages have `tests/` directory
- PHPStan often includes both `src/` and `tests/` in analysis
- Test framework not consistently visible (PHPUnit vs. Pest vs. other)

**Question:**
- What is the standard test framework for Decode Labs packages?
- Should test structure be standardized (e.g., mirror `src/` structure)?
- Are there conventions for test naming or organization?

**Recommendation:**
- Document test structure conventions.
- Specify preferred test framework (or acceptable alternatives).

---

## 8. Helper Files

### 8.1 Global Helper Functions

**Observed:**
- `exceptional/composer.json`: includes `src/helpers.php` as a file
- Other packages may have similar helper files

**Question:**
- When should global helper functions be used vs. static methods on classes?
- Are there naming conventions for helper functions?
- Should helper files be documented in the public API?

**Recommendation:**
- Document when helper functions are appropriate.
- Provide naming conventions for global helpers.

---

## 9. Internal vs. Public API Boundaries

### 9.1 Internal Class Visibility

**Observed:**
- `Exceptional\Parameters` is used internally but not part of the primary public API
- Other packages likely have similar internal helper classes

**Question:**
- How should internal classes be marked or documented?
- Should they be in separate namespaces (e.g., `Internal\`, `Implementation\`)?
- Are there conventions for when internal classes can be used by other packages?

**Recommendation:**
- Document conventions for internal API boundaries.
- Clarify when internal classes can be used across packages vs. kept private.

---

## 10. Optional Dependencies

### 10.1 Runtime Detection vs. Composer Conflicts

**Observed:**
- `remnant` uses runtime detection for `monarch` (optional integration)
- Some packages may use `conflict` in `composer.json` for incompatible versions

**Question:**
- When should optional dependencies use runtime detection vs. `suggest` in `composer.json`?
- How should optional integrations be documented?
- Are there patterns for graceful degradation when optional dependencies are missing?

**Recommendation:**
- Document patterns for optional dependencies.
- Provide guidance on runtime detection vs. composer suggestions.

---

## 11. Value Object Immutability

### 11.1 Readonly Properties vs. Immutable Classes

**Observed:**
- Many classes use `public readonly` properties (e.g., `Frame`, `Node`)
- Some classes may use other immutability patterns

**Question:**
- Should all value objects use `readonly` properties?
- Are there cases where mutable value objects are acceptable?
- How should nested value objects handle immutability?

**Recommendation:**
- Document immutability conventions for value objects.
- Clarify when mutability is acceptable.

---

## 12. Exception Naming in Exceptional

### 12.1 Dynamic Exception Generation

**Observed:**
- `Exceptional` generates exception classes dynamically based on names
- Traits can be mixed into generated exceptions

**Question:**
- Are there conventions for exception names (e.g., should they always end in `Exception`)?
- How should exception naming be documented in packages that use Exceptional?
- Are there patterns for exception hierarchies that should be followed?

**Recommendation:**
- Document exception naming conventions for Exceptional.
- Provide guidance on when to use Exceptional vs. custom exception classes.

---

## Next Steps

1. **Review each pattern** with the architecture team.
2. **Create ADRs** for patterns that need standardization.
3. **Update coding standards** with clarified conventions.
4. **Update package-specific docs** where patterns are intentional deviations.
5. **Remove resolved items** from this document as they are addressed.

---

> **Note:** This document should be reviewed periodically as the ecosystem evolves. Patterns that are clarified should be moved to the appropriate documentation (coding standards, ADRs, etc.) and removed from this list.

