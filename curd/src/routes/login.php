<?php
use Lcobucci\JWT\Configuration;
use Lcobucci\JWT\Signer\Hmac\Sha256;
use Lcobucci\JWT\Signer\Key\InMemory;
use Psr\Http\Message\ResponseInterface as Response;
use Psr\Http\Message\ServerRequestInterface as Request;

$app->post('/api/login', function (Request $request, Response $response, array $args) {
    $params = (array)$request->getParsedBody();
    $email = $params['email'] ?? '';
    $password = $params['password'] ?? '';

    try {
        $db = new PDO('mysql:host=localhost;dbname=record;charset=utf8', 'root', '');
        $db->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

        $stmt = $db->prepare('SELECT * FROM studentrecord WHERE emailid = :email');
        $stmt->bindParam(':email', $email);
        $stmt->execute();
        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        // Verify password
        if ($user && md5($password) === $user['password']) {
            $config = Configuration::forSymmetricSigner(
                new Sha256(),
                InMemory::plainText('Aman')
            );

            $now = new \DateTimeImmutable();
            $token = $config->builder()
                ->issuedBy('your_domain.com')
                ->permittedFor('your_domain.com')
                ->issuedAt($now)
                ->expiresAt($now->modify('+1 hour'))
                ->withClaim('id', $user['id'])
                ->getToken($config->signer(), $config->signingKey());

            // Update the user's record with the new token
            $updateStmt = $db->prepare('UPDATE studentrecord SET token = :token WHERE id = :id');
            $tokenString = $token->toString();
            $updateStmt->bindParam(':token', $tokenString);
            $updateStmt->bindParam(':id', $user['id']);
            $updateStmt->execute();

            $responseData = [
                'success' => 'Successfully login',
                'user' => [
                    'id' => $user['id'],
                    'name' => $user['f_name'],
                    'emailid' => $user['emailId'],
                ],
                'token' => $tokenString,

            ];

            return $response
                ->withHeader('Content-Type', 'application/json')
                ->withJson($responseData, 200);
        } else {
            $errorData = ['error' => 'Invalid email or password'];
            return $response->withJson($errorData, 401);
        }
    } catch (PDOException $e) {
        $errorData = ['error' => 'Database error: ' . $e->getMessage()];
        return $response->withJson($errorData, 500);
    }
});
?>
