<?php
require 'vendor/autoload.php';
use Dotenv\Dotenv;

use Src\Database\DatabaseConnector;

$dotenv = new DotEnv(__DIR__);
$dotenv->load();

$dbConnection = (new DatabaseConnector())->getConnection();
