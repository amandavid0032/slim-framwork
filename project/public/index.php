<?php

require __DIR__ . '/../../vendor/autoload.php';

$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true,
    ]
]);

$app->get('/', function ($request, $response, $args) {
    $indexHtml = file_get_contents(__DIR__ . '/view/index.html');
    return $response->write($indexHtml);
});

$app->get('/add', function ($request, $response, $args) {
    $addHtml = file_get_contents(__DIR__ . '/view/add.html');
    return $response->write($addHtml);
});


require __DIR__ . '/../../src/routes/api.php';
require __DIR__ . '/../../src/routes/post.php';
require __DIR__ . '/../../src/routes/delete.php';
require __DIR__ . '/../../src/routes/update.php';
require __DIR__ . '/../../src/routes/singleRecord.php';
$app->run();
