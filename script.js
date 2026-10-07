// ==================================================
// VARIABLES GLOBALES
// ==================================================

let productos = [];
let productosFiltrados = [];
let paginaActual = 1;

const productosPorPagina = 9;

let carrito =
  JSON.parse(localStorage.getItem("carrito")) || [];

let favoritos =
  JSON.parse(localStorage.getItem("favoritos")) || [];



// ==================================================
// ELEMENTOS
// ==================================================

const grid =
  document.getElementById("product-grid");

const search =
  document.getElementById("menu-search");

const productosTitulo =
  document.getElementById("productos-titulo");



// ==================================================
// GUARDAR FAVORITOS
// ==================================================

function guardarFavoritos() {

  localStorage.setItem(
    "favoritos",
    JSON.stringify(favoritos)
  );

}



// ==================================================
// GUARDAR CARRITO
// ==================================================

function guardarCarrito() {

  localStorage.setItem(
    "carrito",
    JSON.stringify(carrito)
  );

}



// ==================================================
// NOTIFICACIONES
// ==================================================

function mostrarNotificacion(
  mensaje,
  tipo = "success"
) {

  const contenedor =
    document.getElementById(
      "notificaciones"
    );

  if (!contenedor) return;


  const notificacion =
    document.createElement("div");

  notificacion.className =
    `notificacion notificacion-${tipo}`;


  let icono = "✅";

  if (tipo === "error") {
    icono = "❌";
  }

  if (tipo === "info") {
    icono = "ℹ️";
  }

  if (tipo === "heart") {
    icono = "❤️";
  }

  if (tipo === "cart") {
    icono = "🛒";
  }


  notificacion.innerHTML = `

    <span class="notificacion-icon">
      ${icono}
    </span>

    <span class="notificacion-texto">
      ${mensaje}
    </span>

    <button
      class="notificacion-cerrar"
      type="button"
      aria-label="Cerrar"
    >
      ✕
    </button>

  `;


  contenedor.appendChild(
    notificacion
  );


  requestAnimationFrame(() => {

    notificacion.classList.add(
      "notificacion-visible"
    );

  });


  const cerrar =
    () => {

      notificacion.classList.remove(
        "notificacion-visible"
      );

      notificacion.classList.add(
        "notificacion-saliendo"
      );


      setTimeout(() => {

        notificacion.remove();

      }, 300);

    };


  notificacion
    .querySelector(
      ".notificacion-cerrar"
    )
    .addEventListener(
      "click",
      cerrar
    );


  setTimeout(
    cerrar,
    3500
  );

}



// ==================================================
// CAMBIAR TÍTULO
// ==================================================

function cambiarTitulo(titulo) {

  if (productosTitulo) {

    productosTitulo.textContent =
      titulo;

  }

}



// ==================================================
// CARGAR PRODUCTOS
// ==================================================

fetch("obtener_productos.php")

  .then(res => {

    if (!res.ok) {

      throw new Error(
        "Error HTTP: " + res.status
      );

    }

    return res.json();

  })

  .then(data => {

    productos = data;

    productosFiltrados =
      productos;

    cambiarTitulo(
      "Productos disponibles"
    );

    mostrarProductos();

    const pendienteId = localStorage.getItem("productoParaAgregar");
    if (pendienteId) {
      localStorage.removeItem("productoParaAgregar");
      agregarAlCarrito(pendienteId);
    }
  })

  .catch(error => {

    console.error(
      "Error cargando productos:",
      error
    );


    mostrarNotificacion(
      "No se pudieron cargar los productos.",
      "error"
    );

  });



// ==================================================
// COMPROBAR FAVORITO
// ==================================================

function esFavorito(id) {

  return favoritos.some(
    favorito =>
      Number(favorito) ===
      Number(id)
  );

}



// ==================================================
// AGREGAR / QUITAR FAVORITO
// ==================================================

function cambiarFavorito(id, event) {

  if (event) {

    event.preventDefault();
    event.stopPropagation();

  }


  const boton =
    document.querySelector(
      `.favorite-btn[data-id="${id}"]`
    );


  const indice =
    favoritos.findIndex(
      favorito =>
        Number(favorito) ===
        Number(id)
    );


  // ==============================================
  // QUITAR FAVORITO
  // ==============================================

  if (indice !== -1) {

    favoritos.splice(
      indice,
      1
    );


    guardarFavoritos();


    if (boton) {

      boton.classList.remove(
        "favorite-active"
      );

      boton.classList.add(
        "heart-animation"
      );

      boton.textContent =
        "🤍";


      setTimeout(() => {

        boton.classList.remove(
          "heart-animation"
        );

      }, 450);

    }


    mostrarNotificacion(
      "Producto eliminado de favoritos.",
      "info"
    );


    return;

  }



  // ==============================================
  // AGREGAR FAVORITO
  // ==============================================

  favoritos.push(
    Number(id)
  );


  guardarFavoritos();


  if (boton) {

    boton.classList.add(
      "favorite-active"
    );

    boton.classList.add(
      "heart-animation"
    );

    boton.textContent =
      "❤️";


    setTimeout(() => {

      boton.classList.remove(
        "heart-animation"
      );

    }, 450);

  }


  mostrarNotificacion(
    "Producto agregado a favoritos ❤️",
    "heart"
  );

}



// ==================================================
// ETIQUETA DEL PRODUCTO
// ==================================================

function obtenerEtiqueta(prod) {

  if (prod.etiqueta) {

    return prod.etiqueta;

  }


  if (
    prod.oferta === true ||
    prod.oferta === "1" ||
    prod.oferta === 1
  ) {

    return "OFERTA";

  }


  return "";

}



// ==================================================
// MOSTRAR PRODUCTOS
// ==================================================

function mostrarProductos() {

  grid.innerHTML = "";


  const inicio =
    (paginaActual - 1) *
    productosPorPagina;


  const fin =
    inicio +
    productosPorPagina;


  const productosPagina =
    productosFiltrados.slice(
      inicio,
      fin
    );


  if (
    productosPagina.length === 0
  ) {

    grid.innerHTML = `

      <div class="sin-productos">

        <div class="sin-productos-icon">
          🔍
        </div>

        <h3>
          No encontramos productos
        </h3>

        <p>
          Probá con otro nombre o categoría.
        </p>

      </div>

    `;


    crearPaginacion();

    return;

  }



  productosPagina.forEach(
    (prod, index) => {

const card =
  document.createElement(
    "div"
  );


card.classList.add("card");

card.style.cursor = "pointer";

// Abrir página del producto al hacer clic en la tarjeta
card.addEventListener("click", (event) => {
    if (
        event.target.closest(".favorite-btn") ||
        event.target.closest(".add-btn")
    ) {
        return;
    }

    window.location.href = `producto.php?id=${prod.id}`;
});

      card.style.animationDelay =
        `${index * 0.06}s`;


      const favorito =
        esFavorito(
          prod.id
        );


      const etiqueta =
        obtenerEtiqueta(
          prod
        );


      card.innerHTML = `

        <div class="card-image-container">

          <img
            src="${prod.imagen}"
            alt="${prod.nombre}"
          >


          ${
            etiqueta
            ?
            `
              <span
                class="product-tag ${etiqueta
                  .toLowerCase()
                  .replaceAll(
                    " ",
                    "-"
                  )}"
              >
                ${etiqueta}
              </span>
            `
            :
            ""
          }


          <button
            class="favorite-btn
            ${
              favorito
              ?
              "favorite-active"
              :
              ""
            }"
            data-id="${prod.id}"
            aria-label="Agregar a favoritos"
            type="button"
          >

            ${
              favorito
              ?
              "❤️"
              :
              "🤍"
            }

          </button>

        </div>


        <div class="card-info">

          <h3>
            ${prod.nombre}
          </h3>


          <span class="precio">

            $${Number(
              prod.precio
            ).toLocaleString()}

          </span>


          <button
            class="add-btn"
            data-id="${prod.id}"
            type="button"
          >

            <span class="add-btn-icon">
              🛒
            </span>

            <span>
              Agregar al carrito
            </span>

          </button>

        </div>

      `;


      // ==========================================
      // BOTÓN FAVORITO
      // ==========================================

      const favoriteButton =
        card.querySelector(
          ".favorite-btn"
        );


      favoriteButton.addEventListener(
        "click",
        event => {

          cambiarFavorito(
            prod.id,
            event
          );

        }
      );



      // ==========================================
      // BOTÓN CARRITO
      // ==========================================

      const addButton =
        card.querySelector(
          ".add-btn"
        );


      addButton.addEventListener(
        "click",
        event => {

          event.preventDefault();
          event.stopPropagation();


          agregarAlCarrito(
            prod.id,
            addButton
          );

        }
      );


      grid.appendChild(
        card
      );

    }
  );


  crearPaginacion();

}



// ==================================================
// PAGINACIÓN
// ==================================================

function crearPaginacion() {

  let paginacion =
    document.getElementById(
      "paginacion"
    );


  if (!paginacion) {

    paginacion =
      document.createElement(
        "div"
      );


    paginacion.id =
      "paginacion";


    paginacion.classList.add(
      "paginacion"
    );


    grid.after(
      paginacion
    );

  }


  paginacion.innerHTML = "";


  const totalPaginas =
    Math.ceil(
      productosFiltrados.length /
      productosPorPagina
    );


  if (
    totalPaginas <= 1
  ) {

    return;

  }



  const prevBtn =
    document.createElement(
      "button"
    );


  prevBtn.innerHTML =
    "⬅️";


  prevBtn.disabled =
    paginaActual === 1;


  prevBtn.type =
    "button";


  prevBtn.onclick = event => {

    event.preventDefault();


    if (
      paginaActual > 1
    ) {

      paginaActual--;

      mostrarProductos();

    }

  };


  paginacion.appendChild(
    prevBtn
  );



  for (
    let i = 1;
    i <= totalPaginas;
    i++
  ) {

    const btn =
      document.createElement(
        "button"
      );


    btn.textContent =
      i;


    btn.type =
      "button";


    if (
      i === paginaActual
    ) {

      btn.classList.add(
        "active"
      );

    }


    btn.onclick = event => {

      event.preventDefault();


      paginaActual =
        i;


      mostrarProductos();


      window.scrollTo({

        top: 0,

        behavior: "smooth"

      });

    };


    paginacion.appendChild(
      btn
    );

  }



  const nextBtn =
    document.createElement(
      "button"
    );


  nextBtn.innerHTML =
    "➡️";


  nextBtn.type =
    "button";


  nextBtn.disabled =
    paginaActual ===
    totalPaginas;


  nextBtn.onclick = event => {

    event.preventDefault();


    if (
      paginaActual <
      totalPaginas
    ) {

      paginaActual++;

      mostrarProductos();

    }

  };


  paginacion.appendChild(
    nextBtn
  );

}



// ==================================================
// BUSCADOR
// ==================================================

search.addEventListener(
  "input",
  e => {

    const texto =
      e.target.value
        .toLowerCase()
        .trim();


    productosFiltrados =
      productos.filter(
        p =>
          p.nombre
            .toLowerCase()
            .includes(
              texto
            )
      );


    paginaActual = 1;


    cambiarTitulo(
      texto
      ?
      `🔍 Resultados para "${e.target.value}"`
      :
      "Productos disponibles"
    );


    mostrarProductos();

  }
);



// ==================================================
// FILTRAR CATEGORÍA
// ==================================================

function filtrarCategoria(
  categoria
) {

  if (
    categoria === "all"
  ) {

    productosFiltrados =
      productos;


    cambiarTitulo(
      "Productos disponibles"
    );

  }

  else {

    productosFiltrados =
      productos.filter(
        p =>
          p.categoria ===
          categoria
      );


    const nombresCategorias = {

      remeras:
        "👕 Remeras",

      interior:
        "🩲 Ropa Interior",

      calzas:
        "👖 Calzas",

      medias:
        "🧦 Medias"

    };


    cambiarTitulo(
      nombresCategorias[
        categoria
      ] ||
      "Productos disponibles"
    );

  }


  paginaActual = 1;


  mostrarProductos();

}



// ==================================================
// FAVORITOS
// ==================================================

function mostrarFavoritos() {

  productosFiltrados =
    productos.filter(
      producto =>
        esFavorito(
          producto.id
        )
    );


  paginaActual = 1;


  cambiarTitulo(
    "❤️ Mis favoritos"
  );


  mostrarProductos();

}



// ==================================================
// CARRITO
// ==================================================

function agregarAlCarrito(
  id,
  boton = null
) {

  const prod =
    productos.find(
      p =>
        Number(p.id) ===
        Number(id)
    );


  if (!prod) {

    return;

  }


  const productoExistente =
    carrito.find(
      item =>
        Number(item.id) ===
        Number(id)
    );


  if (
    productoExistente
  ) {

    productoExistente.cantidad++;

  }

  else {

    carrito.push({

      id:
        prod.id,

      nombre:
        prod.nombre,

      precio:
        Number(
          prod.precio
        ),

      imagen:
        prod.imagen,

      cantidad:
        1

    });

  }


  guardarCarrito();

  actualizarCarrito();



  // ==============================================
  // ANIMACIÓN DEL BOTÓN
  // ==============================================

  if (boton) {

    boton.classList.remove(
      "cart-animation"
    );


    void boton.offsetWidth;


    boton.classList.add(
      "cart-animation"
    );


    const textoOriginal =
      boton.innerHTML;


    boton.innerHTML = `

      <span>
        ✓ Agregado
      </span>

    `;


    setTimeout(() => {

      boton.innerHTML =
        textoOriginal;

      boton.classList.remove(
        "cart-animation"
      );

    }, 800);

  }



  mostrarNotificacion(
    `"${prod.nombre}" fue agregado al carrito.`,
    "cart"
  );

}



// ==================================================
// AUMENTAR CANTIDAD
// ==================================================

function aumentarCantidad(id) {

  const producto =
    carrito.find(
      p =>
        Number(p.id) ===
        Number(id)
    );


  if (!producto) {

    return;

  }


  producto.cantidad++;


  guardarCarrito();

  actualizarCarrito();

}



// ==================================================
// DISMINUIR CANTIDAD
// ==================================================

function disminuirCantidad(id) {

  const producto =
    carrito.find(
      p =>
        Number(p.id) ===
        Number(id)
    );


  if (!producto) {

    return;

  }


  producto.cantidad--;


  if (
    producto.cantidad <= 0
  ) {

    eliminarProductoCarrito(
      id
    );

    return;

  }


  guardarCarrito();

  actualizarCarrito();

}



// ==================================================
// ELIMINAR PRODUCTO
// ==================================================

function eliminarProductoCarrito(
  id
) {

  const producto =
    carrito.find(
      p =>
        Number(p.id) ===
        Number(id)
    );


  carrito =
    carrito.filter(
      p =>
        Number(p.id) !==
        Number(id)
    );


  guardarCarrito();

  actualizarCarrito();


  if (producto) {

    mostrarNotificacion(
      `"${producto.nombre}" fue eliminado del carrito.`,
      "info"
    );

  }

}



// ==================================================
// ACTUALIZAR CARRITO
// ==================================================

function actualizarCarrito() {

  const cartItems =
    document.getElementById(
      "cart-items"
    );


  const cartTotal =
    document.getElementById(
      "cart-total"
    );


  const cartCount =
    document.getElementById(
      "cart-count"
    );


  const cartQuantity =
    document.getElementById(
      "cart-quantity"
    );


  if (
    !cartItems ||
    !cartTotal ||
    !cartCount ||
    !cartQuantity
  ) {

    return;

  }


  cartItems.innerHTML = "";


  let total = 0;

  let cantidadTotal = 0;


  if (
    carrito.length === 0
  ) {

    cartItems.innerHTML = `

      <div class="cart-empty">

        <div>
          🛒
        </div>

        <p>
          Tu carrito está vacío.
        </p>

      </div>

    `;

  }



  carrito.forEach(
    producto => {

      const subtotal =
        Number(
          producto.precio
        ) *
        producto.cantidad;


      total +=
        subtotal;


      cantidadTotal +=
        producto.cantidad;


      const item =
        document.createElement(
          "div"
        );


      item.classList.add(
        "cart-item"
      );


      item.innerHTML = `

        <img
          src="${producto.imagen}"
          alt="${producto.nombre}"
          class="cart-item-image"
        >


        <div class="cart-item-info">

          <strong>
            ${producto.nombre}
          </strong>


          <span>
            $${Number(
              producto.precio
            ).toLocaleString()}
          </span>


          <div class="quantity-controls">

            <button
              onclick="disminuirCantidad(${producto.id})"
              type="button"
            >
              −
            </button>


            <span>
              ${producto.cantidad}
            </span>


            <button
              onclick="aumentarCantidad(${producto.id})"
              type="button"
            >
              +
            </button>

          </div>

        </div>


        <div class="cart-item-right">

          <strong>
            $${subtotal.toLocaleString()}
          </strong>


          <button
            class="remove-cart-item"
            onclick="eliminarProductoCarrito(${producto.id})"
            type="button"
            aria-label="Eliminar producto"
          >
            🗑️
          </button>

        </div>

      `;


      cartItems.appendChild(
        item
      );

    }
  );


  cartTotal.textContent =
    `Total: $${total.toLocaleString()}`;


  cartQuantity.textContent =
    `Productos: ${cantidadTotal}`;


  cartCount.textContent =
    cantidadTotal;


  // Animar contador

  cartCount.classList.remove(
    "cart-count-animation"
  );


  void cartCount.offsetWidth;


  cartCount.classList.add(
    "cart-count-animation"
  );

}



// ==================================================
// ELEMENTOS MODALES
// ==================================================

const cartBtn =
  document.getElementById(
    "cart-btn"
  );


const cartModal =
  document.getElementById(
    "cart-modal"
  );


const closeCart =
  document.getElementById(
    "close-cart"
  );


const buyCart =
  document.getElementById(
    "buy-cart"
  );



const loginModal =
  document.getElementById(
    "login-modal"
  );


const registerModal =
  document.getElementById(
    "register-modal"
  );



// ==================================================
// ABRIR MODAL
// ==================================================

function abrirModal(modal) {

  if (!modal) return;


  modal.style.display =
    "flex";


  requestAnimationFrame(() => {

    modal.classList.add(
      "modal-visible"
    );

  });

}



// ==================================================
// CERRAR MODAL
// ==================================================

function cerrarModal(modal) {

  if (!modal) return;


  modal.classList.remove(
    "modal-visible"
  );


  setTimeout(() => {

    modal.style.display =
      "none";

  }, 250);

}



// ==================================================
// CARRITO MODAL
// ==================================================

cartBtn.addEventListener(
  "click",
  event => {

    event.preventDefault();

    abrirModal(
      cartModal
    );

  }
);


closeCart.addEventListener(
  "click",
  event => {

    event.preventDefault();

    cerrarModal(
      cartModal
    );

  }
);



buyCart.addEventListener("click", async event => {
  event.preventDefault();

  if (carrito.length === 0) {
    mostrarNotificacion("El carrito está vacío.", "info");
    return;
  }

  buyCart.disabled = true;
  const textoOriginal = buyCart.innerHTML;
  buyCart.innerHTML = "Conectando con Mercado Pago... ⏳";

  try {
    const respuesta = await fetch("crear_preferencia.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        items: carrito
      })
    });

    const resultado = await respuesta.json();

    if (resultado.success && resultado.init_point) {
      mostrarNotificacion("Redirigiendo a Mercado Pago seguro... 💳", "info");
      setTimeout(() => {
        window.location.href = resultado.init_point;
      }, 500);
    } else {
      mostrarNotificacion(resultado.message || "No se pudo iniciar el pago.", "error");
      buyCart.disabled = false;
      buyCart.innerHTML = textoOriginal;
    }
  } catch (err) {
    console.error(err);
    mostrarNotificacion("Error al conectar con la pasarela de pago.", "error");
    buyCart.disabled = false;
    buyCart.innerHTML = textoOriginal;
  }
});



// ==================================================
// MENÚ HAMBURGUESA
// ==================================================

const menuBtn =
  document.getElementById(
    "menu-btn"
  );


const sideMenu =
  document.getElementById(
    "side-menu"
  );


const menuClose =
  document.getElementById(
    "menu-close"
  );


const menuOverlay =
  document.getElementById(
    "menu-overlay"
  );


function abrirMenu() {

  sideMenu.classList.add(
    "open"
  );


  menuOverlay.classList.add(
    "active"
  );

}


function cerrarMenu() {

  sideMenu.classList.remove(
    "open"
  );


  menuOverlay.classList.remove(
    "active"
  );

}


menuBtn.addEventListener(
  "click",
  event => {

    event.preventDefault();


    if (
      sideMenu.classList.contains(
        "open"
      )
    ) {

      cerrarMenu();

    }

    else {

      abrirMenu();

    }

  }
);


menuClose.addEventListener(
  "click",
  event => {

    event.preventDefault();

    cerrarMenu();

  }
);


menuOverlay.addEventListener(
  "click",
  cerrarMenu
);



// ==================================================
// BUSCADOR DEL MENÚ
// ==================================================

const menuSearchBtn =
  document.getElementById(
    "menu-search-btn"
  );


const menuSearchContainer =
  document.getElementById(
    "menu-search-container"
  );


const closeSearch =
  document.getElementById(
    "close-search"
  );


menuSearchBtn.addEventListener(
  "click",
  event => {

    event.preventDefault();


    menuSearchContainer.classList.add(
      "active"
    );


    menuSearchBtn.style.display =
      "none";


    setTimeout(
      () => {

        search.focus();

      },
      200
    );

  }
);


closeSearch.addEventListener(
  "click",
  event => {

    event.preventDefault();


    search.value = "";


    productosFiltrados =
      productos;


    paginaActual = 1;


    cambiarTitulo(
      "Productos disponibles"
    );


    mostrarProductos();


    menuSearchContainer.classList.remove(
      "active"
    );


    menuSearchBtn.style.display =
      "block";

  }
);



// ==================================================
// CATEGORÍAS
// ==================================================

const menuCategories =
  document.querySelectorAll(
    ".menu-category"
  );


menuCategories.forEach(
  boton => {

    boton.addEventListener(
      "click",
      event => {

        event.preventDefault();


        const categoria =
          boton.dataset.category;


        filtrarCategoria(
          categoria
        );


        cerrarMenu();


        window.scrollTo({

          top: 0,

          behavior: "smooth"

        });

      }
    );

  }
);



// ==================================================
// FAVORITOS DESDE MENÚ
// ==================================================

const menuFavorites =
  document.getElementById(
    "menu-favorites"
  );


menuFavorites.addEventListener(
  "click",
  event => {

    event.preventDefault();


    mostrarFavoritos();


    cerrarMenu();


    window.scrollTo({

      top: 0,

      behavior: "smooth"

    });

  }
);



// ==================================================
// CARRITO DESDE MENÚ
// ==================================================

const menuCart =
  document.querySelector(
    ".menu-cart"
  );


menuCart.addEventListener(
  "click",
  event => {

    event.preventDefault();


    cerrarMenu();


    abrirModal(
      cartModal
    );

  }
);



// ==================================================
// LOGIN
// ==================================================

const menuLogin =
  document.getElementById(
    "menu-login"
  );


const closeLogin =
  document.getElementById(
    "close-login"
  );


menuLogin.addEventListener(
  "click",
  event => {

    event.preventDefault();


    cerrarMenu();


    abrirModal(
      loginModal
    );

  }
);


closeLogin.addEventListener(
  "click",
  event => {

    event.preventDefault();


    cerrarModal(
      loginModal
    );

  }
);



// ==================================================
// REGISTRO
// ==================================================

const menuRegister =
  document.getElementById(
    "menu-register"
  );


const closeRegister =
  document.getElementById(
    "close-register"
  );


menuRegister.addEventListener(
  "click",
  event => {

    event.preventDefault();


    cerrarMenu();


    document.getElementById(
      "reg-user"
    ).value = "";


    document.getElementById(
      "reg-pass"
    ).value = "";


    abrirModal(
      registerModal
    );


    setTimeout(
      () => {

        if (
          window.turnstile
        ) {

          const widget =
            document.querySelector(
              ".cf-turnstile"
            );


          if (widget) {

            try {

              turnstile.reset(
                widget
              );

            }

            catch (error) {

              console.log(
                "Turnstile listo para utilizar."
              );

            }

          }

        }

      },
      300
    );

  }
);


closeRegister.addEventListener(
  "click",
  event => {

    event.preventDefault();


    cerrarModal(
      registerModal
    );

  }
);



// ==================================================
// REGISTRO + TURNSTILE
// ==================================================

const registerConfirm =
  document.getElementById(
    "register-confirm"
  );


registerConfirm.addEventListener(
  "click",
  async event => {

    event.preventDefault();


    const usuario =
      document.getElementById(
        "reg-user"
      ).value.trim();


    const password =
      document.getElementById(
        "reg-pass"
      ).value;


    if (!usuario) {

      mostrarNotificacion(
        "Por favor, escribí un usuario.",
        "error"
      );

      return;

    }


    if (!password) {

      mostrarNotificacion(
        "Por favor, escribí una contraseña.",
        "error"
      );

      return;

    }


    const turnstileInput =
      document.querySelector(
        '[name="cf-turnstile-response"]'
      );


    const token =
      turnstileInput
      ?
      turnstileInput.value
      :
      "";


    if (!token) {

      mostrarNotificacion(
        "Esperá un momento y completá la verificación de Cloudflare.",
        "info"
      );

      return;

    }


    registerConfirm.disabled = true;
    registerConfirm.textContent = "Registrando...";

    try {
      const respuesta = await fetch("auth_registro.php", {
        method: "POST",
        headers: {
          "Content-Type": "application/json"
        },
        body: JSON.stringify({
          usuario: usuario,
          password: password,
          token: token
        })
      });

      const resultado = await respuesta.json();

      if (!resultado.success) {
        mostrarNotificacion(resultado.message || "Error al registrarse.", "error");

        if (window.turnstile) {
          const widget = document.querySelector(".cf-turnstile");
          if (widget) {
            try { turnstile.reset(widget); } catch (e) {}
          }
        }
        return;
      }

      mostrarNotificacion(resultado.message, "success");
      cerrarModal(registerModal);

      document.getElementById("reg-user").value = "";
      document.getElementById("reg-pass").value = "";

      await actualizarEstadoUsuario();
    } catch (error) {
      console.error(error);
      mostrarNotificacion("Ocurrió un error al procesar el registro.", "error");
    } finally {
      registerConfirm.disabled = false;
      registerConfirm.textContent = "Registrarse";
    }
  }
);



// ==================================================
// LOGIN
// ==================================================

const loginConfirm =
  document.getElementById("login-confirm");


loginConfirm.addEventListener("click", async event => {
  event.preventDefault();

  const usuario = document.getElementById("login-user").value.trim();
  const password = document.getElementById("login-pass").value;

  if (!usuario || !password) {
    mostrarNotificacion("Completá usuario y contraseña.", "error");
    return;
  }

  loginConfirm.disabled = true;
  loginConfirm.textContent = "Ingresando...";

  try {
    const respuesta = await fetch("auth_login.php", {
      method: "POST",
      headers: {
        "Content-Type": "application/json"
      },
      body: JSON.stringify({
        usuario: usuario,
        password: password
      })
    });

    const resultado = await respuesta.json();

    if (!resultado.success) {
      mostrarNotificacion(resultado.message || "Usuario o contraseña incorrectos.", "error");

      if (resultado.no_existe) {
        setTimeout(() => {
          if (confirm("El usuario '" + usuario + "' no está registrado. ¿Querés abrir el formulario de registro ahora?")) {
            cerrarModal(loginModal);
            document.getElementById("reg-user").value = usuario;
            abrirModal(registerModal);
          }
        }, 600);
      }
      return;
    }

    mostrarNotificacion(resultado.message, "success");
    cerrarModal(loginModal);

    document.getElementById("login-user").value = "";
    document.getElementById("login-pass").value = "";

    await actualizarEstadoUsuario();

    // Si es administrador, ofrecer ir al panel de admin
    if (resultado.rol === "admin") {
      mostrarNotificacion("👑 Has iniciado como Administrador. Acceso al panel habilitado en el menú.", "info");
    }
  } catch (error) {
    console.error(error);
    mostrarNotificacion("Error de conexión al iniciar sesión.", "error");
  } finally {
    loginConfirm.disabled = false;
    loginConfirm.textContent = "Ingresar";
  }
});



// ==================================================
// CERRAR SESIÓN
// ==================================================

const menuLogout =
  document.getElementById("menu-logout");


menuLogout.addEventListener("click", async event => {
  event.preventDefault();

  try {
    await fetch("auth_logout.php");
  } catch (e) {}

  localStorage.removeItem("usuarioActual");
  localStorage.removeItem("rolActual");

  await actualizarEstadoUsuario();
  cerrarMenu();
  mostrarNotificacion("Sesión cerrada correctamente.", "success");
});



// ==================================================
// ACTUALIZAR ESTADO USUARIO (DESDE SERVIDOR)
// ==================================================

async function actualizarEstadoUsuario() {
  const menuUserName = document.getElementById("menu-user-name");
  const menuUserStatus = document.getElementById("menu-user-status");
  const menuUserIcon = document.getElementById("menu-user-icon");
  const menuAdmin = document.getElementById("menu-admin");

  try {
    const res = await fetch("auth_estado.php");
    const data = await res.json();

    if (data.loggedIn && data.usuario) {
      localStorage.setItem("usuarioActual", data.usuario);
      localStorage.setItem("rolActual", data.rol);

      if (menuUserName) menuUserName.textContent = data.usuario;
      if (menuUserStatus) menuUserStatus.textContent = data.rol === "admin" ? "👑 Administrador" : "Sesión iniciada";
      if (menuUserIcon) menuUserIcon.textContent = data.rol === "admin" ? "👑" : "👤";

      if (menuLogin) menuLogin.style.display = "none";
      if (menuRegister) menuRegister.style.display = "none";
      if (menuLogout) menuLogout.style.display = "flex";

      if (menuAdmin) {
        menuAdmin.style.display = data.rol === "admin" ? "block" : "none";
      }
    } else {
      localStorage.removeItem("usuarioActual");
      localStorage.removeItem("rolActual");

      if (menuUserName) menuUserName.textContent = "No has iniciado sesión";
      if (menuUserStatus) menuUserStatus.textContent = "Visitante";
      if (menuUserIcon) menuUserIcon.textContent = "👤";

      if (menuLogin) menuLogin.style.display = "flex";
      if (menuRegister) menuRegister.style.display = "flex";
      if (menuLogout) menuLogout.style.display = "none";

      if (menuAdmin) menuAdmin.style.display = "none";
    }
  } catch (e) {
    console.error("Error verificando sesión:", e);
  }
}



// ==================================================
// CERRAR MODALES AL HACER CLIC AFUERA
// ==================================================

window.addEventListener(
  "click",
  event => {

    if (
      event.target ===
      cartModal
    ) {

      cerrarModal(
        cartModal
      );

    }


    if (
      event.target ===
      loginModal
    ) {

      cerrarModal(
        loginModal
      );

    }


    if (
      event.target ===
      registerModal
    ) {

      cerrarModal(
        registerModal
      );

    }

  }
);



// ==================================================
// ESC
// ==================================================

document.addEventListener(
  "keydown",
  event => {

    if (
      event.key ===
      "Escape"
    ) {

      cerrarMenu();


      cerrarModal(
        cartModal
      );


      cerrarModal(
        loginModal
      );


      cerrarModal(
        registerModal
      );

    }

  }
);



// ==================================================
// MOSTRAR / OCULTAR CONTRASEÑA
// ==================================================

const togglePasswords =
  document.querySelectorAll(
    ".toggle-pass"
  );


togglePasswords.forEach(
  button => {

    button.addEventListener(
      "click",
      event => {

        event.preventDefault();


        const target =
          document.getElementById(
            button.dataset.target
          );


        if (
          target.type ===
          "password"
        ) {

          target.type =
            "text";


          button.textContent =
            "🙈";

        }

        else {

          target.type =
            "password";


          button.textContent =
            "👁️";

        }

      }
    );

  }
);



// ==================================================
// INICIAR
// ==================================================

actualizarEstadoUsuario();

actualizarCarrito();