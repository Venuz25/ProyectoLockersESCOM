document.addEventListener('DOMContentLoaded', function () {
    const modal = document.getElementById('listaEsperaModal');
    const modalContent = document.getElementById('modalContent');

    // Función para cargar datos
    function cargarDatos() {
        fetch('/ProyectoWeb/php/acuse/modalLE.php')
            .then(response => {
                if (!response.ok) {
                    throw new Error('Error al obtener la lista de espera');
                }
                return response.json();
            })
            .then(data => {
                modalContent.innerHTML = ''; // Limpiar contenido previo

                // Insertar los datos dinámicos
                if (data.length > 0) {
                    const table = document.createElement('table');
                    table.className = 'table table-striped';

                    // Crear encabezado de la tabla
                    const thead = document.createElement('thead');
                    thead.innerHTML = `
                        <tr>
                            <th>#</th>
                            <th>Nombre</th>
                            <th>ApellidoP</th>
                            <th>ApellidoM</th>
                            <th>Boleta</th>
                        </tr>
                    `;
                    table.appendChild(thead);

                    // Crear cuerpo de la tabla
                    const tbody = document.createElement('tbody');
                    data.forEach((item, index) => {
                        const row = document.createElement('tr');
                        row.innerHTML = `
                            <td>${index + 1}</td>
                            <td>${item.nombre}</td>
                            <td>${item.apellidoP}</td>
                            <td>${item.apellidoM}</td>
                            <td>${item.boleta}</td>
                        `;
                        tbody.appendChild(row);
                    });
                    table.appendChild(tbody);

                    modalContent.appendChild(table);
                } else {
                    modalContent.innerHTML = '<p>No hay datos disponibles.</p>';
                }
            })
            .catch(error => {
                console.error('Error al cargar los datos:', error);
                modalContent.innerHTML = '<p>Ocurrió un error al cargar los datos.</p>';
            });
    }

    // Configurar el evento que se dispara al mostrar el modal
    modal.addEventListener('show.bs.modal', function () {
        cargarDatos(); // Llamar a la función de carga de datos
    });
});
