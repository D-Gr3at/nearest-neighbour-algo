<?php

namespace Database;

require_once 'vendor/autoload.php';

use Dotenv\Dotenv;
use mysqli;

$dotEnv = Dotenv::create('.');

$dotEnv->load();

$dotEnv->required(['DB_NAME', 'DB_USER'])->notEmpty();

class Database{

    public static function getConnectionInstance(): mysqli
    {
        $hostName = getenv('DB_HOST');
        $databaseName = getenv('DB_NAME');
        $username = getenv('DB_USER');
        $password = getenv('DB_PASSWORD');
        return new mysqli($hostName, $username, $password, $databaseName);
    }

}