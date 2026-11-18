<?php
header("Content-Type: application/json; charset=UTF-8");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/conexion.php";
require_once __DIR__ . "/config.php";

$datos = json_decode(file_get_contents("php://input"), true);

$accion = isset($datos["accion"]) ? trim($datos["accion"]) : "";
$identificador = isset($datos["identificador"]) ? trim($datos["identificador"]) : (isset($datos["usuario"]) ? trim($datos["usuario"]) : "");

function ofuscarEmail($email) {
    $partes = explode("@", $email);
    if (count($partes) !== 2) return $email;
    $nombre = $partes[0];
    $dominio = $partes[1];
    $long = strlen($nombre);
    if ($long <= 2) {
        $ofuscado = substr($nombre, 0, 1) . "***";
    } else {
        $ofuscado = substr($nombre, 0, 2) . str_repeat("*", max(2, $long - 4)) . substr($nombre, -2);
    }
    return $ofuscado . "@" . $dominio;
}

function enviarCorreoRecuperacion($destinatario, $nombreUsuario, $codigo) {
    $asunto = "Código de recuperación de contraseña: " . $codigo . " - Michell Store";
    $cuerpoHtml = "
    <!DOCTYPE html>
    <html lang='es'>
    <head>
      <meta charset='UTF-8'>
      <style>
        body { font-family: 'Helvetica Neue', Arial, sans-serif; background-color: #FFF5F4; margin: 0; padding: 20px; color: #3B151B; }
        .card { max-width: 500px; margin: 0 auto; background: #ffffff; border-radius: 16px; padding: 30px; box-shadow: 0 4px 15px rgba(139,38,53,0.08); border-top: 5px solid #8B2635; }
        .logo { text-align: center; font-size: 24px; font-weight: bold; color: #8B2635; margin-bottom: 20px; }
        .code-box { background: #FFF1EF; border: 2px dashed #8B2635; border-radius: 12px; font-size: 32px; font-weight: 700; letter-spacing: 8px; color: #8B2635; text-align: center; padding: 18px; margin: 25px 0; }
        .footer { font-size: 12px; color: #888; text-align: center; margin-top: 25px; border-top: 1px solid #f0d0ce; padding-top: 15px; }
      </style>
    </head>
    <body>
      <div class='card'>
        <div class='logo'>✨ Michell Store</div>
        <h2>Hola, " . htmlspecialchars($nombreUsuario) . " 👋</h2>
        <p>Recibimos una solicitud para restablecer la contraseña de tu cuenta.</p>
        <p>Tu código de seguridad de 6 dígitos es:</p>
        <div class='code-box'>" . htmlspecialchars($codigo) . "</div>
        <p style='font-size: 13px; color: #666;'>⏱️ Este código es válido durante los próximos <strong>15 minutos</strong>.</p>
        <p style='font-size: 13px; color: #999;'>Si vos no pediste este código, podés desestimar este mensaje; tu cuenta sigue segura.</p>
        <div class='footer'>Michell Store • Mensaje automático del sistema</div>
      </div>
    </body>
    </html>";

    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $fromEmail = defined("ADMIN_EMAIL") ? ADMIN_EMAIL : "no-reply@michellstore.com";
    $headers .= "From: Michell Store <" . $fromEmail . ">\r\n";
    $headers .= "Reply-To: " . $fromEmail . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    $enviado = @mail($destinatario, $asunto, $cuerpoHtml, $headers);

    $logDir = __DIR__ . "/logs";
    if (!file_exists($logDir)) {
        @mkdir($logDir, 0777, true);
    }
    $logMsg = date("Y-m-d H:i:s") . " | Código enviado a: {$destinatario} ({$nombreUsuario}) | Código: {$codigo} | mail(): " . ($enviado ? "ENVIADO" : "PENDIENTE") . PHP_EOL;
    @file_put_contents($logDir . "/recuperaciones.log", $logMsg, FILE_APPEND);

    return $enviado;
}

// =====================================================
// ACCIÓN 1: Enviar código de 6 dígitos al correo
// =====================================================
if ($accion === "enviar_codigo") {

    if (empty($identificador)) {
        echo json_encode([
            "success" => false,
            "message" => "Por favor, ingresá tu usuario o correo electrónico."
        ]);
        exit;
    }

    $stmt = $conexion->prepare("SELECT id, usuario, email FROM usuarios WHERE LOWER(TRIM(usuario)) = LOWER(?) OR LOWER(TRIM(email)) = LOWER(?)");
    $stmt->bind_param("ss", $identificador, $identificador);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
        echo json_encode([
            "success" => false,
            "message" => "No se encontró ninguna cuenta asociada a '" . $identificador . "'."
        ]);
        $stmt->close();
        exit;
    }

    $user = $resultado->fetch_assoc();
    $stmt->close();

    if (empty($user["email"])) {
        echo json_encode([
            "success" => false,
            "message" => "Esta cuenta no tiene un correo electrónico registrado. Usá tu pregunta de seguridad para recuperarla."
        ]);
        exit;
    }

    // Generar código de 6 dígitos numéricos
    $codigo = (string) random_int(100000, 999999);
    $expira = date("Y-m-d H:i:s", strtotime("+15 minutes"));

    // Guardar código y expiración
    $stmtUp = $conexion->prepare("UPDATE usuarios SET codigo_recuperacion = ?, codigo_expira = ? WHERE id = ?");
    $stmtUp->bind_param("ssi", $codigo, $expira, $user["id"]);
    $stmtUp->execute();
    $stmtUp->close();

    // Enviar correo
    $enviado = enviarCorreoRecuperacion($user["email"], $user["usuario"], $codigo);

    $emailOfuscado = ofuscarEmail($user["email"]);

    // En entorno de desarrollo localhost, también podemos devolver el código en el payload para facilitar pruebas
    $isLocalhost = in_array($_SERVER["REMOTE_ADDR"] ?? "", ["127.0.0.1", "::1"]);

    echo json_encode([
        "success" => true,
        "message" => "¡Código enviado! Te enviamos un código de seguridad de 6 dígitos a " . $emailOfuscado . ".",
        "email_ofuscado" => $emailOfuscado,
        "usuario" => $user["usuario"],
        "codigo_debug" => $isLocalhost ? $codigo : null
    ]);
    exit;
}

// =====================================================
// ACCIÓN 2: Verificar código y cambiar contraseña
// =====================================================
if ($accion === "restablecer_con_codigo") {

    $codigo = isset($datos["codigo"]) ? trim($datos["codigo"]) : "";
    $nueva_password = isset($datos["nueva_password"]) ? $datos["nueva_password"] : "";

    if (empty($identificador) || empty($codigo) || empty($nueva_password)) {
        echo json_encode([
            "success" => false,
            "message" => "Completá todos los campos (código y nueva contraseña)."
        ]);
        exit;
    }

    if (strlen($nueva_password) < 4) {
        echo json_encode([
            "success" => false,
            "message" => "La nueva contraseña debe tener al menos 4 caracteres."
        ]);
        exit;
    }

    $stmt = $conexion->prepare("SELECT id, usuario, email, codigo_recuperacion, codigo_expira FROM usuarios WHERE LOWER(TRIM(usuario)) = LOWER(?) OR LOWER(TRIM(email)) = LOWER(?)");
    $stmt->bind_param("ss", $identificador, $identificador);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
        echo json_encode([
            "success" => false,
            "message" => "Usuario no encontrado."
        ]);
        $stmt->close();
        exit;
    }

    $user = $resultado->fetch_assoc();
    $stmt->close();

    if (empty($user["codigo_recuperacion"])) {
        echo json_encode([
            "success" => false,
            "message" => "No hay ningún código activo para esta cuenta. Solicitá uno nuevo."
        ]);
        exit;
    }

    if (trim($user["codigo_recuperacion"]) !== $codigo) {
        echo json_encode([
            "success" => false,
            "message" => "El código de seguridad ingresado es incorrecto. Verificalo e intentá de nuevo."
        ]);
        exit;
    }

    if (!empty($user["codigo_expira"]) && strtotime($user["codigo_expira"]) < time()) {
        echo json_encode([
            "success" => false,
            "message" => "El código ha expirado (validez: 15 minutos). Por favor solicitá uno nuevo."
        ]);
        exit;
    }

    // Actualizar contraseña y limpiar el código
    $nuevoHash = password_hash($nueva_password, PASSWORD_DEFAULT);
    $stmtUpdate = $conexion->prepare("UPDATE usuarios SET password = ?, codigo_recuperacion = NULL, codigo_expira = NULL WHERE id = ?");
    $stmtUpdate->bind_param("si", $nuevoHash, $user["id"]);

    if ($stmtUpdate->execute()) {
        // Resetear contador de intentos fallidos
        $claveUser = strtolower($user["usuario"]);
        if (isset($_SESSION["login_intentos"][$claveUser])) {
            unset($_SESSION["login_intentos"][$claveUser]);
        }

        echo json_encode([
            "success" => true,
            "message" => "¡Contraseña restablecida con éxito! Ya podés iniciar sesión con tu nueva clave."
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Error al actualizar la contraseña. Intentá nuevamente."
        ]);
    }

    $stmtUpdate->close();
    exit;
}

// =====================================================
// ACCIÓN 3: Pregunta de seguridad (Método alternativo)
// =====================================================
if ($accion === "obtener_pregunta") {

    if (empty($identificador)) {
        echo json_encode([
            "success" => false,
            "message" => "Por favor, ingresá tu usuario o correo."
        ]);
        exit;
    }

    $stmt = $conexion->prepare("SELECT usuario, pregunta_seguridad FROM usuarios WHERE LOWER(TRIM(usuario)) = LOWER(?) OR LOWER(TRIM(email)) = LOWER(?)");
    $stmt->bind_param("ss", $identificador, $identificador);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
        echo json_encode([
            "success" => false,
            "message" => "La cuenta '" . $identificador . "' no existe."
        ]);
        $stmt->close();
        exit;
    }

    $row = $resultado->fetch_assoc();
    $stmt->close();

    if (empty($row["pregunta_seguridad"])) {
        echo json_encode([
            "success" => false,
            "message" => "Esta cuenta no tiene configurada una pregunta secreta. Podés usar la recuperación por código al correo."
        ]);
        exit;
    }

    echo json_encode([
        "success" => true,
        "usuario" => $row["usuario"],
        "pregunta" => $row["pregunta_seguridad"]
    ]);
    exit;
}

if ($accion === "restablecer") {

    $respuesta = isset($datos["respuesta"]) ? trim($datos["respuesta"]) : "";
    $nueva_password = isset($datos["nueva_password"]) ? $datos["nueva_password"] : "";

    if (empty($identificador) || empty($respuesta) || empty($nueva_password)) {
        echo json_encode([
            "success" => false,
            "message" => "Completá todos los campos."
        ]);
        exit;
    }

    if (strlen($nueva_password) < 4) {
        echo json_encode([
            "success" => false,
            "message" => "La nueva contraseña debe tener al menos 4 caracteres."
        ]);
        exit;
    }

    $stmt = $conexion->prepare("SELECT id, usuario, respuesta_seguridad FROM usuarios WHERE LOWER(TRIM(usuario)) = LOWER(?) OR LOWER(TRIM(email)) = LOWER(?)");
    $stmt->bind_param("ss", $identificador, $identificador);
    $stmt->execute();
    $resultado = $stmt->get_result();

    if ($resultado->num_rows === 0) {
        echo json_encode([
            "success" => false,
            "message" => "La cuenta no existe."
        ]);
        $stmt->close();
        exit;
    }

    $row = $resultado->fetch_assoc();
    $stmt->close();

    if (mb_strtolower(trim($row["respuesta_seguridad"]), "UTF-8") !== mb_strtolower(trim($respuesta), "UTF-8")) {
        echo json_encode([
            "success" => false,
            "message" => "La respuesta de seguridad es incorrecta."
        ]);
        exit;
    }

    $nuevoHash = password_hash($nueva_password, PASSWORD_DEFAULT);
    $stmtUpdate = $conexion->prepare("UPDATE usuarios SET password = ?, codigo_recuperacion = NULL, codigo_expira = NULL WHERE id = ?");
    $stmtUpdate->bind_param("si", $nuevoHash, $row["id"]);

    if ($stmtUpdate->execute()) {
        $claveUser = strtolower($row["usuario"]);
        if (isset($_SESSION["login_intentos"][$claveUser])) {
            unset($_SESSION["login_intentos"][$claveUser]);
        }

        echo json_encode([
            "success" => true,
            "message" => "¡Contraseña restablecida con éxito! Ya podés iniciar sesión."
        ]);
    } else {
        echo json_encode([
            "success" => false,
            "message" => "Error al actualizar la contraseña. Intentá de nuevo."
        ]);
    }

    $stmtUpdate->close();
    exit;
}

echo json_encode([
    "success" => false,
    "message" => "Acción no válida."
]);
?>