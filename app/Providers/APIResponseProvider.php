<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use Illuminate\Support\Facades\Response;

class APIResponseProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        Response::macro('success', function ($message = '', $data = NULL) {
            return Response::json([
                'success' => true,
                'message' => $message,
                'data' => $data,
            ]);
        });

        Response::macro('error', function ($message = '', $errors = [], $status) {
            return Response::json([
                'success' => false,
                'message' => $message,
                'errors' => $errors,
            ], $status);
        });
    }
}
