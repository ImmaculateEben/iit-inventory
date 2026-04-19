<?php

use App\Http\Controllers\AuthController;
use App\Livewire\Dashboard\Index as DashboardIndex;
use App\Livewire\Inventory\Index as InventoryIndex;
use App\Livewire\Inventory\Create as InventoryCreate;
use App\Livewire\Inventory\Show as InventoryShow;
use App\Livewire\Inventory\Edit as InventoryEdit;

use App\Livewire\Issues\Create as IssuesCreate;
use App\Livewire\Issues\Index as IssuesIndex;
use App\Livewire\Returns\Create as ReturnsCreate;
use App\Livewire\Returns\Index as ReturnsIndex;
use App\Livewire\Repairs\Index as RepairsIndex;
use App\Livewire\Repairs\Create as RepairsCreate;
use App\Livewire\Repairs\Show as RepairsShow;
use App\Livewire\Repairs\Edit as RepairsEdit;
use App\Livewire\Adjustments\Index as AdjustmentsIndex;
use App\Livewire\Adjustments\Create as AdjustmentsCreate;
use App\Livewire\Departments\Index as DepartmentsIndex;
use App\Livewire\Departments\Create as DepartmentsCreate;
use App\Livewire\Departments\Edit as DepartmentsEdit;
use App\Livewire\Categories\Index as CategoriesIndex;
use App\Livewire\Categories\Create as CategoriesCreate;
use App\Livewire\Categories\Edit as CategoriesEdit;
use App\Livewire\Users\Index as UsersIndex;
use App\Livewire\Users\Create as UsersCreate;
use App\Livewire\Users\Edit as UsersEdit;
use App\Livewire\Roles\Index as RolesIndex;
use App\Livewire\Roles\Create as RolesCreate;
use App\Livewire\Roles\Edit as RolesEdit;
use App\Livewire\AuditLog\Index as AuditLogIndex;
use Illuminate\Support\Facades\Route;

// Guest routes
Route::middleware('guest')->group(function () {
    Route::get('/login', [AuthController::class, 'showLogin'])->name('login');
    Route::post('/login', [AuthController::class, 'login']);
});

// Authenticated routes
Route::middleware(['auth', 'active'])->group(function () {
    Route::post('/logout', [AuthController::class, 'logout'])->name('logout');
    Route::get('/', DashboardIndex::class)->name('dashboard')->middleware('permission:view_dashboard');

    // Inventory
    Route::get('/inventory', InventoryIndex::class)->name('inventory.index');
    Route::get('/inventory/create', InventoryCreate::class)->name('inventory.create')->middleware('permission:manage_inventory');
    Route::get('/inventory/{inventoryItem}', InventoryShow::class)->name('inventory.show');
    Route::get('/inventory/{inventoryItem}/edit', InventoryEdit::class)->name('inventory.edit')->middleware('permission:manage_inventory');

    // Issues
    Route::get('/issues', IssuesIndex::class)->name('issues.index')->middleware('permission:issue_items');
    Route::get('/issues/create', IssuesCreate::class)->name('issues.create')->middleware('permission:issue_items');

    // Returns
    Route::get('/returns', ReturnsIndex::class)->name('returns.index')->middleware('permission:receive_returns');
    Route::get('/returns/create', ReturnsCreate::class)->name('returns.create')->middleware('permission:receive_returns');

    // Repairs
    Route::get('/repairs', RepairsIndex::class)->name('repairs.index')->middleware('permission:manage_repairs');
    Route::get('/repairs/create', RepairsCreate::class)->name('repairs.create')->middleware('permission:manage_repairs');
    Route::get('/repairs/{repairRecord}', RepairsShow::class)->name('repairs.show')->middleware('permission:manage_repairs');
    Route::get('/repairs/{repairRecord}/edit', RepairsEdit::class)->name('repairs.edit')->middleware('permission:manage_repairs');

    // Stock Adjustments
    Route::get('/adjustments', AdjustmentsIndex::class)->name('adjustments.index')->middleware('permission:adjust_stock');
    Route::get('/adjustments/create', AdjustmentsCreate::class)->name('adjustments.create')->middleware('permission:adjust_stock');

    // Departments
    Route::get('/departments', DepartmentsIndex::class)->name('departments.index')->middleware('permission:manage_departments');
    Route::get('/departments/create', DepartmentsCreate::class)->name('departments.create')->middleware('permission:manage_departments');
    Route::get('/departments/{department}/edit', DepartmentsEdit::class)->name('departments.edit')->middleware('permission:manage_departments');

    // Categories
    Route::get('/categories', CategoriesIndex::class)->name('categories.index')->middleware('permission:manage_categories');
    Route::get('/categories/create', CategoriesCreate::class)->name('categories.create')->middleware('permission:manage_categories');
    Route::get('/categories/{category}/edit', CategoriesEdit::class)->name('categories.edit')->middleware('permission:manage_categories');

    // Users
    Route::get('/users', UsersIndex::class)->name('users.index')->middleware('permission:manage_users');
    Route::get('/users/create', UsersCreate::class)->name('users.create')->middleware('permission:manage_users');
    Route::get('/users/{user}/edit', UsersEdit::class)->name('users.edit')->middleware('permission:manage_users');

    // Roles
    Route::get('/roles', RolesIndex::class)->name('roles.index')->middleware('permission:manage_roles_permissions');
    Route::get('/roles/create', RolesCreate::class)->name('roles.create')->middleware('permission:manage_roles_permissions');
    Route::get('/roles/{role}/edit', RolesEdit::class)->name('roles.edit')->middleware('permission:manage_roles_permissions');

    // Audit Log
    Route::get('/audit-log', AuditLogIndex::class)->name('audit-log.index')->middleware('permission:view_audit_log');
});
