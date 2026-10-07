<?php
header("Content-Type: application/json; charset=UTF-8");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/conexion.php";

$datos = json_decode(file_get_contents("php://input"), true);

$producto_id = isset($datos["producto_id"]) ? intval($datos["producto_id"]) : 0;
$comentario = isset($datos["comentario"]) ? trim($datos["comentario"]) : "";
$usuario = $_SESSION["usuario"] ?? (isset($datos["usuario"]) ? trim($datos["usuario"]) : "");

if ($producto_id <= 0) {
    echo json_encode(["success" => false, "message" => "Producto inválido."]);
    exit;
}

if (empty($usuario)) {
    echo json_encode(["success" => false, "message" => "Debes iniciar sesión para escribir un comentario."]);
    exit;
}

if (empty($comentario)) {
    echo json_encode(["success" => false, "message" => "Por favor, escribí un comentario."]);
    exit;
}

// Comprobar existencia del producto
$stmtProd = $conexion->prepare("SELECT id FROM productos WHERE id = ?");
$stmtProd->bind_param("i", $producto_id);
$stmtProd->execute();
if ($stmtProd->get_result()->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "El producto no existe."]);
    $stmtProd->close();
    exit;
}
$stmtProd->close();

// Insertar comentario
$stmt = $conexion->prepare("INSERT INTO comentarios (producto_id, usuario, comentario) VALUES (?, ?, ?)");
$stmt->bind_param("iss", $producto_id, $usuario, $comentario);

if ($stmt->execute()) {
    echo json_encode([
        "success" => true,
        "message" => "¡Comentario publicado correctamente!"
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "No se pudo guardar el comentario."
    ]);
}

$stmt->close();
$conexion->close();
?>

