<?php
header("Content-Type: application/json; charset=UTF-8");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/conexion.php";

$datos = json_decode(file_get_contents("php://input"), true);
$id = isset($datos["id"]) ? intval($datos["id"]) : (isset($_POST["id"]) ? intval($_POST["id"]) : 0);

if ($id <= 0) {
    echo json_encode(["success" => false, "message" => "ID de comentario inválido."]);
    exit;
}

$usuarioActual = $_SESSION["usuario"] ?? "";
$esAdmin = (isset($_SESSION["rol"]) && $_SESSION["rol"] === "admin");

if (empty($usuarioActual) && !$esAdmin) {
    http_response_code(401);
    echo json_encode(["success" => false, "message" => "Debes iniciar sesión."]);
    exit;
}

// Buscar el comentario
$stmt = $conexion->prepare("SELECT id, usuario FROM comentarios WHERE id = ?");
$stmt->bind_param("i", $id);
$stmt->execute();
$res = $stmt->get_result();

if ($res->num_rows === 0) {
    echo json_encode(["success" => false, "message" => "El comentario no existe."]);
    $stmt->close();
    exit;
}

$comentario = $res->fetch_assoc();
$stmt->close();

// Verificar permiso: Solo el autor o el administrador pueden eliminar
if (!$esAdmin && strtolower($usuarioActual) !== strtolower($comentario["usuario"])) {
    http_response_code(403);
    echo json_encode(["success" => false, "message" => "No tienes permiso para eliminar este comentario."]);
    exit;
}

$stmtDel = $conexion->prepare("DELETE FROM comentarios WHERE id = ?");
$stmtDel->bind_param("i", $id);

if ($stmtDel->execute()) {
    echo json_encode(["success" => true, "message" => "Comentario eliminado correctamente."]);
} else {
    echo json_encode(["success" => false, "message" => "Error al eliminar el comentario."]);
}

$stmtDel->close();
$conexion->close();
?>

