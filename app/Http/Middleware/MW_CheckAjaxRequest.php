<?php

namespace App\Http\Middleware;
use Illuminate\Support\Facades\DB;

use Closure;

class MW_CheckAjaxRequest
{
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next){
        if(Request()->ajax()){
          return $next($request);
        }else{
          return redirect('./view403/?message=Access not allowed.&redirect=');
        }
    }
}
