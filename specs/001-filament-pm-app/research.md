# Research: Filament Project Management Application

**Branch**: `001-filament-pm-app` | **Date**: 2026-03-09

## R1: Filament Version Selection

**Decision**: Filament v5

**Rationale**: Filament v5 was released January 16, 2026 and is the current active version. It requires Laravel 11.28+ (we have Laravel 12), Livewire 4 (we have it), Tailwind CSS v4 (we have it), and PHP 8.2+ (we have it). v5 is functionally identical to v4 — the major bump exists solely to support Livewire 4, which our project already uses.

**Alternatives considered**:
- Filament v3.3+: Works with Laravel 12 but uses Livewire 3. Would conflict with our Livewire 4 dependency.
- Filament v4: Designed for Livewire 3. Not compatible with our Livewire 4 stack.

## R2: Bilingual Content Storage (Translatable Fields)

**Decision**: Spatie Laravel Translatable + Filament Spatie Translatable Plugin

**Rationale**: The official Filament plugin for Spatie Translatable provides native form-level locale switching for Create/Edit/View pages. Translatable attributes are stored as JSON columns, which SQLite supports. The plugin adds a `LocaleSwitcher` component to form pages for switching between EN and AR.

**Alternatives considered**:
- Manual JSON columns with custom accessors: More work, no ecosystem support, no Filament integration.
- Separate columns per locale (e.g., `title_en`, `title_ar`): Doesn't scale to more locales, requires custom Filament form logic.
- astrotomic/laravel-translatable: Uses a separate translations table. More complex schema, less Filament plugin support.

**Implementation notes**:
- Models use `HasTranslations` trait from Spatie
- Translatable attributes defined in `$translatable` array on each model
- Filament resources use `TranslatableContent` plugin on Create/Edit/View pages
- Known caveat in v4/v5: table columns may only show the default locale value in list views; acceptable for initial implementation

## R3: RTL/LTR Direction Switching

**Decision**: `bezhansalleh/filament-language-switch` plugin + Filament's built-in RTL support

**Rationale**: The Language Switch plugin provides zero-config locale switching in the panel top bar. Filament core has built-in RTL support using Tailwind's `rtl:` variant utilities. When the locale is set to Arabic (script: `Arab`), Filament automatically renders the entire layout in RTL.

**Alternatives considered**:
- Custom Livewire component for switching: Reinventing existing, well-maintained plugin.
- Browser-level direction only: Wouldn't persist preference or integrate with Filament's locale system.

**Implementation notes**:
- Plugin configured with `en` and `ar` locales, with Arabic flagged as RTL script
- Direction switch may require page refresh for full effect
- Language preference stored in session/cookie

## R4: Authorization with Spatie Laravel Permission

**Decision**: Spatie Laravel Permission v7 with teams mode disabled (simple roles)

**Rationale**: The spec defines Owner as a data entity, not an auth role. Authorization needs are straightforward: control which users can perform CRUD on companies, projects, and tasks. Simple roles (`super-admin`, `admin`, `member`) with associated permissions suffice. Teams mode adds complexity not warranted by the current spec (company scoping is handled by Eloquent relationships, not permission scoping).

**Alternatives considered**:
- Spatie with teams mode: Scopes permissions per company. Over-engineered for current needs — company data scoping is already achieved through the Company→Project→Task relationship chain and Eloquent query scopes.
- Laravel Gates only (no package): Less structured, harder to manage in Filament UI, no role management interface.
- Filament Shield plugin: Adds auto-generated permissions per resource. Could be useful but adds another dependency; manual permission definition gives more control.

**Implementation notes**:
- User model uses `HasRoles` trait
- Roles: `super-admin` (full access), `admin` (manage own companies), `member` (access assigned company resources)
- Permissions: `view_owners`, `create_owners`, `update_owners`, `delete_owners`, `view_companies`, `create_companies`, ... (per-resource CRUD)
- `super-admin` bypasses all permission checks via Gate::before
- Filament policies use permission checks; Filament resources register policy gates

## R5: File Attachment Storage

**Decision**: Laravel's built-in filesystem (`storage/app/public`) with Filament's `FileUpload` component

**Rationale**: Filament's `FileUpload` form component handles file uploads natively with drag-and-drop, preview, and validation. Using Laravel's local disk storage avoids external dependencies. The 10MB limit is enforced via Filament's validation rules and PHP's upload limits.

**Alternatives considered**:
- Spatie Media Library + Filament plugin: Powerful but adds significant complexity (media collections, conversions, responsive images) that isn't needed for simple file storage.
- S3-compatible storage: Over-engineered for the current scope; can be migrated later by changing the filesystem disk config.

**Implementation notes**:
- Attachments stored in `storage/app/public/attachments/{type}/{id}/`
- Filament's `FileUpload` field with `->maxSize(10240)` (10MB in KB)
- Polymorphic `Attachment` model tracks file metadata (path, name, size, mime type)
- `php artisan storage:link` required for public access

## R6: Soft-Delete Strategy

**Decision**: Laravel `SoftDeletes` trait on all core models; Filament trash/restore support

**Rationale**: Soft-delete preserves audit trails and enables recovery. Filament has built-in support for soft-deleted records: `TrashedFilter` in tables, `ForceDeleteAction`, `RestoreAction` in resource pages.

**Alternatives considered**:
- Hard delete with cascade: Destroys audit trail, no recovery. Conflicts with status log preservation requirement.
- Soft-delete core only (hard-delete comments/attachments): Inconsistent behavior, orphaned references in audit logs.

**Implementation notes**:
- All models (Owner, Company, Project, Task, Comment, Attachment) use `SoftDeletes` trait
- StatusLog does NOT use soft-deletes (audit records are permanent)
- Filament resources include `TrashedFilter` and Restore/ForceDelete actions
- Cascade soft-delete: when a Project is soft-deleted, its Tasks are also soft-deleted (via model observer or event)
- Company soft-delete cascades to Projects (and transitively to Tasks)

## R7: Rich-Text Notes on Tasks

**Decision**: Filament's built-in `RichEditor` component with HTML storage

**Rationale**: Filament v5 includes a `RichEditor` form field that supports headers, lists, links, bold, italic, and other formatting out of the box. Content is stored as HTML in a `text` column. No additional packages needed.

**Alternatives considered**:
- TipTap Editor (Filament plugin): More powerful but adds dependency for features not required by the spec.
- Markdown storage with rendering: Adds complexity for content that will only be displayed in the admin panel.

**Implementation notes**:
- Task model has a `notes` column (`text`, nullable)
- Filament form uses `RichEditor::make('notes')` with toolbar buttons for headers, lists, links
- Display in View pages via `HtmlEntry` or rendered Blade output

## R8: Status Tracking and Audit Logs

**Decision**: Shared PHP Enum for statuses + Eloquent model observer for automatic status logging

**Rationale**: A backed PHP enum (`Status`) defines the 6 statuses shared between Projects and Tasks, providing type safety and IDE support. Model observers on Project and Task detect status changes in the `updating` event and create StatusLog records automatically.

**Alternatives considered**:
- Spatie Laravel Activitylog: Full-featured activity logging. Over-powered for just status changes; adds dependency and storage overhead for data we don't need.
- Database triggers: Not portable across database engines; harder to test.
- Manual logging in controllers/Filament actions: Error-prone; easy to miss a status change path.

**Implementation notes**:
- `Status` enum: `ToDo`, `InProgress`, `InReview`, `InTest`, `Blocked`, `Done`
- Enum backed by string values: `to_do`, `in_progress`, `in_review`, `in_test`, `blocked`, `done`
- `StatusLog` model: polymorphic (`loggable`), stores `old_status`, `new_status`, `user_id`, `created_at`
- Observers registered in `AppServiceProvider`
- Filament displays status logs as a timeline in ViewProject / ViewTask pages using a custom Infolist entry or Relation Manager

## R9: MFA Implementation

**Decision**: Laravel Fortify's built-in two-factor authentication (TOTP)

**Rationale**: The project already includes `laravel/fortify` as a dependency. Fortify provides TOTP-based 2FA with QR code setup, recovery codes, and challenge middleware out of the box. No additional packages needed.

**Alternatives considered**:
- Custom TOTP implementation: Reinventing what Fortify already provides.
- WebAuthn/Passkeys: Modern but more complex; spec only requires MFA, not specific method.

**Implementation notes**:
- Enable `Features::twoFactorAuthentication()` in Fortify config
- Filament panel protected by `TwoFactorAuthenticatable` contract on User model
- Custom Filament page for 2FA setup (enable, show QR code, confirm, show recovery codes)
- Challenge middleware integrated with Filament's auth flow
