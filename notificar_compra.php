<?php
require_once __DIR__ . "/config.php";

function enviarAvisoCompra($pedido) {
    $destinatario = ADMIN_EMAIL;
    $asunto = "🛍️ ¡Nueva compra realizada en Michell Store! - Pedido #" . $pedido["id"];

    $totalFormateado = number_format($pedido["total"], 0, ',', '.');
    $usuario = htmlspecialchars($pedido["usuario"]);
    $fecha = $pedido["fecha"] ?? date("d/m/Y H:i:s");
    $paymentId = $pedido["payment_id"] ?? "N/A";

    $detalles = is_array($pedido["detalles"]) ? $pedido["detalles"] : json_decode($pedido["detalles_json"] ?? "[]", true);

    $filasProductos = "";
    if (!empty($detalles)) {
        foreach ($detalles as $item) {
            $nombre = htmlspecialchars($item["nombre"] ?? "Producto");
            $cant = intval($item["cantidad"] ?? 1);
            $precio = number_format($item["precio"] ?? 0, 0, ',', '.');
            $subtotal = number_format(($item["precio"] ?? 0) * $cant, 0, ',', '.');
            $filasProductos .= "
                <tr>
                    <td style='padding: 10px; border-bottom: 1px solid #EEE;'>{$nombre}</td>
                    <td style='padding: 10px; border-bottom: 1px solid #EEE; text-align: center;'>{$cant}</td>
                    <td style='padding: 10px; border-bottom: 1px solid #EEE; text-align: right;'>\${$precio}</td>
                    <td style='padding: 10px; border-bottom: 1px solid #EEE; text-align: right; font-weight: bold;'>\${$subtotal}</td>
                </tr>
            ";
        }
    }

    $cuerpoHtml = "
    <!DOCTYPE html>
    <html lang='es'>
    <head>
        <meta charset='UTF-8'>
        <style>
            body { font-family: 'Poppins', Arial, sans-serif; background-color: #FFE5E1; margin: 0; padding: 20px; color: #3B151B; }
            .card { background: #FFFFFF; max-width: 600px; margin: 0 auto; border-radius: 16px; padding: 30px; box-shadow: 0 4px 15px rgba(0,0,0,0.08); }
            .header { text-align: center; border-bottom: 2px solid #FFE5E1; padding-bottom: 20px; margin-bottom: 20px; }
            .header h1 { color: #E96B6B; margin: 0; font-size: 26px; }
            .badge-success { background: #D4EDDA; color: #155724; padding: 8px 16px; border-radius: 20px; font-weight: bold; display: inline-block; margin-top: 10px; }
            .info-box { background: #FFF8F7; border-left: 4px solid #E96B6B; padding: 15px; border-radius: 8px; margin-bottom: 20px; font-size: 14px; }
            table { width: 100%; border-collapse: collapse; margin-bottom: 20px; font-size: 14px; }
            th { background: #FAF2F2; color: #3B151B; padding: 10px; text-align: left; }
            .total-box { text-align: right; font-size: 18px; font-weight: bold; color: #8B2635; padding: 15px 0; border-top: 2px solid #FFE5E1; }
            .footer { text-align: center; color: #888; font-size: 12px; margin-top: 20px; }
        </style>
    </head>
    <body>
        <div class='card'>
            <div class='header'>
                <h1>Michell Store</h1>
                <div class='badge-success'>🎉 ¡Pago Acreditado!</div>
            </div>
            
            <div class='info-box'>
                <p><strong>👤 Comprador:</strong> {$usuario}</p>
                <p><strong>📅 Fecha:</strong> {$fecha}</p>
                <p><strong>💳 N° Operación Mercado Pago:</strong> {$paymentId}</p>
            </div>

            <h3>📦 Resumen del Pedido:</h3>
            <table>
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th style='text-align: center;'>Cant.</th>
                        <th style='text-align: right;'>Precio</th>
                        <th style='text-align: right;'>Subtotal</th>
                    </tr>
                </thead>
                <tbody>
                    {$filasProductos}
                </tbody>
            </table>

            <div class='total-box'>
                Total Cobrado: \${$totalFormateado} ARS
            </div>

            <div class='footer'>
                Este es un mensaje automático de tu tienda Michell Store.
            </div>
        </div>
    </body>
    </html>
    ";

    $headers  = "MIME-Version: 1.0\r\n";
    $headers .= "Content-type: text/html; charset=UTF-8\r\n";
    $headers .= "From: Michell Store <no-reply@michellstore.com>\r\n";
    $headers .= "Reply-To: " . ADMIN_EMAIL . "\r\n";
    $headers .= "X-Mailer: PHP/" . phpversion();

    // Enviar correo
    @mail($destinatario, $asunto, $cuerpoHtml, $headers);

    // Guardar registro de aviso en archivo log para auditoría
    $logDir = __DIR__ . "/logs";
    if (!file_exists($logDir)) {
        @mkdir($logDir, 0777, true);
    }
    $logMsg = date("Y-m-d H:i:s") . " | Compra Pedido #" . $pedido["id"] . " | Total: $" . $pedido["total"] . " | Usuario: " . $usuario . " | Notificado a: " . $destinatario . PHP_EOL;
    @file_put_contents($logDir . "/compras.log", $logMsg, FILE_APPEND);

    return true;
}
?>

