<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;
use App\Enums\UserRole;
use App\Models\Role;
use Illuminate\Support\Facades\Auth;

class IsDelivery
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        $role = Role::find(Auth::user()->role_id);
        if (!($role->name == UserRole::DELIVERY->value)) {
            abort(403, "You don't have delivery access.");
        }
        return $next($request);
    }
}
