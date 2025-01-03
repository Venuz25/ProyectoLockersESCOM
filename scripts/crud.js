//Cambio de estilos de los botones de casilleros y CRUD
document.addEventListener("DOMContentLoaded", function () {
    const casillerosButton = document.getElementById("btnCasilleros");
    const crudButton = document.getElementById("btnCrud");
    const casillerosDiv = document.getElementById("casilleros");
    const crudDiv = document.getElementById("crud");

    // Función estilos de los botones
    function actualizarEstilos(activo, inactivo) {
        activo.style.backgroundColor = "#225560";
        activo.style.color = "white";
        inactivo.style.backgroundColor = "white";
        inactivo.style.color = "#333";
    }

    // Mostrar el div de casilleros 
    casillerosButton.addEventListener("click", function () {
        casillerosDiv.style.display = "block"; 
        crudDiv.style.display = "none"; 
        actualizarEstilos(casillerosButton, crudButton);
        location.reload();
    });

    // Mostrar el div de CRUD 
    crudButton.addEventListener("click", function () {
        crudDiv.style.display = "block"; 
        casillerosDiv.style.display = "none"; 
        actualizarEstilos(crudButton, casillerosButton);
        loadTableData(table);
    });
});

//Logica Crud
document.addEventListener("DOMContentLoaded", function () {
    const tableSelect = document.getElementById('tableSelect');
    const tableContainer = document.getElementById('tableContainer');
    const crudModal = new bootstrap.Modal(document.getElementById('crudModal'));
    const crudForm = document.getElementById('crudForm');
    const submitBtn = document.getElementById('submitBtn');

    tableSelect.addEventListener('change', function () {
        const table = tableSelect.value;
        if (table) {
            loadTableData(table);
        } else {
            tableContainer.innerHTML = '';
        }
    });

    // Cargar registros de la tabla seleccionada
    function loadTableData(table) {
        fetch(`/ProyectoWeb/php/admin/CRUD/getTableData.php?table=${table}`)
            .then(response => response.json())
            .then(data => {
                generateTable(data, table);
            });
    }

    // Generar la tabla de registros
    function generateTable(data, table) {
        if (data.length === 0) {
            tableContainer.innerHTML = `
                    <div class="alert alert-warning text-center" role="alert">
                        Sin registros que mostrar en esta tabla.
                    </div>`;
            return;
        }

        let tableHTML = `<div class="table-responsive">
            <table class="table table-bordered">
                <thead>
                    <tr>
                        <th>Acciones</th>
                        ${Object.keys(data[0]).map(key => `<th>${key}</th>`).join('')}
                    </tr>
                </thead>
                <tbody>
                    ${data.map(record => `
                        <tr>
                            <td>
                                <button class="btn btn-warning btn-sm editBtn" data-id="${(table === 'administradores'? record.id : record.noBoleta) || record.boleta || (record.boletaAsignada === null ? record.noCasillero : record.boletaAsignada)}">Editar</button>
                                <button class="btn btn-danger btn-sm deleteBtn" data-id="${(table === 'administradores'? record.id : record.noBoleta) || record.boleta || (record.boletaAsignada === null ? record.noCasillero : record.boletaAsignada)}">Eliminar</button>
                            </td>
                            ${Object.keys(record).map(key => {
                                let value = record[key];
                                if (value && (value.includes('.pdf') || value.includes('comprobantePago') || value.includes('credencial') || value.includes('horario'))) {
                                    let fileName = value.split('/').pop();
                                    return `<td><a href="${value}" target="_blank">${fileName}</a></td>`;
                                } else {
                                    return `<td>${value}</td>`;
                                }
                            }).join('')}
                        </tr>
                    `).join('')}
                </tbody>
            </table>
        </div>`;

        tableContainer.innerHTML = tableHTML;

        // Botones de acción (editar/eliminar)
        document.querySelectorAll('.editBtn').forEach(button => {
            button.addEventListener('click', function () {
                const recordId = button.dataset.id;
                openModal('edit', recordId, table);
            });
        });

        document.querySelectorAll('.deleteBtn').forEach(button => {
            button.addEventListener('click', function () {
                const recordId = button.dataset.id;
                if (confirm(`¿Estás seguro de que deseas eliminar el registro? Esta acción no se puede deshacer.`)) {
                    deleteRecord(recordId, table);
                }
            });
        });
    }

    // Función para eliminar el registro
    async function deleteRecord(recordId, table) {
        let deleteSQL = '';
        let successMessage = '';
        let errorMessage = 'Error al eliminar el registro.';
    
        try {
            switch (table) {
                case 'administradores':
                    deleteSQL = `DELETE FROM administradores WHERE id = '${recordId}'`;
                    successMessage = 'Administrador eliminado exitosamente';
                    break;
    
                case 'alumnos':
                    // Eliminar solicitudes asociadas
                    await executeQuery(`DELETE FROM solicitudes WHERE noBoleta = '${recordId}'`);
    
                    // Actualizar casilleros
                    await executeQuery(`UPDATE casilleros SET estado = 'Disponible', boletaAsignada = NULL WHERE boletaAsignada = '${recordId}'`);
    
                    // Eliminar al alumno
                    deleteSQL = `DELETE FROM alumnos WHERE boleta = '${recordId}'`;
                    successMessage = 'Alumno eliminado exitosamente';
                    break;
    
                case 'casilleros':
                    // Si hay boleta asignada al casillero cambia la solicitud
                    await executeQuery(`UPDATE solicitudes SET estadoSolicitud = 'Pendiente' WHERE noBoleta = '${recordId}'`);
    
                    // Eliminar el casillero
                    deleteSQL = `DELETE FROM casilleros WHERE (noCasillero = '${recordId}' OR boletaAsignada = '${recordId}') LIMIT 1;`;
                    successMessage = 'Casillero eliminado exitosamente';
                    break;
    
                case 'solicitudes':
                    // Actualizar el casillero asociado
                    await executeQuery(`UPDATE casilleros SET estado = 'Disponible', boletaAsignada = NULL WHERE boletaAsignada = '${recordId}'`);
            
                    // Eliminar el alumno asociado
                    await executeQuery(`DELETE FROM solicitudes WHERE noBoleta = '${recordId}'`);
                    
                    // Eliminar la solicitud
                    deleteSQL = `DELETE FROM alumnos WHERE boleta = '${recordId}'`;
                    successMessage = 'Solicitud eliminada exitosamente';
                    break;
    
                default:
                    throw new Error('Tabla no reconocida');
            }
    
            // Ejecutar la eliminación principal
            await executeQuery(deleteSQL);
            alert(successMessage);
            loadTableData(table); // Actualizar la tabla después de la eliminación
        } catch (error) {
            alert(errorMessage);
            console.error('Error al eliminar el registro:', error.message || error);
        }
    }
    
    // Función para ejecutar consultas SQL
    async function executeQuery(query) {
        try {
            const response = await fetch(`/ProyectoWeb/php/admin/CRUD/executeQuery.php`, {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json'
                },
                body: JSON.stringify({ query })
            });
            const result = await response.json();
            if (!result.success) throw new Error(result.message || 'Error en la consulta SQL');
            return result.data;
        } catch (error) {
            console.error('Error al ejecutar la consulta:', error.message || error);
            throw error;
        }
    }
         
});
