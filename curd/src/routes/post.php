<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->post('/api/post', function (Request $request, Response $response, array $args) {
    $data = $request->getParsedBody();

    // Check if required data exists
    if (!isset($data['f_name']) || !isset($data['l_name']) || !isset($data['emailId']) || !isset($data['age']) || !isset($data['phone']) || !isset($data['gender'])) {
        $response->getBody()->write(json_encode(['error' => 'Missing required data']));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(400);
    }

    // Connect to the database
    $db = new PDO('mysql:host=localhost;dbname=record;charset=utf8', 'root', '');

    // Prepare the SQL statement
    $stmt = $db->prepare('INSERT INTO studentrecord (f_name, l_name, age, emailId, phone, gender) VALUES (:f_name, :l_name, :age, :emailId, :phone, :gender)');

    // Bind parameters and execute the query
    $stmt->execute([
        ':f_name' => $data['f_name'],
        ':l_name' => $data['l_name'],
        ':age' => $data['age'],
        ':emailId' => $data['emailId'],
        ':phone' => $data['phone'],
        ':gender' => $data['gender']
    ]);

    // Return success message
    $response->getBody()->write(json_encode(['message' => 'Data inserted successfully']));
    return $response->withHeader('Content-Type', 'application/json')->withStatus(201);
});
