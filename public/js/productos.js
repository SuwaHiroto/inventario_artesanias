// /public/js/productos.js

document.addEventListener("DOMContentLoaded", () => {
  // Referencias a elementos del DOM
  const tabla = document.querySelector("#tabla-productos tbody");
  const modalStock = document.getElementById("modalStock");
  const formStock = document.getElementById("formStock");
  const idStockInput = document.getElementById("idStock");
  const btnMostrarActivos = document.getElementById("btn-activos");
  const inputBusqueda = document.getElementById("busqueda");

  // <-- CAMBIO: Elementos solo para admin
  let formProducto, modal, tituloModal, modalEliminar, formEliminar, idEliminarInput, btnMostrarInactivos;
  if (esUsuarioAdmin) {
    formProducto = document.getElementById("formProducto");
    modal = document.getElementById("modal");
    tituloModal = document.getElementById("titulo-modal");
    modalEliminar = document.getElementById("modalEliminar");
    formEliminar = document.getElementById("formEliminar");
    idEliminarInput = document.getElementById("idEliminar");
    btnMostrarInactivos = document.getElementById("btn-inactivos");
  }

  // Estado de la aplicación
  let editando = false;
  let idEditando = null;
  let mostrandoInactivos = false;

  async function cargarProductos(q = "") {
    const accion = q ? 'buscar' : (mostrandoInactivos ? 'listarInactivos' : 'listar');
    let url = `/app/Controllers/ProductosController.php?action=${accion}`;
    if (accion === 'buscar') {
      url += `&q=${encodeURIComponent(q)}${mostrandoInactivos ? '&inactivos=1' : ''}`;
    }
    try {
      const res = await fetch(url);
      const data = await res.json();
      tabla.innerHTML = "";
      if (data.estado === "ok") {
        data.productos.forEach(p => {
          const tr = document.createElement("tr");

          // <-- CAMBIO: Lógica de botones condicional
          let botonesAcciones = '';
          if (mostrandoInactivos) {
            if (esUsuarioAdmin) { // Solo admin puede reactivar
              botonesAcciones = `<button onclick="reactivarProducto(${p.id})">Reactivar</button>`;
            }
          } else {
            // Todos pueden editar stock
            botonesAcciones = `<button onclick="abrirEditarStock(${p.id}, ${p.stock})">Stock</button>`;
            if (esUsuarioAdmin) { // Solo admin puede editar y eliminar
              botonesAcciones += `
                <button onclick="editarProducto(${p.id}, '${p.nombre}', '${p.descripcion}', ${p.precio}, ${p.stock}, ${p.id_categoria})">Actualizar</button>
                <button onclick="confirmarEliminar(${p.id})">Dar de Baja</button>
              `;
            }
          }

          tr.innerHTML = `
            <td data-label="Nombre">${p.nombre}</td>
            <td data-label="Descripción">${p.descripcion}</td>
            <td data-label="Precio">${parseFloat(p.precio).toFixed(2)}</td>
            <td data-label="Categoría">${p.categoria || ''}</td>
            <td data-label="Stock">${p.stock}</td>
            <td data-label="Acciones">${botonesAcciones}</td>
          `;
          tabla.appendChild(tr);
        });
      }
    } catch (e) {
      console.error(e);
      mostrarAlertaBootstrap("Error al conectar con el servidor.", "danger");
    }
  }

  async function cargarCategorias() {
    // <-- CAMBIO: Solo cargamos categorías si el formulario de admin existe
    if (!esUsuarioAdmin) return;
    const selectCategoria = document.getElementById("id_categoria");
    const res = await fetch("/app/Controllers/ProductosController.php?action=categorias");
    const data = await res.json();
    if (data.estado === "ok") {
      selectCategoria.innerHTML = '<option value="">Seleccione una categoría</option>';
      data.categorias.forEach(cat => {
        selectCategoria.innerHTML += `<option value="${cat.id}">${cat.nombre}</option>`;
      });
    }
  }

  // Eventos de botones y búsqueda
  btnMostrarActivos.addEventListener("click", () => {
    mostrandoInactivos = false;
    cargarProductos(inputBusqueda.value);
  });
  inputBusqueda.addEventListener("input", (e) => cargarProductos(e.target.value));

  // <-- CAMBIO: Eventos solo para admin
  if (esUsuarioAdmin) {
    btnMostrarInactivos.addEventListener("click", () => {
      mostrandoInactivos = true;
      cargarProductos(inputBusqueda.value);
    });

    formProducto.addEventListener("submit", async (e) => {
      e.preventDefault();
      const datos = {
        nombre: document.getElementById("nombre").value,
        descripcion: document.getElementById("descripcion").value,
        precio: document.getElementById("precio").value,
        stock: document.getElementById("stock").value,
        id_categoria: document.getElementById("id_categoria").value
      };
      const action = editando ? "editar" : "registrar";
      if (editando) datos.id = idEditando;

      try {
        const res = await fetch(`/app/Controllers/ProductosController.php?action=${action}`, { method: "POST", headers: { "Content-Type": "application/json" }, body: JSON.stringify(datos) });
        const result = await res.json();
        if (result.estado === "ok") {
          cerrarModal();
          cargarProductos(inputBusqueda.value);
          mostrarAlertaBootstrap("Producto guardado.", "success");
        } else { throw new Error(result.mensaje); }
      } catch (error) { mostrarAlertaBootstrap(error.message, 'danger'); }
    });

    formEliminar.addEventListener("submit", async (e) => {
      e.preventDefault();
      try {
        const res = await fetch(`/app/Controllers/ProductosController.php?action=eliminar`, { method: "POST", headers: { "Content-Type": "application/json" }, body: JSON.stringify({ id: idEliminarInput.value }) });
        const result = await res.json();
        if (result.estado === "ok") {
          cerrarModalEliminar();
          cargarProductos(inputBusqueda.value);
          mostrarAlertaBootstrap("Producto dado de baja.", "success");
        } else { throw new Error(result.mensaje); }
      } catch (error) { mostrarAlertaBootstrap(error.message, 'danger'); }
    });

    window.reactivarProducto = async (id) => {
      try {
        const res = await fetch(`/app/Controllers/ProductosController.php?action=reactivar`, { method: 'POST', body: JSON.stringify({ id }), headers: { 'Content-Type': 'application/json' } });
        const data = await res.json();
        if (data.estado === 'ok') {
          mostrarAlertaBootstrap('Producto reactivado.', 'success');
          cargarProductos(inputBusqueda.value);
        } else { throw new Error(data.mensaje); }
      } catch (error) { mostrarAlertaBootstrap(error.message, 'danger'); }
    };
  }

  // Formulario de stock (para todos)
  formStock.addEventListener("submit", async (e) => {
    e.preventDefault();
    const datos = { id: idStockInput.value, stock: document.getElementById("nuevoStock").value };
    try {
      const res = await fetch('/app/Controllers/ProductosController.php?action=actualizarStock', { method: "POST", headers: { "Content-Type": "application/json" }, body: JSON.stringify(datos) });
      const result = await res.json();
      if (result.estado === 'ok') {
        cerrarModalStock();
        cargarProductos(inputBusqueda.value);
        mostrarAlertaBootstrap("Stock actualizado.", "success");
      } else { throw new Error(result.mensaje); }
    } catch (error) { mostrarAlertaBootstrap(error.message, 'danger'); }
  });

  // Funciones globales para abrir/cerrar modales
  window.abrirModal = () => {
    if (!esUsuarioAdmin) return;
    editando = false;
    idEditando = null;
    tituloModal.textContent = "Registrar Producto";
    formProducto.reset();
    modal.showModal();
  };

  window.editarProducto = (id, nombre, descripcion, precio, stock, id_categoria) => {
    if (!esUsuarioAdmin) return;
    editando = true;
    idEditando = id;
    tituloModal.textContent = "Editar Producto";
    document.getElementById("nombre").value = nombre;
    document.getElementById("descripcion").value = descripcion;
    document.getElementById("precio").value = precio;
    document.getElementById("stock").value = stock;
    document.getElementById("id_categoria").value = id_categoria;
    modal.showModal();
  };

  window.confirmarEliminar = (id) => {
    if (!esUsuarioAdmin) return;
    idEliminarInput.value = id;
    modalEliminar.showModal();
  };

  window.abrirEditarStock = (id, stock) => {
    idStockInput.value = id;
    document.getElementById("nuevoStock").value = stock;
    modalStock.showModal();
  };

  // <-- CAMBIO: Solo se definen si es admin
  if (esUsuarioAdmin) {
    window.cerrarModal = () => modal.close();
    window.cerrarModalEliminar = () => modalEliminar.close();
  }
  window.cerrarModalStock = () => modalStock.close();

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
    // Auto cerrar después de 3 segundos
    setTimeout(() => {
      const alertElem = document.getElementById(id);
      if (alertElem) {
        alertElem.classList.remove('show');
        alertElem.classList.add('hide');
        setTimeout(() => alertElem.parentNode && alertElem.parentNode.remove(), 500);
      }
    }, 3000);
  }
  // Carga inicial
  cargarProductos();
  cargarCategorias();
});