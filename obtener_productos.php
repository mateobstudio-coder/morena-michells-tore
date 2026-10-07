<?php

header('Content-Type: application/json');

require_once __DIR__ . "/conexion.php";

// consulta (solo productos visibles para clientes)
$sql = "SELECT * FROM productos WHERE visible = 1 ORDER BY id DESC";

$resultado = $conexion->query($sql);

$productos = [];

while ($fila = $resultado->fetch_assoc()) {
    $productos[] = $fila;
}

// devolver JSON limpio
echo json_encode($productos);

$conexion->close();

?>