# DecodeLabs Agents Guide  
**Global Rules & Expectations for AI Tools and Contributors**

This document provides the **canonical, ecosystem-wide rules** for all AI agents and human contributors working on any DecodeLabs repository.

It centralises the behaviour guidelines, coding expectations, safety rails, and documentation requirements that previously had to be duplicated across 80+ repositories.

Per-package `AGENTS.md` files now act only as thin bootstraps.  
**All global standards live here.**

---

# 1. Purpose of This Document

DecodeLabs consists of many independent but interconnected PHP packages that collectively replace large legacy frameworks and power high-traffic, revenue-critical client systems.

Because changes can ripple across multiple layers (libraries → frameworks → clients), AI agents must behave under strict, predictable rules.

This guide ensures:

- consistent code quality  
- safe changes  
- reliable migrations  
- stable client deployments  
- reproducible architectural decisions  

Every agent must follow this document as a **non-negotiable baseline**.

---

# 2. How to Begin Work in Any Repository

Before making changes in ANY DecodeLabs repository:

### 2.1 Always Read This File
This is the global behaviour contract.

### 2.2 Read the Package’s Local Files
In the target repo, always read:

- `AGENTS.md` (bootstrap only)  
- `README.md`  
- `docs/meta/spec.md` (package specification)  
- any relevant feature specs in `docs/meta/features/`  
- any local `docs/` content  

These describe the package’s purpose and public surface.

### 2.3 Locate Chorus for Global Rules
Every package depends on Chorus.

Find it in this order:

1. **Sibling directory** (most common during development):  
   ```
   ../chorus
   ```

2. **Composer dev dependency** (installed via Packagist):  
   ```
   vendor/decodelabs/chorus
   ```

3. **Remote repository** (read-only fallback):  
   ```
   https://github.com/decodelabs/chorus
   ```

Chorus is the source of:

- architecture principles  
- coding standards  
- templates (README, package-spec, feature-spec, change-spec)  
- workflows  
- ecosystem metadata (`packages.json`)  

These apply to *every* package.

---

# 3. Architectural Rules for AI Agents

## 3.1 Single-Repository Rule

**An agent may only modify one repository at a time.**

Agents must NOT:

- make cross-repo behavioural changes  
- perform sweeping multi-repo refactors  
- update multiple packages simultaneously  
- propagate changes automatically across frameworks or client projects  

If a change spans multiple repos, document it in Chorus and wait for human instruction.

---

## 3.2 Behaviour When Unsure

If any of the following are unclear:

- API semantics  
- nullability or error expectations  
- deprecation behaviour  
- architectural intent  
- coding standard interpretation  
- impact of a change  

Then the agent must:

1. **Stop**, and  
2. **Ask for clarification**, OR  
3. Add a safe note, such as:

```php
// TODO: clarify expected behaviour here.
```

```markdown
<!-- TODO: determine correct nullability and error handling semantics -->
```

**Never guess.**  
**Never invent APIs or behaviour.**

---

## 3.3 Allowed vs Forbidden Actions

### Allowed

- Reading and summarising multiple repos  
- Generating documentation  
- Implementing changes inside one package  
- Creating tests  
- Generating migration plans (but not applying them to other repos)  
- Drafting codemods for human review  
- Updating specs, READMEs, and templates  
- Following SemVer requirements  

### Forbidden

- Cross-repo edits  
- Behavioural changes without a corresponding **Change Spec**  
- Removing deprecated features without explicit instruction  
- Silent refactors  
- Code generation that ignores coding standards  
- Guessing intent where the spec is ambiguous  

---

# 4. Code Quality Expectations (Ecosystem-Wide)

DecodeLabs libraries must maintain **exceptional** quality.

Agents must ensure:

- clear, well-structured APIs  
- focused responsibilities  
- zero code smells  
- no unused code or dead paths  
- no hidden side effects  
- no ambiguous nullability  
- strong test coverage  
- readable documentation  
- correct use of DecodeLabs patterns  

In practice, this includes:

- **Method names as verbs**  
- **Consistent signature formatting**  
- **Nullable returns use `try*` + non-nullable variants**  
- **Prefer properties over trivial getters/setters**  
- **Small, cohesive classes**  
- **Internal APIs separated from public APIs**  
- **Predictable exceptions (`decodelabs/exceptional`)**  
- **Clear, strict type usage**  
- **Minimal magic / reflection unless necessary**  
- **Immutable value objects where appropriate**  

Full details are in `coding-standards.md`.

---

# 5. Coding Standards Summary (See Full Details in `coding-standards.md`)

### 5.1 Method Signatures

If parameters exist:

```php
public function example(
    string $foo,
    int $bar,
): void {
}
```

If there are none:

```php
public function reset(): void
{
}
```

### 5.2 Nullable Return Pattern

- `tryGetThing(): ?Thing`  
- `getThing(): Thing` (throws if not found)

### 5.3 Properties vs Methods

Prefer:

```php
public readonly string $id;
```

Avoid:

```php
getId();
setId();
```

### 5.4 Traits and Interfaces

- Traits mirror related interfaces  
- Trait names end in `Trait`  
- Interfaces end in `Interface`  
- Keep responsibilities narrow

### 5.5 Files, Namespaces, and Classes

- One class/interface/trait per file  
- Namespaces reflect architecture clusters  
- Avoid deep inheritance  
- Prefer composition

### 5.6 php-cs-fixer baseline

```
@PSR12,-method_argument_space,array_syntax
```

---

# 6. Documentation Expectations

All packages must maintain:

- `README.md`
- `docs/meta/spec.md`
- Additional feature specs in `docs/meta/features/` if needed
- Accurate docblocks where useful (especially for generics)
- Clear examples of usage
- Inline TODOs for unresolved behaviour

Documentation in each package must follow templates in:

```
chorus/docs/templates/
```

If a template is missing, mimic existing standards.

---

# 7. Change Management & Migration Coordination

This entire ecosystem operates under a **versioned change pipeline** described in:

`ai-integration-workflow.md`

All behavioural changes must originate from Chorus via:

- **Change Specs** (`docs/templates/change-spec.md`)  
- **Feature Specs** (`docs/templates/feature-spec.md`)  

Agents implementing code must:

- Read the relevant spec  
- Follow it strictly  
- Not exceed its scope  
- Not generate alternative designs unless asked  

Migration of:

- **libraries**,  
- **frameworks**,  
- **client projects**

is handled in separate phases and **must not** be performed automatically unless specifically instructed.

---

# 8. Template Usage

Chorus provides templates for:

- README  
- AGENTS  
- Package specs  
- Feature specs  
- Change specs  

Agents should:

- Use them when creating new files  
- Update them only in Chorus  
- Avoid deviating from structure without permission  

If a template does not exist:

- Add a placeholder with `<!-- TODO: fill template when defined -->`  
- Ask for clarification

---

# 9. Error Handling for Agents

If an agent encounters missing, contradictory, or ambiguous information:

- Stop and ask  
- Or write TODO markers  
- Or propose alternative interpretations **without committing code**

Do NOT:

- silently resolve ambiguity  
- invent missing behaviour  
- infer hidden requirements  
- perform sweeping changes  

---

# 10. Final Checklist for Any Agent

Before acting:

- [ ] Found Chorus (sibling/vendor/remote)  
- [ ] Read this guide  
- [ ] Read package README  
- [ ] Read package spec  
- [ ] Confirmed task is one-repo only  
- [ ] Identified relevant template(s)  
- [ ] Confirmed SemVer constraints  
- [ ] Confirmed behavioural scope  
- [ ] Validated no conflicting rules  

While acting:

- [ ] Followed coding standards  
- [ ] Followed documentation format  
- [ ] Followed change/spec templates  
- [ ] Avoided guesswork  
- [ ] Added TODOs instead of inventing answers  
- [ ] Kept changes small and focused  

After acting:

- [ ] Updated docs (spec/test/README where needed)  
- [ ] Ensured code remains readable, consistent, and safe  

---

# 11. Purpose of Having This Central File

This file:  

- replaces duplicated rules across 80+ repositories  
- prevents divergence in behaviour between agents  
- ensures consistency in quality and architecture  
- reduces maintenance overhead  
- makes the ecosystem predictable and stable  
- provides a single point of truth that agents can fall back to if lost or confused  

Every per-repo AGENTS.md now simply redirects to Chorus + explains the single-repo rule.

---

# 12. If Anything Becomes Unclear

Agents must **stop**, ask for clarification, or leave a TODO.

This document is the fallback that guarantees safety even if other repo docs drift.

