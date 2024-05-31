<?php

use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->put('/api/Update/{id}', function (Request $request, Response $response, array $args) {
    $id = $args['id'];
    $data = $request->getParsedBody();

    // Establish database connection
    $db = new PDO('mysql:host=localhost;dbname=record;charset=utf8', 'root', '');

    // Prepare SQL statement
    $stmt = $db->prepare('UPDATE studentrecord SET f_name = :f_name, l_name = :l_name, emailId = :emailId, phone = :phone, gender = :gender WHERE id = :id');

    // Execute SQL statement
    try {
        $stmt->execute([
            ':id' => $id,
            ':f_name' => $data['f_name'],
            ':l_name' => $data['l_name'],
            ':emailId' => $data['emailId'],
            ':phone' => $data['phone'],
            ':gender' => $data['gender']
        ]);

        // Return success message
        $response->getBody()->write(json_encode(['message' => 'Record updated successfully']));
        return $response->withHeader('Content-Type', 'application/json');
    } catch (PDOException $e) {
        // Return error message if update fails
        $response->getBody()->write(json_encode(['error' => $e->getMessage()]));
        return $response->withHeader('Content-Type', 'application/json')->withStatus(500);
    }
});
