<?php
require_once (__DIR__ . '/../config/config.php');

function connect()

{
    global $config;

    try {
        $conn = new mysqli($config['host'], $config['username'], $config['password'], $config['dbname']);

        if ($conn->connect_error) {
            echo ("Connection failed: " . $conn->connect_error);

            exit();
        }

    }catch (Exception $e) {
        return false;
    }

    return $conn;
}

function disconnect($conn)
{
    if ($conn) {
        $conn->close();
    }
}