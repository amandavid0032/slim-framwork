<!-- <?php
// require __DIR__ . '/ApiLogic.php';

// // Initialize ApiLogic with container
// $apiLogic = new ApiLogic($app->getContainer()); -->

// Define routes using ApiLogic methods
$app->post('/api/login', [$apiLogic, 'login']);
$app->post('/api/validatetoken', [$apiLogic, 'validateToken']);