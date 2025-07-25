document.addEventListener('DOMContentLoaded', function () {
    console.log('Script sidebar.js cargado');

    const botonMenu = document.getElementById('boton-menu');
    const barraLateral = document.querySelector('.barra-lateral');
    const contenidoPrincipal = document.querySelector('.contenido-principal'); // solo si lo usas
    const menusDesplegables = document.querySelectorAll('.tiene-submenu');

    if (!botonMenu || !barraLateral) {
        console.error('Elementos no encontrados:',
            !botonMenu ? 'boton-menu no encontrado' : '',
            !barraLateral ? 'barra-lateral no encontrada' : ''
        );
        return;
    }

    // Verifica si es móvil
    const esMovil = () => window.innerWidth <= 768;

    // Evento botón menú
    botonMenu.addEventListener('click', function (e) {
        e.preventDefault();
        console.log('Botón de menú clickeado');

        if (esMovil()) {
            barraLateral.classList.toggle('menu-activo');
        } else {
            barraLateral.classList.toggle('contraido');
            if (contenidoPrincipal) {
                contenidoPrincipal.classList.toggle('expandido');
            }
        }
    });

    // Desplegar submenús
    menusDesplegables.forEach(menu => {
        const trigger = menu.querySelector('.elemento-menu');
        if (trigger) {
            trigger.addEventListener('click', function (e) {
                e.preventDefault();
                if (esMovil() || !barraLateral.classList.contains('contraido')) {
                    menusDesplegables.forEach(otroMenu => {
                        if (otroMenu !== menu) otroMenu.classList.remove('activo');
                    });
                    menu.classList.toggle('activo');
                }
            });
        }
    });

    // Cerrar barra en móvil si clic fuera
    document.addEventListener('click', function (event) {
        if (
            esMovil() &&
            barraLateral.classList.contains('menu-activo') &&
            !barraLateral.contains(event.target) &&
            !botonMenu.contains(event.target)
        ) {
            barraLateral.classList.remove('menu-activo');
        }
    });

    // Reset en resize
    window.addEventListener('resize', function () {
        if (!esMovil()) {
            barraLateral.classList.remove('menu-activo');
        }
    });

    // Dashboard: actualizar contadores
    actualizarDashboard();
});

// Funciones auxiliares
function filtrarProductos() {
    const categoria = document.getElementById('categoria').value;
    window.location.href = `index.php${categoria ? '?categoria=' + categoria : ''}`;
}

function buscarProductos() {
    const searchTerm = document.getElementById('search-input').value.toLowerCase();
    const rows = document.querySelectorAll('table tbody tr');
    rows.forEach(row => {
        const text = row.textContent.toLowerCase();
        row.style.display = text.includes(searchTerm) ? '' : 'none';
    });
}

function actualizarDashboard() {
    const rows = document.querySelectorAll('table tbody tr');
    const stockBajo = document.querySelectorAll('.stock-bajo').length;

    const totalProductos = document.getElementById('total-productos');
    const stockBajoElem = document.getElementById('stock-bajo');
    const totalCategorias = document.getElementById('total-categorias');
    const totalArtesanos = document.getElementById('total-artesanos');

    if (totalProductos) totalProductos.textContent = rows.length;
    if (stockBajoElem) stockBajoElem.textContent = stockBajo;
    if (totalCategorias) totalCategorias.textContent = '5'; // puedes traerlo desde PHP
    if (totalArtesanos) totalArtesanos.textContent = '5'; // lo mismo
}
