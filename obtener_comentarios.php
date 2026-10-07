<?php
header("Content-Type: application/json; charset=UTF-8");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/conexion.php";

$producto_id = isset($_GET["producto_id"]) ? intval($_GET["producto_id"]) : 0;

if ($producto_id <= 0) {
    echo json_encode(["success" => false, "message" => "Producto inválido."]);
    exit;
}

$sql = "SELECT id, producto_id, usuario, comentario, DATE_FORMAT(fecha, '%d/%m/%Y %H:%i') AS fecha_formateada FROM comentarios WHERE producto_id = ? ORDER BY fecha DESC";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $producto_id);
$stmt->execute();
$res = $stmt->get_result();

$comentarios = [];
$usuarioActual = $_SESSION["usuario"] ?? "";
$esAdmin = (isset($_SESSION["rol"]) && $_SESSION["rol"] === "admin");

while ($fila = $res->fetch_assoc()) {
    // Indicar si el usuario actual tiene permiso de eliminar este comentario
    $fila["puede_eliminar"] = $esAdmin || (strtolower($usuarioActual) === strtolower($fila["usuario"]) && !empty($usuarioActual));
    $comentarios[] = $fila;
}

$stmt->close();
$conexion->close();

echo json_encode([
    "success" => true,
    "comentarios" => $comentarios
]);
?>

