document.addEventListener('DOMContentLoaded', function () {
    const app = new CategoriasApp();
});

class CategoriasApp {
    constructor() {
        this.categorias = [];
        this.orden = { columna: 'nombre', direccion: 'asc' };

        // <-- CAMBIO: Leemos la variable global que creamos en la vista PHP.
        // Si no está definida, asumimos que no es admin por seguridad.
        this.esAdmin = typeof esUsuarioAdmin !== 'undefined' && esUsuarioAdmin;

        this.initElements();
        this.initEventListeners();
        this.cargarCategorias();
    }

    initElements() {
        // Elementos principales
        this.buscador = document.getElementById('buscador');
        this.tabla = document.getElementById('cuerpo-tabla');

        // <-- CAMBIO: Solo buscamos los elementos de admin si el usuario tiene el rol.
        // Esto previene errores de "elemento no encontrado" para los empleados.
        if (this.esAdmin) {
            this.btnNueva = document.getElementById('btnNuevaCategoria');
            this.modal = document.getElementById('modalCategoria');
            this.btnCerrarModal = document.getElementById('btnCerrarModal');
            this.form = document.getElementById('formCategoria');
            this.tituloModal = document.getElementById('titulo-modal');
            this.categoriaId = document.getElementById('categoriaId');
            this.nombreInput = document.getElementById('nombreCategoria');
            this.descripcionInput = document.getElementById('descripcionCategoria');
        }
    }

    initEventListeners() {
        // Eventos de búsqueda y orden (disponibles para todos los roles)
        this.buscador.addEventListener('input', () => this.filtrarCategorias());
        document.querySelectorAll('[data-col]').forEach(th => {
            th.addEventListener('click', () => {
                const columna = th.getAttribute('data-col');
                this.ordenarPor(columna);
            });
        });

        // <-- CAMBIO: Los eventos para crear, editar y guardar solo se asignan si el usuario es admin.
        if (this.esAdmin) {
            this.btnNueva.addEventListener('click', () => this.mostrarModal());
            this.btnCerrarModal.addEventListener('click', () => this.cerrarModal());
            this.modal.addEventListener('click', (e) => {
                if (e.target === this.modal) this.cerrarModal();
            });
            this.form.addEventListener('submit', (e) => this.guardarCategoria(e));
        }
    }

    async cargarCategorias() {
        try {
            // Esta ruta es pública, así que no hay problema.
            const response = await fetch('/app/Controllers/CategoriasController.php?action=listar');
            const data = await response.json();

            if (data.success) {
                this.categorias = data.data;
                this.renderTabla();
            } else {
                throw new Error(data.error || 'Error al cargar categorías');
            }
        } catch (error) {
            console.error('Error:', error);
            this.mostrarNotificacion(error.message, 'error');
        }
    }

    filtrarCategorias() {
        const termino = this.buscador.value.toLowerCase();
        this.renderTabla(termino);
    }

    ordenarPor(columna) {
        if (this.orden.columna === columna) {
            this.orden.direccion = this.orden.direccion === 'asc' ? 'desc' : 'asc';
        } else {
            this.orden.columna = columna;
            this.orden.direccion = 'asc';
        }
        this.renderTabla();
    }

    renderTabla(filtro = '') {
        let categoriasFiltradas = [...this.categorias];

        if (filtro) {
            categoriasFiltradas = categoriasFiltradas.filter(cat =>
                cat.nombre.toLowerCase().includes(filtro) ||
                (cat.descripcion && cat.descripcion.toLowerCase().includes(filtro))
            );
        }

        categoriasFiltradas.sort((a, b) => {
            const valorA = a[this.orden.columna] || '';
            const valorB = b[this.orden.columna] || '';
            return this.orden.direccion === 'asc' ? valorA.localeCompare(valorB) : valorB.localeCompare(valorA);
        });

        // <-- CAMBIO: El número de columnas en la tabla ahora depende del rol del usuario.
        const colspan = this.esAdmin ? 3 : 2;
        this.tabla.innerHTML = categoriasFiltradas.length > 0
            ? categoriasFiltradas.map(cat => this.crearFilaCategoria(cat)).join('')
            : `<tr><td colspan="${colspan}" class="text-center">No se encontraron categorías</td></tr>`;

        // <-- CAMBIO: Los eventos de los botones de la tabla solo se asignan si es admin.
        if (this.esAdmin) {
            this.tabla.querySelectorAll('.btn-editar').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const id = e.currentTarget.getAttribute('data-id');
                    const categoria = this.categorias.find(c => c.id == id);
                    if (categoria) this.mostrarModal(categoria);
                });
            });

            this.tabla.querySelectorAll('.btn-eliminar').forEach(btn => {
                btn.addEventListener('click', (e) => {
                    const id = e.currentTarget.getAttribute('data-id');
                    this.eliminarCategoria(id);
                });
            });
        }
    }

    crearFilaCategoria(categoria) {
        // <-- CAMBIO: Creamos la celda de "Acciones" solo si es admin. Si no, es una cadena vacía.
        const celdaAcciones = this.esAdmin ? `
            <td class="acciones">
                <button class="btn-editar" data-id="${categoria.id}">
                    <i class="fas fa-edit"></i> Editar
                </button>
                <button class="btn-eliminar" data-id="${categoria.id}">
                    <i class="fas fa-trash-alt"></i> Eliminar
                </button>
            </td>
        ` : '';

        // Ahora el HTML de la fila se adapta al rol.
        return `
            <tr>
                <td>${categoria.nombre}</td>
                <td>${categoria.descripcion || '-'}</td>
                ${celdaAcciones}
            </tr>
        `;
    }

    // El resto de métodos no necesitan cambios porque solo se pueden llamar a través de
    // los event listeners que ya hemos protegido con la condición this.esAdmin.

    mostrarModal(categoria = null) {
        if (categoria) {
            this.tituloModal.textContent = 'Editar Categoría';
            this.categoriaId.value = categoria.id;
            this.nombreInput.value = categoria.nombre;
            this.descripcionInput.value = categoria.descripcion || '';
        } else {
            this.tituloModal.textContent = 'Nueva Categoría';
            this.form.reset();
            this.categoriaId.value = '';
        }
        this.modal.style.display = 'flex';
    }

    cerrarModal() {
        this.modal.style.display = 'none';
    }

    async guardarCategoria(e) {
        e.preventDefault();
        const btnGuardar = e.target.querySelector('button[type="submit"]');
        const textoOriginal = btnGuardar.innerHTML;
        btnGuardar.disabled = true;
        btnGuardar.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Guardando...';

        const categoria = {
            id: this.categoriaId.value || null,
            nombre: this.nombreInput.value.trim(),
            descripcion: this.descripcionInput.value.trim()
        };

        try {
            if (!categoria.nombre) throw new Error('El nombre de la categoría es requerido');

            const action = categoria.id ? 'actualizar' : 'crear';
            const response = await fetch(`/app/Controllers/CategoriasController.php?action=${action}`, {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(categoria)
            });

            const data = await response.json();
            if (!response.ok) throw new Error(data.error || `Error HTTP ${response.status}`);

            this.mostrarNotificacion(`Categoría ${categoria.id ? 'actualizada' : 'creada'} correctamente`, 'exito');
            this.cerrarModal();
            await this.cargarCategorias();
        } catch (error) {
            console.error('Error:', error);
            this.mostrarNotificacion(error.message, 'error');
        } finally {
            btnGuardar.disabled = false;
            btnGuardar.innerHTML = textoOriginal;
        }
    }

    async eliminarCategoria(id) {
        mostrarConfirmacionBootstrap('¿Estás seguro de eliminar esta categoría? Esta acción no se puede deshacer.', async () => {
            try {
                const response = await fetch('/app/Controllers/CategoriasController.php?action=eliminar', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ id })
                });

                const data = await response.json();
                if (!response.ok) throw new Error(data.error || `Error HTTP ${response.status}`);

                this.mostrarNotificacion('Categoría eliminada correctamente', 'exito');
                await this.cargarCategorias();
            } catch (error) {
                console.error('Error:', error);
                this.mostrarNotificacion(error.message, 'error');
            }
        });
    }

    mostrarNotificacion(mensaje, tipo) {
        const notificacion = document.createElement('div');
        notificacion.className = `notificacion notificacion-${tipo}`;
        notificacion.innerHTML = `<i class="fas ${tipo === 'exito' ? 'fa-check-circle' : 'fa-exclamation-circle'}"></i> <span>${mensaje}</span>`;
        document.body.appendChild(notificacion);
        setTimeout(() => {
            notificacion.style.animation = 'fadeOut 0.5s ease-out';
            setTimeout(() => notificacion.remove(), 500);
        }, 3000);
    }
}

function mostrarConfirmacionBootstrap(mensaje, onConfirm) {
    let modal = document.getElementById('modalConfirmacionCategoria');
    if (!modal) {
        modal = document.createElement('div');
        modal.innerHTML = `
        <div class="modal fade" id="modalConfirmacionCategoria" tabindex="-1">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title">Confirmar acción</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal"></button>
              </div>
              <div class="modal-body"><span>${mensaje}</span></div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnConfirmarCategoria">Confirmar</button>
              </div>
            </div>
          </div>
        </div>`;
        document.body.appendChild(modal);
    }
    const modalInstance = new bootstrap.Modal(document.getElementById('modalConfirmacionCategoria'));
    const btnConfirmar = document.getElementById('btnConfirmarCategoria');
    btnConfirmar.onclick = function () {
        modalInstance.hide();
        if (typeof onConfirm === 'function') onConfirm();
    };
    modalInstance.show();
}