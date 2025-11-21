# AI Workflow: Ecosystem Analysis and Documentation

This document describes how AI agents (Cursor, Codex CLI, ChatGPT) should approach **analyzing and documenting** the Decode Labs ecosystem.

---

## Purpose

This workflow helps AI agents:

- Understand the **structure and patterns** across Decode Labs packages
- Generate **consistent, high-quality documentation** that aligns with ecosystem conventions
- Avoid **inventing APIs or behaviours** that don't exist
- Maintain **architectural consistency** when suggesting changes

---

## When to Use This Workflow

Use this workflow when:

- Analyzing multiple packages to identify patterns
- Generating cross-package documentation
- Updating Chorus with ecosystem-wide conventions
- Creating templates or standards documents
- Reviewing code for consistency with ecosystem patterns

---

## Step 1: Understanding Package Structure

Before analyzing a package, read:

1. **`composer.json`**
   - Dependencies (Decode Labs and external)
   - Autoload structure
   - PHP version requirements

2. **`README.md`**
   - Package purpose and usage
   - Quick start examples
   - Public API overview

3. **`src/` directory structure**
   - Namespace organization
   - Interface/trait/class relationships
   - Subdirectory patterns

4. **`docs/meta/spec.md`** (if present)
   - Detailed package specification
   - Public surface definition
   - Behaviour contracts

5. **`AGENTS.md`** (if present)
   - Package-specific guidance
   - Special constraints or patterns

---

## Step 2: Identifying Patterns

When analyzing multiple packages, look for:

### 2.1 Coding Patterns

- **Method signatures**: Multi-line parameters, trailing commas, brace placement
- **Naming conventions**: camelCase for methods/variables, PascalCase for classes
- **Property usage**: Readonly properties, property hooks, protected(set)
- **Interface-trait pairs**: Interface `X` with `XTrait` implementation
- **Static factories**: `create()`, `fromString()`, `fromDebugBacktrace()` patterns
- **Error handling**: `Exceptional::` factory usage, `try*` method patterns

### 2.2 Structural Patterns

- **Directory organization**: PSR-4 autoloading, namespace matching structure
- **File headers**: License comments, `declare(strict_types=1)`
- **Test organization**: Mirroring `src/` structure
- **PHPStan configuration**: Level max/9, extension includes
- **ECS configuration**: PSR12 base, common skips

### 2.3 API Patterns

- **Verb-based method names**: `handleRequest()`, `buildResponse()`
- **Properties over getters**: For simple data
- **Nullable returns**: `try*` methods for optional values
- **Generic type annotations**: PHPStan `@template` usage

---

## Step 3: Documenting Findings

When documenting patterns:

### 3.1 What to Document

- **Consistent patterns** found across 3+ packages
- **Clear conventions** that appear intentional
- **Real examples** from actual code (with file references)
- **Exceptions** where patterns diverge (and why, if known)

### 3.2 What NOT to Document

- **Inconsistent patterns** (flag these for clarification instead)
- **Single-package quirks** (these belong in package-specific docs)
- **Assumed patterns** (only document what you observe)
- **Invented conventions** (only document what exists)

### 3.3 How to Document

- Use **concrete examples** from the codebase
- Reference **actual file paths** and line numbers
- Include **code snippets** showing the pattern
- Note **ambiguities** with `<!-- TODO: clarify -->` comments

---

## Step 4: Updating Chorus Documentation

When updating Chorus:

### 4.1 Coding Standards (`docs/architecture/coding-standards.md`)

Add:
- New patterns discovered across multiple packages
- Examples from actual code
- Rules for when to use different patterns
- PHPStan/PHP 8.4+ conventions

### 4.2 Architecture Principles (`docs/architecture/principles.md`)

Enhance with:
- Concrete examples from the codebase
- Real-world usage patterns
- Clarifications where principles were vague

### 4.3 Workflow Documentation (`docs/workflows/`)

Create/update:
- AI agent guidance
- Documentation generation workflows
- Pattern analysis procedures

### 4.4 Templates (`docs/templates/`)

Create/update:
- README.md template
- AGENTS.md template
- Package spec template
- Other standard documents

---

## Step 5: Handling Ambiguities

When patterns are unclear or inconsistent:

### 5.1 Flag for Clarification

Use clear markers:

```markdown
<!-- TODO: clarify whether protected properties should be preferred over private in trait implementations -->
```

### 5.2 Document Multiple Approaches

If multiple valid patterns exist:

```markdown
## Pattern Variation

Some packages use approach A, others use approach B. Both are acceptable:

- **Approach A**: [example]
- **Approach B**: [example]

<!-- TODO: decide if one should be preferred going forward -->
```

### 5.3 Ask for Guidance

When genuinely uncertain:

- Document what you found
- Note the inconsistency
- Ask for human clarification rather than guessing

---

## Step 6: Validation Checklist

Before finalizing documentation updates:

- [ ] All examples are from actual code in the repository
- [ ] File paths and line numbers are accurate
- [ ] Patterns are documented as observed, not invented
- [ ] Ambiguities are clearly marked
- [ ] Examples compile and are syntactically correct
- [ ] Documentation aligns with existing Chorus standards
- [ ] Cross-references to other docs are valid

---

## Common Pitfalls to Avoid

### ❌ Don't Invent Patterns

**Bad**: "Packages should use X pattern" (when X doesn't exist)

**Good**: "Packages commonly use X pattern" (with examples)

### ❌ Don't Over-Generalize

**Bad**: "All packages use trait X" (when only some do)

**Good**: "Packages X, Y, Z use trait pattern" (with examples)

### ❌ Don't Ignore Inconsistencies

**Bad**: Picking one pattern arbitrarily when multiple exist

**Good**: Documenting the inconsistency and flagging for clarification

### ❌ Don't Skip Examples

**Bad**: "Use property hooks for computed values" (no example)

**Good**: "Use property hooks for computed values" (with code example)

---

## Example: Analyzing Interface-Trait Pattern

**Observation**: Multiple packages use interface-trait pairs:

- `Collection` interface with `CollectionTrait`
- `Node` interface with `NodeTrait`
- `Processor` interface with `ProcessorTrait`

**Documentation approach**:

1. Note the pattern exists across packages
2. Show concrete examples from code
3. Document the naming convention (Interface → InterfaceTrait)
4. Note PHPStan requirements (`@phpstan-require-implements`)
5. Add to coding standards document

**Result**: Clear, actionable guidance for future packages

---

## Integration with AGENTS.md

This workflow complements package-level `AGENTS.md` files:

- **Chorus workflows**: Ecosystem-wide patterns and conventions
- **Package AGENTS.md**: Package-specific constraints and guidance

Both should be consulted when working in a package.

---

## Evolution

This workflow should evolve as:

- New patterns emerge in the ecosystem
- Tooling improves (PHPStan, ECS, etc.)
- Architectural decisions are made
- Best practices are refined

Update this document when workflow changes are needed.

