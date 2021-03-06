<?php

namespace App\Http\Middleware;

use Closure;
use App\SUtils\SValidation;

class SMPermission
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next, $iPermissionCode)
    {
        if (SValidation::hasPermission($iPermissionCode))
        {
          return $next($request);
        }
        else
        {
          return redirect()->route('notauthorized');
        }
    }
}
