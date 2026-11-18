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

$stmt = $conexion->prepare("SELECT id, usuario, email, password, rol, indicio_password FROM usuarios WHERE LOWER(TRIM(usuario)) = LOWER(?) OR LOWER(TRIM(email)) = LOWER(?)");
$stmt->bind_param("ss", $usuario, $usuario);
$stmt->execute();
$resultado = $stmt->get_result();

// 1. El usuario NO existe en la base de datos
if ($resultado->num_rows === 0) {
    echo json_encode([
        "success" => false,
        "no_existe" => true,
        "message" => "La cuenta '" . $usuario . "' no existe. Te recomendamos registrarte si aún no tienes cuenta."
    ]);
    $stmt->close();
    exit;
}

$usuarioData = $resultado->fetch_assoc();
$stmt->close();

// 2. El usuario SÍ existe, pero la contraseña no coincide
if (!password_verify($password, $usuarioData["password"])) {
    if (!isset($_SESSION["login_intentos"])) {
        $_SESSION["login_intentos"] = [];
    }
    $claveUser = strtolower($usuarioData["usuario"]);
    $_SESSION["login_intentos"][$claveUser] = ($_SESSION["login_intentos"][$claveUser] ?? 0) + 1;
    $intentos = $_SESSION["login_intentos"][$claveUser];

    $tieneIndicio = !empty($usuarioData["indicio_password"]);
    $mostrarIndicio = ($intentos >= 2); // A partir de 2 intentos fallidos

    $response = [
        "success" => false,
        "password_invalida" => true,
        "intentos" => $intentos,
        "mostrar_indicio" => $mostrarIndicio,
        "indicio" => ($mostrarIndicio && $tieneIndicio) ? $usuarioData["indicio_password"] : null,
        "sugerir_recuperacion" => ($mostrarIndicio && !$tieneIndicio),
        "message" => "La contraseña ingresada para el usuario '" . $usuarioData["usuario"] . "' es incorrecta."
    ];

    if ($mostrarIndicio) {
        if ($tieneIndicio) {
            $response["message"] = "Contraseña incorrecta (intento " . $intentos . "). 💡 Indicio: \"" . $usuarioData["indicio_password"] . "\"";
        } else {
            $response["message"] = "Contraseña incorrecta (intento " . $intentos . "). Recordá que podés usar tu pregunta de seguridad para recuperarla.";
        }
    }

    echo json_encode($response);
    exit;
}

// 3. Credenciales correctas: Iniciar sesión en el servidor
$claveUser = strtolower($usuarioData["usuario"]);
if (isset($_SESSION["login_intentos"][$claveUser])) {
    unset($_SESSION["login_intentos"][$claveUser]);
}

$_SESSION["usuario_id"] = $usuarioData["id"];
$_SESSION["usuario"] = $usuarioData["usuario"];
$_SESSION["email"] = $usuarioData["email"];
$_SESSION["rol"] = $usuarioData["rol"];

echo json_encode([
    "success" => true,
    "message" => "¡Bienvenido/a " . $usuarioData["usuario"] . "!",
    "usuario" => $usuarioData["usuario"],
    "email" => $usuarioData["email"],
    "rol" => $usuarioData["rol"]
]);
?>
