<?php

header("Content-Type: application/json; charset=UTF-8");


// ==================================================
// CLAVE SECRETA DE CLOUDFLARE TURNSTILE
// ==================================================

$secretKey = "0x4AAAAAAETdB5BtuJvNImeNSG_tHSNFkYc";


// ==================================================
// RECIBIR DATOS
// ==================================================

$input = json_decode(
    file_get_contents("php://input"),
    true
);


$token = $input["token"] ?? "";


// ==================================================
// COMPROBAR TOKEN
// ==================================================

if (empty($token)) {

    echo json_encode([
        "success" => false,
        "message" => "No se recibió el token de Cloudflare."
    ]);

    exit;

}


// ==================================================
// ENVIAR A CLOUDFLARE
// ==================================================

$url =
    "https://challenges.cloudflare.com/turnstile/v0/siteverify";


$data = [

    "secret" =>
        $secretKey,

    "response" =>
        $token

];


// ==================================================
// CURL
// ==================================================

$ch = curl_init($url);


curl_setopt(
    $ch,
    CURLOPT_RETURNTRANSFER,
    true
);


curl_setopt(
    $ch,
    CURLOPT_POST,
    true
);


curl_setopt(
    $ch,
    CURLOPT_POSTFIELDS,
    http_build_query($data)
);


curl_setopt(
    $ch,
    CURLOPT_TIMEOUT,
    10
);


$response =
    curl_exec($ch);


$curlError =
    curl_error($ch);


curl_close($ch);


// ==================================================
// ERROR CURL
// ==================================================

if ($response === false) {

    echo json_encode([

        "success" => false,

        "message" =>
            "No se pudo conectar con Cloudflare."

    ]);

    exit;

}


// ==================================================
// RESPUESTA CLOUDFLARE
// ==================================================

$result =
    json_decode(
        $response,
        true
    );


// ==================================================
// VERIFICACIÓN
// ==================================================

if (
    isset($result["success"]) &&
    $result["success"] === true
) {

    echo json_encode([

        "success" => true,

        "message" =>
            "Verificación correcta."

    ]);

}

else {

    echo json_encode([

        "success" => false,

        "message" =>
            "La verificación de Cloudflare falló."

    ]);

}

?>