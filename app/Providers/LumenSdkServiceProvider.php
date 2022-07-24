<?php

namespace Exchange\Providers;

use Illuminate\Support\ServiceProvider;
use App\Models\Notification;
use Exchange\Observers\NotificationObserver;
use ImanRjb\JwtAuth\Services\AccessToken\AccessToken;

class LumenSdkServiceProvider extends ServiceProvider
{
    public function register()
    {
        //Proxies
        \Exchange\Facades\BaseRepositoryFacade::shouldProxyTo(\Exchange\Repositories\BaseRepository::class);
        \Exchange\Facades\MongoBaseRepositoryFacade::shouldProxyTo(\Exchange\Repositories\MongoBaseRepository::class);
        \Exchange\Facades\NotificationRepositoryFacade::shouldProxyTo(\Exchange\Repositories\NotificationRepository::class);

        // Register depends packages service providers just for lumen
        if (! $this->app instanceof \Illuminate\Foundation\Application) {
            $this->app->register(\Flipbox\LumenGenerator\LumenGeneratorServiceProvider::class);
            $this->app->register(\ImanRjb\JwtAuth\JwtAuthServiceProvider::class);
            $this->app->register(\Anik\Form\FormRequestServiceProvider::class);
            $this->app->register(\Fruitcake\Cors\CorsServiceProvider::class);
            $this->app->register(\Illuminate\Redis\RedisServiceProvider::class);
            $this->app->register(\SwooleTW\Http\LumenServiceProvider::class);
            $this->app->register(\Laravel\Horizon\HorizonServiceProvider::class);
            $this->app->register(\ExchangeModel\Providers\ExchangeModelServiceProvider::class);
        }

        // Register Commands
        if ($this->app->runningInConsole()) {
            $this->commands(['\Exchange\Console\Commands\MakeRepository']);
        }

        //Services
        $this->app->alias(\Exchange\Repositories\BaseRepository::class, 'BaseRepository');

        // Configures
        $this->publishes([
            __DIR__.'/../../config/database.php' => lumen_config_path('database.php'),
            __DIR__.'/../../config/cors.php' => lumen_config_path('cors.php'),
            __DIR__.'/../../config/horizon.php' => lumen_config_path('horizon.php'),
            __DIR__.'/../../config/fee.php' => lumen_config_path('fee.php'),
            __DIR__.'/../../config/exchange-model.php' => lumen_config_path('exchange-model.php'),
        ], 'lumen-sdk');

        // For load config files
        if (file_exists($this->app->basePath() . '/config/database.php')) {
            $this->mergeConfigFrom($this->app->basePath() . '/config/database.php', 'database');
        }
        if (file_exists($this->app->basePath() . '/config/cors.php')) {
            $this->mergeConfigFrom($this->app->basePath() . '/config/cors.php', 'cors');
        }
        if (file_exists($this->app->basePath() . '/config/exchange-model.php')) {
            $this->mergeConfigFrom($this->app->basePath() . '/config/exchange-model.php', 'model');
        }
        if (file_exists($this->app->basePath() . '/config/horizon.php')) {
            $this->mergeConfigFrom($this->app->basePath() . '/config/horizon.php', 'horizon');
        }
        if (file_exists(__DIR__ . '/../../config/fee.php')) {
            $this->mergeConfigFrom(__DIR__ . '/../../config/fee.php', 'fee');
        }
        if (file_exists(__DIR__ . '/../../config/swoole_http.php')) {
            $this->mergeConfigFrom(__DIR__ . '/../../config/swoole_http.php', 'swoole_http');
        }

        // Register Middlewares
        if ($this->app instanceof \Illuminate\Foundation\Application) {
            $router = $this->app['router'];
            $router->pushMiddlewareToGroup('api', \Exchange\Http\Middleware\PersianNumber::class);
            $router->pushMiddlewareToGroup('web', \Exchange\Http\Middleware\PersianNumber::class);
        } else {
            $this->app->middleware(\Exchange\Http\Middleware\PersianNumber::class);
            $this->app->middleware(\Exchange\Http\Middleware\MeasureExecutionTime::class);
            $this->app->middleware(\Fruitcake\Cors\HandleCors::class);
        }

        // Change app lang to Fa
        $this->app->singleton('translator', function () {
            $this->app->instance('path.lang', __DIR__ . '/../../lang');
            $this->app->register(\Illuminate\Translation\TranslationServiceProvider::class);
            return $this->app->make('translator');
        });
        $this->app->setLocale('fa');
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

        //Observers
        if (file_exists(base_path('app/Models/') . 'Notification.php')) {
            Notification::observe(NotificationObserver::class);
        }

        // Files, e.g validations and ..
        require(__DIR__ . '/../../helper/validations.php');
    }
}
