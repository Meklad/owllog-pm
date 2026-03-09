<?php

use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Spatie\Permission\Models\Role;
use Spatie\Permission\Models\Permission;
use function Pest\Laravel\actingAs;
use function Pest\Laravel\assertSoftDeleted;
use function Pest\Laravel\assertNotSoftDeleted;

uses(RefreshDatabase::class);

test('user model uses soft deletes', function () {
    $user = User::factory()->create([
        'name' => 'Test User',
        'email' => 'test@example.com',
    ]);

    expect($user->id)->not->toBeNull();

    $user->delete();

    // Use Pest's soft delete assertions
    assertSoftDeleted('users', ['id' => $user->id]);

    // User should still exist in database but with deleted_at
    $this->assertDatabaseHas('users', [
        'id' => $user->id,
        'deleted_at' => $user->deleted_at,
    ]);
});

test('user model uses spatie has roles trait', function () {
    $user = User::factory()->create();

    // Create a role
    $role = Role::create(['name' => 'test-role']);

    // Assign role to user
    $user->assignRole($role);

    expect($user->hasRole('test-role'))->toBeTrue();
    expect($user->roles)->toHaveCount(1);
});

test('user model can have multiple roles', function () {
    $user = User::factory()->create();

    $adminRole = Role::create(['name' => 'admin']);
    $memberRole = Role::create(['name' => 'member']);

    $user->assignRole([$adminRole, $memberRole]);

    expect($user->hasAllRoles(['admin', 'member']))->toBeTrue();
    expect($user->roles)->toHaveCount(2);
});

test('user model can have permissions directly', function () {
    $user = User::factory()->create();

    $permission = Permission::create(['name' => 'edit-posts']);

    $user->givePermissionTo($permission);

    expect($user->hasPermissionTo('edit-posts'))->toBeTrue();
    expect($user->getAllPermissions())->toHaveCount(1);
});

test('user model permissions include role permissions', function () {
    $user = User::factory()->create();

    $role = Role::create(['name' => 'editor']);
    $permission = Permission::create(['name' => 'edit-posts']);

    $role->givePermissionTo($permission);
    $user->assignRole($role);

    expect($user->hasPermissionTo('edit-posts'))->toBeTrue();
});

test('soft deleted users can be restored', function () {
    $user = User::factory()->create();

    $user->delete();
    assertSoftDeleted('users', ['id' => $user->id]);

    $user->restore();

    assertNotSoftDeleted('users', ['id' => $user->id]);
});
