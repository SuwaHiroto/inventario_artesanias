document.addEventListener('DOMContentLoaded', function () {
  // Elementos del DOM
  const tipoReporteSelect = document.getElementById('tipoReporte');
  const generarBtn = document.getElementById('generarReporte');
  const exportarPdfBtn = document.getElementById('exportarPdf');
  const exportarExcelBtn = document.getElementById('exportarExcel');
  const tablaReportes = document.getElementById('tablaReportes');

  // Event listeners
  if (generarBtn) {
    generarBtn.addEventListener('click', generarReporte);
  }
  if (exportarPdfBtn) {
    exportarPdfBtn.addEventListener('click', exportarPDF);
  }
  if (exportarExcelBtn) {
    exportarExcelBtn.addEventListener('click', exportarExcel);
  }

  // Cargar reporte inicial
  generarReporte();
});

function generarReporte() {
  const tipo = document.getElementById('tipoReporte').value;
  const url = `/app/Controllers/ReportesController.php?action=view&tipo=${tipo}`;

  // Mostrar carga
  const tbody = document.querySelector('#tablaReportes tbody');
  tbody.innerHTML = '<tr><td colspan="10" class="text-center">Cargando datos...</td></tr>';

  fetch(url)
    .then(response => {
      if (!response.ok) {
        throw new Error('Error en la respuesta del servidor');
      }
      return response.json();
    })
    .then(data => {
      renderizarTabla(data);
    })
    .catch(error => {
      console.error('Error al generar reporte:', error);
      mostrarError('Error al cargar el reporte: ' + error.message);
    });
}

function renderizarTabla(data) {
  const thead = document.querySelector('#tablaReportes thead');
  const tbody = document.querySelector('#tablaReportes tbody');

  // Limpiar tabla
  thead.innerHTML = '';
  tbody.innerHTML = '';

  if (data.length === 0) {
    tbody.innerHTML = '<tr><td colspan="10" class="text-center">No hay datos disponibles</td></tr>';
    return;
  }

  // Crear encabezados
  const headers = Object.keys(data[0]);
  const headerRow = document.createElement('tr');

  headers.forEach(header => {
    const th = document.createElement('th');
    th.textContent = header.replace(/_/g, ' ');
    headerRow.appendChild(th);
  });

  thead.appendChild(headerRow);

  // Crear filas
  data.forEach(row => {
    const tr = document.createElement('tr');

    headers.forEach(header => {
      const td = document.createElement('td');
      td.textContent = row[header] || '-';
      tr.appendChild(td);
    });

    tbody.appendChild(tr);
  });
}

function exportarPDF() {
  const tipo = document.getElementById('tipoReporte').value;
  const url = `/app/Controllers/ReportesController.php?action=pdf&tipo=${tipo}`;
  window.open(url, '_blank');
}

function exportarExcel() {
  const tipo = document.getElementById('tipoReporte').value;
  const url = `/app/Controllers/ReportesController.php?action=excel&tipo=${tipo}`;
  window.open(url, '_blank');
}

function mostrarError(mensaje) {
  const tbody = document.querySelector('#tablaReportes tbody');
  tbody.innerHTML = `<tr><td colspan="10" class="text-center text-danger">${mensaje}</td></tr>`;

  // Opcional: Mostrar notificación
  alert(mensaje);
}