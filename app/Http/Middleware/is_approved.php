<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Auth;

class is_approved
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure(\Illuminate\Http\Request): (\Illuminate\Http\Response|\Illuminate\Http\RedirectResponse)  $next
     * @return \Illuminate\Http\Response|\Illuminate\Http\RedirectResponse
     */
    public function handle(Request $request, Closure $next)
    {
        if(auth::guard('merchant')->user()->status == 1)
        {
            return $next($request);
        }
        else
        {
            Auth::guard('merchant')->logout();
            return redirect()->route('merchant_login')->with('errormessage',"Your account is yet not approved");
        }
        
    }
}
