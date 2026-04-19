<?php

namespace App\Http\Middleware;

use App\Support\Audit\AuditLogger;
use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Symfony\Component\HttpFoundation\Response;

class RequirePermission
{
    public function handle(Request $request, Closure $next, string ...$permissions): Response
    {
        $user = $request->user();

        if (!$user) {
            abort(403);
        }

        // Admin bypasses permission checks
        if ($user->isAdmin()) {
            Log::info('Admin permission bypass', [
                'user_id' => $user->id,
                'permissions_required' => $permissions,
                'path' => $request->path(),
                'ip' => $request->ip(),
            ]);
            return $next($request);
        }

        foreach ($permissions as $permission) {
            if ($user->hasPermission($permission)) {
                return $next($request);
            }
        }

        // Log permission denial for security monitoring
        Log::warning('Permission denied', [
            'user_id' => $user->id,
            'permissions_required' => $permissions,
            'path' => $request->path(),
            'ip' => $request->ip(),
        ]);

        abort(403, 'You do not have permission to access this resource.');
    }
}
