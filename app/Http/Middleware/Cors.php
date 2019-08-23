<?php

namespace App\Http\Middleware;

use Asm89\Stack\CorsService;
use Closure;

class Cors
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
        $response=$next($request);
        return $response
            ->header('Access-Control-Allow-Origin', 'localhost')//put your  company domain
            ->header('Access-Control-Allow-Methods', '*')
            ->header('Access-Control-Allow-Headers', '*');
        //  ->header('Access-Control-Allow-Headers', 'Content-Language,Content-Type, Authorization, X-XSRF-TOKEN, X-Requested-With');
    }

}
