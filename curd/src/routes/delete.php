<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;
$app->delete('/api/delete/{id}', function (Request $request, Response $response, array $args) {
    $id = $args['id'];
    $db = new PDO('mysql:host=localhost;dbname=record;charset=utf8', 'root', '');
    $stmt = $db->prepare("DELETE FROM studentrecord WHERE id = :id");
    $stmt->execute([':id' => $id]);
    $response->getBody()->write(json_encode(['message' => 'Record deleted successfully']));
    return $response->withHeader('Content-Type', 'application/json');
});
