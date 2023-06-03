<?php
require "../bootstrap.php";

use Firebase\JWT\JWT;

$clientId = getenv('APP_CLIENTID');
$secret = getenv('APP_SECRET');
$issuer = getenv('APP_ISSUER');

$token = generateToken($issuer, $clientId, $secret);

function generateToken($issuer, $clientId, $secret) {
    $date = new DateTimeImmutable();
    $expire_at = $date->modify('+60 minutes')->getTimestamp();
    $request_data = [
      'aud'  => $clientId,
      'iat'  => $date->getTimestamp(),
      'iss'  => $issuer,
      'nbf'  => $date->getTimestamp(),
      'exp'  => $expire_at
    ];

    echo 'Bearer '.JWT::encode(
      $request_data,
      $secret,
      'HS512'
    );
}
?>