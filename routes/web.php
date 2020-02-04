<?php

/*
|--------------------------------------------------------------------------
| Application Routes
|--------------------------------------------------------------------------
|
| Here is where you can register all of the routes for an application.
| It is a breeze. Simply tell Lumen the URIs it should respond to
| and give it the Closure to call when that URI is requested.
|
*/

/* @var \Laravel\Lumen\Routing\Router $router */

$router->get('/', function () use ($router) {
    dump(app('router')->getRoutes());

    return $router->app->version();
});

$router->group(
    ['middleware' => ['auth:api'], 'prefix' => 'api/v1'],
    function (\Laravel\Lumen\Routing\Router $router) {
        //users
        $router->post('/user', [
            'uses' => 'API\UserController@create',
            'middleware' => 'permission:users_create'
        ]);
        $router->put('/user/{id}', [
            'uses' => 'API\UserController@update',
            'middleware' => 'permission:users_update'
        ]);
        $router->delete('/user/{id}', [
            'uses' => 'API\UserController@delete',
            'middleware' => 'permission:users_delete'
        ]);
        $router->get('/user/{id}', [
            'uses' => 'API\UserController@get',
            'middleware' => 'permission:users_view'
        ]);
        $router->get('/users', [
            'uses' => 'API\UserController@getAll',
            'middleware' => 'permission:users_view'
        ]);

        //bank
        $router->post('/bank', [
            'uses' => 'API\BankController@create',
            'middleware' => 'permission:banks_create'
        ]);
        $router->put('/bank/{id}', [
            'uses' => 'API\BankController@update',
            'middleware' => 'permission:banks_update'
        ]);
        $router->delete('/bank/{id}', [
            'uses' => 'API\BankController@delete',
            'middleware' => 'permission:banks_delete'
        ]);
        $router->get('/bank/{id}', [
            'uses' => 'API\BankController@get',
            'middleware' => 'permission:banks_view'
        ]);
        $router->get('/banks', [
            'uses' => 'API\BankController@getAll',
            'middleware' => 'permission:banks_view'
        ]);

        //bank-account
        $router->post('/bank-account', [
            'uses' => 'API\BankAccountController@create',
            'middleware' => 'permission:bank_accounts_create'
        ]);
        $router->put('/bank-account/{id}', [
            'uses' => 'API\BankAccountController@update',
            'middleware' => 'permission:bank_accounts_update'
        ]);
        $router->delete('/bank-account/{id}', [
            'uses' => 'API\BankAccountController@delete',
            'middleware' => 'permission:bank_accounts_delete'
        ]);
        $router->get('/bank-account/{id}', [
            'uses' => 'API\BankAccountController@get',
            'middleware' => 'permission:bank_accounts_view'
        ]);
        $router->get('/bank-accounts', [
            'uses' => 'API\BankAccountController@getAll',
            'middleware' => 'permission:bank_accounts_view'
        ]);

        //deposit
        $router->post('/deposit', [
            'uses' => 'API\DepositController@create',
            'middleware' => 'permission:deposits_create'
        ]);
        $router->put('/deposit/{id}', [
            'uses' => 'API\DepositController@update',
            'middleware' => 'permission:deposits_update'
        ]);
        $router->delete('/deposit/{id}', [
            'uses' => 'API\DepositController@delete',
            'middleware' => 'permission:deposits_delete'
        ]);
        $router->get('/deposit/{id}', [
            'uses' => 'API\DepositController@get',
            'middleware' => 'permission:deposits_view'
        ]);
        $router->get('/deposits', [
            'uses' => 'API\DepositController@getAll',
            'middleware' => 'permission:deposits_view'
        ]);
        $router->post('/deposit/import', [
            'uses' => 'API\DepositController@importCsv',
            'middleware' => 'permission:deposits_import'
        ]);
        $router->put('/deposit/update-status/{id}', [
            'uses' => 'API\DepositController@updateStatus',
            'middleware' => 'permission:deposits_update_status'
        ]);

        //withdraw
        $router->post('/withdrawal', [
            'uses' => 'API\WithdrawalController@create',
            'middleware' => 'permission:withdrawals_create'
        ]);
        $router->put('/withdrawal/{id}', [
            'uses' => 'API\WithdrawalController@update',
            'middleware' => 'permission:withdrawals_update'
        ]);
        $router->delete('/withdrawal/{id}', [
            'uses' => 'API\WithdrawalController@delete',
            'middleware' => 'permission:withdrawals_delete'
        ]);
        $router->get('/withdrawal/{id}', [
            'uses' => 'API\WithdrawalController@get',
            'middleware' => 'permission:withdrawals_view'
        ]);
        $router->get('/withdrawals', [
            'uses' => 'API\WithdrawalController@getAll',
            'middleware' => 'permission:withdrawals_view'
        ]);
        $router->post('/withdrawal/import', [
            'uses' => 'API\WithdrawalController@importCsv',
            'middleware' => 'permission:withdrawals_import'
        ]);
        $router->put('/withdrawal/update-status/{id}', [
            'uses' => 'API\WithdrawalController@updateStatus',
            'middleware' => 'permission:withdrawals_update_status'
        ]);

        //exchange rate
        $router->post('/exchange-rate', [
            'uses' => 'API\ExchangeRateController@create',
            'middleware' => 'permission:exchange_rates_create'
        ]);
        $router->put('/exchange-rate/{id}', [
            'uses' => 'API\ExchangeRateController@update',
            'middleware' => 'permission:exchange_rates_update'
        ]);
        $router->delete('/exchange-rate/{id}', [
            'uses' => 'API\ExchangeRateController@delete',
            'middleware' => 'permission:exchange_rates_delete'
        ]);
        $router->get('/exchange-rate/{id}', [
            'uses' => 'API\ExchangeRateController@get',
            'middleware' => 'permission:exchange_rates_view'
        ]);
        $router->get('/exchange-rates', [
            'uses' => 'API\ExchangeRateController@getAll',
            'middleware' => 'permission:exchange_rates_view'
        ]);
        $router->get('/exchange-rates/summarized', [
            'uses' => 'API\ExchangeRateController@getSummarized',
            'middleware' => 'permission:exchange_rates_view'
        ]);

        //exchange rate
        $router->post('/balance', [
            'uses' => 'API\BalanceController@create',
            'middleware' => 'permission:balances_create'
        ]);
        $router->put('/balance/{id}', [
            'uses' => 'API\BalanceController@update',
            'middleware' => 'permission:balances_update'
        ]);
        $router->delete('/balance/{id}', [
            'uses' => 'API\BalanceController@delete',
            'middleware' => 'permission:balances_delete'
        ]);
        $router->get('/balance/{id}', [
            'uses' => 'API\BalanceController@get',
            'middleware' => 'permission:balances_view'
        ]);
        $router->get('/balances', [
            'uses' => 'API\BalanceController@getAll',
            'middleware' => 'permission:balances_view'
        ]);
        $router->get('/balances/merchant/{merchant_id}', [
            'uses' => 'API\BalanceController@getByMerchant',
            'middleware' => 'permission:balances_view'
        ]);


        //merchant
        $router->put('/merchant/{id}', [
            'uses' => 'API\MerchantController@update',
            'middleware' => 'permission:merchants_update'
        ]);
        $router->delete('/merchant/{id}', [
            'uses' => 'API\MerchantController@delete',
            'middleware' => 'permission:merchants_delete'
        ]);
        $router->get('/merchant/{id}', [
            'uses' => 'API\MerchantController@get',
            'middleware' => 'permission:merchants_view'
        ]);
        $router->get('/merchants', [
            'uses' => 'API\MerchantController@getAll',
            'middleware' => 'permission:merchants_view'
        ]);
        $router->post('/merchant/import', [
            'uses' => 'API\MerchantController@importCsv',
            'middleware' => 'permission:merchants_import'
        ]);
        $router->put('/merchant/update-kyc-status/{id}', [
            'uses' => 'API\MerchantController@updateKycStatus',
            'middleware' => 'permission:merchants_update_status'
        ]);

        $router->post('/kyc-document', [
            'uses' => 'API\KycDocumentController@create',
            'middleware' => 'permission:kyc_create'
        ]);

        $router->get('/kyc-document/merchant/{merchant_id}', [
            'uses' => 'API\KycDocumentController@getByMerchantId',
            'middleware' => 'permission:kyc_view'
        ]);

        $router->put('/kyc-document/update-status/{id}', [
            'uses' => 'API\KycDocumentController@updateStatus',
            'middleware' => 'permission:kyc_update_status'
        ]);
    }
);

$router->group(
    ['prefix' => 'api/v1'],
    function (\Laravel\Lumen\Routing\Router $router) {
        $router->post('/login', 'API\UserController@logIn');
        $router->post('/merchant-login', 'API\MerchantController@logIn');
        $router->get('/home', 'API\DashboardController@index');
        $router->post('/merchant/forgot-password', 'API\UserController@forgotPassword');
        $router->post('/merchant', 'API\MerchantController@create');
        $router->get('/merchant/verify/{hash}', 'API\MerchantController@verifyAccount');
        $router->get('/merchant/reset-password/{hash}', 'API\UserController@checkResetPasswordCredentials');
        $router->post('/merchant/reset-password/{hash}', 'API\UserController@resetPassword');
    }
);
