document.addEventListener("DOMContentLoaded", () => {
    cargarResumenDashboard();
});

function cargarResumenDashboard() {
    // Mostrar estado de carga
    mostrarEstadoCarga(true);

    fetch('/app/Controllers/InicioController.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Error en la respuesta del servidor');
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                throw new Error(data.error);
            }
            actualizarUI(data);
        })
        .catch(err => {
            console.error("Error cargando datos de inicio:", err);
            mostrarError(err.message);
        })
        .finally(() => {
            mostrarEstadoCarga(false);
        });
}

function actualizarUI(data) {
    // Actualizar métricas principales
    document.getElementById("ventas-hoy").textContent = `S/ ${data.ventas_dia.toFixed(2)}`;
    document.getElementById("total-ventas").textContent = `S/ ${data.total_ventas.toFixed(2)}`;
    document.getElementById("ultima-venta").textContent = `S/ ${data.ultima_venta.toFixed(2)}`;
    document.getElementById("total-productos").textContent = data.total_productos;

    // Actualizar lista de stock bajo
    const ul = document.getElementById("lista-stock-bajo");
    ul.innerHTML = "";

    if (data.stock_bajo.length === 0) {
        const li = document.createElement("li");
        li.className = "text-success";
        li.textContent = "Todos los productos tienen stock suficiente.";
        ul.appendChild(li);
    } else {
        data.stock_bajo.forEach(producto => {
            const li = document.createElement("li");
            li.className = "text-warning";

            const icono = document.createElement("i");
            icono.className = "bi bi-exclamation-triangle-fill me-2";

            li.appendChild(icono);
            li.appendChild(document.createTextNode(`${producto.nombre} (stock: ${producto.stock})`));

            ul.appendChild(li);
        });
    }
}

function mostrarEstadoCarga(mostrar) {
    const contenedor = document.getElementById("dashboard-container");
    const cargando = document.getElementById("dashboard-cargando");

    if (mostrar) {
        contenedor.style.display = "none";
        cargando.style.display = "block";
    } else {
        contenedor.style.display = "block";
        cargando.style.display = "none";
    }
}

function mostrarError(mensaje) {
    const contenedorError = document.getElementById("dashboard-error");
    contenedorError.textContent = mensaje;
    contenedorError.style.display = "block";

    // Ocultar después de 5 segundos
    setTimeout(() => {
        contenedorError.style.display = "none";
    }, 5000);
}