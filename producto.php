<?php

require_once __DIR__ . "/conexion.php";

$id = isset($_GET["id"])
    ? intval($_GET["id"])
    : 0;

$sql = "SELECT * FROM productos WHERE id = ?";

$stmt = $conexion->prepare($sql);

$stmt->bind_param("i", $id);

$stmt->execute();

if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

$resultado = $stmt->get_result();

if ($resultado->num_rows === 0) {
    die("Producto no encontrado.");
}

$producto = $resultado->fetch_assoc();
$stmt->close();

// Si está oculto y no es admin, no permitir ver
$esAdmin = isset($_SESSION["rol"]) && $_SESSION["rol"] === "admin";
if ($producto["visible"] == 0 && !$esAdmin) {
    die("<div style='font-family: Poppins, sans-serif; text-align: center; padding: 50px;'><h2 style='color: #6F1D2A;'>Este producto no se encuentra disponible actualmente.</h2><a href='index.php' style='color: #E96B6B; font-weight: bold;'>← Volver a la tienda</a></div>");
}

// Obtener sugerencias de productos (estilo Mercado Libre: misma categoría u otros destacados)
$sqlSug = "SELECT * FROM productos WHERE visible = 1 AND id != ? ORDER BY (categoria = ?) DESC, id DESC LIMIT 10";
$stmtSug = $conexion->prepare($sqlSug);
$stmtSug->bind_param("is", $id, $producto["categoria"]);
$stmtSug->execute();
$resSug = $stmtSug->get_result();
$sugerencias = [];
while ($sug = $resSug->fetch_assoc()) {
    $sugerencias[] = $sug;
}
$stmtSug->close();
$conexion->close();
?>
<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1.0"
    >

    <title>
        <?php echo htmlspecialchars($producto["nombre"]); ?>
        - Michell Store
    </title>

    <link
        rel="stylesheet"
        href="style.css?v=<?php echo time(); ?>"
    >

    <link
        href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap"
        rel="stylesheet"
    >

    <style>
        /* Header Detalle Fijo y Centrado */
        .header-detalle {
            display: grid !important;
            grid-template-columns: 50px 1fr 50px !important;
            align-items: center !important;
            padding: 10px 15px !important;
            position: sticky !important;
            top: 0 !important;
            z-index: 100 !important;
            height: 65px !important;
            box-sizing: border-box !important;
            transition: transform 0.3s ease, opacity 0.3s ease !important;
        }

        .header-detalle .header-back-btn {
            position: static !important;
            width: 42px !important;
            height: 42px !important;
            margin: 0 !important;
        }

        .header-detalle .header-title {
            text-align: center !important;
            margin: 0 !important;
            font-size: 20px !important;
            font-weight: 700 !important;
            cursor: pointer;
            white-space: nowrap;
            overflow: hidden;
            text-overflow: ellipsis;
        }

        .header-detalle .header-cart-btn {
            position: static !important;
            width: auto !important;
            height: 42px !important;
            margin: 0 !important;
            display: flex;
            align-items: center;
            justify-content: center;
        }

        /* Hover effects en botones */
        .detalle-carrito-btn {
            transition: all 0.25s ease !important;
            box-shadow: 0 4px 12px rgba(139, 38, 53, 0.25);
        }
        .detalle-carrito-btn:hover {
            background: #6F1D2A !important;
            transform: translateY(-3px) !important;
            box-shadow: 0 6px 18px rgba(139, 38, 53, 0.4) !important;
        }
        .detalle-carrito-btn:active {
            transform: translateY(0) !important;
        }

        .detalle-favorito-btn {
            transition: all 0.25s ease !important;
        }
        .detalle-favorito-btn:hover {
            background: #FFF1EF !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 4px 12px rgba(139, 38, 53, 0.15) !important;
        }
        .detalle-favorito-btn:active {
            transform: translateY(0) !important;
        }

        .enviar-valoracion {
            transition: all 0.25s ease !important;
            box-shadow: 0 4px 12px rgba(139, 38, 53, 0.2);
            cursor: pointer;
        }
        .enviar-valoracion:hover {
            background: #6F1D2A !important;
            transform: translateY(-2px) !important;
            box-shadow: 0 6px 16px rgba(139, 38, 53, 0.35) !important;
        }

        #selector-estrellas button {
            transition: transform 0.2s ease !important;
        }
        #selector-estrellas button:hover {
            transform: scale(1.3) !important;
        }

        .header-back-btn:hover, .header-cart-btn:hover {
            transform: scale(1.08) !important;
            box-shadow: 0 4px 12px rgba(0,0,0,0.2) !important;
        }
    </style>

</head>

<body>

<!-- Header con disposición segura sin superposición -->
<header id="detalle-header" class="header header-detalle">

    <button
        class="menu-btn header-back-btn"
        type="button"
        onclick="window.location.href='index.php'"
        aria-label="Volver a la tienda"
    >
        ←
    </button>

    <h1 class="logo header-title" onclick="window.location.href='index.php'">
        Michell Store
    </h1>

    <button
        id="cart-btn"
        class="cart-btn header-cart-btn"
        type="button"
        onclick="window.location.href='index.php'"
        aria-label="Ver carrito"
    >
        🛒
        <span id="cart-count">0</span>
    </button>

</header>


<main class="producto-detalle">

    <div class="producto-detalle-card">


        <!-- IMAGEN -->

        <div class="producto-detalle-imagen">

            <img
                src="<?php echo htmlspecialchars($producto["imagen"]); ?>"
                alt="<?php echo htmlspecialchars($producto["nombre"]); ?>"
            >

        </div>


        <!-- INFORMACIÓN -->

        <div class="producto-detalle-info">


            <!-- ETIQUETA -->

            <?php if (!empty($producto["etiqueta"])): ?>

                <span class="detalle-etiqueta">

                    <?php
                    echo htmlspecialchars(
                        $producto["etiqueta"]
                    );
                    ?>

                </span>

            <?php endif; ?>


            <!-- NOMBRE -->

            <h2>

                <?php
                echo htmlspecialchars(
                    $producto["nombre"]
                );
                ?>

            </h2>


            <!-- PRECIO -->

            <div class="detalle-precio">

                $<?php
                echo number_format(
                    $producto["precio"],
                    0,
                    ",",
                    "."
                );
                ?>

            </div>


            <!-- VALORACIÓN -->

            <div class="detalle-valoracion">

                <div
                    id="promedio-estrellas"
                    class="promedio-estrellas"
                >
                    ⭐⭐⭐⭐⭐
                </div>

                <div>

                    <strong id="promedio-numero">
                        0,0 / 5
                    </strong>

                    <span id="cantidad-valoraciones">
                        0 valoraciones
                    </span>

                </div>

            </div>

            <!-- ==================================================
                 VALORAR PRODUCTO
            ================================================== -->

            <div class="valorar-producto">

                <h3>
                    ⭐ Valorar este producto
                </h3>

                <p>
                    Elegí una puntuación:
                </p>


                <div
                    id="selector-estrellas"
                    class="selector-estrellas"
                >

                    <button
                        type="button"
                        data-rating="1"
                    >
                        ★
                    </button>

                    <button
                        type="button"
                        data-rating="2"
                    >
                        ★
                    </button>

                    <button
                        type="button"
                        data-rating="3"
                    >
                        ★
                    </button>

                    <button
                        type="button"
                        data-rating="4"
                    >
                        ★
                    </button>

                    <button
                        type="button"
                        data-rating="5"
                    >
                        ★
                    </button>

                </div>


                <button
                    id="enviar-valoracion"
                    class="enviar-valoracion"
                    type="button"
                >
                    Enviar valoración
                </button>

            </div>

            <!-- DESCRIPCIÓN -->

            <div class="detalle-seccion">

                <h3>
                    Descripción
                </h3>

                <p>

                    <?php
                    echo nl2br(
                        htmlspecialchars(
                            $producto["descripcion"]
                        )
                    );
                    ?>

                </p>

            </div>


            <!-- CATEGORÍA -->

            <div class="detalle-categoria">

                <strong>
                    Categoría:
                </strong>

                <?php
                echo htmlspecialchars(
                    $producto["categoria"]
                );
                ?>

            </div>


            <!-- BOTÓN AGREGAR AL CARRITO -->

            <button
                class="detalle-carrito-btn"
                type="button"
                onclick="agregarProducto(<?php echo $producto['id']; ?>, '<?php echo addslashes(htmlspecialchars($producto['nombre'])); ?>', <?php echo $producto['precio']; ?>, '<?php echo addslashes(htmlspecialchars($producto['imagen'])); ?>')"
            >

                🛒 Agregar al carrito

            </button>


            <!-- FAVORITO -->

            <button
                class="detalle-favorito-btn"
                type="button"
            >

                🤍 Agregar a favoritos

            </button>


        </div>

    </div>


    <!-- VALORACIONES -->

    <section class="detalle-opiniones">

        <h2>
            ⭐ Opiniones del producto
        </h2>

        <div
            id="lista-valoraciones"
            class="lista-valoraciones"
        >

            <p>
                Cargando valoraciones...
            </p>

        </div>
    </section>


    <!-- COMENTARIOS -->

    <section class="detalle-comentarios">

        <h2>
            💬 Preguntas y comentarios
        </h2>

        <!-- Formulario nuevo comentario -->
        <div class="comentario-form-box">
            <textarea id="nuevo-comentario" placeholder="Escribí una pregunta o comentario sobre este producto..." rows="3"></textarea>
            <div style="text-align: right; margin-top: 10px;">
                <button type="button" id="btn-enviar-comentario" class="enviar-valoracion" style="margin: 0;">💬 Publicar Comentario</button>
            </div>
        </div>

        <div id="lista-comentarios" class="lista-comentarios" style="margin-top: 25px;">
            <p>Cargando comentarios...</p>
        </div>

    </section>


    <!-- ==================================================
         CARRUSEL DE SUGERENCIAS (ESTILO MERCADO LIBRE)
    ================================================== -->
    <?php if (!empty($sugerencias)): ?>
    <section class="detalle-sugerencias">
        <div class="sugerencias-header">
            <h2>✨ Quienes vieron este producto también compraron</h2>
            <div class="carrusel-controles">
                <button type="button" class="carrusel-btn" id="sug-prev" aria-label="Anterior">❮</button>
                <button type="button" class="carrusel-btn" id="sug-next" aria-label="Siguiente">❯</button>
            </div>
        </div>
        <div class="sugerencias-carrusel-wrapper">
            <div class="sugerencias-carrusel" id="sugerencias-carrusel">
                <?php foreach ($sugerencias as $sug): ?>
                    <div class="sugerencia-card" onclick="window.location.href='producto.php?id=<?php echo $sug['id']; ?>'">
                        <div class="sugerencia-img-box">
                            <img src="<?php echo htmlspecialchars($sug['imagen']); ?>" alt="<?php echo htmlspecialchars($sug['nombre']); ?>" onerror="this.src='fts/boxer.jpeg'">
                            <?php if (!empty($sug['etiqueta'])): ?>
                                <span class="sugerencia-tag"><?php echo htmlspecialchars($sug['etiqueta']); ?></span>
                            <?php endif; ?>
                        </div>
                        <div class="sugerencia-info">
                            <h3><?php echo htmlspecialchars($sug['nombre']); ?></h3>
                            <div class="sugerencia-precio">$<?php echo number_format($sug['precio'], 0, ',', '.'); ?></div>
                        </div>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </section>
    <?php endif; ?>

</main>

<div id="notificaciones"></div>

<script>
// ==================================================
// OCULTAR HEADER AL BAJAR / MOSTRAR AL SUBIR
// ==================================================
let lastScrollY = 0;
const headerEl = document.getElementById("detalle-header");

window.addEventListener("scroll", () => {
    const currentScroll = window.pageYOffset || document.documentElement.scrollTop;
    if (currentScroll > lastScrollY && currentScroll > 80) {
        headerEl.style.transform = "translateY(-100%)";
        headerEl.style.opacity = "0";
    } else {
        headerEl.style.transform = "translateY(0)";
        headerEl.style.opacity = "1";
    }
    lastScrollY = currentScroll <= 0 ? 0 : currentScroll;
});

// ==================================================
// CONTROLES DEL CARRUSEL DE SUGERENCIAS
// ==================================================
const carrusel = document.getElementById("sugerencias-carrusel");
const btnPrev = document.getElementById("sug-prev");
const btnNext = document.getElementById("sug-next");

if (carrusel && btnPrev && btnNext) {
    btnPrev.addEventListener("click", () => {
        carrusel.scrollBy({ left: -220, behavior: "smooth" });
    });

    btnNext.addEventListener("click", () => {
        carrusel.scrollBy({ left: 220, behavior: "smooth" });
    });
}

// ==================================================
// NOTIFICACIONES TOAST EN DETALLE
// ==================================================
function mostrarNotificacion(mensaje, tipo = "success") {
    const contenedor = document.getElementById("notificaciones");
    if (!contenedor) return;

    const notificacion = document.createElement("div");
    notificacion.className = `notificacion notificacion-${tipo}`;

    let icono = "✅";
    if (tipo === "error") icono = "❌";
    if (tipo === "info") icono = "ℹ️";
    if (tipo === "heart") icono = "❤️";
    if (tipo === "cart") icono = "🛒";

    notificacion.innerHTML = `
        <span class="notificacion-icon">${icono}</span>
        <span class="notificacion-texto">${mensaje}</span>
        <button class="notificacion-cerrar" type="button" aria-label="Cerrar">✕</button>
    `;

    contenedor.appendChild(notificacion);

    requestAnimationFrame(() => {
        notificacion.classList.add("notificacion-visible");
    });

    const cerrar = () => {
        notificacion.classList.remove("notificacion-visible");
        notificacion.classList.add("notificacion-saliendo");
        setTimeout(() => { notificacion.remove(); }, 300);
    };

    notificacion.querySelector(".notificacion-cerrar").addEventListener("click", cerrar);
    setTimeout(cerrar, 3500);
}

// ==================================================
// ACTUALIZAR CONTADOR DE CARRITO EN HEADER
// ==================================================
function actualizarContadorHeader() {
    const cartCount = document.getElementById("cart-count");
    if (!cartCount) return;
    const carrito = JSON.parse(localStorage.getItem("carrito")) || [];
    const totalCantidad = carrito.reduce((acc, item) => acc + (item.cantidad || 1), 0);
    cartCount.textContent = totalCantidad;
}

actualizarContadorHeader();

// ==================================================
// AGREGAR AL CARRITO SIN SALIR DE LA PÁGINA
// ==================================================
function agregarProducto(id, nombre, precio, imagen) {
    let carrito = JSON.parse(localStorage.getItem("carrito")) || [];
    const existente = carrito.find(item => Number(item.id) === Number(id));

    if (existente) {
        existente.cantidad = (existente.cantidad || 1) + 1;
    } else {
        carrito.push({
            id: Number(id),
            nombre: nombre,
            precio: Number(precio),
            imagen: imagen,
            cantidad: 1
        });
    }

    localStorage.setItem("carrito", JSON.stringify(carrito));

    // Animación visual del botón
    const btn = document.querySelector(".detalle-carrito-btn");
    if (btn) {
        const textoOriginal = btn.innerHTML;
        btn.innerHTML = "✓ ¡Agregado al carrito!";
        btn.classList.add("btn-agregado-exito");
        setTimeout(() => {
            btn.innerHTML = textoOriginal;
            btn.classList.remove("btn-agregado-exito");
        }, 1200);
    }

    actualizarContadorHeader();
    mostrarNotificacion(`"${nombre}" fue agregado al carrito 🛒`, "cart");
}
</script>

<script>
const productoId = <?php echo intval($producto["id"]); ?>;
let puntuacionSeleccionada = 0;

// ==================================================
// ESTRELLAS
// ==================================================
const estrellas = document.querySelectorAll("#selector-estrellas button");

estrellas.forEach(estrella => {
    estrella.addEventListener("click", () => {
        puntuacionSeleccionada = Number(estrella.dataset.rating);
        estrellas.forEach(boton => {
            const rating = Number(boton.dataset.rating);
            if (rating <= puntuacionSeleccionada) {
                boton.classList.add("seleccionada");
            } else {
                boton.classList.remove("seleccionada");
            }
        });
    });
});

// ==================================================
// ENVIAR VALORACIÓN
// ==================================================
document.getElementById("enviar-valoracion").addEventListener("click", async () => {
    if (puntuacionSeleccionada === 0) {
        mostrarNotificacion("Elegí una puntuación entre 1 y 5 estrellas.", "error");
        return;
    }

    const usuario = localStorage.getItem("usuarioActual");
    if (!usuario) {
        mostrarNotificacion("Debes iniciar sesión para valorar este producto.", "error");
        return;
    }

    try {
        const respuesta = await fetch("guardar_valoracion.php", {
            method: "POST",
            headers: {
                "Content-Type": "application/json"
            },
            body: JSON.stringify({
                producto_id: productoId,
                usuario: usuario,
                puntuacion: puntuacionSeleccionada
            })
        });

        const resultado = await respuesta.json();
        mostrarNotificacion(resultado.message, resultado.success ? "success" : "error");

        if (resultado.success) {
            puntuacionSeleccionada = 0;
            estrellas.forEach(boton => boton.classList.remove("seleccionada"));
            cargarValoraciones();
        }
    } catch (error) {
        console.error(error);
        mostrarNotificacion("No se pudo guardar la valoración.", "error");
    }
});

// ==================================================
// CARGAR VALORACIONES (CON BOTÓN ELIMINAR)
// ==================================================
async function cargarValoraciones() {
    try {
        const respuesta = await fetch(`obtener_valoraciones.php?producto_id=${productoId}`);
        const datos = await respuesta.json();

        if (!datos.success) return;

        document.getElementById("promedio-numero").textContent = `${datos.promedio.toString().replace(".", ",")} / 5`;
        document.getElementById("cantidad-valoraciones").textContent = `${datos.cantidad} valoraciones`;

        const estrellasPromedio = document.getElementById("promedio-estrellas");
        let estrellasHTML = "";
        const promedio = Number(datos.promedio);

        for (let i = 1; i <= 5; i++) {
            estrellasHTML += i <= promedio ? "★" : "☆";
        }
        estrellasPromedio.textContent = estrellasHTML;

        const lista = document.getElementById("lista-valoraciones");
        lista.innerHTML = "";

        if (datos.valoraciones.length === 0) {
            lista.innerHTML = `
                <div class="opinion-vacia">
                    <div>⭐</div>
                    <h3>Todavía no hay valoraciones</h3>
                    <p>Sé el primero en valorar este producto.</p>
                </div>
            `;
            return;
        }

        datos.valoraciones.forEach(valoracion => {
            const item = document.createElement("div");
            item.className = "valoracion-item";

            let estrellasUsuario = "";
            for (let i = 1; i <= 5; i++) {
                estrellasUsuario += i <= Number(valoracion.puntuacion) ? "★" : "☆";
            }

            const headerDiv = document.createElement("div");
            headerDiv.className = "valoracion-item-header";

            const usuarioSeguro = document.createElement("strong");
            usuarioSeguro.textContent = valoracion.usuario || "Anónimo";

            const fechaSegura = document.createElement("small");
            fechaSegura.textContent = valoracion.fecha || "";

            headerDiv.appendChild(usuarioSeguro);
            headerDiv.appendChild(fechaSegura);

            const estrellasDiv = document.createElement("div");
            estrellasDiv.className = "valoracion-estrellas";
            estrellasDiv.textContent = estrellasUsuario;

            item.appendChild(estrellasDiv);
            item.appendChild(headerDiv);

            // Botón eliminar si es el autor o admin
            if (valoracion.puede_eliminar) {
                const btnEliminar = document.createElement("button");
                btnEliminar.className = "btn-eliminar-item";
                btnEliminar.type = "button";
                btnEliminar.innerHTML = "🗑️ Eliminar";
                btnEliminar.onclick = () => eliminarValoracion(valoracion.id);
                item.appendChild(btnEliminar);
            }

            lista.appendChild(item);
        });
    } catch (error) {
        console.error("Error cargando valoraciones:", error);
    }
}

// ==================================================
// ELIMINAR VALORACIÓN
// ==================================================
async function eliminarValoracion(valId) {
    if (!confirm("¿Deseas eliminar esta valoración?")) return;

    try {
        const res = await fetch("eliminar_valoracion.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id: valId })
        });
        const data = await res.json();
        mostrarNotificacion(data.message, data.success ? "success" : "error");
        if (data.success) {
            cargarValoraciones();
        }
    } catch (err) {
        mostrarNotificacion("Error al eliminar la valoración.", "error");
    }
}

// ==================================================
// CARGAR COMENTARIOS (CON BOTÓN ELIMINAR)
// ==================================================
async function cargarComentarios() {
    const lista = document.getElementById("lista-comentarios");
    try {
        const res = await fetch(`obtener_comentarios.php?producto_id=${productoId}`);
        const data = await res.json();

        if (!data.success) return;

        lista.innerHTML = "";

        if (data.comentarios.length === 0) {
            lista.innerHTML = `
                <div class="comentarios-vacio">
                    <p>💬 Todavía no hay comentarios. ¡Sé el primero en preguntar!</p>
                </div>
            `;
            return;
        }

        data.comentarios.forEach(c => {
            const item = document.createElement("div");
            item.className = "comentario-item";

            const header = document.createElement("div");
            header.className = "comentario-item-header";

            const user = document.createElement("span");
            user.className = "comentario-item-user";
            user.textContent = "👤 " + c.usuario;

            const date = document.createElement("span");
            date.className = "comentario-item-date";
            date.textContent = c.fecha_formateada || "";

            header.appendChild(user);
            header.appendChild(date);

            const text = document.createElement("p");
            text.className = "comentario-item-text";
            text.textContent = c.comentario;

            item.appendChild(header);
            item.appendChild(text);

            // Botón eliminar si es el autor o admin
            if (c.puede_eliminar) {
                const btnEliminar = document.createElement("button");
                btnEliminar.className = "btn-eliminar-item";
                btnEliminar.type = "button";
                btnEliminar.innerHTML = "🗑️ Eliminar";
                btnEliminar.onclick = () => eliminarComentario(c.id);
                item.appendChild(btnEliminar);
            }

            lista.appendChild(item);
        });
    } catch (err) {
        console.error(err);
    }
}

// ==================================================
// ENVIAR COMENTARIO
// ==================================================
document.getElementById("btn-enviar-comentario").addEventListener("click", async () => {
    const comentarioInput = document.getElementById("nuevo-comentario");
    const texto = comentarioInput.value.trim();

    if (!texto) {
        mostrarNotificacion("Por favor, escribí un comentario.", "error");
        return;
    }

    const usuario = localStorage.getItem("usuarioActual");
    if (!usuario) {
        mostrarNotificacion("Debes iniciar sesión para comentar.", "error");
        return;
    }

    try {
        const res = await fetch("guardar_comentario.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({
                producto_id: productoId,
                comentario: texto
            })
        });

        const data = await res.json();
        mostrarNotificacion(data.message, data.success ? "success" : "error");

        if (data.success) {
            comentarioInput.value = "";
            cargarComentarios();
        }
    } catch (err) {
        mostrarNotificacion("Error al publicar el comentario.", "error");
    }
});

// ==================================================
// ELIMINAR COMENTARIO
// ==================================================
async function eliminarComentario(comId) {
    if (!confirm("¿Deseas eliminar este comentario?")) return;

    try {
        const res = await fetch("eliminar_comentario.php", {
            method: "POST",
            headers: { "Content-Type": "application/json" },
            body: JSON.stringify({ id: comId })
        });
        const data = await res.json();
        mostrarNotificacion(data.message, data.success ? "success" : "error");
        if (data.success) {
            cargarComentarios();
        }
    } catch (err) {
        mostrarNotificacion("Error al eliminar el comentario.", "error");
    }
}

// Iniciar cargas
cargarValoraciones();
cargarComentarios();

// ==================================================
// BOTÓN FAVORITO EN DETALLE
// ==================================================
const favBtn = document.querySelector(".detalle-favorito-btn");
if (favBtn) {
    let favoritos = JSON.parse(localStorage.getItem("favoritos")) || [];

    function actualizarBotonFavorito() {
        const esFav = favoritos.some(id => Number(id) === Number(productoId));
        if (esFav) {
            favBtn.textContent = "❤️ Quitar de favoritos";
            favBtn.classList.add("favorite-active");
        } else {
            favBtn.textContent = "🤍 Agregar a favoritos";
            favBtn.classList.remove("favorite-active");
        }
    }

    actualizarBotonFavorito();

    favBtn.addEventListener("click", () => {
        const indice = favoritos.findIndex(id => Number(id) === Number(productoId));
        if (indice !== -1) {
            favoritos.splice(indice, 1);
            mostrarNotificacion("Producto eliminado de favoritos.", "info");
        } else {
            favoritos.push(Number(productoId));
            mostrarNotificacion("Producto agregado a favoritos ❤️", "heart");
        }
        localStorage.setItem("favoritos", JSON.stringify(favoritos));
        actualizarBotonFavorito();
    });
}
</script>
</body>

</html>