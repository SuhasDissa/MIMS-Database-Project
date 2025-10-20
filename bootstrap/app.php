<?php

use Illuminate\Foundation\Application;
use Illuminate\Foundation\Configuration\Exceptions;
use Illuminate\Foundation\Configuration\Middleware;

return Application::configure(basePath: dirname(__DIR__))
    ->withRouting(
        web: __DIR__.'/../routes/web.php',
        commands: __DIR__.'/../routes/console.php',
        health: '/up',
    )
    ->withMiddleware(function (Middleware $middleware) {
        $middleware->alias([
            'employee.manager' => \App\Http\Middleware\EnsureEmployeeIsManager::class,
            'employee.branch_manager' => \App\Http\Middleware\EnsureEmployeeIsBranchManager::class,
            'employee.can_manage_agents' => \App\Http\Middleware\EnsureEmployeeCanManageAgents::class,
        ]);
    })
    ->withExceptions(function (Exceptions $exceptions) {
        //
    })->create();
