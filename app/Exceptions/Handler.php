<?php

namespace App\Exceptions;

use Exception;
use Illuminate\Auth\AuthenticationException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Session\TokenMismatchException;
use Illuminate\Auth\Access\AuthorizationException;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\HttpKernel\Exception\MethodNotAllowedHttpException;

class Handler extends ExceptionHandler
{
    /**
     * A list of the exception types that are not reported.
     *
     * @var array
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
     *
     * @var array
     */
    protected $dontFlash = [
        'password',
        'password_confirmation',
    ];

    /**
     * Report or log an exception.
     *
     * @param  \Exception  $exception
     * @return void
     */
    public function report(Exception $exception)
    {
        parent::report($exception);
    }

    /**
     * Render an exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request  $request
     * @param  \Exception  $exception
     * @return \Illuminate\Http\Response
     */
    public function render($request, Exception $exception)
    {
        $response= $this->handleException($request, $exception);
        return $response;
    }

    public function handleException($request, Exception $exception)
    {
        if($exception instanceof AuthenticationException)
        {
            return $this->unauthenticated($request, $exception);
        }
        if($exception instanceof AuthorizationException)
        {
            response()->json(["error"=> $exception->getMessage(),"code"=> 403], 403);
        }
        if($exception instanceof MethodNotAllowedHttpException)
        {
            return response()->json(["error"=> "The specified method for the request is invalid","code"=> 405], 405);
        }
        if($exception instanceof NotFoundHttpException)
        {
            return response()->json(["error"=>"The specified URL cannot be found","code"=> 404], 404);
        }
        if($exception instanceof HttpException)
        {
            return response()->json(["error"=> $exception->getMessage(),"code"=> $exception->getStatusCode()],$exception->getStatusCode());
        }
        if($exception instanceof TokenMismatchException)
        {
            return response()->json(["error"=> $exception->getMessage(),"code"=> $exception->getStatusCode()],$exception->getStatusCode());
        }
         return response()->json(["error"=>"Unexpected Exception. Try later","code"=> 500], 500);

    }

    protected function unauthenticated($request, AuthenticationException $exception)
    {
        if($request->acceptsHtml() && collect($request->route()->middleware())->contains('web'))
        {
            return redirect()->guest('login');
        }
        return response()->json(["error"=> "Unauthenticated","code"=> 401], 401);
    }
}
