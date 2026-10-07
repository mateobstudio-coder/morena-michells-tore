<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/conexion.php";
require_once __DIR__ . "/config.php";
require_once __DIR__ . "/notificar_compra.php";

// Parámetros de retorno de Mercado Pago
$paymentId = $_GET["payment_id"] ?? $_GET["collection_id"] ?? null;
$status = $_GET["status"] ?? $_GET["collection_status"] ?? null;
$externalRef = $_GET["external_reference"] ?? null;

// =========================================================================
// 1. SI NO VIENE DE MERCADO PAGO O NO SE HA INICIADO EL PAGO
// Redirigir a Mercado Pago para proceder con el pago
// =========================================================================
if (empty($paymentId) && empty($status)) {
    // Si no hay sesión de pedido pendiente o carrito, volver a la tienda
    if (empty($_SESSION["pedido_pendiente"])) {
        header("Location: index.php");
        exit;
    }

    // Redirigir a Mercado Pago si existe una preferencia pendiente
    $pedidoPendiente = $_SESSION["pedido_pendiente"];
    header("Location: index.php?abrir_carrito=1");
    exit;
}

// =========================================================================
// 2. CASO: PAGO RECHAZADO O CANCELADO EN MERCADO PAGO
// =========================================================================
if ($status === "failure" || $status === "rejected" || $status === "cancelled") {
    ?>
    <!DOCTYPE html>
    <html lang="es">
    <head>
        <meta charset="UTF-8">
        <meta name="viewport" content="width=device-width, initial-scale=1.0">
        <title>Pago no completado - Michell Store</title>
        <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
        <style>
            * { box-sizing: border-box; margin: 0; padding: 0; }
            body {
                font-family: 'Poppins', sans-serif;
                background-color: #FFE5E1;
                color: #3B151B;
                min-height: 100vh;
                display: flex;
                justify-content: center;
                align-items: center;
                padding: 20px;
            }
            .success-card {
                background: white;
                max-width: 500px;
                width: 100%;
                border-radius: 20px;
                padding: 35px 30px;
                text-align: center;
                box-shadow: 0 10px 30px rgba(91, 25, 35, 0.12);
            }
            .icon-circle {
                width: 80px;
                height: 80px;
                background: #F8D7DA;
                color: #721C24;
                font-size: 40px;
                display: flex;
                align-items: center;
                justify-content: center;
                border-radius: 50%;
                margin: 0 auto 20px;
            }
            h1 { color: #D32F2F; font-size: 24px; margin-bottom: 10px; }
            p { color: #666; font-size: 14px; line-height: 1.6; margin-bottom: 25px; }
            .btn-home {
                background: #E96B6B;
                color: white;
                text-decoration: none;
                padding: 13px 25px;
                border-radius: 10px;
                font-weight: 600;
                display: inline-block;
                transition: 0.2s ease;
            }
            .btn-home:hover { background: #6F1D2A; }
        </style>
    </head>
    <body>
        <div class="success-card">
            <div class="icon-circle">✕</div>
            <h1>Pago no completado</h1>
            <p>La operación fue cancelada o rechazada en Mercado Pago. No se ha realizado ningún cobro ni registro de pedido.</p>
            <a href="index.php" class="btn-home">← Volver al Carrito e Intentar de Nuevo</a>
        </div>
    </body>
    </html>
    <?php
    exit;
}

// =========================================================================
// 3. CASO: PAGO EXITOSO / APROBADO EN MERCADO PAGO
// Inserción en Base de Datos + Notificación por Correo + Mostrar Success Card
// =========================================================================
$esAprobado = ($status === "approved" || $status === "aprobado");
$usuario = $_SESSION["usuario"] ?? "Cliente";
$pedidoPendiente = $_SESSION["pedido_pendiente"] ?? null;

$itemsArray = [];
$total = 0;
$detallesJson = "[]";

if ($pedidoPendiente && isset($pedidoPendiente["items"])) {
    $itemsArray = $pedidoPendiente["items"];
    $total = floatval($pedidoPendiente["total"] ?? 0);
    $usuario = $pedidoPendiente["usuario"] ?? $usuario;
    $detallesJson = json_encode($itemsArray, JSON_UNESCAPED_UNICODE);
} else {
    // Si no está en sesión pero vino el total en la URL
    $total = floatval($_GET["total"] ?? 0);
}

$pedidoId = 0;
$yaRegistrado = false;

if ($esAprobado && !empty($paymentId)) {
    // Verificar si esta operación ya fue registrada antes (evita duplicados si el usuario recarga la página)
    $stmtCheck = $conexion->prepare("SELECT id, total, detalles_json, usuario FROM pedidos WHERE payment_id = ? LIMIT 1");
    $stmtCheck->bind_param("s", $paymentId);
    $stmtCheck->execute();
    $resCheck = $stmtCheck->get_result();

    if ($resCheck->num_rows > 0) {
        // Pedido ya existente
        $rowPedido = $resCheck->fetch_assoc();
        $pedidoId = $rowPedido["id"];
        $total = floatval($rowPedido["total"]);
        $usuario = $rowPedido["usuario"];
        $yaRegistrado = true;
    } else {
        // INSERCIÓN A LA BASE DE DATOS
        $stmtInsert = $conexion->prepare("INSERT INTO pedidos (payment_id, status, usuario, total, detalles_json) VALUES (?, ?, ?, ?, ?)");
        $stmtInsert->bind_param("sssds", $paymentId, $status, $usuario, $total, $detallesJson);
        if ($stmtInsert->execute()) {
            $pedidoId = $stmtInsert->insert_id;
        }
        $stmtInsert->close();

        // Enviar notificación por correo a morenamichell.storem@gmail.com
        $datosAviso = [
            "id" => $pedidoId > 0 ? $pedidoId : time(),
            "payment_id" => $paymentId,
            "usuario" => $usuario,
            "total" => $total,
            "detalles" => $itemsArray,
            "detalles_json" => $detallesJson,
            "fecha" => date("d/m/Y H:i:s")
        ];
        enviarAvisoCompra($datosAviso);

        // Limpiar el pedido pendiente de la sesión una vez insertado
        unset($_SESSION["pedido_pendiente"]);
    }
    $stmtCheck->close();
}
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>¡Compra Exitosa! - Michell Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #FFE5E1;
            color: #3B151B;
            min-height: 100vh;
            display: flex;
            justify-content: center;
            align-items: center;
            padding: 20px;
        }
        .success-card {
            background: white;
            max-width: 550px;
            width: 100%;
            border-radius: 20px;
            padding: 35px 30px;
            text-align: center;
            box-shadow: 0 10px 30px rgba(91, 25, 35, 0.12);
            animation: fadeIn 0.4s ease-out;
        }
        @keyframes fadeIn {
            from { opacity: 0; transform: translateY(15px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .icon-circle {
            width: 80px;
            height: 80px;
            background: #D4EDDA;
            color: #28a745;
            font-size: 40px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 50%;
            margin: 0 auto 20px;
            box-shadow: 0 4px 15px rgba(40, 167, 69, 0.2);
        }
        h1 {
            color: #6F1D2A;
            font-size: 26px;
            margin-bottom: 10px;
        }
        p {
            color: #666;
            font-size: 14px;
            line-height: 1.6;
            margin-bottom: 20px;
        }
        .details-box {
            background: #FFF8F7;
            border: 1px solid #F0B7B7;
            border-radius: 12px;
            padding: 18px;
            text-align: left;
            margin-bottom: 25px;
            font-size: 14px;
        }
        .details-box p {
            margin-bottom: 8px;
            color: #3B151B;
        }
        .details-box p:last-child {
            margin-bottom: 0;
            font-weight: 700;
            color: #6F1D2A;
            font-size: 16px;
        }
        .btn-home {
            background: #E96B6B;
            color: white;
            text-decoration: none;
            padding: 14px 28px;
            border-radius: 10px;
            font-weight: 600;
            font-size: 15px;
            display: inline-block;
            transition: all 0.2s ease;
            box-shadow: 0 4px 12px rgba(233, 107, 107, 0.35);
        }
        .btn-home:hover {
            background: #6F1D2A;
            transform: translateY(-2px);
            box-shadow: 0 6px 16px rgba(111, 29, 42, 0.35);
        }
    </style>
</head>
<body>
    <div class="success-card">
        <div class="icon-circle">✓</div>
        <h1>¡Pago Acreditado con Éxito!</h1>
        <p>Gracias por tu compra en <strong>Michell Store</strong>. Tu pago fue procesado correctamente en Mercado Pago y el pedido ha sido confirmado.</p>

        <div class="details-box">
            <p><strong>🛍️ Comprador:</strong> <?php echo htmlspecialchars($usuario); ?></p>
            <p><strong>💳 N° Operación Mercado Pago:</strong> <?php echo htmlspecialchars($paymentId ?? 'N/A'); ?></p>
            <p><strong>📦 N° Pedido en Sistema:</strong> #<?php echo htmlspecialchars($pedidoId > 0 ? $pedidoId : ($paymentId ?? '1')); ?></p>
            <p><strong>📧 Notificado a:</strong> <?php echo ADMIN_EMAIL; ?></p>
            <?php if ($total > 0): ?>
                <p><strong>💰 Total Abonado:</strong> $<?php echo number_format($total, 0, ',', '.'); ?> ARS</p>
            <?php endif; ?>
        </div>

        <a href="index.php" class="btn-home" onclick="limpiarCarrito()">← Volver a la Tienda</a>
    </div>

    <script>
        // Limpiar el carrito en el navegador tras la compra exitosa
        localStorage.removeItem("carrito");
        function limpiarCarrito() {
            localStorage.removeItem("carrito");
        }
    </script>
</body>
</html>
