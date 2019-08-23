<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Foundation\Application;

class Localization
{
    public function __construct(Application $app)
    {
        $this->app= $app;
    }
    /**
     * Handle an incoming request.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Closure  $next
     * @return mixed
     */
    public function handle($request, Closure $next)
    {
        $locale= $request->header('Accept-Language');
        if(!$locale)
        {
            $locale= $this->app->config->get('app.locale');
        }
        if(!array_key_exists($locale,$this->app->config->get('app.supported_languages')))
        {
            return response()->json(["message"=> "Language not supported."],401);
        }
        $this->app->setLocale($locale);
        $response = $next($request);
        $response->headers->set('Accept-Language', $locale);
        return $response;
    }
}
