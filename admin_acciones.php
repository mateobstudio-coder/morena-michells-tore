<?php
header("Content-Type: application/json; charset=UTF-8");

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// 1. Proteger endpoint: Solo Administradores
if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "admin") {
    http_response_code(403);
    echo json_encode([
        "success" => false,
        "message" => "Acceso no autorizado. Se requieren permisos de administrador."
    ]);
    exit;
}

require_once __DIR__ . "/conexion.php";

$accion = $_POST["accion"] ?? $_GET["accion"] ?? "";

switch ($accion) {

    // ==========================================
    // LISTAR TODOS LOS PRODUCTOS (INCLUYE OCULTOS)
    // ==========================================
    case "listar":
        $sql = "SELECT * FROM productos ORDER BY id DESC";
        $res = $conexion->query($sql);
        $productos = [];
        while ($fila = $res->fetch_assoc()) {
            $productos[] = $fila;
        }
        echo json_encode([
            "success" => true,
            "productos" => $productos
        ]);
        break;

    // ==========================================
    // CREAR O ACTUALIZAR PRODUCTO
    // ==========================================
    case "guardar":
        $id = isset($_POST["id"]) ? intval($_POST["id"]) : 0;
        $nombre = isset($_POST["nombre"]) ? trim($_POST["nombre"]) : "";
        $categoria = isset($_POST["categoria"]) ? trim($_POST["categoria"]) : "";
        $precio = isset($_POST["precio"]) ? floatval($_POST["precio"]) : 0;
        $descripcion = isset($_POST["descripcion"]) ? trim($_POST["descripcion"]) : "";
        $etiqueta = isset($_POST["etiqueta"]) ? trim($_POST["etiqueta"]) : "";
        $visible = isset($_POST["visible"]) ? intval($_POST["visible"]) : 1;
        $imagen = isset($_POST["imagen_url"]) ? trim($_POST["imagen_url"]) : "";

        if (empty($nombre) || empty($categoria) || $precio <= 0) {
            echo json_encode([
                "success" => false,
                "message" => "Completá nombre, categoría y un precio válido."
            ]);
            exit;
        }

        // Manejo de subida de archivo de imagen
        if (isset($_FILES["imagen_archivo"]) && $_FILES["imagen_archivo"]["error"] === UPLOAD_ERR_OK) {
            $archivoTmp = $_FILES["imagen_archivo"]["tmp_name"];
            $nombreOriginal = $_FILES["imagen_archivo"]["name"];
            $extension = strtolower(pathinfo($nombreOriginal, PATHINFO_EXTENSION));

            $extensionesPermitidas = ["jpg", "jpeg", "png", "webp", "gif"];
            if (in_array($extension, $extensionesPermitidas)) {
                $dirDestino = __DIR__ . "/fts/";
                if (!file_exists($dirDestino)) {
                    mkdir($dirDestino, 0777, true);
                }
                $nombreLimpio = preg_replace("/[^a-zA-Z0-9_-]/", "_", pathinfo($nombreOriginal, PATHINFO_FILENAME));
                $nuevoNombreArchivo = $nombreLimpio . "_" . time() . "." . $extension;
                $rutaFinal = $dirDestino . $nuevoNombreArchivo;

                if (move_uploaded_file($archivoTmp, $rutaFinal)) {
                    $imagen = "fts/" . $nuevoNombreArchivo;
                }
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "Formato de imagen inválido. Usá JPG, PNG, WEBP o GIF."
                ]);
                exit;
            }
        }

        if ($id > 0) {
            // EDITAR PRODUCTO EXISTENTE
            if (!empty($imagen)) {
                $stmt = $conexion->prepare("UPDATE productos SET nombre = ?, categoria = ?, precio = ?, imagen = ?, descripcion = ?, etiqueta = ?, visible = ? WHERE id = ?");
                $stmt->bind_param("ssdsssii", $nombre, $categoria, $precio, $imagen, $descripcion, $etiqueta, $visible, $id);
            } else {
                $stmt = $conexion->prepare("UPDATE productos SET nombre = ?, categoria = ?, precio = ?, descripcion = ?, etiqueta = ?, visible = ? WHERE id = ?");
                $stmt->bind_param("ssdssii", $nombre, $categoria, $precio, $descripcion, $etiqueta, $visible, $id);
            }

            if ($stmt->execute()) {
                echo json_encode([
                    "success" => true,
                    "message" => "Producto actualizado correctamente."
                ]);
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "Error al actualizar producto: " . $stmt->error
                ]);
            }
            $stmt->close();
        } else {
            // CREAR NUEVO PRODUCTO
            if (empty($imagen)) {
                $imagen = "fts/boxer.jpeg"; // Imagen por defecto si no se especificó
            }

            $stmt = $conexion->prepare("INSERT INTO productos (nombre, categoria, precio, imagen, descripcion, etiqueta, visible) VALUES (?, ?, ?, ?, ?, ?, ?)");
            $stmt->bind_param("ssdsssi", $nombre, $categoria, $precio, $imagen, $descripcion, $etiqueta, $visible);

            if ($stmt->execute()) {
                echo json_encode([
                    "success" => true,
                    "message" => "¡Producto creado con éxito!",
                    "id" => $stmt->insert_id
                ]);
            } else {
                echo json_encode([
                    "success" => false,
                    "message" => "Error al crear producto: " . $stmt->error
                ]);
            }
            $stmt->close();
        }
        break;

    // ==========================================
    // CAMBIAR VISIBILIDAD (OCULTAR / MOSTRAR)
    // ==========================================
    case "toggle_visibilidad":
        $id = isset($_POST["id"]) ? intval($_POST["id"]) : 0;
        if ($id <= 0) {
            echo json_encode(["success" => false, "message" => "ID de producto inválido."]);
            exit;
        }

        $stmt = $conexion->prepare("UPDATE productos SET visible = 1 - visible WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            // Consultar nuevo estado
            $stmtEstado = $conexion->prepare("SELECT visible FROM productos WHERE id = ?");
            $stmtEstado->bind_param("i", $id);
            $stmtEstado->execute();
            $res = $stmtEstado->get_result()->fetch_assoc();
            $nuevoEstado = $res["visible"] ?? 1;

            echo json_encode([
                "success" => true,
                "message" => $nuevoEstado == 1 ? "El producto ahora es visible en la tienda." : "El producto ahora está oculto en la tienda.",
                "visible" => (int)$nuevoEstado
            ]);
            $stmtEstado->close();
        } else {
            echo json_encode(["success" => false, "message" => "Error al cambiar estado."]);
        }
        $stmt->close();
        break;

    // ==========================================
    // ELIMINAR PRODUCTO
    // ==========================================
    case "eliminar":
        $id = isset($_POST["id"]) ? intval($_POST["id"]) : 0;
        if ($id <= 0) {
            echo json_encode(["success" => false, "message" => "ID de producto inválido."]);
            exit;
        }

        $stmt = $conexion->prepare("DELETE FROM productos WHERE id = ?");
        $stmt->bind_param("i", $id);
        if ($stmt->execute()) {
            echo json_encode([
                "success" => true,
                "message" => "Producto eliminado permanentemente."
            ]);
        } else {
            echo json_encode(["success" => false, "message" => "Error al eliminar producto."]);
        }
        $stmt->close();
        break;

    // ==========================================
    // CAMBIAR CONTRASEÑA DE ADMINISTRADOR
    // ==========================================
    case "cambiar_password":
        $passwordNueva = isset($_POST["password_nueva"]) ? trim($_POST["password_nueva"]) : "";
        $adminUsuario = $_SESSION["usuario"] ?? "";

        if (strlen($passwordNueva) < 4) {
            echo json_encode([
                "success" => false,
                "message" => "La nueva contraseña debe tener al menos 4 caracteres."
            ]);
            exit;
        }

        $nuevoHash = password_hash($passwordNueva, PASSWORD_DEFAULT);
        $stmt = $conexion->prepare("UPDATE usuarios SET password = ? WHERE usuario = ?");
        $stmt->bind_param("ss", $nuevoHash, $adminUsuario);

        if ($stmt->execute()) {
            echo json_encode([
                "success" => true,
                "message" => "¡Contraseña actualizada con éxito!"
            ]);
        } else {
            echo json_encode([
                "success" => false,
                "message" => "Error al actualizar contraseña: " . $stmt->error
            ]);
        }
        $stmt->close();
        break;

    default:
        echo json_encode([
            "success" => false,
            "message" => "Acción no reconocida."
        ]);
        break;
}

$conexion->close();
?>

