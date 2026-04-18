<?php

namespace App\Providers;

use Illuminate\Support\Facades\Gate;
use Illuminate\Support\ServiceProvider;

class AppServiceProvider extends ServiceProvider
{
    public function register(): void
    {
        //
    }

    public function boot(): void
    {
        Gate::before(function ($user, $ability) {
            if ($user->isAdmin()) {
                return true;
            }
        });

        Gate::define('manage_inventory', fn($user) => $user->hasPermission('manage_inventory'));
        Gate::define('issue_items', fn($user) => $user->hasPermission('issue_items'));
        Gate::define('receive_returns', fn($user) => $user->hasPermission('receive_returns'));
        Gate::define('manage_repairs', fn($user) => $user->hasPermission('manage_repairs'));
        Gate::define('adjust_stock', fn($user) => $user->hasPermission('adjust_stock'));
        Gate::define('manage_departments', fn($user) => $user->hasPermission('manage_departments'));
        Gate::define('manage_categories', fn($user) => $user->hasPermission('manage_categories'));
        Gate::define('manage_users', fn($user) => $user->hasPermission('manage_users'));
        Gate::define('manage_roles_permissions', fn($user) => $user->hasPermission('manage_roles_permissions'));
        Gate::define('view_audit_log', fn($user) => $user->hasPermission('view_audit_log'));
        Gate::define('view_reports', fn($user) => $user->hasPermission('view_reports'));
    }
}
