<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmployeeIsManager
{
    /**
     * Handle an incoming request.
     *
     * Ensure the authenticated employee is a Manager.
     * Managers can create branches, add branch managers, and add agents.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $employee = auth()->user();

        if (!$employee->isManager()) {
            abort(403, 'Unauthorized. Only Managers can access this resource.');
        }

        return $next($request);
    }
}
