<?php

/** @var \Laravel\Lumen\Routing\Router $router */

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

$router->get('/', function () use ($router) {
    return $router->app->version();
});


$router->get('/users', ['uses' => 'UserController@getUsers']);

$router->post('/users', ['uses' => 'UserController@add']);

$router->get('/users/{id}', ['uses' => 'UserController@show']);

$router->put('/users/{id}', ['uses' => 'UserController@update']);

$router->patch('/users/{id}', ['uses' => 'UserController@update']);

$router->delete('/users/{id}', ['uses' => 'UserController@delete']);

// user job routes
$router->get('/usersjob', 'UserJobController@index'); 
$router->get('/userjob/{id}', 'UserJobController@show'); // get user by id