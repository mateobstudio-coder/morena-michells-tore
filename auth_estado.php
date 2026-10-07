<?php
header("Content-Type: application/json; charset=UTF-8");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if (isset($_SESSION["usuario"])) {
    echo json_encode([
        "loggedIn" => true,
        "usuario" => $_SESSION["usuario"],
        "rol" => $_SESSION["rol"] ?? "cliente",
        "usuario_id" => $_SESSION["usuario_id"] ?? null
    ]);
} else {
    echo json_encode([
        "loggedIn" => false,
        "usuario" => null,
        "rol" => "visitante"
    ]);
}
?>

