<?php
require_once __DIR__ . '/verificar-sesion.php';
?>

<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Panel de Inicio</title>
    <link rel="stylesheet" href="/public/css/inicio.css">
    <link rel="stylesheet" href="/public/css/sidebar.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.8.0/font/bootstrap-icons.css" rel="stylesheet">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/public/js/sidebar.js" defer></script>
    <script src="/public/js/inicio.js" defer></script>
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
        <section class="header" role="banner">
            <h1>ARTESANÍAS PUMA</h1>
            <p>Tienda de Artesanía Cusqueña</p>
        </section>

        <!-- Estado de carga -->
        <div id="dashboard-cargando" class="text-center my-4">
            <div class="spinner-border text-primary" role="status">
                <span class="visually-hidden">Cargando...</span>
            </div>
            <p class="mt-2">Cargando datos del panel...</p>
        </div>

        <!-- Mensaje de error -->
        <div id="dashboard-error" class="alert alert-danger" style="display: none;"></div>

        <!-- Contenido principal -->
        <div id="dashboard-container" style="display: none;">
            <section class="dashboard" aria-labelledby="dashboard-title">
                <article class="card">
                    <h3 class="card-title">Ventas del Día</h3>
                    <p class="card-value" id="ventas-hoy">S/ 0.00</p>
                </article>
                <article class="card">
                    <h3 class="card-title">Total de Ventas</h3>
                    <p class="card-value" id="total-ventas">S/ 0.00</p>
                </article>
                <article class="card">
                    <h3 class="card-title">Última Venta</h3>
                    <p class="card-value" id="ultima-venta">S/ 0.00</p>
                </article>
                <article class="card">
                    <h3 class="card-title">Total de Productos</h3>
                    <p class="card-value" id="total-productos">0</p>
                </article>
            </section>

            <section class="notificaciones" aria-labelledby="stock-alert-title">
                <h2 id="stock-alert-title">Productos con Stock Bajo</h2>
                <ul id="lista-stock-bajo" class="lista-alertas">
                    <li>Cargando información de stock...</li>
                </ul>
            </section>
        </div>
    </main>
</body>
<script>
    window.addEventListener('pageshow', function(event) {
        if (event.persisted || window.performance.navigation.type === 2) {
            window.location.reload();
        }
    });
</script>

</html>