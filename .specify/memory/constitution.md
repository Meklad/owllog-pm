<!--
Sync Impact Report
==================
Version change: (none) → 1.0.0
Modified principles: N/A (initial ratification)
Added sections: All filled from template
Removed sections: None
Templates requiring updates:
  - .specify/templates/plan-template.md ✅ (Constitution Check remains generic; gates derived from constitution)
  - .specify/templates/spec-template.md ✅ (no constitution-specific sections; scope/requirements aligned)
  - .specify/templates/tasks-template.md ✅ (task categorization aligns with principles)
  - .specify/templates/commands/*.md ⚠ N/A (no commands directory found)
Follow-up TODOs: None
-->

# owllog-pm Constitution

## Core Principles

### I. Livewire-First UI

Reactive UIs MUST be built with Livewire components. Components MUST be self-contained
and independently testable. Use `wire:model`, `wire:click`, and related directives for
reactivity. Rationale: Livewire 4 is the primary UI framework; consistency and testability
depend on component boundaries.

### II. Test-First (NON-NEGOTIABLE)

TDD is mandatory: tests written → user approved → tests fail → then implement.
Red-Green-Refactor cycle strictly enforced. Use Pest 4 for PHP tests. Rationale:
prevents regression and ensures specifications drive implementation.

### III. Specification-Driven

Features MUST be driven by specs, plans, and tasks from the speckit workflow. User
stories MUST have acceptance scenarios and priorities (P1, P2, P3). Each story MUST
be independently testable and deliverable as an MVP slice. Rationale: traceability
and incremental delivery.

### IV. Flux UI Consistency

Use Flux components for forms, modals, inputs, buttons, and related UI elements.
Avoid raw HTML form elements where Flux equivalents exist. Rationale: consistent
look, accessibility, and reduced custom CSS.

### V. Simplicity & Observability

YAGNI principles apply. Complexity MUST be justified. Structured logging required;
Laravel Pail for local debugging. Rationale: maintainability and debuggability.

## Technology Stack

**Runtime**: PHP 8.2+, Laravel 12, Livewire 4, Livewire Flux 2.x, Laravel Fortify.

**Testing**: Pest 4, Pest Laravel plugin.

**Frontend**: Tailwind CSS v4, Vite.

**Storage**: SQLite (default), migrations required for schema changes.

**Commands**: `composer setup`, `composer dev`, `composer test`, `composer lint`.

## Development Workflow

1. **Spec** → **Plan** → **Tasks** (speckit flow)
2. Constitution Check MUST pass before Phase 0 research and after Phase 1 design
3. PRs MUST verify compliance with principles
4. Tests MUST fail before implementation (TDD gate)
5. Use `AGENTS.md` (and `.cursor/rules` when present) for runtime development guidance

## Governance

- This constitution supersedes ad-hoc practices
- Amendments require documentation, approval, and migration plan if behavior changes
- Version bump rules: MAJOR = backward-incompatible principle changes; MINOR = new
  principle or material expansion; PATCH = clarifications, typos, non-semantic fixes
- All PRs/reviews MUST verify compliance with Core Principles

**Version**: 1.0.0 | **Ratified**: 2025-03-09 | **Last Amended**: 2025-03-09
