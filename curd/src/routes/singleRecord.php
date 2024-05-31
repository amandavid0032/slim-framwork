<?php
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->post('/api/single/{id}', function (Request $request, Response $response, array $args) {
    $id = $args['id'];
    $db = new PDO('mysql:host=localhost;dbname=record;charset=utf8', 'root', '');
    $stmt = $db->prepare('SELECT * FROM studentrecord WHERE id = :id');
    $stmt->execute([':id' => $id]);
    $record = $stmt->fetch(PDO::FETCH_ASSOC);
    if (!$record) {
        $response->getBody()->write(json_encode(['error' => 'Record not found']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(404);
    }
    $response->getBody()->write(json_encode($record));
    return $response->withHeader('Content-Type', 'application/json');
});
