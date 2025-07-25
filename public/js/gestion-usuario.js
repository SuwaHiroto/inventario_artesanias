document.addEventListener('DOMContentLoaded', function () {
    // --- Referencias a Elementos del DOM ---
    const rolSelect = document.getElementById('rol');
    const listaPermisosDiv = document.getElementById('listaPermisos');
    const formUsuario = document.getElementById('formUsuario');
    const telefonoInput = document.getElementById('telefono');
    const tablaUsuariosBody = document.getElementById('tablaUsuariosBody');
    const modalEditarUsuario = new bootstrap.Modal(document.getElementById('modalEditarUsuario'));
    const formEditarUsuario = document.getElementById('formEditarUsuario');
    const editTelefonoInput = document.getElementById('edit_telefono');

    // --- Datos y Estado ---
    const permisosPorRol = {
        Empleado: ["Registrar ventas.", "Gestionar inventario.", "Ver reportes.", "<b>No puede</b> eliminar productos/categorías.", "<b>No puede</b> acceder a la gestión de usuarios."],
        Administrador: ["Acceso completo a todas las secciones.", "Crear, editar y eliminar usuarios.", "Realizar todas las operaciones del sistema."]
    };

    // --- Funciones ---

    function mostrarAlerta(mensaje, tipo = "success") {
        const alertContainer = document.getElementById("alert-container");
        if (!alertContainer) return;
        const id = `alert-${Date.now()}`;
        const wrapper = document.createElement("div");
        wrapper.innerHTML = `<div id="${id}" class="alert alert-${tipo} alert-dismissible fade show" role="alert">${mensaje}<button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button></div>`;
        alertContainer.append(wrapper);
        setTimeout(() => bootstrap.Alert.getOrCreateInstance(document.getElementById(id))?.close(), 5000);
    }

    function actualizarPermisosUI() {
        const rol = rolSelect.value;
        const permisos = permisosPorRol[rol] || [];
        let htmlPermisos = '<ul>' + permisos.map(p => `<li>${p}</li>`).join('') + '</ul>';
        listaPermisosDiv.innerHTML = htmlPermisos;
    }

    function validarTelefono(e) {
        e.target.value = e.target.value.replace(/[^0-9]/g, '');
    }

    async function cargarUsuarios() {
        try {
            // CAMBIO: Usamos la variable BASE_URL para la ruta.
            const response = await fetch(`/app/Controllers/UsuarioController.php?action=listar`);
            if (!response.ok) throw new Error(`Error HTTP ${response.status}: ${response.statusText}`);
            const result = await response.json();
            if (!result.success) throw new Error(result.error);

            tablaUsuariosBody.innerHTML = '';
            result.data.forEach(user => {
                // CAMBIO: Se usan las nuevas clases CSS para los badges de estado.
                const estadoBadge = user.estado == 1
                    ? '<span class="badge-estado badge-activo">Activo</span>'
                    : '<span class="badge-estado badge-inactivo">Inactivo</span>';

                const toggleIcon = user.estado == 1 ? 'fa-toggle-on text-success' : 'fa-toggle-off text-danger';
                const toggleTitle = user.estado == 1 ? 'Desactivar usuario' : 'Activar usuario';
                const tr = document.createElement('tr');

                // CAMBIO: Se añaden los atributos data-label para el diseño responsivo.
                tr.innerHTML = `
                    <td data-label="Nombre Completo">${user.nombre} ${user.apellidos}</td>
                    <td data-label="Usuario">${user.user}</td>
                    <td data-label="Rol">${user.rol}</td>
                    <td data-label="Estado">${estadoBadge}</td>
                    <td data-label="Acciones" class="acciones-tabla">
                        <button class="btn btn-sm btn-primary btn-editar" data-id="${user.id}" title="Editar"><i class="fas fa-edit"></i></button>
                        <button class="btn btn-sm btn-light btn-estado" data-id="${user.id}" title="${toggleTitle}"><i class="fas ${toggleIcon} fs-5"></i></button>
                    </td>
                `;
                tablaUsuariosBody.appendChild(tr);
            });
        } catch (error) { mostrarAlerta(`Error al cargar usuarios: ${error.message}`, 'danger'); }
    }

    async function handleTableClick(e) {
        const target = e.target.closest('button');
        if (!target) return;
        const userId = target.dataset.id;

        if (target.classList.contains('btn-editar')) {
            try {
                const response = await fetch(`/app/Controllers/UsuarioController.php?action=obtener&id=${userId}`);
                const result = await response.json();
                if (!result.success) throw new Error(result.error);

                document.getElementById('edit_id').value = result.data.id;
                document.getElementById('edit_nombre').value = result.data.nombre;
                document.getElementById('edit_apellidos').value = result.data.apellidos;
                document.getElementById('edit_telefono').value = result.data.telefono;
                document.getElementById('edit_user').value = result.data.user;
                document.getElementById('edit_rol').value = result.data.rol;
                document.getElementById('edit_contrasena').value = '';
                modalEditarUsuario.show();
            } catch (error) { mostrarAlerta(`Error al obtener datos: ${error.message}`, 'danger'); }
        }

        if (target.classList.contains('btn-estado')) {
            if (confirm('¿Estás seguro de que quieres cambiar el estado de este usuario?')) {
                const formData = new FormData();
                formData.append('id', userId);
                try {
                    const response = await fetch(`/app/Controllers/UsuarioController.php?action=cambiarEstado`, { method: 'POST', body: formData });
                    const result = await response.json();
                    if (!result.success) throw new Error(result.error);
                    mostrarAlerta(result.data.mensaje, 'success');
                    cargarUsuarios();
                } catch (error) { mostrarAlerta(`Error: ${error.message}`, 'danger'); }
            }
        }
    }

    async function handleFormSubmit(e, form, action) {
        e.preventDefault();
        const btn = e.target.querySelector('button[type="submit"]');
        const originalText = btn.innerHTML;
        btn.disabled = true;
        btn.innerHTML = '<span class="spinner-border spinner-border-sm"></span> Procesando...';

        const formData = new FormData(form);
        try {
            const response = await fetch(`/app/Controllers/UsuarioController.php?action=${action}`, { method: 'POST', body: formData });
            const result = await response.json();
            if (!result.success) throw new Error(result.error);

            if (action === 'actualizar') modalEditarUsuario.hide();
            else form.reset();

            mostrarAlerta(result.data.mensaje, 'success');
            actualizarPermisosUI();
            cargarUsuarios();
        } catch (error) { mostrarAlerta(`Error: ${error.message}`, 'danger'); }
        finally {
            btn.disabled = false;
            btn.innerHTML = originalText;
        }
    }

    // --- Asignación de Event Listeners ---
    rolSelect.addEventListener('change', actualizarPermisosUI);
    telefonoInput.addEventListener('input', validarTelefono);
    editTelefonoInput.addEventListener('input', validarTelefono);
    tablaUsuariosBody.addEventListener('click', handleTableClick);
    formUsuario.addEventListener('submit', (e) => handleFormSubmit(e, formUsuario, 'registrar'));
    formEditarUsuario.addEventListener('submit', (e) => handleFormSubmit(e, formEditarUsuario, 'actualizar'));

    // --- Carga Inicial ---
    actualizarPermisosUI();
    cargarUsuarios();
});