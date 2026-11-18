<?php
header("Content-Type: application/json; charset=UTF-8");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/conexion.php";

$datos = json_decode(file_get_contents("php://input"), true);

$usuario = isset($datos["usuario"]) ? trim($datos["usuario"]) : "";
$password = isset($datos["password"]) ? $datos["password"] : "";
$token = isset($datos["token"]) ? $datos["token"] : "";
$pregunta = isset($datos["pregunta"]) ? trim($datos["pregunta"]) : "";
$respuesta = isset($datos["respuesta"]) ? trim($datos["respuesta"]) : "";
$indicio = isset($datos["indicio"]) ? trim($datos["indicio"]) : "";

if (empty($usuario) || empty($password)) {
    echo json_encode([
        "success" => false,
        "message" => "Por favor, completá todos los campos."
    ]);
    exit;
}

if (strlen($usuario) < 3) {
    echo json_encode([
        "success" => false,
        "message" => "El usuario debe tener al menos 3 caracteres."
    ]);
    exit;
}

if (strlen($password) < 4) {
    echo json_encode([
        "success" => false,
        "message" => "La contraseña debe tener al menos 4 caracteres."
    ]);
    exit;
}

// 1. Verificar Turnstile si hay token enviado
if (!empty($token)) {
    $secretKey = "0x4AAAAAAETdB5BtuJvNImeNSG_tHSNFkYc";
    $url = "https://challenges.cloudflare.com/turnstile/v0/siteverify";
    
    $ch = curl_init($url);
    curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
    curl_setopt($ch, CURLOPT_POST, true);
    curl_setopt($ch, CURLOPT_POSTFIELDS, http_build_query([
        "secret" => $secretKey,
        "response" => $token
    ]));
    curl_setopt($ch, CURLOPT_TIMEOUT, 10);
    $cfResponse = curl_exec($ch);
    curl_close($ch);

    if ($cfResponse !== false) {
        $cfData = json_decode($cfResponse, true);
        if (!isset($cfData["success"]) || $cfData["success"] !== true) {
            echo json_encode([
                "success" => false,
                "message" => "La verificación de seguridad de Cloudflare falló."
            ]);
            exit;
        }
    }
}

// 2. Comprobar si el usuario ya existe
$stmtCheck = $conexion->prepare("SELECT id FROM usuarios WHERE LOWER(usuario) = LOWER(?)");
$stmtCheck->bind_param("s", $usuario);
$stmtCheck->execute();
$resCheck = $stmtCheck->get_result();

if ($resCheck->num_rows > 0) {
    echo json_encode([
        "success" => false,
        "message" => "Ese nombre de usuario ya está en uso."
    ]);
    $stmtCheck->close();
    exit;
}
$stmtCheck->close();

// 3. Hashear contraseña e insertar
$passwordHash = password_hash($password, PASSWORD_DEFAULT);
$rol = "cliente";

// Guardar pregunta, respuesta de seguridad e indicio
$preguntaVal = !empty($pregunta) ? $pregunta : null;
$respuestaVal = !empty($respuesta) ? $respuesta : null;
$indicioVal = !empty($indicio) ? $indicio : null;

$stmtInsert = $conexion->prepare("INSERT INTO usuarios (usuario, password, rol, pregunta_seguridad, respuesta_seguridad, indicio_password) VALUES (?, ?, ?, ?, ?, ?)");
$stmtInsert->bind_param("ssssss", $usuario, $passwordHash, $rol, $preguntaVal, $respuestaVal, $indicioVal);

if ($stmtInsert->execute()) {
    $nuevoId = $stmtInsert->insert_id;
    $stmtInsert->close();

    // Iniciar sesión automáticamente
    $_SESSION["usuario_id"] = $nuevoId;
    $_SESSION["usuario"] = $usuario;
    $_SESSION["rol"] = $rol;

    echo json_encode([
        "success" => true,
        "message" => "¡Registro exitoso! Bienvenido/a " . $usuario . ".",
        "usuario" => $usuario,
        "rol" => $rol
    ]);
} else {
    echo json_encode([
        "success" => false,
        "message" => "No se pudo registrar el usuario. Intentá nuevamente."
    ]);
}
?>

