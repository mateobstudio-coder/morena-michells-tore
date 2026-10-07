<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Proteger acceso: Solo administradores logueados
if (!isset($_SESSION["rol"]) || $_SESSION["rol"] !== "admin") {
    header("Location: index.php");
    exit;
}

$adminNombre = $_SESSION["usuario"] ?? "Admin";
?>
<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Administración - Michell Store</title>
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@400;500;600;700&display=swap" rel="stylesheet">
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }
        body {
            font-family: 'Poppins', sans-serif;
            background-color: #F8F4F4;
            color: #3B151B;
            min-height: 100vh;
        }

        /* Header */
        .admin-header {
            background-color: #E96B6B;
            color: white;
            padding: 16px 24px;
            display: flex;
            justify-content: space-between;
            align-items: center;
            box-shadow: 0 2px 10px rgba(0,0,0,0.1);
            position: sticky;
            top: 0;
            z-index: 100;
        }
        .admin-brand {
            display: flex;
            align-items: center;
            gap: 12px;
        }
        .admin-brand h1 {
            font-size: 20px;
            font-weight: 700;
        }
        .admin-brand span {
            background: rgba(255,255,255,0.25);
            padding: 4px 10px;
            border-radius: 20px;
            font-size: 12px;
            font-weight: 600;
            letter-spacing: 0.5px;
        }
        .admin-nav {
            display: flex;
            align-items: center;
            gap: 12px;
            flex-wrap: wrap;
        }
        .admin-user {
            font-size: 14px;
            background: rgba(0,0,0,0.15);
            padding: 6px 14px;
            border-radius: 20px;
        }
        .btn-nav {
            background: white;
            color: #E96B6B;
            text-decoration: none;
            padding: 8px 16px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 13px;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 6px;
            border: none;
            cursor: pointer;
            box-shadow: 0 2px 5px rgba(0,0,0,0.08);
        }
        .btn-nav:hover {
            background: #FFE5E1;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.15);
        }
        .btn-nav:active {
            transform: translateY(0);
        }
        .btn-logout {
            background: #3B151B;
            color: white;
        }
        .btn-logout:hover {
            background: #1f080c;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0,0,0,0.25);
        }

        /* Container */
        .admin-container {
            max-width: 1200px;
            margin: 30px auto;
            padding: 0 20px;
        }

        /* Stats */
        .stats-grid {
            display: grid;
            grid-template-columns: repeat(auto-fit, minmax(220px, 1fr));
            gap: 20px;
            margin-bottom: 30px;
        }
        .stat-card {
            background: white;
            padding: 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            display: flex;
            align-items: center;
            gap: 16px;
            transition: transform 0.2s ease, box-shadow 0.2s ease;
        }
        .stat-card:hover {
            transform: translateY(-3px);
            box-shadow: 0 6px 20px rgba(0,0,0,0.08);
        }
        .stat-icon {
            font-size: 32px;
            background: #FFE5E1;
            width: 60px;
            height: 60px;
            display: flex;
            align-items: center;
            justify-content: center;
            border-radius: 12px;
        }
        .stat-info h3 {
            font-size: 24px;
            color: #3B151B;
            font-weight: 700;
        }
        .stat-info p {
            font-size: 13px;
            color: #777;
        }

        /* Toolbar */
        .toolbar {
            background: white;
            padding: 16px 20px;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            display: flex;
            justify-content: space-between;
            align-items: center;
            flex-wrap: wrap;
            gap: 15px;
            margin-bottom: 25px;
        }
        .toolbar-search {
            display: flex;
            gap: 10px;
            flex: 1;
            min-width: 250px;
            max-width: 450px;
        }
        .toolbar-search input, .toolbar-filter select {
            width: 100%;
            padding: 10px 14px;
            border: 1px solid #ddd;
            border-radius: 8px;
            font-family: inherit;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .toolbar-search input:focus, .toolbar-filter select:focus {
            border-color: #E96B6B;
            box-shadow: 0 0 0 3px rgba(233, 107, 107, 0.15);
        }
        .btn-primary {
            background: #E96B6B;
            color: white;
            border: none;
            padding: 10px 20px;
            border-radius: 8px;
            font-weight: 600;
            font-size: 14px;
            cursor: pointer;
            display: inline-flex;
            align-items: center;
            gap: 8px;
            transition: all 0.2s ease;
            box-shadow: 0 2px 6px rgba(233, 107, 107, 0.25);
        }
        .btn-primary:hover {
            background: #d85757;
            transform: translateY(-2px);
            box-shadow: 0 5px 15px rgba(233, 107, 107, 0.4);
        }
        .btn-primary:active {
            transform: translateY(0);
        }

        /* Table */
        .table-container {
            background: white;
            border-radius: 12px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            text-align: left;
            font-size: 14px;
        }
        th {
            background: #FAF2F2;
            color: #3B151B;
            padding: 14px 16px;
            font-weight: 600;
            border-bottom: 2px solid #EFE0E0;
        }
        td {
            padding: 14px 16px;
            border-bottom: 1px solid #EEE;
            vertical-align: middle;
        }
        tr:hover {
            background-color: #FFFAFA;
        }
        .product-thumb {
            width: 50px;
            height: 50px;
            object-fit: cover;
            border-radius: 8px;
            border: 1px solid #EEE;
        }
        .badge {
            padding: 4px 10px;
            border-radius: 12px;
            font-size: 11px;
            font-weight: 600;
            display: inline-block;
        }
        .badge-visible {
            background: #D4EDDA;
            color: #155724;
        }
        .badge-hidden {
            background: #F8D7DA;
            color: #721C24;
        }
        .badge-tag {
            background: #FFE5E1;
            color: #E96B6B;
        }
        .actions-cell {
            display: flex;
            gap: 8px;
            flex-wrap: wrap;
        }
        .btn-action {
            padding: 7px 12px;
            border-radius: 6px;
            border: none;
            cursor: pointer;
            font-size: 13px;
            font-weight: 600;
            transition: all 0.2s ease;
            display: inline-flex;
            align-items: center;
            gap: 5px;
        }
        .btn-edit { background: #E7F1FF; color: #0066CC; }
        .btn-edit:hover {
            background: #0066CC;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(0, 102, 204, 0.3);
        }
        .btn-edit:active { transform: translateY(0); }

        .btn-toggle { background: #FFF3CD; color: #856404; }
        .btn-toggle:hover {
            background: #D39E00;
            color: white;
            transform: translateY(-2px);
            box-shadow: 0 4px 10px rgba(211, 158, 0, 0.3);
        }
        .btn-toggle:active { transform: translateY(0); }

        .btn-delete {
            background: #D32F2F;
            color: #FFFFFF;
            font-weight: 700;
            box-shadow: 0 2px 6px rgba(211, 47, 47, 0.35);
        }
        .btn-delete:hover {
            background: #B71C1C;
            transform: translateY(-2px) scale(1.04);
            box-shadow: 0 4px 14px rgba(183, 28, 28, 0.5);
        }
        .btn-delete:active { transform: translateY(0); }

        /* Modal */
        .modal-backdrop {
            position: fixed;
            top: 0; left: 0; width: 100%; height: 100%;
            background: rgba(0,0,0,0.5);
            display: none;
            justify-content: center;
            align-items: center;
            z-index: 200;
            padding: 20px;
        }
        .modal-content {
            background: white;
            border-radius: 16px;
            max-width: 600px;
            width: 100%;
            max-height: 90vh;
            overflow-y: auto;
            box-shadow: 0 10px 30px rgba(0,0,0,0.2);
            padding: 25px;
        }
        .modal-header {
            display: flex;
            justify-content: space-between;
            align-items: center;
            margin-bottom: 20px;
            padding-bottom: 10px;
            border-bottom: 1px solid #EEE;
        }
        .modal-header h2 {
            font-size: 18px;
            color: #E96B6B;
        }
        .modal-close {
            background: none;
            border: none;
            font-size: 20px;
            cursor: pointer;
            color: #999;
            transition: all 0.2s ease;
        }
        .modal-close:hover {
            color: #E96B6B;
            transform: scale(1.2);
        }
        .form-group {
            margin-bottom: 16px;
        }
        .form-group label {
            display: block;
            font-size: 13px;
            font-weight: 600;
            margin-bottom: 6px;
            color: #3B151B;
        }
        .form-group input, .form-group select, .form-group textarea {
            width: 100%;
            padding: 10px 12px;
            border: 1px solid #DDD;
            border-radius: 8px;
            font-family: inherit;
            font-size: 14px;
            outline: none;
            transition: border-color 0.2s ease, box-shadow 0.2s ease;
        }
        .form-group input:focus, .form-group select:focus, .form-group textarea:focus {
            border-color: #E96B6B;
            box-shadow: 0 0 0 3px rgba(233, 107, 107, 0.15);
        }
        .form-row {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 15px;
        }
        .preview-img-container {
            margin-top: 10px;
            text-align: center;
        }
        .preview-img {
            max-height: 120px;
            border-radius: 8px;
            border: 1px solid #DDD;
            display: none;
        }
        .modal-footer {
            margin-top: 25px;
            display: flex;
            justify-content: flex-end;
            gap: 12px;
        }
        .btn-secondary {
            background: #EEE;
            color: #555;
            border: none;
            padding: 10px 18px;
            border-radius: 8px;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.2s ease;
        }
        .btn-secondary:hover {
            background: #ddd;
            color: #222;
            transform: translateY(-1px);
        }

        /* Notificaciones Toast */
        .toast-container {
            position: fixed;
            bottom: 20px;
            right: 20px;
            z-index: 300;
            display: flex;
            flex-direction: column;
            gap: 10px;
        }
        .toast {
            background: #3B151B;
            color: white;
            padding: 12px 20px;
            border-radius: 8px;
            box-shadow: 0 5px 15px rgba(0,0,0,0.2);
            font-size: 14px;
            display: flex;
            align-items: center;
            gap: 10px;
            animation: slideIn 0.3s ease forwards;
        }
        .toast-success { background: #28a745; }
        .toast-error { background: #dc3545; }
        @keyframes slideIn {
            from { transform: translateX(100%); opacity: 0; }
            to { transform: translateX(0); opacity: 1; }
        }
    </style>
</head>
<body>

    <!-- Header -->
    <header class="admin-header">
        <div class="admin-brand">
            <h1>Michell Store</h1>
            <span>ADMINISTRACIÓN</span>
        </div>
        <div class="admin-nav">
            <span class="admin-user">👑 <?php echo htmlspecialchars($adminNombre); ?></span>
            <button id="btn-cambiar-pass" class="btn-nav" type="button">🔐 Cambiar Mi Clave</button>
            <a href="index.php" class="btn-nav">🛍️ Ver Tienda</a>
            <button id="btn-admin-logout" class="btn-nav btn-logout">🚪 Salir</button>
        </div>
    </header>

    <main class="admin-container">

        <!-- Stats -->
        <section class="stats-grid">
            <div class="stat-card">
                <div class="stat-icon">📦</div>
                <div class="stat-info">
                    <h3 id="stat-total">0</h3>
                    <p>Total Productos</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">👁️</div>
                <div class="stat-info">
                    <h3 id="stat-visibles">0</h3>
                    <p>Productos Visibles</p>
                </div>
            </div>
            <div class="stat-card">
                <div class="stat-icon">🚫</div>
                <div class="stat-info">
                    <h3 id="stat-ocultos">0</h3>
                    <p>Productos Ocultos</p>
                </div>
            </div>
        </section>

        <!-- Toolbar -->
        <section class="toolbar">
            <div class="toolbar-search">
                <input type="text" id="admin-search" placeholder="🔍 Buscar producto por nombre...">
            </div>
            <div class="toolbar-filter">
                <select id="admin-category-filter">
                    <option value="all">Todas las categorías</option>
                    <option value="remeras">Remeras</option>
                    <option value="interior">Ropa Interior</option>
                    <option value="calzas">Calzas</option>
                    <option value="medias">Medias</option>
                </select>
            </div>
            <button id="btn-nuevo-producto" class="btn-primary">➕ Nuevo Producto</button>
        </section>

        <!-- Product Table -->
        <section class="table-container">
            <table>
                <thead>
                    <tr>
                        <th>Foto</th>
                        <th>Nombre</th>
                        <th>Categoría</th>
                        <th>Precio</th>
                        <th>Etiqueta</th>
                        <th>Estado</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="admin-products-tbody">
                    <tr>
                        <td colspan="7" style="text-align: center; padding: 30px;">Cargando catálogo...</td>
                    </tr>
                </tbody>
            </table>
        </section>

    </main>

    <!-- Modal Formulario Producto (Crear / Editar) -->
    <div id="product-modal" class="modal-backdrop">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modal-title">➕ Cargar Nuevo Producto</h2>
                <button type="button" class="modal-close" onclick="cerrarModal()">✕</button>
            </div>
            <form id="product-form" enctype="multipart/form-data">
                <input type="hidden" id="prod-id" name="id" value="0">
                <input type="hidden" name="accion" value="guardar">
                <input type="hidden" id="prod-imagen-actual" name="imagen_url" value="">

                <div class="form-group">
                    <label for="prod-nombre">Nombre del Producto *</label>
                    <input type="text" id="prod-nombre" name="nombre" placeholder="Ej: Top Deportivo Negro" required>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="prod-categoria">Categoría *</label>
                        <select id="prod-categoria" name="categoria" required>
                            <option value="remeras">👕 Remeras / Tops</option>
                            <option value="interior">🩲 Ropa Interior</option>
                            <option value="calzas">👖 Calzas / Shorts</option>
                            <option value="medias">🧦 Medias</option>
                        </select>
                    </div>

                    <div class="form-group">
                        <label for="prod-precio">Precio ($) *</label>
                        <input type="number" id="prod-precio" name="precio" placeholder="12500" min="0" step="0.01" required>
                    </div>
                </div>

                <div class="form-row">
                    <div class="form-group">
                        <label for="prod-etiqueta">Etiqueta (Opcional)</label>
                        <input type="text" id="prod-etiqueta" name="etiqueta" placeholder="Ej: OFERTA, NUEVO, etc.">
                    </div>

                    <div class="form-group">
                        <label for="prod-visible">Visibilidad en la tienda</label>
                        <select id="prod-visible" name="visible">
                            <option value="1">🟢 Visible para clientes</option>
                            <option value="0">🔴 Oculto (pausado)</option>
                        </select>
                    </div>
                </div>

                <div class="form-group">
                    <label for="prod-imagen-file">Subir Imagen (JPG, PNG, WEBP)</label>
                    <input type="file" id="prod-imagen-file" name="imagen_archivo" accept="image/*">
                    <div class="preview-img-container">
                        <img id="preview-img" class="preview-img" src="" alt="Vista previa">
                    </div>
                </div>

                <div class="form-group">
                    <label for="prod-descripcion">Descripción del Producto</label>
                    <textarea id="prod-descripcion" name="descripcion" rows="3" placeholder="Detalles de tela, talles, calce..."></textarea>
                </div>

                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="cerrarModal()">Cancelar</button>
                    <button type="submit" class="btn-primary" id="btn-guardar-prod">💾 Guardar Producto</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Modal Cambiar Contraseña -->
    <div id="pass-modal" class="modal-backdrop">
        <div class="modal-content" style="max-width: 440px;">
            <div class="modal-header">
                <h2>🔐 Cambiar Mi Contraseña</h2>
                <button type="button" class="modal-close" onclick="cerrarModalPass()">✕</button>
            </div>
            <form id="pass-form">
                <input type="hidden" name="accion" value="cambiar_password">
                <div class="form-group">
                    <label for="pass-nueva">Nueva Contraseña *</label>
                    <input type="password" id="pass-nueva" name="password_nueva" placeholder="Mínimo 4 caracteres" required autocomplete="new-password">
                </div>
                <div class="form-group">
                    <label for="pass-confirmar">Confirmar Nueva Contraseña *</label>
                    <input type="password" id="pass-confirmar" placeholder="Repetir contraseña" required autocomplete="new-password">
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn-secondary" onclick="cerrarModalPass()">Cancelar</button>
                    <button type="submit" class="btn-primary" id="btn-guardar-pass">Guardar Nueva Clave</button>
                </div>
            </form>
        </div>
    </div>

    <!-- Toast container -->
    <div id="toast-container" class="toast-container"></div>

    <script>
        let productosList = [];

        // Mostrar Toast
        function mostrarToast(mensaje, tipo = "success") {
            const container = document.getElementById("toast-container");
            const toast = document.createElement("div");
            toast.className = `toast toast-${tipo}`;
            toast.textContent = (tipo === "success" ? "✅ " : "❌ ") + mensaje;
            container.appendChild(toast);
            setTimeout(() => {
                toast.remove();
            }, 3500);
        }

        // Cargar todos los productos
        async function cargarProductosAdmin() {
            try {
                const res = await fetch("admin_acciones.php?accion=listar");
                const data = await res.json();
                if (data.success) {
                    productosList = data.productos;
                    actualizarEstadisticas();
                    renderizarTabla();
                } else {
                    mostrarToast(data.message, "error");
                }
            } catch (err) {
                console.error(err);
                mostrarToast("Error al cargar la lista de productos.", "error");
            }
        }

        function actualizarEstadisticas() {
            const total = productosList.length;
            const visibles = productosList.filter(p => Number(p.visible) === 1).length;
            const ocultos = total - visibles;

            document.getElementById("stat-total").textContent = total;
            document.getElementById("stat-visibles").textContent = visibles;
            document.getElementById("stat-ocultos").textContent = ocultos;
        }

        function renderizarTabla() {
            const tbody = document.getElementById("admin-products-tbody");
            const busqueda = document.getElementById("admin-search").value.toLowerCase().trim();
            const catFiltro = document.getElementById("admin-category-filter").value;

            const filtrados = productosList.filter(p => {
                const coincideNombre = p.nombre.toLowerCase().includes(busqueda);
                const coincideCat = catFiltro === "all" || p.categoria === catFiltro;
                return coincideNombre && coincideCat;
            });

            if (filtrados.length === 0) {
                tbody.innerHTML = `<tr><td colspan="7" style="text-align: center; padding: 25px; color: #888;">No se encontraron productos.</td></tr>`;
                return;
            }

            tbody.innerHTML = "";
            filtrados.forEach(p => {
                const tr = document.createElement("tr");
                const esVisible = Number(p.visible) === 1;

                tr.innerHTML = `
                    <td><img src="${p.imagen}" alt="${p.nombre}" class="product-thumb" onerror="this.src='fts/boxer.jpeg'"></td>
                    <td><strong>${p.nombre}</strong></td>
                    <td>${p.categoria}</td>
                    <td><strong>$${Number(p.precio).toLocaleString()}</strong></td>
                    <td>${p.etiqueta ? `<span class="badge badge-tag">${p.etiqueta}</span>` : "-"}</td>
                    <td>
                        <span class="badge ${esVisible ? 'badge-visible' : 'badge-hidden'}">
                            ${esVisible ? '🟢 Visible' : '🔴 Oculto'}
                        </span>
                    </td>
                    <td>
                        <div class="actions-cell">
                            <button class="btn-action btn-toggle" onclick="toggleVisibilidad(${p.id})">
                                ${esVisible ? '🚫 Ocultar' : '👁️ Mostrar'}
                            </button>
                            <button class="btn-action btn-edit" onclick="abrirEditar(${p.id})">
                                ✏️ Editar
                            </button>
                            <button class="btn-action btn-delete" onclick="eliminarProducto(${p.id})">
                                🗑️ Eliminar
                            </button>
                        </div>
                    </td>
                `;
                tbody.appendChild(tr);
            });
        }

        // Toggle visibilidad
        async function toggleVisibilidad(id) {
            const formData = new FormData();
            formData.append("accion", "toggle_visibilidad");
            formData.append("id", id);

            try {
                const res = await fetch("admin_acciones.php", {
                    method: "POST",
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    mostrarToast(data.message, "success");
                    cargarProductosAdmin();
                } else {
                    mostrarToast(data.message, "error");
                }
            } catch (err) {
                mostrarToast("Error al cambiar estado.", "error");
            }
        }

        // Eliminar producto
        async function eliminarProducto(id) {
            if (!confirm("⚠️ ¿Estás seguro de que deseas ELIMINAR este producto permanentemente?")) {
                return;
            }
            const formData = new FormData();
            formData.append("accion", "eliminar");
            formData.append("id", id);

            try {
                const res = await fetch("admin_acciones.php", {
                    method: "POST",
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    mostrarToast(data.message, "success");
                    cargarProductosAdmin();
                } else {
                    mostrarToast(data.message, "error");
                }
            } catch (err) {
                mostrarToast("Error al eliminar el producto.", "error");
            }
        }

        // Modal Controls
        const modal = document.getElementById("product-modal");
        const form = document.getElementById("product-form");
        const previewImg = document.getElementById("preview-img");

        function cerrarModal() {
            modal.style.display = "none";
            form.reset();
            previewImg.style.display = "none";
        }

        document.getElementById("btn-nuevo-producto").addEventListener("click", () => {
            form.reset();
            document.getElementById("prod-id").value = "0";
            document.getElementById("prod-imagen-actual").value = "";
            document.getElementById("modal-title").textContent = "➕ Cargar Nuevo Producto";
            previewImg.style.display = "none";
            modal.style.display = "flex";
        });

        function abrirEditar(id) {
            const p = productosList.find(item => Number(item.id) === Number(id));
            if (!p) return;

            form.reset();
            document.getElementById("prod-id").value = p.id;
            document.getElementById("prod-nombre").value = p.nombre;
            document.getElementById("prod-categoria").value = p.categoria;
            document.getElementById("prod-precio").value = p.precio;
            document.getElementById("prod-etiqueta").value = p.etiqueta || "";
            document.getElementById("prod-visible").value = p.visible;
            document.getElementById("prod-descripcion").value = p.descripcion || "";
            document.getElementById("prod-imagen-actual").value = p.imagen;

            if (p.imagen) {
                previewImg.src = p.imagen;
                previewImg.style.display = "inline-block";
            } else {
                previewImg.style.display = "none";
            }

            document.getElementById("modal-title").textContent = "✏️ Editar Producto #" + p.id;
            modal.style.display = "flex";
        }

        // Preview imagen al seleccionar archivo
        document.getElementById("prod-imagen-file").addEventListener("change", function(e) {
            const file = e.target.files[0];
            if (file) {
                const reader = new FileReader();
                reader.onload = function(evt) {
                    previewImg.src = evt.target.result;
                    previewImg.style.display = "inline-block";
                }
                reader.readAsDataURL(file);
            }
        });

        // Enviar Formulario Guardar / Editar
        form.addEventListener("submit", async function(e) {
            e.preventDefault();
            const btnGuardar = document.getElementById("btn-guardar-prod");
            btnGuardar.disabled = true;
            btnGuardar.textContent = "Guardando...";

            const formData = new FormData(form);

            try {
                const res = await fetch("admin_acciones.php", {
                    method: "POST",
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    mostrarToast(data.message, "success");
                    cerrarModal();
                    cargarProductosAdmin();
                } else {
                    mostrarToast(data.message, "error");
                }
            } catch (err) {
                mostrarToast("Error al procesar la solicitud.", "error");
            } finally {
                btnGuardar.disabled = false;
                btnGuardar.textContent = "💾 Guardar Producto";
            }
        });

        // Filtros y Búsqueda
        document.getElementById("admin-search").addEventListener("input", renderizarTabla);
        document.getElementById("admin-category-filter").addEventListener("change", renderizarTabla);

        // Logout
        document.getElementById("btn-admin-logout").addEventListener("click", async () => {
            await fetch("auth_logout.php");
            window.location.href = "index.php";
        });

        // ==========================================
        // CAMBIAR CONTRASEÑA MODAL & HANDLERS
        // ==========================================
        const passModal = document.getElementById("pass-modal");
        const passForm = document.getElementById("pass-form");

        function cerrarModalPass() {
            passModal.style.display = "none";
            passForm.reset();
        }

        document.getElementById("btn-cambiar-pass").addEventListener("click", () => {
            passForm.reset();
            passModal.style.display = "flex";
        });

        passForm.addEventListener("submit", async function(e) {
            e.preventDefault();
            const passNueva = document.getElementById("pass-nueva").value;
            const passConfirm = document.getElementById("pass-confirmar").value;

            if (passNueva !== passConfirm) {
                mostrarToast("Las contraseñas no coinciden.", "error");
                return;
            }

            if (passNueva.length < 4) {
                mostrarToast("La contraseña debe tener al menos 4 caracteres.", "error");
                return;
            }

            const btnGuardarPass = document.getElementById("btn-guardar-pass");
            btnGuardarPass.disabled = true;
            btnGuardarPass.textContent = "Actualizando...";

            const formData = new FormData(passForm);

            try {
                const res = await fetch("admin_acciones.php", {
                    method: "POST",
                    body: formData
                });
                const data = await res.json();
                if (data.success) {
                    mostrarToast(data.message, "success");
                    cerrarModalPass();
                } else {
                    mostrarToast(data.message, "error");
                }
            } catch (err) {
                mostrarToast("Error al cambiar la contraseña.", "error");
            } finally {
                btnGuardarPass.disabled = false;
                btnGuardarPass.textContent = "Guardar Nueva Clave";
            }
        });

        // Iniciar
        cargarProductosAdmin();
    </script>
</body>
</html>
