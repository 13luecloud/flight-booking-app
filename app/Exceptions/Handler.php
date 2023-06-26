<?php

namespace App\Exceptions;

use Illuminate\Foundation\Exceptions\Handler as ExceptionHandler;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Illuminate\Validation\ValidationException;

use Throwable;

class Handler extends ExceptionHandler
{
    protected function invalidJson($request, ValidationException $exception)
    {
        return response()->json([
            'success' => false,
            'message' => $exception->getMessage(),
            'errors' => $exception->errors(),
        ], $exception->status);
    }

    /**
     * A list of the exception types that are not reported.
     *
     * @var array<int, class-string<Throwable>>
     */
    protected $dontReport = [
        //
    ];

    /**
     * A list of the inputs that are never flashed for validation exceptions.
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
        $this->renderable(function (Throwable $e) {
            if ($e instanceof NotFoundHttpException) {
                return response()->error(
                    'Object not found', 
                    [
                        'city' => 'Not found'
                    ],
                    404
                );
            }
        });
    }
}
