<?php
// index.php

require __DIR__ . '/../vendor/autoload.php';

// Load environment variables
$dotenv = Dotenv\Dotenv::createImmutable(__DIR__ . '/../');
$dotenv->load();

$app = new \Slim\App($settings = [
'settings' => [
'addContentLengthHeader' => false, // or true, depending on your needs
'outputBuffering' => 'append', // or 'prepend'
        'displayErrorDetails' => true,
]
]);

require __DIR__ . '/../src/dependencies.php';
// require __DIR__ . '/../src/routes.php';

// Include ApiLogic.php
require __DIR__ . '/../src/ApiLogic.php';

// Initialize ApiLogic with database connection and JWT secret
$apiLogic = new ApiLogic($container['db'], $container['jwt_secret']);

// Define routes using ApiLogic methods
$app->post('/api/login', [$apiLogic, 'login']);
$app->post('/api/validateToken', [$apiLogic, 'validateToken']);

$app->run();