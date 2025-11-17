# Architecture Inbox

This document collects ideas, observations, discussions, and speculative features
that arise during the DecodeLabs ecosystem's design process. Items recorded here are not yet
commitments. They exist to preserve insight without blocking ongoing work or
disrupting architectural flow.

Entries in this inbox may be refined, merged, escalated into formal
architecture documents, incorporated into ADRs, package specifications, or discarded.

---

## Format for New Entries

Each new idea should follow this structure:

```
## <Short Title>
**Tag:** <Core / Infrastructure / Architecture / Package Development / Documentation / Testing / Integration / Workflow / Misc>  
**Priority:** P1 | P2 | P3  
**Status:** proposed | accepted | incorporated | rejected  
A concise description of the idea, why it matters, and when it should be
considered. Include cross-references if relevant.
```

---

## Entries

### Standardized Package Specification Template
**Tag:** Documentation / Package Development  
**Priority:** P1  
**Status:** incorporated  
Established a consistent template structure for `docs/meta/spec.md` across all DecodeLabs packages, including sections for Overview, Role in Ecosystem, Public Surface, Dependencies, Behaviour & Contracts, Error Handling, Configuration, Interactions, Examples, Implementation Notes, Testing, Roadmap, and References. This standardization enables better cross-package understanding and maintains architectural coherence across the ecosystem. See `docs/workflows/ai-package-specs.md` for the generation workflow.

### Optional Dependency Declaration Standard
**Tag:** Package Development / Integration  
**Priority:** P2  
**Status:** proposed  
Establish a standard way for packages to declare optional dependencies (e.g. Monarch, Prophet) that enables runtime detection and graceful degradation. Improves ecosystem cohesion and makes integration patterns more discoverable across packages.
