<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Symfony\Component\HttpFoundation\Response;

class CheckSuperAdmin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Closure(\Illuminate\Http\Request): (\Symfony\Component\HttpFoundation\Response)  $next
     */
    public function handle(Request $request, Closure $next ,string $role =""): Response
    {
        $user = $request->user('sanctum');
        if(!$user || !$user->hasRole($role)){
            return response()->json([
                'status'=>false ,
                'message' => 'Unauthorized. Only  '.$role.' can access this endpoint.'
            ],401);
        }
        return $next($request);
    }
}
