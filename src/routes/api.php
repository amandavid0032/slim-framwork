<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->get('/api/data', function (Request $request, Response $response, array $args) {
    $db = new PDO('mysql:host=localhost;dbname=record;charset=utf8', 'root', '');
    $stmt = $db->query('SELECT * FROM studentrecord');
    $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $response->getBody()->write(json_encode($data));
    return $response->withHeader('Content-Type', 'application/json');
});



