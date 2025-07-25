document.addEventListener("DOMContentLoaded", function () {
  const listaProductos = document.querySelector(".card ul");
  const cardVentas = document.querySelector(".card:nth-child(2)");
  const cardStock = document.querySelector(".card:nth-child(3)");

  // Mostrar estado de carga
  listaProductos.innerHTML = '<li>Cargando datos...</li>';

  fetch('/app/Controllers/ResumenController.php')
    .then(response => {
      if (!response.ok) {
        throw new Error(`HTTP error! status: ${response.status}`);
      }
      return response.json();
    })
    .then(data => {
      if (data.error) {
        throw new Error(data.error);
      }

      // 1. Productos más vendidos
      if (data.productos_mas_vendidos && data.productos_mas_vendidos.length > 0) {
        listaProductos.innerHTML = data.productos_mas_vendidos.map(p =>
          `<li>${p.nombre} – ${p.total_vendido} unidades vendidas</li>`
        ).join('');
      } else {
        listaProductos.innerHTML = '<li>No hay datos de productos vendidos</li>';
      }

      // 2. Gráfico de ventas
      if (data.ventas_por_trimestre) {
        crearGrafico(
          'graficoVentas',
          Object.values(data.ventas_por_trimestre),
          'Ventas por Temporada',
          '#2B3A55',
          'S/. '
        );
      } else {
        cardVentas.innerHTML += '<p class="error">No hay datos de ventas</p>';
      }

      // 3. Gráfico de stock
      if (data.stock_por_trimestre) {
        crearGrafico(
          'graficoProductos',
          Object.values(data.stock_por_trimestre),
          'Productos en Stock',
          '#c93c3c',
          ''
        );
      } else {
        cardStock.innerHTML += '<p class="error">No hay datos de stock</p>';
      }
    })
    .catch(error => {
      console.error("Error:", error);
      listaProductos.innerHTML = '<li>Error al cargar datos. Recarga la página.</li>';

      // Mostrar detalles del error en consola y en la interfaz para desarrollo
      if (confirm("¿Deseas ver detalles del error? (Solo para desarrollo)")) {
        alert(`Error: ${error.message}`);
      }
    });

  function crearGrafico(id, datos, titulo, color, prefijo) {
    const ctx = document.getElementById(id).getContext('2d');
    new Chart(ctx, {
      type: 'bar',
      data: {
        labels: ['Ene-Mar', 'Abr-Jun', 'Jul-Sep', 'Oct-Dic'],
        datasets: [{
          label: titulo,
          data: datos,
          backgroundColor: color
        }]
      },
      options: {
        responsive: true,
        plugins: {
          legend: { display: false },
          tooltip: {
            callbacks: {
              label: ctx => `${prefijo}${ctx.raw}`
            }
          }
        },
        scales: {
          y: { beginAtZero: true }
        }
      }
    });
  }
});