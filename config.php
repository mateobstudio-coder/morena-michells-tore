<?php
// ==================================================
// CONFIGURACIÓN GLOBAL - MICHELL STORE
// ==================================================

// 1. Email del Administrador para recibir avisos de compras
define("ADMIN_EMAIL", "morenamichell.storem@gmail.com");

// 2. Mercado Pago Access Token
// Puedes usar tu Token de Prueba (TEST-...) o tu Token de Producción (APP_USR-...)
// Obtenlo desde https://www.mercadopago.com.ar/developers/panel/app
define("MP_ACCESS_TOKEN", "APP_USR-5660177594931228-100710-725e042869a90e1ade8944dce66654e1-3746889522");

// 3. Moneda
define("MP_CURRENCY", "ARS");

// 4. URL Base de tu tienda
define("BASE_URL", "https://celebrated-mochi-612381.netlify.app/");

// 5. Configuración de Correo SMTP (Gmail)
define("SMTP_HOST", "smtp.gmail.com");
define("SMTP_PORT", 587);
define("SMTP_USER", "morenamichell.storem@gmail.com");
// Contraseña de aplicación generada en tu cuenta de Google (Seguridad -> Contraseñas de aplicaciones)
define("SMTP_PASS", "tu_contraseña_de_aplicacion");
?>

