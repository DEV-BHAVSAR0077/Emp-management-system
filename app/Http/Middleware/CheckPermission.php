<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Symfony\Component\HttpFoundation\Response;

class CheckPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     * @param  string  $permission
     * @param  string|null  $modelKey
     */
    public function handle(Request $request, Closure $next, string $permission, ?string $modelKey = null): Response
    {
        $user = Auth::user();

        // 1. Ensure user is authenticated
        if (!$user) {
            abort(403, 'Unauthorized access.');
        }

        // 2. Handle dynamic ownership checks if a modelKey is provided
        if ($modelKey && $request->route($modelKey)) {
            $model = $request->route($modelKey);

            // Handle string ID resolution for soft-deleted models (like in Expense restore)
            if (is_string($model) || is_numeric($model)) {
                if ($modelKey === 'expense') {
                    $model = \App\Models\Expense::withTrashed()->find($model);
                }
            }

            if ($model) {
                // If the model is a User, check if the authenticated user is editing themselves
                if ($model instanceof \App\Models\User && $model->id === $user->id) {
                    return $next($request);
                }

                // If the model has a user_id (like Expense), check ownership
                if (isset($model->user_id) && $model->user_id === $user->id) {
                    return $next($request);
                }
            }
        }

        // 3. Fallback to role-based permission check
        if ($user->hasPermission($permission)) {
            return $next($request);
        }

        // 4. Deny access if neither ownership nor permission is satisfied
        abort(403, 'Unauthorized access: You do not have the required permissions.');
    }
}
