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
    //$router->resource('auth/users', 'UserController');
    //$router->get('users', 'UsersController@index');
    //$router->delete('users/{id}', 'UsersController@destroy');
    //$router->get('users/{id}/edit', 'UsersController@edit');

});
