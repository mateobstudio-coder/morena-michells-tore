<?php
header("Content-Type: text/html; charset=UTF-8");

// Conexión inicial sin seleccionar base de datos para poder crearla
$host = "localhost";
$user = "root";
$pass = "";

$conn = new mysqli($host, $user, $pass);

if ($conn->connect_error) {
    die("<h3>❌ Error de conexión al servidor MySQL: " . $conn->connect_error . "</h3>");
}

$conn->set_charset("utf8mb4");

// 1. Crear base de datos si no existe
$sqlDb = "CREATE DATABASE IF NOT EXISTS `michell_store` DEFAULT CHARACTER SET utf8mb4 COLLATE utf8mb4_unicode_ci";
if (!$conn->query($sqlDb)) {
    die("<h3>❌ Error al crear la base de datos: " . $conn->error . "</h3>");
}

$conn->select_db("michell_store");

// 2. Crear tabla usuarios
$sqlUsuarios = "CREATE TABLE IF NOT EXISTS `usuarios` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `usuario` VARCHAR(50) NOT NULL UNIQUE,
    `password` VARCHAR(255) NOT NULL,
    `rol` ENUM('cliente', 'admin') NOT NULL DEFAULT 'cliente',
    `fecha_registro` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$conn->query($sqlUsuarios);

// 3. Crear tabla productos (con campo visible)
$sqlProductos = "CREATE TABLE IF NOT EXISTS `productos` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `nombre` VARCHAR(150) NOT NULL,
    `categoria` VARCHAR(50) NOT NULL,
    `precio` DECIMAL(10,2) NOT NULL DEFAULT 0.00,
    `imagen` VARCHAR(255) NOT NULL,
    `descripcion` TEXT NULL,
    `etiqueta` VARCHAR(50) NULL,
    `visible` TINYINT(1) NOT NULL DEFAULT 1,
    `fecha_creacion` DATETIME DEFAULT CURRENT_TIMESTAMP
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$conn->query($sqlProductos);

// 4. Crear tabla valoraciones
$sqlValoraciones = "CREATE TABLE IF NOT EXISTS `valoraciones` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `producto_id` INT NOT NULL,
    `usuario` VARCHAR(50) NOT NULL,
    `puntuacion` INT NOT NULL,
    `fecha` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX (`producto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$conn->query($sqlValoraciones);

// 5. Crear tabla comentarios
$sqlComentarios = "CREATE TABLE IF NOT EXISTS `comentarios` (
    `id` INT AUTO_INCREMENT PRIMARY KEY,
    `producto_id` INT NOT NULL,
    `usuario` VARCHAR(50) NOT NULL,
    `comentario` TEXT NOT NULL,
    `fecha` DATETIME DEFAULT CURRENT_TIMESTAMP,
    INDEX (`producto_id`)
) ENGINE=InnoDB DEFAULT CHARSET=utf8mb4;";
$conn->query($sqlComentarios);

// 6. Insertar usuario Administrador por defecto si no existe
$adminUser = "admin";
$adminPassPlain = "admin123";
$adminHash = password_hash($adminPassPlain, PASSWORD_DEFAULT);

$stmtCheckAdmin = $conn->prepare("SELECT id FROM usuarios WHERE usuario = ?");
$stmtCheckAdmin->bind_param("s", $adminUser);
$stmtCheckAdmin->execute();
$resAdmin = $stmtCheckAdmin->get_result();

if ($resAdmin->num_rows === 0) {
    $stmtInsertAdmin = $conn->prepare("INSERT INTO usuarios (usuario, password, rol) VALUES (?, ?, 'admin')");
    $stmtInsertAdmin->bind_param("ss", $adminUser, $adminHash);
    $stmtInsertAdmin->execute();
    $adminCreado = true;
} else {
    $adminCreado = false;
}

// 7. Cargar productos iniciales si la tabla productos está vacía
$resCount = $conn->query("SELECT COUNT(*) AS total FROM productos");
$rowCount = $resCount->fetch_assoc();

if ($rowCount['total'] == 0) {
    $productosIniciales = [
        [
            "nombre" => "Top Deportivo Negro",
            "categoria" => "remeras",
            "precio" => 12500,
            "imagen" => "fts/top d N.jpeg",
            "descripcion" => "Top deportivo de alta sujeción y tela transpirable.",
            "etiqueta" => "NUEVO"
        ],
        [
            "nombre" => "Top Deportivo Mostaza",
            "categoria" => "remeras",
            "precio" => 12500,
            "imagen" => "fts/top d M.jpeg",
            "descripcion" => "Top deportivo cómodo con diseño anatómico.",
            "etiqueta" => "OFERTA"
        ],
        [
            "nombre" => "Calza Push Up Verde",
            "categoria" => "calzas",
            "precio" => 21000,
            "imagen" => "fts/calza puhs verde.jpeg",
            "descripcion" => "Calza deportiva push up con cintura alta modeladora.",
            "etiqueta" => "MÁS VENDIDO"
        ],
        [
            "nombre" => "Calza Push Up Rosa",
            "categoria" => "calzas",
            "precio" => 21000,
            "imagen" => "fts/calza puhs rosa.jpeg",
            "descripcion" => "Calza suave al tacto y máxima elasticidad.",
            "etiqueta" => "OFERTA"
        ],
        [
            "nombre" => "Calza Push Up Naranja",
            "categoria" => "calzas",
            "precio" => 21000,
            "imagen" => "fts/calza puhs nara.jpeg",
            "descripcion" => "Calza deportiva color vibrante de secado rápido.",
            "etiqueta" => ""
        ],
        [
            "nombre" => "Short Pollera Negro",
            "categoria" => "calzas",
            "precio" => 18500,
            "imagen" => "fts/short pollera negro.jpeg",
            "descripcion" => "Short pollera con calza interna para máximo confort.",
            "etiqueta" => "TENDENCIA"
        ],
        [
            "nombre" => "Short Pollera Azul",
            "categoria" => "calzas",
            "precio" => 18500,
            "imagen" => "fts/short pollera azul.jpeg",
            "descripcion" => "Ideal para entrenamiento, running o uso casual.",
            "etiqueta" => ""
        ],
        [
            "nombre" => "Short Pollera Verde",
            "categoria" => "calzas",
            "precio" => 18500,
            "imagen" => "fts/short pollera verde.jpeg",
            "descripcion" => "Tela liviana y calce perfecto.",
            "etiqueta" => ""
        ],
        [
            "nombre" => "Conjunto Encaje",
            "categoria" => "interior",
            "precio" => 16500,
            "imagen" => "fts/encaje.jpeg",
            "descripcion" => "Conjunto de lencería de encaje fino y detalles delicados.",
            "etiqueta" => "DESTACADO"
        ],
        [
            "nombre" => "Conjunto Rojo Pasión",
            "categoria" => "interior",
            "precio" => 17000,
            "imagen" => "fts/conjunto rojo.jpeg",
            "descripcion" => "Conjunto sensual de microfibra y puntilla.",
            "etiqueta" => "OFERTA"
        ],
        [
            "nombre" => "Conjunto Blanco Clásico",
            "categoria" => "interior",
            "precio" => 16000,
            "imagen" => "fts/conjunto blanco.jpeg",
            "descripcion" => "Conjunto cómodo para uso diario en algodón y lycra.",
            "etiqueta" => ""
        ],
        [
            "nombre" => "Bombachas Pack",
            "categoria" => "interior",
            "precio" => 9500,
            "imagen" => "fts/bombachas.jpeg",
            "descripcion" => "Pack de bombachas surtidas en algodón suave.",
            "etiqueta" => "OFERTA"
        ],
        [
            "nombre" => "Colaless Regulable",
            "categoria" => "interior",
            "precio" => 4500,
            "imagen" => "fts/colales.jpeg",
            "descripcion" => "Colaless de microfibra con breteles regulables.",
            "etiqueta" => ""
        ],
        [
            "nombre" => "Boxer Algodón",
            "categoria" => "interior",
            "precio" => 7500,
            "imagen" => "fts/boxer.jpeg",
            "descripcion" => "Boxer anatómico de máxima comodidad.",
            "etiqueta" => ""
        ],
        [
            "nombre" => "Medias Bellota Gym",
            "categoria" => "medias",
            "precio" => 3500,
            "imagen" => "fts/media bellota gym.jpeg",
            "descripcion" => "Medias con diseño bordado de Las Chicas Superpoderosas.",
            "etiqueta" => "POPULAR"
        ],
        [
            "nombre" => "Medias Bombón Gym",
            "categoria" => "medias",
            "precio" => 3500,
            "imagen" => "fts/media bombon gym.jpeg",
            "descripcion" => "Medias deportivas de algodón reforzado.",
            "etiqueta" => ""
        ],
        [
            "nombre" => "Medias Burbuja Gym",
            "categoria" => "medias",
            "precio" => 3500,
            "imagen" => "fts/media burbuja gym.jpeg",
            "descripcion" => "Medias de algodón con puño elastizado antideslizante.",
            "etiqueta" => ""
        ],
        [
            "nombre" => "Medias Stitch",
            "categoria" => "medias",
            "precio" => 3500,
            "imagen" => "fts/media stich.jpeg",
            "descripcion" => "Medias con diseño temático de Stitch.",
            "etiqueta" => "OFERTA"
        ],
        [
            "nombre" => "Medias Jordan",
            "categoria" => "medias",
            "precio" => 4000,
            "imagen" => "fts/media jordan.jpeg",
            "descripcion" => "Medias urbanas deportivas acolchadas.",
            "etiqueta" => "NUEVO"
        ],
        [
            "nombre" => "Medias Adidas Clásicas",
            "categoria" => "medias",
            "precio" => 3800,
            "imagen" => "fts/media adidas.jpeg",
            "descripcion" => "Medias caña media con tres tiras clásicas.",
            "etiqueta" => ""
        ]
    ];

    $stmtInsertProd = $conn->prepare("INSERT INTO productos (nombre, categoria, precio, imagen, descripcion, etiqueta, visible) VALUES (?, ?, ?, ?, ?, ?, 1)");
    foreach ($productosIniciales as $p) {
        $stmtInsertProd->bind_param("ssdsss", $p['nombre'], $p['categoria'], $p['precio'], $p['imagen'], $p['descripcion'], $p['etiqueta']);
        $stmtInsertProd->execute();
    }
    $productosCargados = count($productosIniciales);
} else {
    $productosCargados = 0;
}

$conn->close();
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Configuración de Base de Datos - Michell Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;600;700&display=swap" rel="stylesheet">
    <style>
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #FFE5E1;
            color: #3B151B;
            display: flex;
            justify-content: center;
            align-items: center;
            min-height: 100vh;
            margin: 0;
            padding: 20px;
            box-sizing: border-box;
        }
        .card {
            background: white;
            padding: 30px;
            border-radius: 16px;
            max-width: 550px;
            box-shadow: 0 10px 30px rgba(0,0,0,0.1);
            text-align: center;
        }
        h1 { color: #E96B6B; margin-bottom: 10px; font-size: 24px; }
        .success-badge {
            background: #d4edda;
            color: #155724;
            padding: 10px 15px;
            border-radius: 8px;
            font-weight: 600;
            display: inline-block;
            margin-bottom: 20px;
        }
        .info-box {
            background: #FFF0ED;
            border-left: 4px solid #E96B6B;
            padding: 15px;
            text-align: left;
            border-radius: 6px;
            margin: 20px 0;
            font-size: 14px;
        }
        .btn {
            background: #E96B6B;
            color: white;
            padding: 12px 24px;
            border-radius: 8px;
            text-decoration: none;
            font-weight: 600;
            display: inline-block;
            transition: 0.2s;
            margin-top: 10px;
        }
        .btn:hover { background: #d35555; }
    </style>
</head>
<body>
    <div class="card">
        <h1>Michell Store</h1>
        <div class="success-badge">✅ Base de Datos y Tablas Listas</div>
        <p>Se crearon y verificaron las tablas: <strong>usuarios, productos, valoraciones, comentarios</strong>.</p>
        
        <div class="info-box">
            <strong>🔑 Cuenta de Administrador configurada:</strong><br>
            • <strong>Usuario:</strong> <code>admin</code><br>
            • <strong>Contraseña:</strong> <code>admin123</code><br>
            <small style="color: #666;">(Podrás cambiarla luego desde la base de datos o panel).</small>
        </div>

        <?php if ($productosCargados > 0): ?>
            <p>🛍️ Se importaron <strong><?php echo $productosCargados; ?> productos iniciales</strong>.</p>
        <?php endif; ?>

        <a href="index.php" class="btn">Ir a la Tienda</a>
    </div>
</body>
</html>

