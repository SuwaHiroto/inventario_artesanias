<?php
require_once __DIR__ . '/../layouts/verificar-sesion.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Productos</title>
    <link rel="stylesheet" href="/public/css/sidebar.css">
    <link rel="stylesheet" href="/public/css/productos.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/public/js/sidebar.js" defer></script>
    <script src="/public/js/productos.js" defer></script>

</head>

<body>
    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $esAdmin = isset($_SESSION['rol_usuario']) && $_SESSION['rol_usuario'] === 'Administrador';
    include '../layouts/barra-lateral.php';
    ?>

    <main class="contenedor">
        <div id="alert-container" style="position:fixed;top:20px;right:20px;z-index:9999;width:350px;"></div>
        <header class="header" role="banner">
            <h1>Lista de Productos</h1>
        </header>

        <div class="controles">
            <input type="text" id="busqueda" placeholder="Buscar por nombre o categoría" class="barra-busqueda" />
            <div class="controles-botones">
                <!-- CAMBIO: Ocultamos el botón "Registrar" si no es admin -->
                <?php if ($esAdmin): ?>
                    <button onclick="abrirModal()">Registrar Producto</button>
                    <button id="btn-inactivos">Mostrar Inactivos</button>
                <?php endif; ?>
                <button id="btn-activos">Mostrar Activos</button>
            </div>
        </div>

        <div class="table-responsive-wrapper">
            <table id="tabla-productos" class="table table-striped table-bordered align-middle">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Descripción</th>
                        <th>Precio</th>
                        <th>Categoría</th>
                        <th>Stock</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody></tbody>
            </table>
        </div>

        <!-- CAMBIO: El modal de registrar/editar solo es necesario para el admin -->
        <?php if ($esAdmin): ?>
            <dialog id="modal">
                <form id="formProducto">
                    <h2 id="titulo-modal">Registrar Producto</h2>
                    <div class="form-group"><label for="nombre">Nombre</label><input type="text" id="nombre" required /></div>
                    <div class="form-group"><label for="descripcion">Descripción</label><input type="text" id="descripcion" required /></div>
                    <div class="form-group"><label for="precio">Precio</label><input type="number" id="precio" step="0.01" required /></div>
                    <div class="form-group"><label for="stock_inicial">Stock Inicial</label><input type="number" id="stock" required /></div>
                    <div class="form-group"><label for="id_categoria">Categoría</label><select id="id_categoria" required>
                            <option value="">Seleccione</option>
                        </select></div>
                    <div class="form-actions"><button type="submit">Guardar</button><button type="button" onclick="cerrarModal()">Cancelar</button></div>
                </form>
            </dialog>

            <dialog id="modalEliminar">
                <form id="formEliminar">
                    <h2 style="color: red;">Dar de Baja Producto</h2>
                    <p>¿Estás seguro de dar de baja este producto? No se eliminará permanentemente.</p>
                    <input type="hidden" id="idEliminar" />
                    <div class="form-actions"><button type="submit">Confirmar</button><button type="button" onclick="cerrarModalEliminar()">Cancelar</button></div>
                </form>
            </dialog>
        <?php endif; ?>

        <!-- El modal de stock está disponible para todos -->
        <dialog id="modalStock">
            <form id="formStock">
                <h2>Actualizar Stock</h2>
                <input type="hidden" id="idStock" />
                <div class="form-group">
                    <label for="nuevoStock">Nuevo stock</label>
                    <input type="number" id="nuevoStock" required />
                </div>
                <div class="form-actions"><button type="submit">Actualizar</button><button type="button" onclick="cerrarModalStock()">Cancelar</button></div>
            </form>
        </dialog>
    </main>

    <!-- Pasamos el rol a JavaScript -->
    <script>
        const esUsuarioAdmin = <?php echo json_encode($esAdmin); ?>;
    </script>
</body>

</html>