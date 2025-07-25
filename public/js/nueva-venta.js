document.addEventListener("DOMContentLoaded", function () {
    // Elementos del DOM
    const productoSelect = document.getElementById('productoSelect');
    const cantidadInput = document.getElementById('cantidad');
    const btnAgregar = document.getElementById('btnAgregar');
    const formVenta = document.getElementById('formVenta');
    const detalleVenta = document.getElementById('detalleVenta');
    const totalVenta = document.getElementById('totalVenta');

    let productos = [];
    let carrito = [];

    // Cargar productos disponibles
    function cargarProductos() {
        fetch('/app/Controllers/VentasController.php?action=obtener_productos')
            .then(res => {
                if (!res.ok) throw new Error('Error en la respuesta del servidor');
                return res.json();
            })
            .then(data => {
                if (data.success && Array.isArray(data.productos)) {
                    productos = data.productos;
                    renderizarProductos();
                } else {
                    throw new Error(data.error || "No se pudieron cargar los productos");
                }
            })
            .catch(err => {
                console.error('Error al cargar productos:', err);
                mostrarError(err.message);
            });
    }

    // Renderizar productos en el select
    function renderizarProductos() {
        productoSelect.innerHTML = '<option value="">Seleccionar Producto</option>';

        productos.forEach(p => {
            const option = document.createElement('option');
            option.value = p.id;
            option.textContent = `${p.nombre} - S/. ${parseFloat(p.precio).toFixed(2)}`;
            option.setAttribute('data-precio', p.precio);
            productoSelect.appendChild(option);
        });
    }

    // Agregar producto al carrito
    function agregarProducto() {
        try {
            const id = parseInt(productoSelect.value);
            const cantidad = parseInt(cantidadInput.value);

            // Validaciones
            if (isNaN(id) || id <= 0) {
                throw new Error("Debes seleccionar un producto válido");
            }

            if (isNaN(cantidad) || cantidad <= 0) {
                throw new Error("La cantidad debe ser un número mayor a cero");
            }

            const productoSeleccionado = productoSelect.options[productoSelect.selectedIndex];
            const nombre = productoSeleccionado.text.split(' - ')[0];
            const precio = parseFloat(productoSeleccionado.getAttribute('data-precio'));

            if (!nombre || isNaN(precio)) {
                throw new Error("No se pudo obtener la información del producto");
            }

            // Agregar al carrito
            carrito.push({
                id,
                nombre,
                cantidad,
                precio
            });

            actualizarTabla();
            cantidadInput.value = 1;
            productoSelect.focus();

        } catch (error) {
            console.error('Error al agregar producto:', error);
            mostrarError(error.message);
        }
    }

    // Actualizar tabla de venta
    function actualizarTabla() {
        detalleVenta.innerHTML = '';
        let total = 0;

        if (carrito.length === 0) {
            detalleVenta.innerHTML = '<tr><td colspan="5" class="text-center">No hay productos agregados</td></tr>';
            totalVenta.textContent = 'S/. 0.00';
            return;
        }

        carrito.forEach((item, index) => {
            const subtotal = item.cantidad * item.precio;
            total += subtotal;

            const tr = document.createElement('tr');
            tr.innerHTML = `
                <td>${item.nombre}</td>
                <td>${item.cantidad}</td>
                <td>S/. ${item.precio.toFixed(2)}</td>
                <td>S/. ${subtotal.toFixed(2)}</td>
                <td>
                    <button type="button" class="btn-eliminar" onclick="eliminarProducto(${index})">
                        <i class="fas fa-trash-alt"></i>
                    </button>
                </td>
            `;
            detalleVenta.appendChild(tr);
        });

        totalVenta.textContent = `S/. ${total.toFixed(2)}`;
    }

    // Eliminar producto del carrito
    window.eliminarProducto = function (index) {
        mostrarConfirmacionBootstrap('¿Estás seguro de eliminar este producto?', function () {
            carrito.splice(index, 1);
            actualizarTabla();
            mostrarAlertaBootstrap('Producto eliminado del carrito', 'success');
        });
    };

    // Función para mostrar notificaciones Bootstrap
    function mostrarAlertaBootstrap(mensaje, tipo = "success") {
        const alertContainer = document.getElementById("alert-container");
        const wrapper = document.createElement("div");
        const id = `alert-${Date.now()}`;
        wrapper.innerHTML = `
            <div id="${id}" class="alert alert-${tipo} alert-dismissible fade show" role="alert">
                ${mensaje}
                <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
            </div>
        `;
        alertContainer.appendChild(wrapper);
        setTimeout(() => {
            const alertElem = document.getElementById(id);
            if (alertElem) {
                alertElem.classList.remove('show');
                alertElem.classList.add('hide');
                setTimeout(() => alertElem.parentNode && alertElem.parentNode.remove(), 500);
            }
        }, 3000);
    }

    // Modal de confirmación Bootstrap
    function mostrarConfirmacionBootstrap(mensaje, onConfirm) {
        let modal = document.getElementById('modalConfirmacionVenta');
        if (!modal) {
            modal = document.createElement('div');
            modal.innerHTML = `
        <div class="modal fade" id="modalConfirmacionVenta" tabindex="-1" aria-labelledby="modalConfirmacionVentaLabel" aria-hidden="true">
          <div class="modal-dialog modal-dialog-centered">
            <div class="modal-content">
              <div class="modal-header">
                <h5 class="modal-title" id="modalConfirmacionVentaLabel">Confirmar acción</h5>
                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
              </div>
              <div class="modal-body">
                <span id="mensajeConfirmacionVenta"></span>
              </div>
              <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="btnConfirmarVenta" >Confirmar</button>
              </div>
            </div>
          </div>
        </div>`;
            document.body.appendChild(modal);
        }
        document.getElementById('mensajeConfirmacionVenta').textContent = mensaje;
        const modalInstance = new bootstrap.Modal(document.getElementById('modalConfirmacionVenta'));
        modalInstance.show();
        const btnConfirmar = document.getElementById('btnConfirmarVenta');
        btnConfirmar.onclick = function () {
            modalInstance.hide();
            if (typeof onConfirm === 'function') onConfirm();
        };
    }

    // Registrar venta
    function registrarVenta(e) {
        e.preventDefault();

        if (carrito.length === 0) {
            mostrarAlertaBootstrap("Debes agregar al menos un producto para registrar la venta", "danger");
            return;
        }

        // Mostrar confirmación visual Bootstrap
        mostrarConfirmacionBootstrap('¿Confirmas registrar esta venta?', function () {
            // Deshabilitar botón para evitar múltiples clics
            const btnSubmit = e.target.querySelector('button[type="submit"]');
            btnSubmit.disabled = true;
            btnSubmit.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Registrando...';

            fetch('/app/Controllers/VentasController.php?action=registrar_venta', {
                method: 'POST',
                headers: { 'Content-Type': 'application/json' },
                body: JSON.stringify(carrito)
            })
                .then(res => {
                    if (!res.ok) throw new Error('No se puede vender una cantidad mayor al stock');
                    return res.json();
                })
                .then(data => {
                    if (data.success) {
                        mostrarAlertaBootstrap("Venta registrada correctamente", "success");
                        setTimeout(() => location.reload(), 1500);
                    } else {
                        throw new Error(data.error || "Error al registrar la venta");
                    }
                })
                .catch(err => {
                    console.error('Error al registrar venta:', err);
                    mostrarAlertaBootstrap(err.message, "danger");
                })
                .finally(() => {
                    btnSubmit.disabled = false;
                    btnSubmit.innerHTML = 'Registrar Venta';
                });
        });
    }

    // Event listeners
    btnAgregar.addEventListener('click', agregarProducto);
    formVenta.addEventListener('submit', registrarVenta);

    // Inicializar
    cargarProductos();
});