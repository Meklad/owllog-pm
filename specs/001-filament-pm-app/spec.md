# Feature Specification: Filament Project Management Application

**Feature Branch**: `001-filament-pm-app`  
**Created**: 2025-03-09  
**Status**: Draft  
**Input**: Build a Laravel project management application using Laravel Filament as the admin dashboard. Full English and Arabic support with RTL/LTR direction switching.

## User Scenarios & Testing *(mandatory)*

### User Story 1 - Multilingual Dashboard Access (Priority: P1)

As an admin user, I need to access the dashboard in my preferred language (English or Arabic) and have the interface layout match the text direction (LTR for English, RTL for Arabic) so that I can work comfortably in my language.

**Why this priority**: Without language and direction support, Arabic-speaking users cannot effectively use the system. This is foundational for the target audience.

**Independent Test**: Switch dashboard language and direction, verify all labels and layout adapt correctly. Can be validated without any other feature.

**Acceptance Scenarios**:

1. **Given** I am logged into the admin dashboard, **When** I select English as the language, **Then** the interface displays in English with left-to-right layout
2. **Given** I am logged into the admin dashboard, **When** I select Arabic as the language, **Then** the interface displays in Arabic with right-to-left layout
3. **Given** I am viewing the dashboard, **When** I use the language switcher in the top bar, **Then** the entire dashboard updates immediately to the selected language and direction
4. **Given** I am viewing the dashboard, **When** I use the direction switcher in the top bar, **Then** the layout flips between RTL and LTR without requiring a page reload

---

### User Story 2 - Company and Team Structure (Priority: P1)

As an owner, I need to create companies and assign users (team members) to companies so that each company has its own team with access to that company's projects.

**Why this priority**: The ownership and company structure is the foundation for multi-tenant project management.

**Independent Test**: Create an owner, create a company, add users to the company, and verify users can only access that company's projects. Delivers isolated company workspaces.

**Acceptance Scenarios**:

1. **Given** I am an owner, **When** I create a new company, **Then** I can set company details and the company appears in my company list
2. **Given** I have a company, **When** I add a user as a team member, **Then** that user gains access to the company's projects
3. **Given** I am an owner with multiple companies, **When** I view my companies, **Then** I see all companies I own and can manage each one
4. **Given** I am a team member of a company, **When** I log in, **Then** I see only projects belonging to my company or companies

---

### User Story 3 - Project Management (Priority: P1)

As a company user, I need to create and manage projects with bilingual titles and descriptions, dates, attachments, and comments so that my team can collaborate on project work in both English and Arabic.

**Why this priority**: Projects are the core unit of work; without them, tasks have no context.

**Independent Test**: Create a project with bilingual content, add attachments and comments, verify all content persists and displays in both languages. Delivers a complete project record.

**Acceptance Scenarios**:

1. **Given** I am a company user, **When** I create a project, **Then** I can enter title and description in both English and Arabic
2. **Given** I have a project, **When** I set start and end dates, **Then** the dates are stored and displayed
3. **Given** I have a project, **When** I upload file attachments, **Then** the files are associated with the project and viewable
4. **Given** I have a project, **When** I add a comment, **Then** the comment is saved with my identity and timestamp and visible to other users with access

---

### User Story 4 - Task Management (Priority: P1)

As a company user, I need to create and manage tasks within projects with bilingual names and descriptions, rich-text notes, dates, attachments, comments, and status change history so that work is tracked and auditable.

**Why this priority**: Tasks are the actionable work items; this is the primary value of the application.

**Independent Test**: Create a task in a project with bilingual content and rich notes, change status, add comments and attachments, verify status history is visible. Delivers full task lifecycle tracking.

**Acceptance Scenarios**:

1. **Given** I have a project, **When** I create a task, **Then** I can enter name and description in both English and Arabic
2. **Given** I am editing a task, **When** I enter notes with formatting (headers, lists, links), **Then** the formatted notes are saved and displayed correctly
3. **Given** I have a task, **When** I change its status, **Then** the change is logged with my identity and timestamp and visible in a timeline
4. **Given** I have a task, **When** I upload attachments or add comments, **Then** they are associated with the task and viewable
5. **Given** I view a project, **When** I navigate to its tasks, **Then** I see tasks in a nested structure within the project context

---

### User Story 5 - Status Logs and Audit Trail (Priority: P2)

As a user, I need to see a timeline of status changes for projects and tasks so that I can understand the history of work and who made each change.

**Why this priority**: Audit trail supports accountability and debugging; important but not blocking basic CRUD.

**Independent Test**: Change a project or task status multiple times, view the status log timeline, verify all changes with user and timestamp are shown.

**Acceptance Scenarios**:

1. **Given** I view a project or task, **When** I open the status log section, **Then** I see a chronological timeline of all status changes
2. **Given** a status change exists, **When** I view the log entry, **Then** I see who made the change and when

---

### User Story 6 - Admin Security (MFA) (Priority: P2)

As an admin user, I need to enable multi-factor authentication so that the admin dashboard is protected against unauthorized access.

**Why this priority**: Security is important for admin access; can follow initial dashboard delivery.

**Independent Test**: Enable MFA for an admin account, log in and verify MFA challenge is required. Delivers stronger admin security.

**Acceptance Scenarios**:

1. **Given** I am an admin user, **When** I enable MFA on my account, **Then** I am required to complete a second factor on subsequent logins
2. **Given** MFA is enabled, **When** an unauthorized person attempts login with my password, **Then** they cannot complete authentication without the second factor

---

### Edge Cases

- What happens when a user belongs to multiple companies? They see projects from all companies they belong to; company/project scope is enforced per action.
- How does the system handle incomplete translations? Display the available language; fallback to the other language or a placeholder when content is missing.
- What happens when a project is deleted? All associated tasks, comments, and attachments are soft-deleted (recoverable). Audit trail is fully preserved.
- How does the system handle very large file uploads? Enforce a 10MB per-file limit; reject uploads exceeding the limit with a clear error message.
- What happens when dates are invalid (e.g., end before start)? Validation rejects invalid dates with a clear message.
- How does the system handle concurrent edits to the same task? Last-write-wins with optimistic UI; conflicts surfaced where feasible.

## Clarifications

### Session 2026-03-09

- Q: How should user roles be modeled (owner/admin/team member)? → A: Owner is a separate data entity (no auth/authz) storing owner info, linked to a User record. All authentication and authorization handled via the Users table using Spatie Laravel Permission.
- Q: Can tasks be assigned to users? → A: Single assignee — each task can be assigned to one team member from the project's company.
- Q: What statuses should projects and tasks use? → A: Standard shared set (6): To Do, In Progress, In Review, In Test, Blocked, Done.
- Q: What deletion strategy for projects and tasks? → A: Soft-delete all (projects, tasks, comments, attachments). Recoverable. Audit trail fully preserved.
- Q: What storage strategy and size limit for file attachments? → A: Local disk (Laravel storage/app), 10MB max per file.

## Requirements *(mandatory)*

### Functional Requirements

- **FR-001**: System MUST support two interface languages: English and Arabic
- **FR-002**: System MUST switch layout direction (LTR for English, RTL for Arabic) when language or direction changes
- **FR-003**: System MUST provide language and direction switchers in the dashboard top bar
- **FR-004**: System MUST support an ownership model where owners have one or more companies
- **FR-005**: System MUST support company membership: users can be assigned to companies as team members
- **FR-006**: System MUST scope projects to companies; users see only projects of their companies
- **FR-007**: System MUST store project title and description in both English and Arabic
- **FR-008**: System MUST store task name and description in both English and Arabic
- **FR-009**: System MUST support rich-text notes on tasks (formatting such as headers, lists, links)
- **FR-010**: System MUST support file attachments on projects and tasks
- **FR-011**: System MUST support comments on projects and tasks, with author and timestamp
- **FR-012**: System MUST log status changes on projects and tasks with user identity and timestamp
- **FR-013**: System MUST display status change history as a timeline on project and task pages
- **FR-014**: System MUST support nested tasks within projects (tasks belong to a project)
- **FR-015**: System MUST provide CRUD interfaces for owners, companies, users, projects, and tasks in the admin dashboard
- **FR-016**: System MUST support multi-factor authentication for admin users
- **FR-017**: System MUST use Spatie Laravel Permission for all role and permission management
- **FR-018**: System MUST model Owner as a data-only entity linked to a User record; Owner has no direct auth/authz responsibilities
- **FR-019**: System MUST support assigning a task to exactly one team member (single assignee) from the project's company
- **FR-020**: System MUST use a shared status set for projects and tasks: To Do, In Progress, In Review, In Test, Blocked, Done
- **FR-021**: System MUST soft-delete projects, tasks, comments, and attachments (no hard deletes); records remain recoverable and audit trail is preserved
- **FR-022**: System MUST store file attachments on local disk (Laravel storage/app) with a 10MB per-file size limit

### Key Entities

- **Owner**: Data entity storing owner information (name, contact details). Has no authentication or authorization role. Must be linked to a User record. A User who is an owner has one Owner profile.
- **Company**: Belongs to an owner. Has users (team members) and projects. Represents an organization or tenant.
- **User**: Central identity and authentication model. Handles all auth/authz via Spatie Laravel Permission. Can be linked to an Owner profile and/or be a team member of one or more companies.
- **Project**: Belongs to a company. Has bilingual title and description, start/end dates, attachments, comments, status (To Do | In Progress | In Review | In Test | Blocked | Done), status logs, and tasks.
- **Task**: Belongs to a project. Has bilingual name and description, rich-text notes, start/end dates, a single assignee (one User from the project's company), attachments, comments, status (To Do | In Progress | In Review | In Test | Blocked | Done), and status logs.
- **Comment**: Polymorphic—can belong to a project or a task. Has author, content, and timestamp.
- **Attachment**: File associated with a project or a task. Stored on local disk (Laravel `storage/app`), 10MB max per file. Has file metadata and association.
- **Status Log**: Polymorphic record of a status change. Has previous/new status, user who made the change, and timestamp.

## Success Criteria *(mandatory)*

### Measurable Outcomes

- **SC-001**: Users can switch dashboard language and direction and see the full interface update in under 3 seconds
- **SC-002**: Users can create a project with bilingual content in under 2 minutes
- **SC-003**: Users can create a task within a project with full metadata in under 90 seconds
- **SC-004**: All status changes are recorded and visible in the timeline with correct user and timestamp
- **SC-005**: Arabic-speaking users can complete primary workflows (create project, create task, add comment) entirely in Arabic with correct RTL layout
- **SC-006**: Admin users can enable MFA and complete a protected login flow in under 2 minutes

## Assumptions

- Admin dashboard technology, database, and package choices are specified by the stakeholder in the planning input
- Bilingual content storage uses a structure supporting English and Arabic keys per entity
- File attachments and comments use polymorphic associations to support both projects and tasks
- Status logs capture user identity and timestamp for each change
