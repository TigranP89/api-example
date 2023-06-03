<?php
require '../vendor/autoload.php';

$openapi = \OpenApi\Generator::scan(['../src/Controller']);
header('Content-Type: application/x-json');

$myfile = fopen("web/swagger.json", "w") or die("Unable to open file!");
fwrite($myfile, $openapi->toJSON());
fclose($myfile);