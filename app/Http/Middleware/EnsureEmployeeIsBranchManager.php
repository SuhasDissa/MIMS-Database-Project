<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmployeeIsBranchManager
{
    /**
     * Handle an incoming request.
     *
     * Ensure the authenticated employee is at least a Branch Manager.
     * Branch Managers (and Managers) can view branch stats, generate reports, and view agent activity.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $employee = auth()->user();

        if (!$employee->canViewBranchStats()) {
            abort(403, 'Unauthorized. Only Branch Managers and Managers can access this resource.');
        }

        return $next($request);
    }
}
