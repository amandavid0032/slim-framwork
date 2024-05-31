<?php

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\ValidAt;
use Lcobucci\Clock\SystemClock;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

// Middleware for token validation
$app->add(function (Request $request, Response $response, $next) {
    $authorizationHeader = $request->getHeaderLine('Authorization');

    // Check if Authorization header is not empty
    if (empty($authorizationHeader)) {
        return $response->withJson(['error' => 'Token is empty or malformed'], 400);
    }

    $tokenParts = explode(' ', $authorizationHeader);

    // Check if the token parts are valid
    if (count($tokenParts) !== 2 || $tokenParts[0] !== 'Bearer') {
        return $response->withJson(['error' => 'Token is empty or malformed'], 400);
    }

    $tokenString = $tokenParts[1];

    try {
        $config = Configuration::forSymmetricSigner(new Sha256(), InMemory::plainText($this->get('jwt_secret')));
        $token = $config->parser()->parse($tokenString);
        $constraints = [
            new SignedWith($config->signer(), $config->signingKey()),
            new ValidAt(SystemClock::fromSystemTimezone())
        ];
        $validator = $config->validator();

        if (!$validator->validate($token, ...$constraints)) {
            return $response->withJson(['error' => 'Invalid token'], 401);
        }

        return $next($request, $response);
    } catch (\Exception $e) {
        return $response->withJson(['error' => 'Invalid token'], 401);
    }
});
