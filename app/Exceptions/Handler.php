<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Throwable;
use Illuminate\Http\Response;

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
     */
    public function register(): void
    {
        $this->reportable(function (Throwable $e) {
            //
        });
    }

    /**
     * Render an exception into an HTTP response.
     */
    public function render($request, Throwable $exception)
    {
        // If it's a ViewException related to syntax highlighting, return a simple error
        if ($exception instanceof \Illuminate\View\ViewException && 
            str_contains($exception->getMessage(), 'syntax-highlight')) {
            
            if (config('app.debug')) {
                return response()->view('errors.custom', [
                    'message' => 'A view rendering error occurred. Please check the logs for details.',
                    'exception' => $exception
                ], 500);
            }
            
            return response()->view('errors.500', [], 500);
        }

        return parent::render($request, $exception);
    }
}
