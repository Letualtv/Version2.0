function exportarJSON() {
    fetch('obtenerPreguntas.php')
      .then(response => response.json())
      .then(data => {
        const blob = new Blob([JSON.stringify(data, null, 2)], { type: 'application/json' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        a.download = 'preguntas.json';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
      });
  }
  function exportarCSV() {
    fetch('obtenerPreguntas.php')
      .then(response => response.json())
      .then(data => {
        const csvRows = [];
        const headers = ['ID', 'Título', 'Nº Página', 'Tipo'];
        csvRows.push(headers.join(','));
  
        data.forEach(pregunta => {
          const values = [pregunta.id, pregunta.titulo, pregunta.n_pag, pregunta.tipo];
          csvRows.push(values.join(','));
        });
  
        const csvString = csvRows.join('\n');
        const blob = new Blob([csvString], { type: 'text/csv' });
        const url = window.URL.createObjectURL(blob);
        const a = document.createElement('a');
        a.style.display = 'none';
        a.href = url;
        a.download = 'preguntas.csv';
        document.body.appendChild(a);
        a.click();
        window.URL.revokeObjectURL(url);
      });
  }
  
  function exportarExcel() {
    fetch('obtenerPreguntas.php')
      .then(response => response.json())
      .then(data => {
        const wb = XLSX.utils.book_new();
        const ws = XLSX.utils.json_to_sheet(data);
        XLSX.utils.book_append_sheet(wb, ws, 'Preguntas');
        XLSX.writeFile(wb, 'preguntas.xlsx');
      });
  }
  async function exportarPDF() {
    // Asegúrate de que jsPDF está cargado
    if (typeof window.jspdf === 'undefined') {
      console.error("jsPDF no está definido. Asegúrate de que la biblioteca está incluida correctamente.");
      return;
    }
  
    // Obtener los datos desde el servidor
    const response = await fetch('obtenerPreguntas.php');
    const data = await response.json();
  
    // Inicializar jsPDF y crear el PDF
    const { jsPDF } = window.jspdf;
    const doc = new jsPDF();
    
    const columns = ['ID', 'Título', 'Nº Página', 'Tipo'];
    const rows = data.map(pregunta => [pregunta.id, pregunta.titulo, pregunta.n_pag, pregunta.tipo]);
  
    // Añadir título y tabla al PDF
    doc.text('Lista de Preguntas', 14, 16);
    doc.autoTable({
      head: [columns],
      body: rows,
      startY: 20,
      theme: 'grid'
    });
  
    // Guardar el PDF
    doc.save('preguntas.pdf');
  }
  
  
  