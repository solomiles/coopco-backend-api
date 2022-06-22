<?php

namespace App\Exceptions;

use Illuminate\Database\QueryException;
use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Illuminate\Http\Exceptions\HttpResponseException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Throwable;

class Handler extends ExceptionHandler
{
    /**
     * A list of exception types with their corresponding custom log levels.
     *
     * @var array<class-string<\Throwable>, \Psr\Log\LogLevel::*>
     */
    protected $levels = [
        //
    ];

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<\Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed to the session on validation exceptions.
     *
     * @var array<int, string>
     */
    protected $dontFlash = [
        'current_password',
        'password',
        'password_confirmation',
    ];

    /**
     * Register the exception handling callbacks for the application.
     *
     * @return void
     */
    public function register()
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render the exception into an HTTP response.
     *
     * @param  \Illuminate\Http\Request $request
     * @return \Illuminate\Http\Response
     */
    public function render($request, Throwable $e)
    {

        // Handle Query/DB Related exceptions
        if($e instanceof QueryException) {
            return response([
                'status' => false,
                'message' => g('SERVER_ERROR')
            ], 500);
        }
        
        // Handle 401 HttpResponseException exceptions
        if($e instanceof HttpResponseException && $e->getResponse()->getStatusCode() == 401) {
            return response([
                'success' => false, 
                'message' => g('UNAUTHORIZED')
            ],  401);
        }

        // Handle 404 NotFoundHttpException exceptions
        if($e instanceof NotFoundHttpException) {
            return response([
                'success' => false, 
                'message' => g('NOT_FOUND')
            ],  404);
        }
    }
}
