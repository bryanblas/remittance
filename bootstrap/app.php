<?php

require_once __DIR__ . '/../vendor/autoload.php';

(new Laravel\Lumen\Bootstrap\LoadEnvironmentVariables(
    dirname(__DIR__)
))->bootstrap();

/*
|--------------------------------------------------------------------------
| Create The Application
|--------------------------------------------------------------------------
|
| Here we will load the environment and create the application instance
| that serves as the central piece of this framework. We'll use this
| application as an "IoC" container and router for this framework.
|
*/

$app = new Laravel\Lumen\Application(
    dirname(__DIR__)
);

$app->withFacades();
$app->withEloquent();

$app->withFacades(true, [
    'Illuminate\Support\Facades\Notification' => 'Notification',
]);

$app->configure('auth');
$app->configure('permission');
$app->configure('cors');
$app->configure('activitylog');

$app->alias('cache', \Illuminate\Cache\CacheManager::class);
$app->alias('mailer', \Illuminate\Contracts\Mail\Mailer::class);

/*
|--------------------------------------------------------------------------
| Register Container Bindings
|--------------------------------------------------------------------------
|
| Now we will register a few bindings in the service container. We will
| register the exception handler and the console kernel. You may add
| your own bindings here if you like or you can make another file.
|
*/

$app->singleton(
    Illuminate\Contracts\Debug\ExceptionHandler::class,
    App\Exceptions\Handler::class
);

$app->singleton(
    Illuminate\Contracts\Console\Kernel::class,
    App\Console\Kernel::class
);

$app->singleton(
    App\Repositories\User\UserInterface::class,
    App\Repositories\User\UserRepository::class
);

$app->singleton(
    App\Repositories\Permission\PermissionInterface::class,
    App\Repositories\Permission\PermissionRepository::class
);

$app->singleton(
    App\Repositories\Role\RoleInterface::class,
    App\Repositories\Role\RoleRepository::class
);

$app->singleton(
    App\Repositories\ActivityLog\ActivityLogInterface::class,
    App\Repositories\ActivityLog\ActivityLogRepository::class
);

$app->singleton(
    App\Repositories\ModelHasRole\ModelHasRoleInterface::class,
    App\Repositories\ModelHasRole\ModelHasRoleRepository::class
);

$app->singleton(
    App\Repositories\Google\GoogleInterface::class,
    App\Repositories\Google\GoogleRepository::class
);

$app->singleton(
    App\Repositories\Passport\PassportInterface::class,
    App\Repositories\Passport\PassportRepository::class
);

$app->singleton(
    App\Repositories\PasswordReset\PasswordResetInterface::class,
    App\Repositories\PasswordReset\PasswordResetRepository::class
);

$app->singleton(
    App\Repositories\Bank\BankInterface::class,
    App\Repositories\Bank\BankRepository::class
);

$app->singleton(
    App\Repositories\BankAccount\BankAccountInterface::class,
    App\Repositories\BankAccount\BankAccountRepository::class
);

$app->singleton(
    App\Repositories\Merchant\MerchantInterface::class,
    App\Repositories\Merchant\MerchantRepository::class
);

$app->singleton(
    App\Repositories\Deposit\DepositInterface::class,
    App\Repositories\Deposit\DepositRepository::class
);

$app->singleton(
    App\Repositories\Withdrawal\WithdrawalInterface::class,
    App\Repositories\Withdrawal\WithdrawalRepository::class
);

$app->singleton(
    App\Repositories\KycDocument\KycDocumentInterface::class,
    App\Repositories\KycDocument\KycDocumentRepository::class
);

$app->singleton(
    App\Repositories\VerifyUser\VerifyUserInterface::class,
    App\Repositories\VerifyUser\VerifyUserRepository::class
);

$app->singleton(
    App\Repositories\ExchangeRate\ExchangeRateInterface::class,
    App\Repositories\ExchangeRate\ExchangeRateRepository::class
);

$app->singleton(
    App\Repositories\Balance\BalanceInterface::class,
    App\Repositories\Balance\BalanceRepository::class
);


/*
|--------------------------------------------------------------------------
| Register Middleware
|--------------------------------------------------------------------------
|
| Next, we will register the middleware with the application. These can
| be global middleware that run before and after each request into a
| route or middleware that'll be assigned to some specific routes.
|
*/

// $app->middleware([
//     App\Http\Middleware\ExampleMiddleware::class
// ]);

$app->middleware([
    \Barryvdh\Cors\HandleCors::class
]);

$app->routeMiddleware([
    'auth' => App\Http\Middleware\Authenticate::class,
    'permission' => Spatie\Permission\Middlewares\PermissionMiddleware::class,
    'role' => Spatie\Permission\Middlewares\RoleMiddleware::class,
    'client' => \Laravel\Passport\Http\Middleware\CheckClientCredentials::class,
    'cors' => \Barryvdh\Cors\HandleCors::class
]);

/*
|--------------------------------------------------------------------------
| Register Service Providers
|--------------------------------------------------------------------------
|
| Here we will register all of the application's service providers which
| are used to bind services into the container. Service providers are
| totally optional, so you are not required to uncomment this line.
|
*/

$app->register(App\Providers\AppServiceProvider::class);
$app->register(App\Providers\AuthServiceProvider::class);
$app->register(App\Providers\EventServiceProvider::class);
$app->register(App\Providers\Passport\LumenPassportServiceProvider::class);
$app->register(Spatie\Permission\PermissionServiceProvider::class);
$app->register(Spatie\Activitylog\ActivitylogServiceProvider::class);
$app->register(Barryvdh\Cors\ServiceProvider::class);
$app->register(Illuminate\Notifications\NotificationServiceProvider::class);

$app->singleton(Illuminate\Auth\AuthManager::class, function ($app) {
    return $app->make('auth');
});

/*
|--------------------------------------------------------------------------
| Load The Application Routes
|--------------------------------------------------------------------------
|
| Next we will include the routes file so that they can all be added to
| the application. This will provide all of the URLs the application
| can respond to, as well as the controllers that may handle them.
|
*/

if (! function_exists('config_path')) {
    function config_path($path = '')
    {
        return app()->basePath() . '/config' . ($path ? '/' . $path : $path);
    }
}

$app->router->group([
    'namespace' => 'App\Http\Controllers',
], function ($router) {
    require __DIR__ . '/../routes/web.php';
});

return $app;
