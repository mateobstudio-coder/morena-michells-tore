<?php

header("Content-Type: application/json; charset=UTF-8");


// ==================================================
// CONEXIÓN A LA BASE DE DATOS
// ==================================================

require_once __DIR__ . "/conexion.php";


// ==================================================
// RECIBIR DATOS
// ==================================================

$datos = json_decode(
    file_get_contents("php://input"),
    true
);


$producto_id =
    isset($datos["producto_id"])
    ? intval($datos["producto_id"])
    : 0;


$usuario =
    isset($datos["usuario"])
    ? trim($datos["usuario"])
    : "";


$puntuacion =
    isset($datos["puntuacion"])
    ? intval($datos["puntuacion"])
    : 0;


$comentario =
    isset($datos["comentario"])
    ? trim($datos["comentario"])
    : "";


// ==================================================
// VALIDACIONES
// ==================================================

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
        "message" => "Usuario inválido."
    ]);

    exit;

}


if (
    $puntuacion < 1 ||
    $puntuacion > 5
) {

    echo json_encode([
        "success" => false,
        "message" => "La puntuación debe estar entre 1 y 5 estrellas."
    ]);

    exit;

}


if ($comentario === "") {

    echo json_encode([
        "success" => false,
        "message" => "Escribí un comentario."
    ]);

    exit;

}


// ==================================================
// COMPROBAR QUE EL PRODUCTO EXISTE
// ==================================================

$sqlProducto = "
    SELECT id
    FROM productos
    WHERE id = ?
";


$stmtProducto =
    $conexion->prepare(
        $sqlProducto
    );


$stmtProducto->bind_param(
    "i",
    $producto_id
);


$stmtProducto->execute();


$resultadoProducto =
    $stmtProducto->get_result();


if (
    $resultadoProducto->num_rows === 0
) {

    echo json_encode([
        "success" => false,
        "message" => "El producto no existe."
    ]);

    exit;

}


// ==================================================
// GUARDAR VALORACIÓN
// ==================================================

$sqlValoracion = "
    INSERT INTO valoraciones
    (
        producto_id,
        usuario,
        puntuacion
    )
    VALUES
    (
        ?,
        ?,
        ?
    )
";


$stmtValoracion =
    $conexion->prepare(
        $sqlValoracion
    );


$stmtValoracion->bind_param(
    "isi",
    $producto_id,
    $usuario,
    $puntuacion
);


if (
    !$stmtValoracion->execute()
) {

    echo json_encode([
        "success" => false,
        "message" => "No se pudo guardar la valoración."
    ]);

    exit;

}


// ==================================================
// GUARDAR COMENTARIO
// ==================================================

$sqlComentario = "
    INSERT INTO comentarios
    (
        producto_id,
        usuario,
        comentario
    )
    VALUES
    (
        ?,
        ?,
        ?
    )
";


$stmtComentario =
    $conexion->prepare(
        $sqlComentario
    );


$stmtComentario->bind_param(
    "iss",
    $producto_id,
    $usuario,
    $comentario
);


if (
    !$stmtComentario->execute()
) {

    echo json_encode([
        "success" => false,
        "message" => "La valoración se guardó, pero no se pudo guardar el comentario."
    ]);

    exit;

}


// ==================================================
// RESPUESTA
// ==================================================

echo json_encode([
    "success" => true,
    "message" => "¡Opinión publicada correctamente!"
]);


$conexion->close();

?>