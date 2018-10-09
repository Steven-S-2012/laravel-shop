<?php

use Illuminate\Routing\Router;

Admin::registerAuthRoutes();

Route::group([
    'prefix'        => config('admin.route.prefix'),
    'namespace'     => config('admin.route.namespace'),
    'middleware'    => config('admin.route.middleware'),
], function (Router $router) {

    $router->get('/', 'HomeController@index');
    $router->resource('users', 'UsersController');
    $router->resource('products', 'ProductsController');
    $router->get('orders', 'OrdersController@index')->name('admin.orders.index');
    $router->get('orders/{order}', 'OrdersController@show')->name('admin.orders.show');
    //$router->get('users', 'UsersController@index');
    //$router->delete('users/{id}', 'UsersController@destroy');
    //$router->get('users/{id}/edit', 'UsersController@edit');

});
