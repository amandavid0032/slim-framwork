<?php

$container = $app->getContainer();

$container['CustomMiddleware'] = function ($container) {
    return new \App\Middleware\CustomMiddleware();
};

$container['HomeController'] = function ($container) {
    return new \App\Controllers\HomeController();
};


$container = $app->getContainer();

$container['jwt_secret'] = '';
