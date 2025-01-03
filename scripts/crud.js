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
    });

    // Mostrar el div de CRUD 
    crudButton.addEventListener("click", function () {
        crudDiv.style.display = "block"; 
        casillerosDiv.style.display = "none"; 
        actualizarEstilos(crudButton, casillerosButton);
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
                                <button class="btn btn-warning btn-sm editBtn" data-id="${record.id || record.boleta || record.noCasillero}">Editar</button>
                                <button class="btn btn-danger btn-sm deleteBtn" data-id="${record.id || record.boleta || record.noCasillero}">Eliminar</button>
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
                if (confirm(`¿Estás seguro de que deseas eliminar el registro con ID: ${recordId}?`)) {
                    deleteRecord(recordId, table);
                }
            });
        });
    }

    // Función para eliminar el registro
    function deleteRecord(recordId, table) {
        let deleteSQL = '';
        let dependentActions = [];
        let successMessage = '';
        let errorMessage = 'Error al eliminar el registro.';
    
        // Cambiar la lógica de acuerdo con la tabla seleccionada
        switch (table) {
            case 'administradores':
                deleteSQL = `DELETE FROM administradores WHERE id = '${recordId}'`;
                successMessage = 'Administrador eliminado exitosamente';
                break;
    
            case 'alumnos':
                deleteSQL = `DELETE FROM alumnos WHERE boleta = '${recordId}'`;
                dependentActions.push(() => {
                    let deleteSolicitudSQL = `DELETE FROM solicitudes WHERE noBoleta = '${recordId}'`;
                    executeQuery(deleteSolicitudSQL);
    
                    let checkCasilleroSQL = `SELECT boletaAsignada FROM casilleros WHERE boletaAsignada = '${recordId}'`;
                    executeQuery(checkCasilleroSQL, result => {
                        if (result && result.length > 0) {
                            let updateCasilleroSQL = `UPDATE casilleros SET estado = 'Disponible', boletaAsignada = NULL WHERE boletaAsignada = '${recordId}'`;
                            executeQuery(updateCasilleroSQL);
                        }
                    });
                });
                successMessage = 'Alumno eliminado exitosamente';
                break;
    
            case 'casilleros':
                deleteSQL = `DELETE FROM casilleros WHERE noCasillero = '${recordId}'`;
                dependentActions.push(() => {
                    // Verificar si el casillero tiene una boleta asignada
                    let checkCasilleroSQL = `SELECT boletaAsignada FROM casilleros WHERE noCasillero = '${recordId}'`;
                    executeQuery(checkCasilleroSQL, casilleroResult => {
                        if (casilleroResult && casilleroResult.length > 0 && casilleroResult[0].boletaAsignada) {
                            // Si el casillero tiene boleta asignada, cambiar estado de la solicitud asociada
                            let updateSolicitudSQL = `UPDATE solicitudes SET estadoSolicitud = 'Pendiente' WHERE noBoleta = '${casilleroResult[0].boletaAsignada}'`;
                            executeQuery(updateSolicitudSQL);
                        }
                    });
                });
                successMessage = 'Casillero eliminado exitosamente';
                break;
    
            case 'solicitudes':
                deleteSQL = `DELETE FROM solicitudes WHERE id = '${recordId}'`;
                dependentActions.push(() => {
                    let getBoletaSQL = `SELECT noBoleta FROM solicitudes WHERE id = '${recordId}'`;
                    executeQuery(getBoletaSQL, result => {
                        if (result && result[0]) {
                            let boleta = result[0].noBoleta;
    
                            // Eliminar alumno asociado
                            let deleteAlumnoSQL = `DELETE FROM alumnos WHERE boleta = '${boleta}'`;
                            executeQuery(deleteAlumnoSQL);
    
                            // Verificar si el alumno tiene un casillero asignado antes de intentar actualizarlo
                            let checkCasilleroSQL = `SELECT boletaAsignada FROM casilleros WHERE boletaAsignada = '${boleta}'`;
                            executeQuery(checkCasilleroSQL, casilleroResult => {
                                if (casilleroResult && casilleroResult.length > 0 && casilleroResult[0].boletaAsignada) {
                                    let updateCasilleroSQL = `UPDATE casilleros SET estado = 'Disponible', boletaAsignada = NULL WHERE boletaAsignada = '${boleta}'`;
                                    executeQuery(updateCasilleroSQL);
                                }
                            });
                        }
                    });
                });
                successMessage = 'Solicitud eliminada exitosamente';
                break;
        }
    
        // Ejecutar la consulta para eliminar el registro principal
        executeQuery(deleteSQL, (result) => {
            if (result && result.success) {
                // Ejecutar acciones dependientes si hay
                dependentActions.forEach(action => action());
                alert(successMessage);
                loadTableData(table); // Actualizar la tabla después de la eliminación
            } else {
                alert(errorMessage);
                console.error('Error en la consulta de eliminación: ', result);
            }
        });
    }
    
    // Función para ejecutar consultas SQL
    function executeQuery(query, callback) {
        fetch(`/ProyectoWeb/php/admin/CRUD/executeQuery.php`, {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json'
            },
            body: JSON.stringify({ query })
        })
        .then(response => response.json())
        .then(result => {
            if (callback) callback(result);
        })
        .catch(error => {
            console.error('Error al ejecutar la consulta:', error);
            if (callback) callback(null);
        });
    }
     
});
