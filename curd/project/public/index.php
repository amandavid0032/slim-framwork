<?php

// require __DIR__ . '/../../vendor/autoload.php';

// $app = new \Slim\App([
//     'settings' => [
//         'displayErrorDetails' => true,
//     ]
// ]);

// $app->get('/', function ($request, $response, $args) {
//     $indexHtml = file_get_contents(__DIR__ . '/view/index.html');
//     return $response->write($indexHtml);
// });

// $app->get('/add', function ($request, $response, $args) {
//     $addHtml = file_get_contents(__DIR__ . '/view/add.html');
//     return $response->write($addHtml);
// });


// require __DIR__ . '/../../src/routes/api.php';
// require __DIR__ . '/../../src/routes/post.php';
// require __DIR__ . '/../../src/routes/delete.php';
// require __DIR__ . '/../../src/routes/update.php';
// require __DIR__ . '/../../src/routes/singleRecord.php';
// $app->run();


require __DIR__ . '/../../vendor/autoload.php';

$app = new \Slim\App([
    'settings' => [
        'displayErrorDetails' => true,
    ]
]);

// Route for displaying the index page
$app->get('/', function ($request, $response, $args) {
    $indexHtml = file_get_contents(__DIR__ . '/view/login.html');
    return $response->write($indexHtml);
});

// Route for displaying the View Index page
$app->get('/view', function ($request, $response, $args) {
    $indexHtml = file_get_contents(__DIR__ . '/view/index.html');
    return $response->write($indexHtml);
});
// Route for displaying the add page
$app->get('/add', function ($request, $response, $args) {
    $addHtml = file_get_contents(__DIR__ . '/view/add.html');
    return $response->write($addHtml);
});

// Include routes for API endpoints
require __DIR__ . '/../../src/routes/api.php';
require __DIR__ . '/../../src/routes/post.php';
require __DIR__ . '/../../src/routes/delete.php';
require __DIR__ . '/../../src/routes/update.php';
require __DIR__ . '/../../src/routes/singleRecord.php';
require __DIR__ . '/../../src/routes/login.php';
require __DIR__ . '/../../src/routes/validation.php';


$app->run();
