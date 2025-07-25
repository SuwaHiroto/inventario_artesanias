<?php
require_once __DIR__ . '/../layouts/verificar-sesion.php';
?>
<!DOCTYPE html>
<html lang="es">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Gestión de Usuarios</title>
    <link rel="stylesheet" href="/public/css/sidebar.css">
    <link rel="stylesheet" href="/public/css/gestion-usuario.css">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
</head>

<body>
    <script>
        const BASE_URL = '/'; // AJUSTA ESTO si tu proyecto está en una subcarpeta (ej: '/mi_proyecto/')
    </script>
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
            <h1>Gestión de Usuarios</h1>
        </header>
        <div class="card-gestion">
            <form id="formUsuario" class="formulario-usuario" novalidate>
                <div class="fila-form">
                    <div class="grupo-form"><label for="nombre">Nombre:</label><input type="text" id="nombre" name="nombre" required></div>
                    <div class="grupo-form"><label for="apellidos">Apellidos:</label><input type="text" id="apellidos" name="apellidos" required></div>
                </div>
                <div class="grupo-form"><label for="telefono">Teléfono (9 dígitos):</label><input type="tel" id="telefono" name="telefono" maxlength="9"></div>
                <div class="fila-form">
                    <div class="grupo-form"><label for="user">Usuario (login):</label><input type="text" id="user" name="user" required></div>
                    <div class="grupo-form"><label for="contrasena">Contraseña:</label><input type="password" id="contrasena" name="contrasena" required></div>
                </div>
                <div class="grupo-form"><label for="rol">Rol:</label><select id="rol" name="rol" required>
                        <option value="Empleado">Empleado</option>
                        <option value="Administrador">Administrador</option>
                    </select></div>
                <div class="grupo-form"><label>Permisos:</label>
                    <div id="listaPermisos" class="permisos-info"></div>
                </div>
                <button type="submit" class="btn-crear">Crear Usuario</button>
            </form>
            <div class="lista-usuarios">
                <h2 class="titulo-lista">Lista de Usuarios</h2>
                <div class="table-responsive">
                    <table class="table table-hover align-middle">
                        <thead>
                            <tr>
                                <th>Nombre Completo</th>
                                <th>Usuario</th>
                                <th>Rol</th>
                                <th>Estado</th>
                                <th>Acciones</th>
                            </tr>
                        </thead>
                        <tbody id="tablaUsuariosBody"></tbody>
                    </table>
                </div>
            </div>
        </div>
    </main>

    <div class="modal fade" id="modalEditarUsuario" tabindex="-1" aria-hidden="true">
        <div class="modal-dialog modal-lg modal-dialog-centered">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title">Editar Usuario</h5><button type="button" class="btn-close" data-bs-dismiss="modal"></button>
                </div>
                <div class="modal-body">
                    <form id="formEditarUsuario" novalidate>
                        <input type="hidden" id="edit_id" name="id">
                        <div class="fila-form">
                            <div class="grupo-form"><label for="edit_nombre">Nombre:</label><input type="text" id="edit_nombre" name="nombre" required></div>
                            <div class="grupo-form"><label for="edit_apellidos">Apellidos:</label><input type="text" id="edit_apellidos" name="apellidos" required></div>
                        </div>
                        <div class="grupo-form"><label for="edit_telefono">Teléfono (9 dígitos):</label><input type="tel" id="edit_telefono" name="telefono" maxlength="9"></div>
                        <div class="fila-form">
                            <div class="grupo-form"><label for="edit_user">Usuario:</label><input type="text" id="edit_user" name="user" required></div>
                            <div class="grupo-form"><label for="edit_rol">Rol:</label><select id="edit_rol" name="rol" required>
                                    <option value="Empleado">Empleado</option>
                                    <option value="Administrador">Administrador</option>
                                </select></div>
                        </div>
                        <div class="grupo-form"><label for="edit_contrasena">Nueva Contraseña (opcional):</label><input type="password" id="edit_contrasena" name="contrasena" placeholder="Dejar en blanco para no cambiar"></div>
                    </form>
                </div>
                <div class="modal-footer"><button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button><button type="submit" form="formEditarUsuario" class="btn btn-primary">Guardar Cambios</button></div>
            </div>
        </div>
    </div>

    <script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.0/dist/js/bootstrap.bundle.min.js"></script>
    <script src="/public/js/sidebar.js" defer></script>
    <script src="/public/js/gestion-usuario.js" defer></script>
</body>

</html>