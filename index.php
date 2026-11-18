<!DOCTYPE html>
<html lang="es">

<head>

  <meta charset="UTF-8">

  <meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
  >

  <title>Morena Store</title>

  <link
    rel="icon"
    type="image/png"
    href="fts/favicon.png"
  >

  <link
    rel="stylesheet"
    href="style.css?v=<?php echo time(); ?>"
  >

  <link
    href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
    rel="stylesheet"
  >

  <!-- CLOUDFLARE TURNSTILE -->

  <script
    src="https://challenges.cloudflare.com/turnstile/v0/api.js"
    async
    defer
  ></script>

</head>


<body>


<!-- ==================================================
     HEADER
================================================== -->

<header class="header">


  <!-- HAMBURGUESA -->

  <button
    id="menu-btn"
    class="menu-btn"
    type="button"
    aria-label="Abrir menú"
  >
    ☰
  </button>


  <!-- LOGO -->

  <a
    href="index.php"
    class="logo-link"
    aria-label="Morena Store - Inicio"
  >
    <img
      src="fts/logo.png"
      alt="Morena Store"
      class="logo-img"
    >
    <h1 class="logo-seo-text">Morena Store</h1>
  </a>


  <!-- CARRITO -->

  <button
    id="cart-btn"
    class="cart-btn"
    type="button"
  >

    🛒

    <span id="cart-count">
      0
    </span>

  </button>


</header>



<!-- ==================================================
     FONDO MENÚ
================================================== -->

<div
  id="menu-overlay"
  class="menu-overlay"
></div>



<!-- ==================================================
     MENÚ LATERAL
================================================== -->

<aside
  id="side-menu"
  class="side-menu"
>


  <!-- CABECERA -->

  <div class="menu-header">

    <h2>
      Morena Store
    </h2>

    <button
      id="menu-close"
      class="menu-close"
      type="button"
    >
      ✕
    </button>

  </div>



  <!-- USUARIO -->

  <div
    id="menu-user"
    class="menu-user"
  >

    <div
      id="menu-user-icon"
      class="menu-user-icon"
    >
      👤
    </div>

    <div>

      <div
        id="menu-user-name"
        class="menu-user-name"
      >
        No has iniciado sesión
      </div>

      <div
        id="menu-user-status"
        class="menu-user-status"
      >
        Visitante
      </div>

    </div>

  </div>



  <!-- CONTENIDO MENÚ -->

  <div class="menu-content">


    <!-- INICIO -->

    <button
      class="menu-item menu-category"
      data-category="all"
      type="button"
    >
      🏠 Inicio
    </button>



    <!-- BUSCADOR -->

    <div class="menu-search-area">

      <button
        id="menu-search-btn"
        class="menu-item"
        type="button"
      >
        🔍 Buscar productos
      </button>


      <div
        id="menu-search-container"
        class="menu-search-container"
      >

        <input
          type="text"
          id="menu-search"
          placeholder="Buscar producto..."
          autocomplete="off"
        >


        <button
          id="close-search"
          type="button"
        >
          ✕
        </button>

      </div>

    </div>



    <!-- CATEGORÍAS -->

    <div class="menu-title">
      CATEGORÍAS
    </div>


    <button
      class="menu-item menu-category"
      data-category="all"
      type="button"
    >
      🛍️ Todos los productos
    </button>


    <button
      class="menu-item menu-category"
      data-category="remeras"
      type="button"
    >
      👕 Remeras
    </button>


    <button
      class="menu-item menu-category"
      data-category="interior"
      type="button"
    >
      🩲 Ropa Interior
    </button>


    <button
      class="menu-item menu-category"
      data-category="calzas"
      type="button"
    >
      👖 Calzas
    </button>


    <button
      class="menu-item menu-category"
      data-category="medias"
      type="button"
    >
      🧦 Medias
    </button>


    <div class="menu-divider"></div>



    <!-- FAVORITOS -->

    <button
      id="menu-favorites"
      class="menu-item"
      type="button"
    >
      ❤️ Mis favoritos
    </button>



    <!-- CARRITO -->

    <button
      class="menu-item menu-cart"
      type="button"
    >
      🛒 Carrito
    </button>


    <!-- PANEL ADMINISTRADOR (SOLO ADMIN) -->

    <a
      id="menu-admin"
      class="menu-item"
      href="admin.php"
      style="display: none; text-decoration: none; color: inherit;"
    >
      🛠️ Panel Administrador
    </a>



    <!-- LOGIN -->

    <button
      id="menu-login"
      class="menu-item"
      type="button"
    >
      🔑 Iniciar sesión
    </button>



    <!-- REGISTRO -->

    <button
      id="menu-register"
      class="menu-item"
      type="button"
    >
      📝 Registrarse
    </button>



    <!-- LOGOUT -->

    <button
      id="menu-logout"
      class="menu-item menu-logout"
      type="button"
      style="display: none;"
    >
      🚪 Cerrar sesión
    </button>


  </div>

</aside>



<!-- ==================================================
     PRODUCTOS
================================================== -->

<main class="productos">

  <h2 id="productos-titulo">
    Productos disponibles
  </h2>


  <div
    id="product-grid"
    class="grid"
  ></div>

</main>



<!-- ==================================================
     CARRITO
================================================== -->

<div
  id="cart-modal"
  class="cart-modal"
>

  <div class="cart-content">


    <button
      id="close-cart"
      class="modal-close"
      type="button"
      aria-label="Cerrar carrito"
    >
      ✕
    </button>


    <h3>
      🛒 Carrito de Compras
    </h3>


    <div id="cart-items"></div>


    <p id="cart-quantity">
      Productos: 0
    </p>


    <p id="cart-total">
      Total: $0
    </p>


    <button
      id="buy-cart"
      class="buy-btn"
      type="button"
    >
      💳 Pagar con Mercado Pago / Tarjetas
    </button>


  </div>

</div>



<!-- ==================================================
     REGISTRO
================================================== -->

<div
  id="register-modal"
  class="cart-modal"
>

  <div class="cart-content">


    <button
      id="close-register"
      class="modal-close"
      type="button"
      aria-label="Cerrar registro"
    >
      ✕
    </button>


    <h3>
      📝 Registro
    </h3>


    <input
      type="text"
      id="reg-user"
      placeholder="Nombre de usuario"
      autocomplete="username"
    >


    <input
      type="email"
      id="reg-email"
      placeholder="Correo electrónico (ej: tuemail@gmail.com)"
      autocomplete="email"
    >


    <div class="password-container">

      <input
        type="password"
        id="reg-pass"
        placeholder="Contraseña"
        autocomplete="new-password"
      >


      <button
        type="button"
        class="toggle-pass"
        data-target="reg-pass"
      >
        👁️
      </button>

    </div>


    <!-- PREGUNTA DE SEGURIDAD Y RECUPERACIÓN -->
    <div class="registro-seguridad-banner">
      <div class="seguridad-banner-titulo">
        <span class="seguridad-icono">🛡️</span>
        <strong>Recuperación de cuenta</strong>
      </div>
      <p class="seguridad-banner-desc">
        Configurá una pregunta y respuesta secreta. <strong>¿Para qué es?</strong> Te servirá para verificar tu identidad y restablecer tu contraseña si en algún momento la olvidás.
      </p>
    </div>

    <select
      id="reg-pregunta"
      class="security-select"
    >
      <option value="">-- Seleccioná una pregunta de seguridad --</option>
      <option value="¿Cuál es el nombre de tu mascota?">¿Cuál es el nombre de tu mascota?</option>
      <option value="¿En qué ciudad naciste?">¿En qué ciudad naciste?</option>
      <option value="¿Cuál es tu comida favorita?">¿Cuál es tu comida favorita?</option>
      <option value="¿Cuál es el nombre de tu mejor amigo/a?">¿Cuál es el nombre de tu mejor amigo/a?</option>
      <option value="¿Cuál fue tu primera escuela?">¿Cuál fue tu primera escuela?</option>
    </select>

    <input
      type="text"
      id="reg-respuesta"
      placeholder="Respuesta de seguridad"
      autocomplete="off"
    >

    <!-- INDICIO DE CONTRASEÑA -->
    <div class="registro-indicio-banner">
      <div class="indicio-banner-titulo">
        <span class="indicio-icono">💡</span>
        <strong>Indicio de contraseña <small>(opcional)</small></strong>
      </div>
      <p class="indicio-banner-desc">
        Escribí una pista que solo vos entiendas. Si intentás ingresar y fallás varias veces, el sistema te mostrará este indicio para ayudarte a recordar tu contraseña.
      </p>
      <input
        type="text"
        id="reg-indicio"
        placeholder="Ej: Nombre de mi primera mascota o fecha clave"
        autocomplete="off"
      >
    </div>


    <!-- CLOUDFLARE TURNSTILE -->

    <div class="turnstile-container">

      <div
        class="cf-turnstile"
        data-sitekey="0x4AAAAAAETdByVdYVQ7oyNX"
        data-theme="light"
      ></div>

    </div>


    <button
      id="register-confirm"
      type="button"
    >
      Registrarse
    </button>


  </div>

</div>



<!-- ==================================================
     LOGIN
================================================== -->

<div
  id="login-modal"
  class="cart-modal"
>

  <div class="cart-content">


    <button
      id="close-login"
      class="modal-close"
      type="button"
      aria-label="Cerrar login"
    >
      ✕
    </button>


    <h3>
      🔑 Iniciar Sesión
    </h3>


    <input
      type="text"
      id="login-user"
      placeholder="Usuario o Correo electrónico"
      autocomplete="username"
    >


    <div class="password-container">

      <input
        type="password"
        id="login-pass"
        placeholder="Contraseña"
        autocomplete="current-password"
      >


      <button
        type="button"
        class="toggle-pass"
        data-target="login-pass"
      >
        👁️
      </button>

    </div>


    <!-- INDICIO DE CONTRASEÑA TRAS INTENTOS FALLIDOS -->
    <div id="login-hint-box" class="login-hint-box" style="display: none;">
      <div class="hint-header">
        <span class="hint-icon">💡</span>
        <strong>Indicio de contraseña:</strong>
      </div>
      <p id="login-hint-text" class="hint-text"></p>
      <div class="hint-footer">
        <span>¿Seguís sin recordarla?</span>
        <a href="#" id="hint-open-recuperar">Recuperar cuenta</a>
      </div>
    </div>


    <button
      id="login-confirm"
      type="button"
    >
      Ingresar
    </button>

    <p class="forgot-password-link">
      <a href="#" id="open-recuperar">¿Olvidaste tu contraseña?</a>
    </p>


  </div>

</div>



<!-- ==================================================
     RECUPERAR CONTRASEÑA
================================================== -->

<div
  id="recuperar-modal"
  class="cart-modal"
>

  <div class="cart-content">


    <button
      id="close-recuperar"
      class="modal-close"
      type="button"
      aria-label="Cerrar recuperación"
    >
      ✕
    </button>


    <h3>
      🔓 Recuperar Contraseña
    </h3>

    <p class="recuperar-instruccion" id="recuperar-instruccion">
      Ingresá tu correo electrónico o usuario para recibir un código de seguridad de 6 dígitos.
    </p>

    <!-- Paso 1: Ingresar email o usuario para enviar código -->
    <div id="recuperar-paso1">

      <input
        type="text"
        id="recuperar-user"
        placeholder="Tu correo o nombre de usuario"
        autocomplete="email"
      >

      <button
        id="recuperar-buscar"
        type="button"
      >
        Enviar código a mi correo ✉️
      </button>

      <div class="recuperar-alt-opcion">
        <span>¿Preferís usar tu pregunta secreta?</span>
        <a href="#" id="recuperar-ir-pregunta">Usar pregunta de seguridad</a>
      </div>

    </div>

    <!-- Paso 2: Ingresar código de 6 dígitos y nueva contraseña -->
    <div id="recuperar-paso2" style="display: none;">

      <div class="codigo-info-badge">
        <span>✉️ Código de 6 dígitos enviado a:</span>
        <strong id="recuperar-email-destino"></strong>
      </div>

      <input
        type="text"
        id="recuperar-codigo"
        placeholder="Código de 6 dígitos (ej: 123456)"
        maxlength="6"
        autocomplete="one-time-code"
        class="codigo-input"
      >

      <div class="password-container">

        <input
          type="password"
          id="recuperar-nueva-pass"
          placeholder="Nueva contraseña"
          autocomplete="new-password"
        >

        <button
          type="button"
          class="toggle-pass"
          data-target="recuperar-nueva-pass"
        >
          👁️
        </button>

      </div>

      <button
        id="recuperar-confirmar"
        type="button"
      >
        Restablecer contraseña
      </button>

      <div class="recuperar-alt-opcion">
        <a href="#" id="recuperar-reenviar">¿No te llegó el código? Reenviar</a>
      </div>

    </div>

    <!-- Paso 3 (Alternativo): Pregunta secreta -->
    <div id="recuperar-paso-pregunta" style="display: none;">

      <p id="recuperar-pregunta-texto" class="recuperar-pregunta"></p>

      <input
        type="text"
        id="recuperar-respuesta"
        placeholder="Tu respuesta secreta"
        autocomplete="off"
      >

      <div class="password-container">

        <input
          type="password"
          id="recuperar-nueva-pass-pregunta"
          placeholder="Nueva contraseña"
          autocomplete="new-password"
        >

        <button
          type="button"
          class="toggle-pass"
          data-target="recuperar-nueva-pass-pregunta"
        >
          👁️
        </button>

      </div>

      <button
        id="recuperar-confirmar-pregunta"
        type="button"
      >
        Cambiar contraseña
      </button>

      <div class="recuperar-alt-opcion">
        <a href="#" id="recuperar-volver-codigo">← Volver al método de código por correo</a>
      </div>

    </div>


  </div>

</div>



<!-- ==================================================
     CONTENEDOR DE NOTIFICACIONES
================================================== -->

<div
  id="notificaciones"
></div>



<!-- ==================================================
     JAVASCRIPT
================================================== -->

<script src="script.js?v=<?php echo time(); ?>"></script>



<!-- ==================================================
     OCULTAR HEADER AL BAJAR
================================================== -->

<script>

let lastScrollTop = 0;

const header =
  document.querySelector(".header");


window.addEventListener(
  "scroll",
  () => {

    const currentScroll =
      window.pageYOffset ||
      document.documentElement.scrollTop;


    if (
      currentScroll > lastScrollTop &&
      currentScroll > 60
    ) {

      header.style.transform =
        "translateY(-100%)";

      header.style.opacity =
        "0";

      header.style.pointerEvents =
        "none";

    }

    else {

      header.style.transform =
        "translateY(0)";

      header.style.opacity =
        "1";

      header.style.pointerEvents =
        "auto";

    }


    lastScrollTop =
      currentScroll <= 0
        ? 0
        : currentScroll;

  }
);

</script>


</body>

</html>