<?php
header("Content-Type: application/json; charset=UTF-8");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/conexion.php";

$datos = json_decode(file_get_contents("php://input"), true);

$usuario = isset($datos["usuario"]) ? trim($datos["usuario"]) : "";
$password = isset($datos["password"]) ? trim($datos["password"]) : "";

if (empty($usuario) || empty($password)) {
    echo json_encode([
        "success" => false,
        "message" => "Por favor, ingresá usuario y contraseña."
    ]);
    exit;
}

$stmt = $conexion->prepare("SELECT id, usuario, password, rol FROM usuarios WHERE LOWER(TRIM(usuario)) = LOWER(?)");
$stmt->bind_param("s", $usuario);
$stmt->execute();
$resultado = $stmt->get_result();

// 1. El usuario NO existe en la base de datos
if ($resultado->num_rows === 0) {
    echo json_encode([
        "success" => false,
        "no_existe" => true,
        "message" => "El usuario '" . $usuario . "' no existe. Te recomendamos registrarte si aún no tienes cuenta."
    ]);
    $stmt->close();
    exit;
}

$usuarioData = $resultado->fetch_assoc();
$stmt->close();

// 2. El usuario SÍ existe, pero la contraseña no coincide
if (!password_verify($password, $usuarioData["password"])) {
    echo json_encode([
        "success" => false,
        "password_invalida" => true,
        "message" => "La contraseña ingresada para el usuario '" . $usuarioData["usuario"] . "' es incorrecta. Verificá mayúsculas o espacios."
    ]);
    exit;
}

// 3. Credenciales correctas: Iniciar sesión en el servidor
$_SESSION["usuario_id"] = $usuarioData["id"];
$_SESSION["usuario"] = $usuarioData["usuario"];
$_SESSION["rol"] = $usuarioData["rol"];

echo json_encode([
    "success" => true,
    "message" => "¡Bienvenido/a " . $usuarioData["usuario"] . "!",
    "usuario" => $usuarioData["usuario"],
    "rol" => $usuarioData["rol"]
]);
?>
