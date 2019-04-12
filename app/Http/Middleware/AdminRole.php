<?php

namespace App\Http\Middleware;

use App\User;
use Closure;

class AdminRole
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request $request
     * @param  \Closure $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $userRole = \Auth::user()->role_id;
        if ($userRole == User::ROLE_ADMIN) {
            return $next($request);

        }
        return redirect('/task/new');

    }
}
