# Filament Resource Contracts

**Branch**: `001-filament-pm-app` | **Date**: 2026-03-09

This document defines the UI contract for each Filament resource — the fields, columns, filters, actions, and relation managers exposed in the admin panel.

---

## OwnerResource

**Model**: `App\Models\Owner`  
**Navigation group**: Organization  
**Icon**: `heroicon-o-user-circle`

### Table Columns

| Column | Type | Sortable | Searchable | Notes |
|--------|------|----------|------------|-------|
| id | TextColumn | yes | no | |
| name | TextColumn | yes | yes | Translatable (shows current locale) |
| email | TextColumn | no | yes | |
| phone | TextColumn | no | no | |
| user.name | TextColumn | yes | yes | Linked User account |
| created_at | TextColumn | yes | no | Date format |

### Table Filters

| Filter | Type | Notes |
|--------|------|-------|
| trashed | TrashedFilter | Soft-delete filter |

### Form Fields (Create / Edit)

| Field | Component | Required | Notes |
|-------|-----------|----------|-------|
| user_id | Select | yes | Searchable, filtered to Users without Owner profiles |
| name | TextInput | yes | Translatable (locale switcher on page) |
| email | TextInput | no | Email validation |
| phone | TextInput | no | |

### Actions

- View, Edit, Delete, Restore, ForceDelete

---

## CompanyResource

**Model**: `App\Models\Company`  
**Navigation group**: Organization  
**Icon**: `heroicon-o-building-office`

### Table Columns

| Column | Type | Sortable | Searchable | Notes |
|--------|------|----------|------------|-------|
| id | TextColumn | yes | no | |
| name | TextColumn | yes | yes | Translatable |
| owner.name | TextColumn | yes | yes | Owner name (translatable) |
| members_count | TextColumn | yes | no | Count badge |
| projects_count | TextColumn | yes | no | Count badge |
| created_at | TextColumn | yes | no | |

### Table Filters

| Filter | Type | Notes |
|--------|------|-------|
| owner | SelectFilter | Filter by owner |
| trashed | TrashedFilter | |

### Form Fields (Create / Edit)

| Field | Component | Required | Notes |
|-------|-----------|----------|-------|
| owner_id | Select | yes | Searchable, shows Owner names |
| name | TextInput | yes | Translatable |
| description | Textarea | no | Translatable |

### Relation Managers

| Relation | Manager | Notes |
|----------|---------|-------|
| members | MembersRelationManager | Attach/detach Users; searchable select |
| projects | ProjectsRelationManager | Inline project list with create action |

### Actions

- View, Edit, Delete, Restore, ForceDelete

---

## UserResource

**Model**: `App\Models\User`  
**Navigation group**: Organization  
**Icon**: `heroicon-o-users`

### Table Columns

| Column | Type | Sortable | Searchable | Notes |
|--------|------|----------|------------|-------|
| id | TextColumn | yes | no | |
| name | TextColumn | yes | yes | |
| email | TextColumn | yes | yes | |
| roles.name | TextColumn | no | no | Badge list |
| companies_count | TextColumn | yes | no | |
| created_at | TextColumn | yes | no | |

### Table Filters

| Filter | Type | Notes |
|--------|------|-------|
| role | SelectFilter | Filter by Spatie role |
| verified | TernaryFilter | Email verified status |
| trashed | TrashedFilter | |

### Form Fields (Create / Edit)

| Field | Component | Required | Notes |
|-------|-----------|----------|-------|
| name | TextInput | yes | |
| email | TextInput | yes | Email, unique |
| password | TextInput | create only | Hidden on edit unless changed |
| roles | Select | yes | Multiple, from Spatie roles |

### Actions

- View, Edit, Delete, Restore, ForceDelete

---

## ProjectResource

**Model**: `App\Models\Project`  
**Navigation group**: Work  
**Icon**: `heroicon-o-rectangle-stack`

### Table Columns

| Column | Type | Sortable | Searchable | Notes |
|--------|------|----------|------------|-------|
| id | TextColumn | yes | no | |
| title | TextColumn | yes | yes | Translatable |
| company.name | TextColumn | yes | yes | Translatable |
| status | BadgeColumn | yes | no | Color-coded by status |
| tasks_count | TextColumn | yes | no | |
| start_date | TextColumn | yes | no | |
| end_date | TextColumn | yes | no | |
| created_at | TextColumn | yes | no | |

### Table Filters

| Filter | Type | Notes |
|--------|------|-------|
| company | SelectFilter | Filter by company |
| status | SelectFilter | Filter by Status enum |
| trashed | TrashedFilter | |

### Form Fields (Create / Edit)

| Field | Component | Required | Notes |
|-------|-----------|----------|-------|
| company_id | Select | yes | Searchable; scoped to user's companies |
| title | TextInput | yes | Translatable |
| description | RichEditor | no | Translatable |
| status | Select | yes | Status enum options |
| start_date | DatePicker | no | |
| end_date | DatePicker | no | After or equal to start_date |

### Relation Managers

| Relation | Manager | Notes |
|----------|---------|-------|
| tasks | TasksRelationManager | Inline task list with create, status quick-change |
| comments | CommentsRelationManager | Create/delete comments; shows author + timestamp |
| attachments | AttachmentsRelationManager | Upload/delete files; shows metadata |
| statusLogs | StatusLogsRelationManager | Read-only timeline view |

### Actions

- View, Edit, Delete, Restore, ForceDelete

### Infolist (View page)

| Entry | Type | Notes |
|-------|------|-------|
| title | TextEntry | Translatable |
| description | HtmlEntry | Translatable |
| company.name | TextEntry | |
| status | BadgeEntry | Color-coded |
| start_date | TextEntry | |
| end_date | TextEntry | |
| Status Timeline | RepeatableEntry | StatusLog records in reverse chronological order |

---

## TaskResource

**Model**: `App\Models\Task`  
**Navigation group**: Work  
**Icon**: `heroicon-o-clipboard-document-check`

### Table Columns

| Column | Type | Sortable | Searchable | Notes |
|--------|------|----------|------------|-------|
| id | TextColumn | yes | no | |
| title | TextColumn | yes | yes | Translatable |
| project.title | TextColumn | yes | yes | Translatable |
| assignee.name | TextColumn | yes | yes | Nullable |
| status | BadgeColumn | yes | no | Color-coded |
| start_date | TextColumn | yes | no | |
| end_date | TextColumn | yes | no | |
| created_at | TextColumn | yes | no | |

### Table Filters

| Filter | Type | Notes |
|--------|------|-------|
| project | SelectFilter | Filter by project |
| assignee | SelectFilter | Filter by assigned user |
| status | SelectFilter | Status enum |
| trashed | TrashedFilter | |

### Form Fields (Create / Edit)

| Field | Component | Required | Notes |
|-------|-----------|----------|-------|
| project_id | Select | yes | Searchable; scoped to user's company projects |
| assigned_to | Select | no | Searchable; scoped to project's company members |
| title | TextInput | yes | Translatable |
| description | Textarea | no | Translatable |
| notes | RichEditor | no | NOT translatable; HTML storage |
| status | Select | yes | Status enum options |
| start_date | DatePicker | no | |
| end_date | DatePicker | no | After or equal to start_date |

### Relation Managers

| Relation | Manager | Notes |
|----------|---------|-------|
| comments | CommentsRelationManager | Shared component with ProjectResource |
| attachments | AttachmentsRelationManager | Shared component with ProjectResource |
| statusLogs | StatusLogsRelationManager | Read-only timeline view |

### Actions

- View, Edit, Delete, Restore, ForceDelete

### Infolist (View page)

| Entry | Type | Notes |
|-------|------|-------|
| title | TextEntry | Translatable |
| description | TextEntry | Translatable |
| notes | HtmlEntry | Rendered rich-text |
| project.title | TextEntry | |
| assignee.name | TextEntry | |
| status | BadgeEntry | Color-coded |
| start_date | TextEntry | |
| end_date | TextEntry | |
| Status Timeline | RepeatableEntry | StatusLog records in reverse chronological order |

---

## Shared Relation Managers

### CommentsRelationManager

Used by: ProjectResource, TaskResource

| Column | Type | Notes |
|--------|------|-------|
| user.name | TextColumn | Author |
| body | TextColumn | Truncated in table |
| created_at | TextColumn | Relative time |

**Form**: `body` (Textarea, required). `user_id` auto-set to authenticated user.

**Actions**: Create, Delete. No edit (comments are append-only after creation).

### AttachmentsRelationManager

Used by: ProjectResource, TaskResource

| Column | Type | Notes |
|--------|------|-------|
| file_name | TextColumn | Original name |
| file_size | TextColumn | Human-readable (KB/MB) |
| mime_type | TextColumn | |
| user.name | TextColumn | Uploader |
| created_at | TextColumn | |

**Form**: `FileUpload` with `->maxSize(10240)`, `->directory('attachments')`. Metadata extracted on upload.

**Actions**: Create (upload), Delete, Download.

### StatusLogsRelationManager

Used by: ProjectResource, TaskResource

| Column | Type | Notes |
|--------|------|-------|
| old_status | BadgeColumn | Color-coded; "—" if null |
| new_status | BadgeColumn | Color-coded |
| user.name | TextColumn | |
| created_at | TextColumn | Relative time |

**Read-only**: No create/edit/delete actions. Sorted by `created_at` descending.

---

## Panel Configuration

**Panel ID**: `admin`  
**Path**: `/admin`  
**Login**: Filament's built-in login with Fortify backend  
**Plugins**:
- `SpatieLaravelTranslatablePlugin` (locales: `['en', 'ar']`)
- `FilamentLanguageSwitchPlugin` (locales: `en`, `ar` with RTL script)

**Middleware**:
- Standard Filament auth middleware
- Company-scoping middleware (filters data based on authenticated user's company memberships)

**Navigation Groups**:
- Organization (Owners, Companies, Users)
- Work (Projects, Tasks)
