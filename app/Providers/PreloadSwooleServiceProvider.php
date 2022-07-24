<?php

namespace Exchange\Providers;

use Illuminate\Support\ServiceProvider;
use ImanRjb\JwtAuth\Services\AccessToken\AccessToken;

class PreloadSwooleServiceProvider extends ServiceProvider
{
    public function register()
    {
        //All of extra validations (iban, credit_card, national_code, zip_code, ...) in this file
        require(__DIR__ . '/../../helper/validations.php');
    }

    public function boot()
    {
        // Auth guard
        $this->app['auth']->viaRequest('api', function ($request) {
            $token = $request->bearerToken();
            if ($token) {
                try {
                    return AccessToken::checkToken($token);
                } catch (\Exception $exception) {
                    return;
                }
            }
        });
    }
}