<?php

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\ValidAt;
use Lcobucci\Clock\SystemClock;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->post('/api/validatetoken', function (Request $request, Response $response, array $args) {
    $params = (array)$request->getParsedBody();
    $tokenString = $params['token'] ?? '';

    try {
        $authorizationHeader = $request->getHeaderLine('Authorization');
        $tokenParts = explode(' ', $authorizationHeader);
        if (count($tokenParts) !== 2 || $tokenParts[0] !== 'Bearer') {
            return $response->withJson(['error' => 'Invalid Authorization header'], 400);
        }
        $tokenString = $tokenParts[1];
        if (empty($tokenString)) {
            return $response->withJson(['error' => 'Token is empty'], 400);
        }
        $config = Configuration::forSymmetricSigner(
            new Sha256(),
            InMemory::plainText('Aman')
        );
        $token = $config->parser()->parse($tokenString);
        $constraints = [
            new SignedWith($config->signer(), $config->signingKey()),
            new ValidAt(SystemClock::fromSystemTimezone())
        ];
        $validator = $config->validator();
        if (!$validator->validate($token, ...$constraints)) {
            return $response->withJson(['error' => 'Invalid token'], 401);
        }
        $db = new PDO('mysql:host=localhost;dbname=record;charset=utf8', 'root', '', array(PDO::MYSQL_ATTR_INIT_COMMAND => "SET NAMES utf8mb4"));
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        $stmt = $db->query('SELECT * FROM studentrecord');
        $data = $stmt->fetchAll(PDO::FETCH_ASSOC);
        return $response->withJson(['success' => 'Token is valid', 'data' => $data], 200);
    } catch (\Exception $e) {
        return $response->withJson(['error' => 'An error occurred: ' . $e->getMessage()], 500);
    }
});
