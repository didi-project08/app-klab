<?php

namespace App\Http\Middleware;

use Closure;

class MW_ModuleAccessByUser
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next){
        $userId = Request()->session()->get('sessUserId');
        if($userId == 92 || $userId == 93){
            // return true;
        }else{
            // return redirect('/');
            return redirect('/view403/?message=Access not allowed.&redirect=');
        }
        return $next($request);
    }
}