<?php
// /app/Views/layouts/barra-lateral.php

// Es crucial iniciar la sesión si aún no se ha hecho,
// para poder leer la variable de rol.
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}
?>
<?php
require_once __DIR__ . '/verificar-sesion.php';
?>
<!-- Botón de menú móvil -->
<button class="boton-menu" id="boton-menu">
    <i class="fas fa-bars"></i>
</button>

<!-- Barra lateral -->
<div class="barra-lateral">
    <nav class="menu-lateral">
        <ul>
            <li>
                <a href="/app/Views/layouts/inicio.php" class="elemento-menu">
                    <i class="fas fa-house"></i>
                    <span class="texto-menu">Inicio</span>
                </a>
            </li>
            <li class="tiene-submenu">
                <a href="#" class="elemento-menu">
                    <i class="fas fa-boxes-stacked"></i>
                    <span class="texto-menu">Inventario</span>
                    <i class="fas fa-chevron-down flecha"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="/app/Views/inventario/productos.php" class="elemento-submenu">
                            <i class="fas fa-box"></i>
                            <span class="texto-menu">Productos</span>
                        </a>
                    </li>
                    <li>
                        <a href="/app/Views/inventario/categorias.php" class="elemento-submenu">
                            <i class="fas fa-tags"></i>
                            <span class="texto-menu">Categorías</span>
                        </a>
                    </li>
                    <li>
                        <a href="/app/Views/inventario/resumen.php" class="elemento-submenu">
                            <i class="fas fa-box-archive"></i>
                            <span class="texto-menu">Resumen</span>
                        </a>
                    </li>
                </ul>
            </li>
            <li class="tiene-submenu">
                <a href="#" class="elemento-menu">
                    <i class="fas fa-cash-register"></i>
                    <span class="texto-menu">Ventas</span>
                    <i class="fas fa-chevron-down flecha"></i>
                </a>
                <ul class="submenu">
                    <li>
                        <a href="/app/Views/ventas/nueva-venta.php" class="elemento-submenu">
                            <i class="fas fa-cart-plus"></i>
                            <span class="texto-menu">Nueva Venta</span>
                        </a>
                    </li>
                    <li>
                        <a href="/app/Views/ventas/historial.php" class="elemento-submenu">
                            <i class="fas fa-receipt"></i>
                            <span class="texto-menu">Historial</span>
                        </a>
                    </li>
                </ul>
            </li>

            <!-- ======================================================= -->
            <!--   AQUÍ ESTÁ LA LÓGICA DE PERMISOS PARA "USUARIOS" (CORREGIDA)   -->
            <!-- ======================================================= -->
            <?php
            // La condición ahora envuelve TODA la etiqueta <li>.
            // Solo si el usuario es Administrador, se imprimirá este bloque de HTML.
            if (isset($_SESSION['rol_usuario']) && $_SESSION['rol_usuario'] === 'Administrador'):
            ?>
                <li>
                    <a href="/app/Views/Usuario/usuario.php" class="elemento-menu">
                        <i class="fa-regular fa-circle-user"></i>
                        <span class="texto-menu">Usuarios</span>
                    </a>
                </li>
            <?php
            endif;
            // --- FIN DE LA LÓGICA ---
            ?>
            <!-- ======================================================= -->

            <li>
                <a href="/app/Views/Reportes/Reportes.php" class="elemento-menu">
                    <i class="fas fa-chart-line"></i>
                    <span class="texto-menu">Reportes</span>
                </a>
            </li>
            <li>
                <!-- Asegúrate de que esta ruta apunta a tu script de logout -->
                <a href="/app/logout.php" class="elemento-menu logout">
                    <i class="fas fa-sign-out-alt"></i>
                    <span class="texto-menu">Cerrar sesión</span>
                </a>
            </li>
        </ul>
    </nav>
</div>