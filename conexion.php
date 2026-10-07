<?php

$host = "localhost";
$user = "root";
$pass = "";
$db = "michell_store";

$conexion = new mysqli($host, $user, $pass, $db);

if ($conexion->connect_error) {
    header("Content-Type: application/json; charset=UTF-8");
    die(json_encode([
        "success" => false,
        "message" => "Error de conexión a la base de datos: " . $conexion->connect_error
    ]));
}

$conexion->set_charset("utf8mb4");
$conn = $conexion;

?>