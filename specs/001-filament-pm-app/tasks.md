# Tasks: Filament Project Management Application

**Input**: Design documents from `/specs/001-filament-pm-app/`
**Prerequisites**: plan.md, spec.md, research.md, data-model.md, contracts/filament-resources.md, quickstart.md

**Tests**: Included per constitution (Principle II: Test-First is NON-NEGOTIABLE). Tests are written first and must fail before implementation.

**Organization**: Tasks are grouped by user story to enable independent implementation and testing of each story.

## Format: `[ID] [P?] [Story] Description`

- **[P]**: Can run in parallel (different files, no dependencies)
- **[Story]**: Which user story this task belongs to (e.g., US1, US2, US3)
- Include exact file paths in descriptions

## Path Conventions

- Laravel single-project structure at repository root
- Models: `app/Models/`
- Filament Resources: `app/Filament/Resources/`
- Enums: `app/Enums/`
- Policies: `app/Policies/`
- Observers: `app/Observers/`
- Migrations: `database/migrations/`
- Factories: `database/factories/`
- Seeders: `database/seeders/`
- Feature Tests: `tests/Feature/`
- Unit Tests: `tests/Unit/`

---

## Phase 1: Setup (Shared Infrastructure)

**Purpose**: Install packages, configure Filament panel, and create shared utilities

- [X] T001 Install Filament v5 and plugins via Composer: `filament/filament:^5.0`, `filament/spatie-laravel-translatable-plugin:^5.0`, `spatie/laravel-permission:^7.0`, `bezhansalleh/filament-language-switch:^3.0`
- [X] T002 Run `php artisan filament:install --panels` and configure AdminPanelProvider in app/Providers/Filament/AdminPanelProvider.php with SpatieLaravelTranslatablePlugin (locales: en, ar) and FilamentLanguageSwitchPlugin
- [X] T003 [P] Publish and configure Spatie Permission: `php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"` and run migrations
- [X] T004 [P] Publish and configure Language Switch: `php artisan vendor:publish --tag="filament-language-switch-config"` with en and ar locales (ar with RTL script)
- [X] T005 [P] Create Status enum in app/Enums/Status.php with values: ToDo, InProgress, InReview, InTest, Blocked, Done (string-backed)
- [X] T006 [P] Run `php artisan storage:link` and configure local disk for attachments in config/filesystems.php

---

## Phase 2: Foundational (Blocking Prerequisites)

**Purpose**: Core User model setup, roles/permissions seeding, and base Filament auth that ALL user stories depend on

**⚠️ CRITICAL**: No user story work can begin until this phase is complete

### Tests for Foundational

- [X] T007 [P] Write test for Status enum values and labels in tests/Unit/StatusEnumTest.php
- [X] T008 [P] Write test for User model HasRoles trait and soft-delete in tests/Feature/Models/UserTest.php

### Implementation for Foundational

- [X] T009 Update User model in app/Models/User.php: add SoftDeletes trait, HasRoles trait (Spatie), TwoFactorAuthenticatable trait (Fortify)
- [X] T010 Create migration to add `deleted_at` column to users table in database/migrations/
- [X] T011 Create RoleAndPermissionSeeder in database/seeders/RoleAndPermissionSeeder.php with roles (super-admin, admin, member) and per-resource permissions (view/create/update/delete for owners, companies, projects, tasks, comments, attachments; view for status_logs)
- [X] T012 Register RoleAndPermissionSeeder in database/seeders/DatabaseSeeder.php and run `php artisan db:seed`
- [X] T013 Configure Filament panel auth, navigation groups (Organization, Work), and middleware in app/Providers/Filament/AdminPanelProvider.php

**Checkpoint**: Foundation ready — User model has roles/permissions, Filament panel is configured, status enum exists. User story implementation can now begin.

---

## Phase 3: User Story 1 — Multilingual Dashboard Access (Priority: P1) 🎯 MVP

**Goal**: Admin users can switch the dashboard between English and Arabic, with automatic RTL/LTR layout switching.

**Independent Test**: Switch dashboard language and direction, verify all labels and layout adapt correctly. Can be validated without any other feature.

### Tests for User Story 1

> **NOTE: Write these tests FIRST, ensure they FAIL before implementation**

- [ ] T014 [P] [US1] Write test for language switcher rendering and locale change in tests/Feature/Filament/LanguageSwitchTest.php
- [ ] T015 [P] [US1] Write test for RTL/LTR direction switching when locale changes in tests/Feature/Filament/DirectionSwitchTest.php

### Implementation for User Story 1

- [ ] T016 [US1] Configure Language Switch plugin locales (en: English LTR, ar: Arabic RTL) with native names and flag codes in config/filament-language-switch.php
- [ ] T017 [P] [US1] Create English translation files in lang/en/filament.php with dashboard labels, navigation, and common UI strings
- [ ] T018 [P] [US1] Create Arabic translation files in lang/ar/filament.php with dashboard labels, navigation, and common UI strings (RTL)
- [ ] T019 [US1] Create custom Dashboard page in app/Filament/Pages/Dashboard.php with bilingual welcome message and locale-aware content

**Checkpoint**: Dashboard displays in English (LTR) and Arabic (RTL) with language switcher in top bar. Fully functional and testable independently.

---

## Phase 4: User Story 2 — Company and Team Structure (Priority: P1)

**Goal**: Owners can create companies and assign team members. Users see only their companies' data.

**Independent Test**: Create an owner, create a company, add users as team members, verify scoped access.

### Tests for User Story 2

> **NOTE: Write these tests FIRST, ensure they FAIL before implementation**

- [ ] T020 [P] [US2] Write test for Owner model (create, relationships, translatable name, soft-delete) in tests/Feature/Models/OwnerTest.php
- [ ] T021 [P] [US2] Write test for Company model (create, owner relationship, members, translatable fields, soft-delete) in tests/Feature/Models/CompanyTest.php
- [ ] T022 [P] [US2] Write test for OwnerResource CRUD (list, create, edit, delete, restore) in tests/Feature/Filament/OwnerResourceTest.php
- [ ] T023 [P] [US2] Write test for CompanyResource CRUD and MembersRelationManager in tests/Feature/Filament/CompanyResourceTest.php
- [ ] T024 [P] [US2] Write test for UserResource CRUD with role assignment in tests/Feature/Filament/UserResourceTest.php

### Implementation for User Story 2

- [ ] T025 [P] [US2] Create Owner model with HasTranslations and SoftDeletes traits, migration (user_id unique FK, name JSON, email, phone, timestamps, deleted_at), and OwnerFactory in app/Models/Owner.php, database/migrations/, database/factories/OwnerFactory.php
- [ ] T026 [P] [US2] Create Company model with HasTranslations and SoftDeletes traits, migration (owner_id FK, name JSON, description JSON, timestamps, deleted_at), and CompanyFactory in app/Models/Company.php, database/migrations/, database/factories/CompanyFactory.php
- [ ] T027 [US2] Create company_user pivot table migration with unique composite index (company_id, user_id) in database/migrations/
- [ ] T028 [US2] Add relationships: Owner hasOne on User, belongsTo User on Owner, belongsToMany User on Company (members), belongsToMany Company on User (companies), hasMany Company on Owner
- [ ] T029 [P] [US2] Create OwnerPolicy in app/Policies/OwnerPolicy.php with permission checks (view_owners, create_owners, update_owners, delete_owners)
- [ ] T030 [P] [US2] Create CompanyPolicy in app/Policies/CompanyPolicy.php with permission checks (view_companies, create_companies, update_companies, delete_companies)
- [ ] T031 [US2] Create OwnerResource in app/Filament/Resources/OwnerResource.php with translatable form (user_id select, name, email, phone), table (name, email, user.name), TrashedFilter, and View/Edit/Delete/Restore actions
- [ ] T032 [US2] Create CompanyResource in app/Filament/Resources/CompanyResource.php with translatable form (owner_id select, name, description), table (name, owner.name, members_count, projects_count), and TrashedFilter
- [ ] T033 [US2] Create MembersRelationManager in app/Filament/Resources/CompanyResource/RelationManagers/MembersRelationManager.php with attach/detach User actions and searchable select
- [ ] T034 [US2] Create UserResource in app/Filament/Resources/UserResource.php with form (name, email, password, roles select), table (name, email, roles badges, companies_count), role and verified filters, TrashedFilter

**Checkpoint**: Owners, Companies, and Users are fully manageable via Filament. Team membership works. Users can be scoped to companies. Testable independently.

---

## Phase 5: User Story 3 — Project Management (Priority: P1)

**Goal**: Company users can create/manage projects with bilingual content, dates, comments, attachments, and status tracking.

**Independent Test**: Create a project with bilingual content, add attachments and comments, change status, verify all content persists.

### Tests for User Story 3

> **NOTE: Write these tests FIRST, ensure they FAIL before implementation**

- [ ] T035 [P] [US3] Write test for Project model (create, company relationship, translatable fields, status cast, date validation, soft-delete) in tests/Feature/Models/ProjectTest.php
- [ ] T036 [P] [US3] Write test for Comment model (polymorphic create on Project, author relationship, soft-delete) in tests/Feature/Models/CommentTest.php
- [ ] T037 [P] [US3] Write test for Attachment model (polymorphic create on Project, file metadata, soft-delete) in tests/Feature/Models/AttachmentTest.php
- [ ] T038 [P] [US3] Write test for ProjectObserver status change logging in tests/Unit/Observers/ProjectObserverTest.php
- [ ] T039 [P] [US3] Write test for ProjectResource CRUD and relation managers in tests/Feature/Filament/ProjectResourceTest.php

### Implementation for User Story 3

- [ ] T040 [P] [US3] Create Project model with HasTranslations and SoftDeletes traits, migration (company_id FK, title JSON, description JSON, status string default 'to_do', start_date, end_date, timestamps, deleted_at), and ProjectFactory in app/Models/Project.php, database/migrations/, database/factories/ProjectFactory.php
- [ ] T041 [P] [US3] Create Comment model with SoftDeletes, migration (commentable_type, commentable_id, user_id FK, body text, timestamps, deleted_at), and CommentFactory in app/Models/Comment.php, database/migrations/, database/factories/CommentFactory.php
- [ ] T042 [P] [US3] Create Attachment model with SoftDeletes, migration (attachable_type, attachable_id, user_id FK, file_path, file_name, file_size, mime_type, timestamps, deleted_at), and AttachmentFactory in app/Models/Attachment.php, database/migrations/, database/factories/AttachmentFactory.php
- [ ] T043 [US3] Create StatusLog model (no soft-delete, immutable), migration (loggable_type, loggable_id, user_id FK, old_status nullable, new_status, created_at only) in app/Models/StatusLog.php, database/migrations/
- [ ] T044 [US3] Add polymorphic relationships to Project model: comments(), attachments(), statusLogs() — and inverse morphTo on Comment, Attachment, StatusLog
- [ ] T045 [US3] Create ProjectObserver in app/Observers/ProjectObserver.php to auto-log status changes to StatusLog with authenticated user
- [ ] T046 [US3] Register ProjectObserver in app/Providers/AppServiceProvider.php
- [ ] T047 [US3] Create ProjectPolicy in app/Policies/ProjectPolicy.php with permission checks and company-scope enforcement
- [ ] T048 [US3] Create ProjectResource in app/Filament/Resources/ProjectResource.php with translatable form (company_id scoped select, title, description RichEditor, status select, start_date, end_date DatePickers with end >= start validation), table (title, company.name, status badge, tasks_count, dates), company and status filters, TrashedFilter
- [ ] T049 [US3] Create CommentsRelationManager (shared, polymorphic) in app/Filament/Resources/RelationManagers/CommentsRelationManager.php with auto-set user_id, create/delete actions, author and timestamp display
- [ ] T050 [US3] Create AttachmentsRelationManager (shared, polymorphic) in app/Filament/Resources/RelationManagers/AttachmentsRelationManager.php with FileUpload (maxSize 10240, directory 'attachments'), download action, metadata display
- [ ] T051 [US3] Create StatusLogsRelationManager (read-only, shared) in app/Filament/Resources/RelationManagers/StatusLogsRelationManager.php with old_status/new_status badges, user.name, created_at, sorted descending
- [ ] T052 [US3] Register CommentsRelationManager, AttachmentsRelationManager, and StatusLogsRelationManager on ProjectResource
- [ ] T053 [US3] Create ViewProject page in app/Filament/Resources/ProjectResource/Pages/ViewProject.php with infolist entries (title, description HtmlEntry, company, status badge, dates, status timeline)

**Checkpoint**: Projects are fully manageable with bilingual content, comments, file attachments, and automatic status change logging. Testable independently within a company context.

---

## Phase 6: User Story 4 — Task Management (Priority: P1)

**Goal**: Company users can create/manage tasks within projects with bilingual content, rich-text notes, single assignee, comments, attachments, and status tracking.

**Independent Test**: Create a task in a project with bilingual content and rich notes, assign a user, change status, add comments/attachments, verify status history.

### Tests for User Story 4

> **NOTE: Write these tests FIRST, ensure they FAIL before implementation**

- [ ] T054 [P] [US4] Write test for Task model (create, project relationship, assignee relationship, translatable fields, rich-text notes, status cast, date validation, soft-delete) in tests/Feature/Models/TaskTest.php
- [ ] T055 [P] [US4] Write test for TaskObserver status change logging in tests/Unit/Observers/TaskObserverTest.php
- [ ] T056 [P] [US4] Write test for TaskResource CRUD, assignee selection scoped to company, and relation managers in tests/Feature/Filament/TaskResourceTest.php

### Implementation for User Story 4

- [ ] T057 [US4] Create Task model with HasTranslations and SoftDeletes traits, migration (project_id FK, assigned_to FK nullable, title JSON, description JSON, notes text nullable, status string default 'to_do', start_date, end_date, timestamps, deleted_at), and TaskFactory in app/Models/Task.php, database/migrations/, database/factories/TaskFactory.php
- [ ] T058 [US4] Add relationships to Task model: project(), assignee() belongsTo User via assigned_to, comments(), attachments(), statusLogs() — and add assignedTasks() hasMany on User, tasks() hasMany on Project
- [ ] T059 [US4] Create TaskObserver in app/Observers/TaskObserver.php to auto-log status changes to StatusLog with authenticated user
- [ ] T060 [US4] Register TaskObserver in app/Providers/AppServiceProvider.php
- [ ] T061 [US4] Create TaskPolicy in app/Policies/TaskPolicy.php with permission checks and company-scope enforcement via project→company chain
- [ ] T062 [US4] Create TaskResource in app/Filament/Resources/TaskResource.php with translatable form (project_id scoped select, assigned_to select scoped to project's company members, title, description, notes RichEditor, status select, start_date, end_date DatePickers), table (title, project.title, assignee.name, status badge, dates), project/assignee/status filters, TrashedFilter
- [ ] T063 [US4] Register CommentsRelationManager, AttachmentsRelationManager, and StatusLogsRelationManager on TaskResource
- [ ] T064 [US4] Create ViewTask page in app/Filament/Resources/TaskResource/Pages/ViewTask.php with infolist entries (title, description, notes HtmlEntry, project, assignee, status badge, dates, status timeline)
- [ ] T065 [US4] Create TasksRelationManager in app/Filament/Resources/ProjectResource/RelationManagers/TasksRelationManager.php with inline task list, status badge column, assignee column, and create action
- [ ] T066 [US4] Register TasksRelationManager on ProjectResource

**Checkpoint**: Tasks are fully manageable within projects, with assignee, rich-text notes, bilingual content, comments, attachments, and automatic status logging. Testable independently within a project context.

---

## Phase 7: User Story 5 — Status Logs and Audit Trail (Priority: P2)

**Goal**: Users can see a chronological timeline of all status changes for projects and tasks, with who made each change and when.

**Independent Test**: Change a project or task status multiple times, view status log timeline, verify all changes are shown with correct user and timestamp.

### Tests for User Story 5

> **NOTE: Write these tests FIRST, ensure they FAIL before implementation**

- [ ] T067 [US5] Write test for status log timeline display on ViewProject and ViewTask pages in tests/Feature/Filament/StatusLogTimelineTest.php

### Implementation for User Story 5

- [ ] T068 [US5] Enhance StatusLogsRelationManager with formatted chronological timeline: relative timestamps, color-coded status badges, user avatars/names, "Initial status" label for null old_status
- [ ] T069 [US5] Add dedicated status history section to ViewProject infolist in app/Filament/Resources/ProjectResource/Pages/ViewProject.php with RepeatableEntry for timeline
- [ ] T070 [US5] Add dedicated status history section to ViewTask infolist in app/Filament/Resources/TaskResource/Pages/ViewTask.php with RepeatableEntry for timeline

**Checkpoint**: Status change timeline is visible on both project and task view pages with full audit information. Testable independently.

---

## Phase 8: User Story 6 — Admin Security / MFA (Priority: P2)

**Goal**: Admin users can enable multi-factor authentication to protect their accounts with a second factor on login.

**Independent Test**: Enable MFA for an admin account, log out and log back in, verify MFA challenge is required.

### Tests for User Story 6

> **NOTE: Write these tests FIRST, ensure they FAIL before implementation**

- [ ] T071 [P] [US6] Write test for enabling/disabling 2FA and viewing recovery codes in tests/Feature/Auth/MfaTest.php
- [ ] T072 [P] [US6] Write test for MFA login challenge flow (valid code passes, invalid code rejects) in tests/Feature/Auth/MfaLoginTest.php

### Implementation for User Story 6

- [ ] T073 [US6] Enable TwoFactorAuthentication feature in config/fortify.php (Features::twoFactorAuthentication with confirm option)
- [ ] T074 [US6] Verify User model has TwoFactorAuthenticatable trait and two_factor columns exist in users migration (two_factor_secret, two_factor_recovery_codes, two_factor_confirmed_at)
- [ ] T075 [US6] Create custom Filament MFA setup page in app/Filament/Pages/TwoFactorAuth.php with enable/disable toggle, QR code display, confirmation code input, and recovery codes display
- [ ] T076 [US6] Integrate 2FA challenge with Filament login flow: add middleware or custom login page that checks for 2FA requirement and prompts for TOTP code

**Checkpoint**: Admin users can enable MFA via the panel, and subsequent logins require a second factor. Testable independently.

---

## Phase 9: Polish & Cross-Cutting Concerns

**Purpose**: Improvements that affect multiple user stories, final quality pass

- [ ] T077 [P] Complete Arabic translations for all Filament resource labels, form fields, table headers, actions, and validation messages in lang/ar/
- [ ] T078 [P] Add Dashboard widgets in app/Filament/Widgets/: project count by status, task count by status, recent activity feed
- [ ] T079 [P] Implement cascade soft-delete logic: Company deletion soft-deletes its Projects (and transitively Tasks) via model observers in app/Observers/CompanyObserver.php and app/Observers/ProjectObserver.php
- [ ] T080 Code cleanup: run `composer lint` (Laravel Pint), fix any formatting issues across all new files
- [ ] T081 Run quickstart.md validation: fresh setup from scratch following quickstart.md steps, verify all features work end-to-end

---

## Dependencies & Execution Order

### Phase Dependencies

- **Setup (Phase 1)**: No dependencies — can start immediately
- **Foundational (Phase 2)**: Depends on Setup completion — BLOCKS all user stories
- **US1 (Phase 3)**: Depends on Foundational — no dependency on other stories
- **US2 (Phase 4)**: Depends on Foundational — no dependency on other stories
- **US3 (Phase 5)**: Depends on Foundational + US2 (Projects belong to Companies)
- **US4 (Phase 6)**: Depends on US3 (Tasks belong to Projects; reuses shared relation managers)
- **US5 (Phase 7)**: Depends on US3 and US4 (enhances status timeline views)
- **US6 (Phase 8)**: Depends on Foundational — no dependency on other stories
- **Polish (Phase 9)**: Depends on all desired user stories being complete

### User Story Dependencies

```text
Phase 1 (Setup)
    │
Phase 2 (Foundational)
    │
    ├── Phase 3 (US1: Multilingual) ──────────┐
    │                                          │
    ├── Phase 4 (US2: Company/Team) ───┐       │
    │                                  │       │
    │                           Phase 5 (US3: Projects)
    │                                  │
    ├── Phase 8 (US6: MFA) ──┐  Phase 6 (US4: Tasks)
    │                        │         │
    │                        │  Phase 7 (US5: Status Logs)
    │                        │         │
    └────────────────────────┴─────────┘
                                  │
                           Phase 9 (Polish)
```

### Within Each User Story

- Tests MUST be written and FAIL before implementation begins
- Models before services/observers
- Observers registered before resource pages that trigger them
- Policies before resources (resources reference policies)
- Shared relation managers before resources that use them (or in same story)
- Core implementation before integration points

### Parallel Opportunities

- **Phase 1**: T003, T004, T005, T006 can run in parallel
- **Phase 2**: T007, T008 (tests) can run in parallel
- **Phase 3**: T014, T015 (tests) in parallel; then T017, T018 (translations) in parallel
- **Phase 4**: T020–T024 (all 5 tests) in parallel; then T025, T026 (models) in parallel; then T029, T030 (policies) in parallel
- **Phase 5**: T035–T039 (all 5 tests) in parallel; then T040, T041, T042 (models) in parallel
- **Phase 6**: T054, T055, T056 (tests) in parallel
- **Phase 8**: T071, T072 (tests) in parallel
- **Phase 9**: T077, T078, T079 all in parallel
- **Cross-phase**: US1 and US2 can be worked in parallel after Foundational. US6 (MFA) can be done in parallel with US3/US4/US5.

---

## Parallel Example: User Story 2

```bash
# Launch all tests for User Story 2 together:
Task: "T020 Write test for Owner model in tests/Feature/Models/OwnerTest.php"
Task: "T021 Write test for Company model in tests/Feature/Models/CompanyTest.php"
Task: "T022 Write test for OwnerResource CRUD in tests/Feature/Filament/OwnerResourceTest.php"
Task: "T023 Write test for CompanyResource CRUD in tests/Feature/Filament/CompanyResourceTest.php"
Task: "T024 Write test for UserResource CRUD in tests/Feature/Filament/UserResourceTest.php"

# Launch parallel models after tests fail:
Task: "T025 Create Owner model, migration, factory"
Task: "T026 Create Company model, migration, factory"

# Launch parallel policies:
Task: "T029 Create OwnerPolicy"
Task: "T030 Create CompanyPolicy"
```

---

## Implementation Strategy

### MVP First (User Story 1 Only)

1. Complete Phase 1: Setup
2. Complete Phase 2: Foundational (CRITICAL — blocks all stories)
3. Complete Phase 3: User Story 1 (Multilingual Dashboard)
4. **STOP and VALIDATE**: Test language/direction switching independently
5. Deploy/demo if ready

### Incremental Delivery

1. Setup + Foundational → Foundation ready
2. US1 (Multilingual) → Test → Deploy/Demo (**MVP!**)
3. US2 (Company/Team) → Test → Deploy/Demo
4. US3 (Projects) → Test → Deploy/Demo
5. US4 (Tasks) → Test → Deploy/Demo
6. US5 (Status Logs) → Test → Deploy/Demo
7. US6 (MFA) → Test → Deploy/Demo
8. Polish → Final validation → Release

### Parallel Team Strategy

With multiple developers:

1. Team completes Setup + Foundational together
2. Once Foundational is done:
   - Developer A: US1 (Multilingual) then US5 (Status Logs)
   - Developer B: US2 (Company/Team) → US3 (Projects) → US4 (Tasks)
   - Developer C: US6 (MFA) then Polish
3. Stories complete and integrate independently

---

## Notes

- [P] tasks = different files, no dependencies
- [Story] label maps task to specific user story for traceability
- Each user story should be independently completable and testable
- Constitution mandates TDD: write tests, verify they fail, THEN implement
- Commit after each task or logical group
- Stop at any checkpoint to validate story independently
- Run `composer test` after each story phase to verify no regressions
