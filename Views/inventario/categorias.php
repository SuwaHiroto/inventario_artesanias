<?php
require_once __DIR__ . '/../layouts/verificar-sesion.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Categorías</title>
    <link rel="stylesheet" href="/public/css/categorias.css">
    <link rel="stylesheet" href="/public/css/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/public/js/sidebar.js" defer></script>
    <script src="/public/js/categorias.js" defer></script>
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
        <header class="header">
            <h1>Categorías</h1>
        </header>

        <div class="controles">
            <div class="busqueda">
                <input type="text" id="buscador" placeholder="Buscar categorías...">
                <i class="fas fa-search"></i>
            </div>

            <!-- CAMBIO: Ocultamos el botón "Nueva Categoría" si no es admin -->
            <?php if ($esAdmin): ?>
                <button id="btnNuevaCategoria" class="btn btn-primary">
                    <i class="fas fa-plus"></i> Nueva Categoría
                </button>
            <?php endif; ?>

        </div>

        <div class="table-responsive">
            <table id="tabla-categorias" class="table table-striped table-bordered align-middle">
                <thead>
                    <tr>
                        <th data-col="nombre">Nombre</th>
                        <th data-col="descripcion">Descripción</th>

                        <!-- CAMBIO: Solo mostramos la columna "Acciones" si es admin -->
                        <?php if ($esAdmin): ?>
                            <th class="acciones-header">Acciones</th>
                        <?php endif; ?>
                    </tr>
                </thead>
                <tbody id="cuerpo-tabla">
                    <!-- El JS llenará esta tabla -->
                </tbody>
            </table>
        </div>

        <!-- El modal se mantiene, ya que el JS controlará su aparición. -->
        <!-- Si un empleado no puede hacer clic en "Nueva Categoría", nunca verá el modal. -->
        <div id="modalCategoria" class="modal" style="display: none;">
            <div class="modal-contenido">
                <span class="cerrar" id="btnCerrarModal">×</span>
                <h2 id="titulo-modal">Nueva Categoría</h2>
                <form id="formCategoria">
                    <input type="hidden" id="categoriaId">
                    <div class="form-group">
                        <label for="nombreCategoria">Nombre:</label>
                        <input type="text" id="nombreCategoria" required>
                    </div>
                    <div class="form-group">
                        <label for="descripcionCategoria">Descripción:</label>
                        <textarea id="descripcionCategoria" rows="3"></textarea>
                    </div>
                    <div class="form-actions">
                        <button type="submit" class="btn btn-success">
                            <i class="fas fa-save"></i> Guardar
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </main>

    <!-- Pasamos la variable de PHP a JavaScript para que JS también sepa el rol -->
    <script>
        const esUsuarioAdmin = <?php echo json_encode($esAdmin); ?>;
    </script>
</body>

</html>