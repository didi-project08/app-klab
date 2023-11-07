<?php

namespace App\Http\Middleware;

use Closure;

class MW_CheckLogin
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next){
        if(Request()->session()->get('sessLogin')){
            // return true;
        }else{
            return redirect('/');
        }
        return $next($request);
    }
}