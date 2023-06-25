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
                'status' => true,
                'message' => $message,
                'data' => $data,
            ]);
        });

        Response::macro('fail', function ($message = '', $errors = []) {
            return Response::json([
                'status' => false,
                'message' => $message,
                'errors' => $errors,
            ]);
        });
    }
}
