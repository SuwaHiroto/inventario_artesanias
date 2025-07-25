<?php
require_once __DIR__ . '/../layouts/verificar-sesion.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <title>Historial de Ventas</title>
    <!-- Estilos -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="/public/css/sidebar.css" rel="stylesheet">
    <link rel="stylesheet" href="/public/css/historial-ventas.css" rel="stylesheet"> <!-- Asegúrate de añadir el CSS para la ordenación aquí -->
</head>

<body>
    <?php
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }
    $esAdmin = isset($_SESSION['rol_usuario']) && $_SESSION['rol_usuario'] === 'Administrador';
    include '../layouts/barra-lateral.php';
    ?>
    <main class="container my-4">
        <header class=" header">
            <h1>Historial de Ventas</h1>
        </header>

        <section class=" p-3 mb-4">
            <div class="row g-3 align-items-center">
                <div class="col-md-4">
                    <label for="fechaInicio" class="form-label">Desde:</label>
                    <input type="date" id="fechaInicio" class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="fechaFin" class="form-label">Hasta:</label>
                    <input type="date" id="fechaFin" class="form-control">
                </div>
                <div class="col-md-4">
                    <label for="productoSelect" class="form-label">Producto:</label>
                    <!-- ID CORRECTO: productoSelect -->
                    <select id="productoSelect" class="form-select">
                        <option value="">Todos los productos</option>
                    </select>
                </div>
            </div>
        </section>

        <div class="table-responsive">
            <!-- ID CORRECTO EN LA TABLA: tablaVentas -->
            <table id="tablaVentas" class="table table-striped table-bordered table-hover align-middle">
                <thead>
                    <!-- ESTRUCTURA CORRECTA DEL THEAD -->
                    <tr class="table-dark">
                        <th class="sortable" data-sort="fecha">Fecha</th>
                        <th class="sortable" data-sort="producto">Producto</th>
                        <th class="sortable" data-sort="cantidad">Cantidad</th>
                        <th class="sortable" data-sort="precio_unitario">Precio Unitario</th>
                        <th class="sortable" data-sort="monto_total">Monto Total</th>
                    </tr>
                </thead>
                <tbody>
                    <!-- El contenido se generará aquí -->
                </tbody>
            </table>
        </div>
    </main>

    <!-- SCRIPTS AL FINAL -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.2/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/public/js/sidebar.js" defer></script>
    <script src="/public/js/historial-ventas.js" defer></script>
</body>

</html>