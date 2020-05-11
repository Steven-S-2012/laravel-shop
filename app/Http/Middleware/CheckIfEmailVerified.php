<?php

namespace App\Http\Middleware;

use Closure;

class CheckIfEmailVerified
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        if (!$request->user()->email_verified) {
            // if AJAX requirement, response JSON style
            if($request->expectsJson()) {
                return response()->json(['msg'=>'Please verify your email box'], 100);
            }
            return redirect(route('email_verify_notice'));
        }
        return $next($request);
    }
}
