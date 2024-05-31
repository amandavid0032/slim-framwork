<?php

use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Lcobucci\JWT\Validation\Constraint\SignedWith;
use Lcobucci\JWT\Validation\Constraint\ValidAt;
use Lcobucci\Clock\SystemClock;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

class ApiLogic
{
    private $db;
    private $jwt_secret;

    public function __construct($db, $jwt_secret)
    {
        $this->db = $db;
        $this->jwt_secret = $jwt_secret;
    }

    public function login(Request $request, Response $response, array $args)
    {
        $params = (array)$request->getParsedBody();
        $email = $params['email'] ?? '';
        $password = $params['password'] ?? '';

        if (empty($email) || empty($password)) {
            return $response->withJson(['error' => 'Email and password are required'], 400);
        }

        $stmt = $this->db->prepare('SELECT * FROM studentrecord WHERE emailid = :email');
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if ($user && md5($password) === $user['password']) {
            $config = Configuration::forSymmetricSigner(new Sha256(), InMemory::plainText($this->jwt_secret));
            $now = new DateTimeImmutable();
            $token = $config->builder()
                ->issuedBy('your_domain.com')
                ->permittedFor('your_domain.com')
                ->issuedAt($now)
                ->expiresAt($now->modify('+1 hour'))
                ->withClaim('id', $user['id'])
                ->getToken($config->signer(), $config->signingKey());

            $updateStmt = $this->db->prepare('UPDATE studentrecord SET token = :token WHERE id = :id');
            $tokenString = $token->toString();
            $updateStmt->bindParam(':token', $tokenString);
            $updateStmt->bindParam(':id', $user['id']);
            $updateStmt->execute();

            $responseData = [
                'success' => 'Successfully logged in',
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['f_name'],
                    'emailid' => $user['emailId'],
                ],
                'token' => $tokenString,
            ];

            return $response->withJson($responseData, 200);
        } else {
            return $response->withJson(['error' => 'Invalid email or password'], 401);
        }
    }
    public function validateToken(Request $request, Response $response, array $args)
    {
        $authorizationHeader = $request->getHeaderLine('Authorization');

        // Check if Authorization header is not empty
        if (empty($authorizationHeader)) {
            return $response->withJson(['error' => 'Token is empty '], 400);
        }

        $tokenParts = explode(' ', $authorizationHeader);

        // Check if the token parts are valid
        if (count($tokenParts) !== 2 || $tokenParts[0] !== 'Bearer') {
            return $response->withJson(['error' => 'no space allowed In Token '], 400);
        }

        $tokenString = $tokenParts[1];
        try {
            $config = Configuration::forSymmetricSigner(new Sha256(), InMemory::plainText($this->jwt_secret));
            $token = $config->parser()->parse($tokenString);
            $constraints = [
                new SignedWith($config->signer(), $config->signingKey()),
                new ValidAt(SystemClock::fromSystemTimezone())
            ];
            $validator = $config->validator();

            if (!$validator->validate($token, ...$constraints)) {
                return $response->withJson(['error' => 'Invalid token'], 401);
            }

            $stmt = $this->db->query('SELECT * FROM studentrecord');
            $data = $stmt->fetchAll(PDO::FETCH_ASSOC);

            return $response->withJson(['success' => 'Token is valid and Your data', 'data' => $data], 200);
        } catch (\Exception $e) {
            return $response->withJson(['error' => 'Invalid token'], 401);
        }
    }

}
