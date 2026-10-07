<?php

header("Content-Type: application/json; charset=UTF-8");

require_once __DIR__ . "/conexion.php";


if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$datos = json_decode(
    file_get_contents("php://input"),
    true
);


$producto_id =
    isset($datos["producto_id"])
    ? intval($datos["producto_id"])
    : 0;


$usuario =
    isset($_SESSION["usuario"])
    ? $_SESSION["usuario"]
    : (isset($datos["usuario"]) ? trim($datos["usuario"]) : "");


$puntuacion =
    isset($datos["puntuacion"])
    ? intval($datos["puntuacion"])
    : 0;



/*
==================================================
VALIDACIONES
==================================================
*/


if ($producto_id <= 0) {

    echo json_encode([
        "success" => false,
        "message" => "Producto inválido."
    ]);

    exit;

}


if ($usuario === "") {

    echo json_encode([
        "success" => false,
        "message" => "Debes iniciar sesión para valorar."
    ]);

    exit;

}


if (
    $puntuacion < 1 ||
    $puntuacion > 5
) {

    echo json_encode([
        "success" => false,
        "message" => "La puntuación debe estar entre 1 y 5."
    ]);

    exit;

}



/*
==================================================
COMPROBAR SI YA VALORÓ
==================================================
*/

$sql = "
    SELECT id
    FROM valoraciones
    WHERE producto_id = ?
    AND usuario = ?
";


$stmt = $conexion->prepare($sql);

$stmt->bind_param(
    "is",
    $producto_id,
    $usuario
);

$stmt->execute();

$resultado =
    $stmt->get_result();


if ($resultado->num_rows > 0) {

    echo json_encode([
        "success" => false,
        "message" => "Ya has valorado este producto."
    ]);

    $stmt->close();

    $conexion->close();

    exit;

}


$stmt->close();



/*
==================================================
GUARDAR VALORACIÓN
==================================================
*/

$sql = "
    INSERT INTO valoraciones
    (
        producto_id,
        usuario,
        puntuacion
    )
    VALUES (?, ?, ?)
";


$stmt =
    $conexion->prepare($sql);


$stmt->bind_param(
    "isi",
    $producto_id,
    $usuario,
    $puntuacion
);


if ($stmt->execute()) {

    echo json_encode([
        "success" => true,
        "message" => "¡Gracias por tu valoración!"
    ]);

}

else {

    echo json_encode([
        "success" => false,
        "message" => "No se pudo guardar la valoración."
    ]);

}


$stmt->close();

$conexion->close();

?>