<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class EnsureEmployeeCanManageAgents
{
    /**
     * Handle an incoming request.
     *
     * Ensure the authenticated employee can manage agents.
     * Both Branch Managers and Managers can add and manage agents.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next): Response
    {
        if (!auth()->check()) {
            return redirect()->route('login');
        }

        $employee = auth()->user();

        if (!$employee->canManageAgents()) {
            abort(403, 'Unauthorized. Only Branch Managers and Managers can manage agents.');
        }

        return $next($request);
    }
}
