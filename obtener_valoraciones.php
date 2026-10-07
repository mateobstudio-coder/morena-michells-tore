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

$sql = "SELECT id, usuario, puntuacion, DATE_FORMAT(fecha, '%d/%m/%Y %H:%i') AS fecha FROM valoraciones WHERE producto_id = ? ORDER BY fecha DESC";
$stmt = $conexion->prepare($sql);
$stmt->bind_param("i", $producto_id);
$stmt->execute();
$resultado = $stmt->get_result();

$valoraciones = [];
$usuarioActual = $_SESSION["usuario"] ?? "";
$esAdmin = (isset($_SESSION["rol"]) && $_SESSION["rol"] === "admin");

while ($fila = $resultado->fetch_assoc()) {
    $fila["puede_eliminar"] = $esAdmin || (strtolower($usuarioActual) === strtolower($fila["usuario"]) && !empty($usuarioActual));
    $valoraciones[] = $fila;
}
$stmt->close();

// Calcular promedio
$sqlPromedio = "SELECT AVG(puntuacion) AS promedio, COUNT(*) AS cantidad FROM valoraciones WHERE producto_id = ?";
$stmt = $conexion->prepare($sqlPromedio);
$stmt->bind_param("i", $producto_id);
$stmt->execute();
$resultadoPromedio = $stmt->get_result();
$datosPromedio = $resultadoPromedio->fetch_assoc();

$promedio = ($datosPromedio["promedio"] !== null) ? round(floatval($datosPromedio["promedio"]), 1) : 0;
$cantidad = intval($datosPromedio["cantidad"]);

$stmt->close();
$conexion->close();

echo json_encode([
    "success" => true,
    "promedio" => $promedio,
    "cantidad" => $cantidad,
    "valoraciones" => $valoraciones
]);
?>