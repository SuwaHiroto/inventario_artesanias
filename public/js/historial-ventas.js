/**
 * Clase para gestionar la lógica del Historial de Ventas.
 * Adaptado para el controlador que devuelve JSON con claves 'ventas' y 'productos'.
 */
class HistorialVentasApp {
    constructor() {
        // Estado de la aplicación
        this.ventas = [];
        this.sortState = {
            column: 'fecha',
            direction: 'desc'
        };

        this.initElements();
        this.initEventListeners();

        // Carga inicial de datos
        this.cargarProductosParaFiltro();
        this.filtrarYMostrarVentas();
    }

    /**
     * Cachea los elementos del DOM.
     */
    initElements() {
        this.fechaInicioInput = document.getElementById("fechaInicio");
        this.fechaFinInput = document.getElementById("fechaFin");
        this.productoSelect = document.getElementById("productoSelect");
        this.tablaBody = document.querySelector("#tablaVentas tbody");
        this.tablaHead = document.querySelector("#tablaVentas thead");
    }

    /**
     * Configura los listeners de eventos.
     */
    initEventListeners() {
        this.fechaInicioInput.addEventListener("change", () => this.filtrarYMostrarVentas());
        this.fechaFinInput.addEventListener("change", () => this.filtrarYMostrarVentas());
        this.productoSelect.addEventListener("change", () => this.filtrarYMostrarVentas());

        // Listener centralizado para la ordenación
        this.tablaHead.addEventListener('click', (e) => {
            const th = e.target.closest('.sortable');
            if (th) {
                this.ordenarPor(th.dataset.sort);
            }
        });
    }

    // --- MÉTODOS DE CARGA DE DATOS (API) ---

    async cargarProductosParaFiltro() {
        try {
            const response = await fetch('/app/Controllers/HistorialVentasController.php?action=obtener_productos');
            const data = await response.json();

            if (data.success && Array.isArray(data.productos)) {
                this.productoSelect.innerHTML = '<option value="">Todos los productos</option>';
                data.productos.forEach(p => {
                    const option = document.createElement("option");
                    option.value = p.id;
                    option.textContent = p.nombre;
                    this.productoSelect.appendChild(option);
                });
            } else {
                throw new Error(data.error || 'No se recibieron los productos.');
            }
        } catch (err) {
            this.mostrarError("No se pudieron cargar los productos para el filtro.");
        }
    }

    async filtrarYMostrarVentas() {
        const fechaInicio = this.fechaInicioInput.value;
        const fechaFin = this.fechaFinInput.value;
        const codigoProducto = this.productoSelect.value;

        let url = `/app/Controllers/HistorialVentasController.php?action=obtener_ventas&`;
        if (fechaInicio) url += `fecha_inicio=${fechaInicio}&`;
        if (fechaFin) url += `fecha_fin=${fechaFin}&`;
        if (codigoProducto) url += `codigo=${codigoProducto}&`;

        try {
            const response = await fetch(url);
            const data = await response.json();

            if (data.success && Array.isArray(data.ventas)) {
                this.ventas = data.ventas;
                this.renderTabla();
            } else {
                throw new Error(data.error || 'Los datos recibidos no son válidos.');
            }
        } catch (err) {
            this.mostrarError(`No se pudo cargar el historial: ${err.message}`);
            this.ventas = [];
            this.renderTabla();
        }
    }

    // --- MÉTODOS DE ORDENACIÓN Y RENDERIZADO ---

    ordenarPor(columna) {
        if (this.sortState.column === columna) {
            this.sortState.direction = this.sortState.direction === 'asc' ? 'desc' : 'asc';
        } else {
            this.sortState.column = columna;
            this.sortState.direction = 'asc';
        }
        this.renderTabla();
    }

    renderTabla() {
        const sortedVentas = [...this.ventas].sort((a, b) => {
            const col = this.sortState.column;
            const dir = this.sortState.direction === 'asc' ? 1 : -1;
            const valA = a[col];
            const valB = b[col];

            if (col === 'cantidad' || col === 'precio_unitario' || col === 'monto_total') {
                return (parseFloat(valA) - parseFloat(valB)) * dir;
            }
            if (col === 'fecha') {
                return (new Date(valA) - new Date(valB)) * dir;
            }
            return String(valA).localeCompare(String(valB)) * dir;
        });

        this.tablaBody.innerHTML = "";
        if (sortedVentas.length === 0) {
            this.tablaBody.innerHTML = `<tr><td colspan="5" class="text-center">No se encontraron ventas con los filtros aplicados.</td></tr>`;
        } else {
            sortedVentas.forEach(v => {
                const fila = document.createElement("tr");
                fila.innerHTML = `
                    <td>${new Date(v.fecha).toLocaleString('es-PE', { day: '2-digit', month: '2-digit', year: '2-digit', hour: 'numeric', minute: 'numeric' })}</td>
                    <td>${v.producto}</td>
                    <td>${v.cantidad}</td>
                    <td>S/. ${parseFloat(v.precio_unitario).toFixed(2)}</td>
                    <td>S/. ${parseFloat(v.monto_total).toFixed(2)}</td>
                `;
                this.tablaBody.appendChild(fila);
            });
        }

        this.tablaHead.querySelectorAll('.sortable').forEach(th => {
            th.classList.remove('asc', 'desc');
            if (th.dataset.sort === this.sortState.column) {
                th.classList.add(this.sortState.direction);
            }
        });
    }

    mostrarError(mensaje) {
        console.error(mensaje);
        alert(mensaje);
    }
}

// Inicia la aplicación cuando el DOM esté listo.
document.addEventListener("DOMContentLoaded", () => new HistorialVentasApp()); 