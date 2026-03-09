# Implementation Plan: Filament Project Management Application

**Branch**: `001-filament-pm-app` | **Date**: 2026-03-09 | **Spec**: [spec.md](spec.md)
**Input**: Feature specification from `/specs/001-filament-pm-app/spec.md`

## Summary

Build a bilingual (English/Arabic) project management application using Laravel Filament v5 as the admin dashboard. Core features include company/team structure with owner profiles, project and task CRUD with translatable fields, single-assignee task management, polymorphic comments and attachments, status tracking with audit logs, and MFA for admin users. Authorization is handled via Spatie Laravel Permission. All entities use soft-deletes to preserve audit trails.

## Technical Context

**Language/Version**: PHP 8.2+, Laravel 12  
**Primary Dependencies**: Filament v5, Livewire 4, Flux 2.x, Laravel Fortify, Spatie Laravel Permission v7, Spatie Laravel Translatable, Filament Spatie Translatable Plugin, Filament Language Switch Plugin  
**Storage**: SQLite (default per constitution; JSON column support for translatable fields)  
**Testing**: Pest 4, Pest Laravel Plugin, Filament testing helpers  
**Target Platform**: Web (Linux server)  
**Project Type**: Web application (Laravel Filament admin dashboard)  
**Performance Goals**: Language/direction switch <3s; project creation <2min; task creation <90s  
**Constraints**: Bilingual EN/AR with RTL/LTR direction switching; 10MB file upload limit; soft-delete all entities  
**Scale/Scope**: Multi-company single-deployment; ~6 Filament resources, ~8 models, ~15 migrations

## Constitution Check

*GATE: Must pass before Phase 0 research. Re-check after Phase 1 design.*

- [x] Livewire-First: Filament v5 is built on Livewire 4; all resources/pages are Livewire components
- [x] Test-First: TDD planned with Pest 4; Filament testing helpers for resource tests; tests written before implementation
- [x] Specification-Driven: 6 user stories with acceptance scenarios, P1/P2 priorities, independently testable slices
- [x] Flux UI: Justified deviation — Filament panels use Filament's own component system (Form Builder, Table Builder, Actions); Flux used for any non-panel pages only (see Complexity Tracking)
- [x] Simplicity: Standard Laravel patterns; established packages (Filament, Spatie); SQLite default; no custom framework abstractions

## Project Structure

### Documentation (this feature)

```text
specs/001-filament-pm-app/
├── plan.md              # This file
├── research.md          # Phase 0 output
├── data-model.md        # Phase 1 output
├── quickstart.md        # Phase 1 output
├── contracts/           # Phase 1 output
│   └── filament-resources.md
└── tasks.md             # Phase 2 output (/speckit.tasks command)
```

### Source Code (repository root)

```text
app/
├── Enums/
│   └── Status.php                  # Shared status enum (To Do, In Progress, In Review, In Test, Blocked, Done)
├── Filament/
│   ├── Resources/
│   │   ├── OwnerResource.php       # Owner CRUD
│   │   ├── CompanyResource.php     # Company CRUD with team member management
│   │   ├── UserResource.php        # User CRUD with role assignment
│   │   ├── ProjectResource.php     # Project CRUD with translatable fields
│   │   └── TaskResource.php        # Task CRUD with assignee, rich notes
│   ├── Pages/
│   │   └── Dashboard.php           # Custom dashboard
│   └── Widgets/                    # Dashboard widgets
├── Models/
│   ├── Owner.php                   # Data-only entity linked to User
│   ├── Company.php                 # Belongs to Owner, has Users (members)
│   ├── User.php                    # Auth model with HasRoles trait
│   ├── Project.php                 # Belongs to Company, translatable
│   ├── Task.php                    # Belongs to Project, single assignee
│   ├── Comment.php                 # Polymorphic (Project|Task)
│   ├── Attachment.php              # Polymorphic (Project|Task)
│   └── StatusLog.php               # Polymorphic (Project|Task)
├── Observers/
│   ├── ProjectObserver.php         # Auto-log status changes
│   └── TaskObserver.php            # Auto-log status changes
├── Policies/
│   ├── OwnerPolicy.php
│   ├── CompanyPolicy.php
│   ├── ProjectPolicy.php
│   └── TaskPolicy.php
└── Providers/
    └── Filament/
        └── AdminPanelProvider.php  # Panel configuration, plugins, middleware

database/
├── migrations/
│   ├── xxxx_create_owners_table.php
│   ├── xxxx_create_companies_table.php
│   ├── xxxx_create_company_user_table.php
│   ├── xxxx_create_projects_table.php
│   ├── xxxx_create_tasks_table.php
│   ├── xxxx_create_comments_table.php
│   ├── xxxx_create_attachments_table.php
│   └── xxxx_create_status_logs_table.php
├── factories/
│   ├── OwnerFactory.php
│   ├── CompanyFactory.php
│   ├── ProjectFactory.php
│   ├── TaskFactory.php
│   ├── CommentFactory.php
│   └── AttachmentFactory.php
└── seeders/
    ├── RoleAndPermissionSeeder.php
    └── DatabaseSeeder.php

tests/
├── Feature/
│   ├── Filament/
│   │   ├── OwnerResourceTest.php
│   │   ├── CompanyResourceTest.php
│   │   ├── UserResourceTest.php
│   │   ├── ProjectResourceTest.php
│   │   └── TaskResourceTest.php
│   ├── Models/
│   │   ├── OwnerTest.php
│   │   ├── CompanyTest.php
│   │   ├── ProjectTest.php
│   │   └── TaskTest.php
│   └── Auth/
│       └── MfaTest.php
└── Unit/
    ├── StatusEnumTest.php
    └── Observers/
        ├── ProjectObserverTest.php
        └── TaskObserverTest.php

lang/
├── en/
│   └── filament.php
└── ar/
    └── filament.php

resources/
└── views/
    └── filament/
        └── pages/
            └── dashboard.blade.php
```

**Structure Decision**: Standard Laravel single-project structure. Filament resources live under `app/Filament/Resources/`. No frontend/backend split — Filament serves as both the UI and API layer. Translations stored as JSON attributes via Spatie Translatable; interface translations in `lang/` directories.

## Complexity Tracking

| Violation | Why Needed | Simpler Alternative Rejected Because |
|-----------|------------|-------------------------------------|
| Flux UI deviation inside Filament panels | Filament v5 has its own integrated Form Builder, Table Builder, Actions, and Notifications component system built on Livewire. Mixing Flux components inside Filament panels would cause style conflicts, break Filament's theming, and lose features like relation managers and form state management. | Using Flux inside Filament panels is not technically viable without significant custom integration work; Filament's own components ARE Livewire components and satisfy the Livewire-First principle. Flux remains available for any non-panel pages. |
