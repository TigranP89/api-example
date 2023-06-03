<?php
require "../bootstrap.php";

use Firebase\JWT\JWT;
use Firebase\JWT\Key;
use Src\Controller\UserController;

header("Access-Control-Allow-Origin: *");
header("Content-Type: application/json; charset=UTF-8");
header("Access-Control-Allow-Methods: OPTIONS,GET,POST,PUT,DELETE");
header("Access-Control-Max-Age: 3600");
header("Access-Control-Allow-Headers: Content-Type, Access-Control-Allow-Headers, Authorization, X-Requested-With");

$uri = parse_url($_SERVER['REQUEST_URI'], PHP_URL_PATH);
$uri = explode('/', $uri);

// Endpoints should start with /user
// otherwise, you will be redirected to the swagger page
if ($uri[1] !== 'user') {
    header("Location: /web/index.html", true, 301);
    exit();
}

$userId = null;
if (isset($uri[2])) {
    $userId = (int) $uri[2];
}

if (! authenticate()) {
    header("HTTP/1.1 401 Unauthorized");
    exit('Unauthorized');
}

$controller = new UserController($dbConnection, $_SERVER["REQUEST_METHOD"], $userId);
$controller->processRequest();

function authenticate() {
    try {
        if (! preg_match('/Bearer\s(\S+)/', $_SERVER['HTTP_AUTHORIZATION'], $matches)) {
            throw new Exception('Token not found');
        }

        $jwt = $matches[1];
        if (! $jwt) {
            throw new Exception('Token not found');
        }

        $clientId = getenv('APP_CLIENTID');
        $secret = getenv('APP_SECRET');
        $issuer = getenv('APP_ISSUER');
        $token = JWT::decode($jwt, new Key($secret, 'HS512'));
        $now = new DateTimeImmutable();

        if ($token->iss !== $issuer ||
          $token->aud !== $clientId ||
          $token->nbf > $now->getTimestamp() ||
          $token->exp < $now->getTimestamp())
        {
            throw new Exception('Not valid token');
        }

    } catch (Exception $e) {
        return false;
    }
    return true;
}

?>
