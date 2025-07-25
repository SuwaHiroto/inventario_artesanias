<?php
require_once __DIR__ . '/../layouts/verificar-sesion.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Nueva Venta - Artesanías Cusco</title>
    <link rel="stylesheet" href="/public/css/sidebar.css">
    <link rel="stylesheet" href="/public/css/nueva-venta.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Bootstrap CSS -->
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <!-- Bootstrap JS -->
    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/public/js/sidebar.js" defer></script>
    <script src="/public/js/nueva-venta.js" defer></script>
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
        <!-- Alert container for notifications -->
        <div id="alert-container" style="position:fixed;top:20px;right:20px;z-index:9999;width:350px;"></div>

        <section class="header">
            <h1>Registrar Nueva Venta</h1>
        </section>

        <form id="formVenta" class="form-venta">
            <div class="seleccion-producto">
                <h2>Seleccionar Producto:</h2>
                <div class="controles-producto">
                    <select id="productoSelect" class="select-producto">
                        <option value="">Seleccione un producto</option>
                    </select>
                    <input type="number" id="cantidad" class="input-cantidad" min="1" value="1">
                    <button type="button" id="btnAgregar" class="btn-agregar">
                        <i class="fas fa-plus"></i> Agregar
                    </button>
                </div>
            </div>

            <table class="table table-striped table-bordered align-middle tabla-venta">
                <thead>
                    <tr>
                        <th>Producto</th>
                        <th>Cantidad</th>
                        <th>Precio Unitario</th>
                        <th>Subtotal</th>
                        <th>Acciones</th>
                    </tr>
                </thead>
                <tbody id="detalleVenta">
                    <tr>
                        <td colspan="5" class="sin-productos">No hay productos agregados</td>
                    </tr>
                </tbody>
                <tfoot>
                    <tr class="fila-total">
                        <td colspan="3">Total:</td>
                        <td colspan="2" id="totalVenta" class="total-venta">S/. 0.00</td>
                    </tr>
                </tfoot>
            </table>

            <button type="submit" id="registrarVenta" class="btn-registrar">
                <i class="fas fa-save"></i> Registrar Venta
            </button>
        </form>
    </main>

</body>

</html>