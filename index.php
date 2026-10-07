<!DOCTYPE html>
<html lang="es">

<head>

  <meta charset="UTF-8">

  <meta
    name="viewport"
    content="width=device-width, initial-scale=1.0"
  >

  <title>Michell Store</title>

  <link
    rel="stylesheet"
    href="style.css"
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

  <h1 class="logo">
    Michell Store
  </h1>


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
      Michell Store
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
      placeholder="Usuario"
      autocomplete="username"
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
      placeholder="Usuario"
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


    <button
      id="login-confirm"
      type="button"
    >
      Ingresar
    </button>


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
      currentScroll > lastScrollTop
    ) {

      header.style.transform =
        "translateY(-100%)";

      header.style.opacity =
        "0";

    }

    else {

      header.style.transform =
        "translateY(0)";

      header.style.opacity =
        "1";

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