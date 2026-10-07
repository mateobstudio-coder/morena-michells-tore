<?php
header("Content-Type: application/json; charset=UTF-8");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/config.php";

$datos = json_decode(file_get_contents("php://input"), true);
$itemsCarrito = $datos["items"] ?? [];

if (empty($itemsCarrito)) {
    echo json_encode(["success" => false, "message" => "El carrito está vacío."]);
    exit;
}

$mpItems = [];
$totalCalculado = 0;

foreach ($itemsCarrito as $item) {
    $precio = floatval($item["precio"] ?? 0);
    $cantidad = intval($item["cantidad"] ?? 1);
    $totalCalculado += ($precio * $cantidad);

    $mpItems[] = [
        "id" => (string)($item["id"] ?? "1"),
        "title" => (string)($item["nombre"] ?? "Producto Michell Store"),
        "description" => (string)($item["nombre"] ?? "Producto Michell Store"),
        "picture_url" => (string)(BASE_URL . "/" . ($item["imagen"] ?? "fts/boxer.jpeg")),
        "category_id" => "fashion",
        "quantity" => $cantidad,
        "currency_id" => MP_CURRENCY,
        "unit_price" => $precio
    ];
}

$usuario = $_SESSION["usuario"] ?? "Cliente";
$externalRef = "PEDIDO_" . time() . "_" . rand(100, 999);

// Guardar pedido pendiente en sesión antes de conectar con Mercado Pago
$_SESSION["pedido_pendiente"] = [
    "items" => $itemsCarrito,
    "total" => $totalCalculado,
    "usuario" => $usuario,
    "ref" => $externalRef
];

// Estructura de la Preferencia de Mercado Pago
$preferenceData = [
    "items" => $mpItems,
    "payer" => [
        "name" => $usuario,
        "email" => "morenamichell.storem@gmail.com"
    ],
    "back_urls" => [
        "success" => BASE_URL . "/pago_exitoso.php",
        "failure" => BASE_URL . "/pago_exitoso.php?status=failure",
        "pending" => BASE_URL . "/pago_exitoso.php?status=pending"
    ],
    "statement_descriptor" => "Michell Store",
    "external_reference" => $externalRef
];

// Llamar a la API oficial de Mercado Pago
$urlMP = "https://api.mercadopago.com/checkout/preferences";

$ch = curl_init($urlMP);
curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
curl_setopt($ch, CURLOPT_HTTPHEADER, [
    "Content-Type: application/json",
    "Authorization: Bearer " . MP_ACCESS_TOKEN
]);
curl_setopt($ch, CURLOPT_POST, true);
curl_setopt($ch, CURLOPT_POSTFIELDS, json_encode($preferenceData));
curl_setopt($ch, CURLOPT_TIMEOUT, 15);
curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, false);

$response = curl_exec($ch);
$httpCode = curl_getinfo($ch, CURLINFO_HTTP_CODE);
$curlError = curl_error($ch);
curl_close($ch);

$mpResponse = json_decode($response, true);

// Si la API de Mercado Pago generó el link de pago exitosamente
if (($httpCode === 200 || $httpCode === 201) && !empty($mpResponse["init_point"])) {
    echo json_encode([
        "success" => true,
        "init_point" => $mpResponse["init_point"],
        "sandbox_init_point" => $mpResponse["sandbox_init_point"] ?? $mpResponse["init_point"],
        "id" => $mpResponse["id"] ?? ""
    ]);
    exit;
}

// Si hubo un error en Mercado Pago, devolver el mensaje exacto
$errorMsg = "No se pudo conectar con Mercado Pago.";
if (!empty($curlError)) {
    $errorMsg .= " Error de conexión: " . $curlError;
} elseif (!empty($mpResponse["message"])) {
    $errorMsg .= " " . $mpResponse["message"];
} elseif (!empty($mpResponse["error"])) {
    $errorMsg .= " " . $mpResponse["error"];
}

echo json_encode([
    "success" => false,
    "message" => $errorMsg,
    "http_code" => $httpCode,
    "detalle" => $mpResponse
]);
