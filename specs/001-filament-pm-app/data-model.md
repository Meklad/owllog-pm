# Data Model: Filament Project Management Application

**Branch**: `001-filament-pm-app` | **Date**: 2026-03-09

## Entity Relationship Diagram (text)

```text
User 1──1 Owner (optional)
User *──* Company (pivot: company_user)
Owner 1──* Company
Company 1──* Project
Project 1──* Task
Task *──1 User (assignee, nullable)
Project|Task 1──* Comment (polymorphic)
Project|Task 1──* Attachment (polymorphic)
Project|Task 1──* StatusLog (polymorphic)
Comment *──1 User (author)
Attachment *──1 User (uploader)
StatusLog *──1 User (actor)
```

## Entities

### User (extends default Laravel auth)

Existing table `users`, extended with soft-deletes and Spatie roles.

| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | bigint unsigned | PK, auto-increment | |
| name | string(255) | required | |
| email | string(255) | required, unique | |
| email_verified_at | timestamp | nullable | |
| password | string(255) | required | |
| two_factor_secret | text | nullable, encrypted | Fortify 2FA |
| two_factor_recovery_codes | text | nullable, encrypted | Fortify 2FA |
| two_factor_confirmed_at | timestamp | nullable | Fortify 2FA |
| remember_token | string(100) | nullable | |
| created_at | timestamp | nullable | |
| updated_at | timestamp | nullable | |
| deleted_at | timestamp | nullable | Soft-delete |

**Traits**: `HasRoles` (Spatie), `TwoFactorAuthenticatable` (Fortify), `SoftDeletes`

**Relationships**:
- `owner()`: hasOne Owner
- `companies()`: belongsToMany Company (pivot: `company_user`)
- `assignedTasks()`: hasMany Task (via `assigned_to`)

---

### Owner

Data-only entity storing owner profile information. No auth/authz role.

| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | bigint unsigned | PK, auto-increment | |
| user_id | bigint unsigned | FK→users, unique | One-to-one with User |
| name | json | required | Translatable: `{"en": "...", "ar": "..."}` |
| email | string(255) | nullable | Contact email (may differ from User email) |
| phone | string(50) | nullable | |
| created_at | timestamp | nullable | |
| updated_at | timestamp | nullable | |
| deleted_at | timestamp | nullable | Soft-delete |

**Traits**: `HasTranslations`, `SoftDeletes`

**Translatable fields**: `name`

**Relationships**:
- `user()`: belongsTo User
- `companies()`: hasMany Company

**Validation**:
- `user_id` must reference an existing, non-deleted User
- `name` must have at least one locale filled (en or ar)

---

### Company

Represents an organization or tenant. Belongs to an Owner, has team members (Users) and Projects.

| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | bigint unsigned | PK, auto-increment | |
| owner_id | bigint unsigned | FK→owners | |
| name | json | required | Translatable: `{"en": "...", "ar": "..."}` |
| description | json | nullable | Translatable |
| created_at | timestamp | nullable | |
| updated_at | timestamp | nullable | |
| deleted_at | timestamp | nullable | Soft-delete |

**Traits**: `HasTranslations`, `SoftDeletes`

**Translatable fields**: `name`, `description`

**Relationships**:
- `owner()`: belongsTo Owner
- `members()`: belongsToMany User (pivot: `company_user`)
- `projects()`: hasMany Project

**Validation**:
- `name` must have at least one locale filled
- `owner_id` must reference an existing Owner

---

### company_user (pivot table)

Tracks which Users are members of which Companies.

| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | bigint unsigned | PK, auto-increment | |
| company_id | bigint unsigned | FK→companies | |
| user_id | bigint unsigned | FK→users | |
| created_at | timestamp | nullable | |
| updated_at | timestamp | nullable | |

**Constraints**: unique composite on `(company_id, user_id)`

---

### Project

Core work container. Belongs to a Company. Has translatable title/description, statuses, dates.

| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | bigint unsigned | PK, auto-increment | |
| company_id | bigint unsigned | FK→companies | |
| title | json | required | Translatable |
| description | json | nullable | Translatable |
| status | string(20) | required, default: `to_do` | Enum: Status |
| start_date | date | nullable | |
| end_date | date | nullable | Must be >= start_date when both set |
| created_at | timestamp | nullable | |
| updated_at | timestamp | nullable | |
| deleted_at | timestamp | nullable | Soft-delete |

**Traits**: `HasTranslations`, `SoftDeletes`

**Translatable fields**: `title`, `description`

**Casts**: `status` → `Status` enum, `start_date` → date, `end_date` → date

**Relationships**:
- `company()`: belongsTo Company
- `tasks()`: hasMany Task
- `comments()`: morphMany Comment
- `attachments()`: morphMany Attachment
- `statusLogs()`: morphMany StatusLog

**Validation**:
- `title` must have at least one locale filled
- `end_date` must be on or after `start_date` when both are provided
- `status` must be a valid Status enum value

---

### Task

Actionable work item within a Project. Single assignee, rich-text notes, translatable name/description.

| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | bigint unsigned | PK, auto-increment | |
| project_id | bigint unsigned | FK→projects | |
| assigned_to | bigint unsigned | FK→users, nullable | Single assignee |
| title | json | required | Translatable |
| description | json | nullable | Translatable |
| notes | text | nullable | Rich-text HTML content |
| status | string(20) | required, default: `to_do` | Enum: Status |
| start_date | date | nullable | |
| end_date | date | nullable | Must be >= start_date when both set |
| created_at | timestamp | nullable | |
| updated_at | timestamp | nullable | |
| deleted_at | timestamp | nullable | Soft-delete |

**Traits**: `HasTranslations`, `SoftDeletes`

**Translatable fields**: `title`, `description`

**Casts**: `status` → `Status` enum, `start_date` → date, `end_date` → date

**Relationships**:
- `project()`: belongsTo Project
- `assignee()`: belongsTo User (via `assigned_to`)
- `comments()`: morphMany Comment
- `attachments()`: morphMany Attachment
- `statusLogs()`: morphMany StatusLog

**Validation**:
- `title` must have at least one locale filled
- `assigned_to` must reference a User who is a member of the task's project's company
- `end_date` must be on or after `start_date` when both are provided
- `status` must be a valid Status enum value

---

### Comment (polymorphic)

User-authored comment on a Project or Task.

| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | bigint unsigned | PK, auto-increment | |
| commentable_type | string(255) | required | Morph type (Project or Task) |
| commentable_id | bigint unsigned | required | Morph ID |
| user_id | bigint unsigned | FK→users | Author |
| body | text | required | Plain text or basic HTML |
| created_at | timestamp | nullable | |
| updated_at | timestamp | nullable | |
| deleted_at | timestamp | nullable | Soft-delete |

**Traits**: `SoftDeletes`

**Relationships**:
- `commentable()`: morphTo (Project or Task)
- `user()`: belongsTo User

**Validation**:
- `body` must not be empty

---

### Attachment (polymorphic)

File attachment on a Project or Task. Stored on local disk.

| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | bigint unsigned | PK, auto-increment | |
| attachable_type | string(255) | required | Morph type (Project or Task) |
| attachable_id | bigint unsigned | required | Morph ID |
| user_id | bigint unsigned | FK→users | Uploader |
| file_path | string(500) | required | Relative path in storage |
| file_name | string(255) | required | Original file name |
| file_size | integer unsigned | required | Size in bytes |
| mime_type | string(100) | required | |
| created_at | timestamp | nullable | |
| updated_at | timestamp | nullable | |
| deleted_at | timestamp | nullable | Soft-delete |

**Traits**: `SoftDeletes`

**Relationships**:
- `attachable()`: morphTo (Project or Task)
- `user()`: belongsTo User

**Validation**:
- `file_size` must be <= 10,485,760 (10MB)
- `file_path` must be a valid storage path

---

### StatusLog (polymorphic)

Immutable audit record of a status change on a Project or Task.

| Column | Type | Constraints | Notes |
|--------|------|-------------|-------|
| id | bigint unsigned | PK, auto-increment | |
| loggable_type | string(255) | required | Morph type (Project or Task) |
| loggable_id | bigint unsigned | required | Morph ID |
| user_id | bigint unsigned | FK→users | Who made the change |
| old_status | string(20) | nullable | Null for initial status assignment |
| new_status | string(20) | required | |
| created_at | timestamp | nullable | |

**No soft-delete**: Audit records are permanent and immutable.

**No `updated_at`**: Records are write-once; use `$timestamps = false` with manual `created_at`.

**Relationships**:
- `loggable()`: morphTo (Project or Task)
- `user()`: belongsTo User

---

## Status Enum

```php
enum Status: string
{
    case ToDo = 'to_do';
    case InProgress = 'in_progress';
    case InReview = 'in_review';
    case InTest = 'in_test';
    case Blocked = 'blocked';
    case Done = 'done';
}
```

All transitions are allowed (no constrained state machine). Any status can change to any other status. Each transition is logged in StatusLog.

## Indexes

| Table | Index | Columns | Type |
|-------|-------|---------|------|
| owners | owners_user_id_unique | user_id | unique |
| companies | companies_owner_id_index | owner_id | index |
| company_user | company_user_unique | company_id, user_id | unique |
| company_user | company_user_user_id_index | user_id | index |
| projects | projects_company_id_index | company_id | index |
| projects | projects_status_index | status | index |
| tasks | tasks_project_id_index | project_id | index |
| tasks | tasks_assigned_to_index | assigned_to | index |
| tasks | tasks_status_index | status | index |
| comments | comments_commentable_index | commentable_type, commentable_id | index |
| comments | comments_user_id_index | user_id | index |
| attachments | attachments_attachable_index | attachable_type, attachable_id | index |
| status_logs | status_logs_loggable_index | loggable_type, loggable_id | index |
| status_logs | status_logs_user_id_index | user_id | index |

## Cascade Behavior (soft-delete)

| When deleted | Also soft-deleted |
|-------------|-------------------|
| Company | All Projects of that Company → all Tasks of those Projects |
| Project | All Tasks of that Project |
| User | Nothing cascaded (Owner, memberships, assignments remain; handled in UI) |
| Owner | Nothing cascaded (Companies remain; ownership transfer handled in UI) |

Cascade soft-deletes are implemented via model observers, not database-level cascades.

## Spatie Permission Schema (seeded roles & permissions)

**Roles**:
- `super-admin`: Full system access (bypasses all checks via Gate::before)
- `admin`: Can manage companies they own, and all resources within
- `member`: Can view/create/update projects and tasks within their companies

**Permissions** (per resource):
- `view_owners`, `create_owners`, `update_owners`, `delete_owners`
- `view_companies`, `create_companies`, `update_companies`, `delete_companies`
- `view_projects`, `create_projects`, `update_projects`, `delete_projects`
- `view_tasks`, `create_tasks`, `update_tasks`, `delete_tasks`
- `view_comments`, `create_comments`, `delete_comments`
- `view_attachments`, `create_attachments`, `delete_attachments`
- `view_status_logs`
