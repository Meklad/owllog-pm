# Quickstart: Filament Project Management Application

**Branch**: `001-filament-pm-app` | **Date**: 2026-03-09

## Prerequisites

- PHP 8.2+
- Composer 2.x
- Node.js 20+ and npm
- SQLite 3.38+ (JSON support required)

## Initial Setup

```bash
# Clone and switch to feature branch
git checkout 001-filament-pm-app

# Install PHP dependencies
composer install

# Install new packages for this feature
composer require filament/filament:"^5.0" -W
composer require filament/spatie-laravel-translatable-plugin:"^5.0" -W
composer require spatie/laravel-permission:"^7.0"
composer require bezhansalleh/filament-language-switch:"^3.0"

# Install frontend dependencies
npm install

# Environment setup
cp .env.example .env
php artisan key:generate

# Create SQLite database
touch database/database.sqlite

# Publish package configs and migrations
php artisan vendor:publish --provider="Spatie\Permission\PermissionServiceProvider"
php artisan vendor:publish --tag="filament-language-switch-config"

# Run migrations
php artisan migrate

# Seed roles and permissions
php artisan db:seed --class=RoleAndPermissionSeeder

# Create storage symlink for file attachments
php artisan storage:link

# Build frontend assets
npm run build
```

## Create Filament Admin Panel

```bash
# Install Filament panel (if not already created)
php artisan filament:install --panels
```

This creates `app/Providers/Filament/AdminPanelProvider.php`. Configure it with:
- SpatieLaravelTranslatablePlugin (locales: `['en', 'ar']`)
- FilamentLanguageSwitchPlugin
- SoftDeletes support on all resources

## Create First Admin User

```bash
php artisan make:filament-user
```

After creation, assign the `super-admin` role via Tinker:

```bash
php artisan tinker
> User::find(1)->assignRole('super-admin');
```

## Development Server

```bash
# Start all services (server, queue, logs, vite)
composer dev
```

Access the admin panel at: `http://localhost:8000/admin`

## Running Tests

```bash
# Run all tests
composer test

# Run specific test file
php artisan test --filter=OwnerResourceTest

# Run tests with coverage
php artisan test --coverage
```

## Development Workflow (TDD)

Per the constitution's Test-First principle:

1. **Write tests first** — Create Pest test for the feature/resource
2. **Verify tests fail** — Run `composer test` to confirm red state
3. **Implement** — Write the minimum code to make tests pass
4. **Refactor** — Clean up while keeping tests green
5. **Lint** — Run `composer lint` before committing

## Key File Locations

| What | Where |
|------|-------|
| Filament Panel Config | `app/Providers/Filament/AdminPanelProvider.php` |
| Filament Resources | `app/Filament/Resources/` |
| Models | `app/Models/` |
| Enums | `app/Enums/Status.php` |
| Migrations | `database/migrations/` |
| Factories | `database/factories/` |
| Policies | `app/Policies/` |
| Observers | `app/Observers/` |
| Feature Tests | `tests/Feature/Filament/` |
| Unit Tests | `tests/Unit/` |
| EN Translations | `lang/en/` |
| AR Translations | `lang/ar/` |
| Language Switch Config | `config/filament-language-switch.php` |
| Permission Config | `config/permission.php` |

## Useful Artisan Commands

```bash
# Generate a new Filament resource
php artisan make:filament-resource Owner --generate

# Generate a model with migration and factory
php artisan make:model Project -mf

# Generate a policy
php artisan make:policy ProjectPolicy --model=Project

# Generate an observer
php artisan make:observer ProjectObserver --model=Project

# Generate a Pest test
php artisan make:test Filament/ProjectResourceTest --pest

# Clear permission cache after seeder changes
php artisan permission:cache-reset

# Run linter
composer lint
```
