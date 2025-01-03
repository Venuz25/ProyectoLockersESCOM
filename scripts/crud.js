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
    const createBtn = document.getElementById('createRecordBtn');


    tableSelect.addEventListener('change', function () {
        const table = tableSelect.value;
        if (table) {
            loadTableData(table);
            createRecordBtn.style.display = "flex"; 
        } else {
            tableContainer.innerHTML = '';
            createRecordBtn.style.display = "none";
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

        // Botones de acción 
        document.querySelectorAll('.editBtn').forEach(button => {
            button.addEventListener('click', function () {
                const recordId = button.dataset.id;
                alert('editando...');
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

        if (createBtn) {
            createBtn.addEventListener('click', function () {
                openModal('create', null, table);
            });
        }
    }

    // Función para mostrar el modal según la acción y tabla
    function openModal(action, recordId, table) {
        const modalTitle = document.getElementById('crudModalLabel');
        const crudForm = document.getElementById('crudForm');
        crudForm.innerHTML = ''; // Limpiar contenido previo

        if (action === 'create') {
            modalTitle.innerText = 'Ingresa los datos del nuevo registro:';
            crudForm.innerHTML = contForms(recordId, table);

            if (table === 'alumnos') {
                document.getElementById('tipo_solicitud').addEventListener('change', function () {
                    const tipoSolicitud = this.value;
                    const casilleroAnterior = document.getElementById('casillero-anterior');

                    if (tipoSolicitud === 'Renovación') {
                        casilleroAnterior.style.display = 'block';
                    } else {
                        casilleroAnterior.style.display = 'none';
                    }
                });

            }

            document.getElementById('submitBtn').onclick = function () {
                 if (table === 'alunos' && validarFormulario()) {
                    handleSubmit('create', table);
                 } else{
                    handleSubmit('create', table);

                 }
            };
        } else if (action === 'edit') {
            modalTitle.innerText = `Modificando registro #${recordId}`;
            fetch(`/ProyectoWeb/php/admin/CRUD/getRecordData.php?recordId=${recordId}&table=${table}`)
                .then(response => response.json())
                .then(data => {
                    crudForm.innerHTML = contForms(recordId, table, data);
                    document.getElementById('submitBtn').onclick = function () {
                        handleSubmit('edit', table, recordId);
                    };
                });
        }

        crudModal.show();
    }

    // Generar contenido dinámico del formulario
    function contForms(recordId, table, data = {}) {
        switch (table) {
            case 'administradores':
                return `
                    <div class="mb-3">
                        <label for="id" class="form-label">Id:</label>
                        <input type="number" class="form-control" id="id" name="id" value="${data.id || ''}" required>
                    </div>
                    <div class="mb-3">
                        <label for="usuario" class="form-label">Usuario:</label>
                        <input type="text" class="form-control" id="usuario" name="usuario" value="${data.usuario || ''}" required>
                    </div>
                    <div class="mb-3">
                        <label for="contrasena" class="form-label">Contraseña:</label>
                        <input type="password" class="form-control" id="contrasena" name="contrasena" value="${data.contrasena || ''}" required>
                    </div>`;
            case 'alumnos':
                return `
                    <fieldset class="border p-3 mb-4">
                            <div class="mb-3">
                                <label for="tipo_solicitud" class="form-label">Selecciona el tipo de solicitud</label>
                                <select id="tipo_solicitud" name="tipo_solicitud" class="form-select" required>
                                    <option value="Renovación">Renovación</option>
                                    <option value="Primera vez">Primera vez</option>
                                </select>
                            </div>
                    </fieldset>

                    <div id="casillero-anterior" class="mt-3">
                        <fieldset class="border p-3 mb-4">
                            <label for="numero-casillero">Número de Casillero Anterior:</label>
                            <input type="text" name="numero-casillero" id="numero-casillero" class="form-control" placeholder="Ejemplo: 123">
                        </fieldset>
                    </div>

                    <div id="cuestionario">
                        <fieldset class="border p-3 mb-4">
                            <div class="mb-3">
                                <label for="nombre" class="form-label">Nombre</label>
                                <input type="text" name="nombre" id="nombre" class="form-control" placeholder="Juan" required>
                            </div>

                            <div class="mb-3">
                                <label for="p_apellido" class="form-label">Primer Apellido</label>
                                <input type="text" name="p_apellido" id="p_apellido" class="form-control" placeholder="Perez" required>
                            </div>

                            <div class="mb-3">
                                <label for="s_apellido" class="form-label">Segundo Apellido</label>
                                <input type="text" name="s_apellido" id="s_apellido" class="form-control" placeholder="Rodriguez" required>
                            </div>

                            <div class="mb-3">
                                <label for="telefono" class="form-label">Teléfono</label>
                                <input type="text" name="telefono" id="telefono" class="form-control" placeholder="10 dígitos" required>
                            </div>

                            <div class="mb-3">
                                <label for="correo" class="form-label">Correo Institucional</label>
                                <input type="email" name="correo" id="correo" class="form-control" placeholder="ejemplo@alumno.ipn.mx" required>
                            </div>

                            <div class="mb-3">
                                <label for="boleta" class="form-label">Número de Boleta</label>
                                <input type="text" name="boleta" id="boleta" class="form-control" placeholder="10 dígitos" required>
                            </div>

                            <div class="mb-3">
                                <label for="curp" class="form-label">CURP</label>
                                <input type="text" name="curp" id="curp" class="form-control" placeholder="18 caracteres" required>
                            </div>

                            <div class="mb-3">
                                <label for="estatura" class="form-label">Estatura</label>
                                <input type="text" name="estatura" id="estatura" class="form-control" placeholder="Ejemplo: 1.70" required>
                            </div>
                        </fieldset>

                        <fieldset class="border p-3 mb-4">
                            <legend class="w-auto">Subir Archivos</legend>
                            <div class="mb-3">
                                <label for="credencial" class="form-label">Sube tu Credencial <i>(nombre: boleta_credencial)</i></label>
                                <input type="file" name="credencial" id="credencial" class="form-control" required>
                            </div>

                            <div class="mb-3">
                                <label for="horario" class="form-label">Sube tu Horario <i>(nombre: boleta_horario)</i></label>
                                <input type="file" name="horario" id="horario" class="form-control" required>
                            </div>

                            <p>*Subir archivos en formato PDF con el nombre correspondiente.</p>
                        </fieldset>

                        <fieldset class="border p-3 mb-4">
                            <legend class="w-auto">Acceso</legend>
                            <div class="mb-3">
                                <label for="user" class="form-label">Usuario</label>
                                <input type="text" id="user" name="usuario" class="form-control" placeholder="Escribe tu Usuario" required autocomplete="off">
                            </div>

                            <div class="mb-3">
                                <label for="contraseña" class="form-label">Contraseña</label>
                                <input type="password" id="contraseña" name="contraseña" class="form-control" placeholder="Escribe tu Contraseña" required autocomplete="off">
                            </div>
                        </fieldset>

                    </div>
                    `;
            case 'casilleros':
                return `
                    Casilleros
                    `;
            case 'solicitudes':
                return `
                    solicitudes
                    `;
        }
    }

    // Manejar el envío del formulario
    function handleSubmit(action, table, recordId = null) {
        const formData = new FormData(document.getElementById('crudForm'));
        formData.append('table', table);

        let endpoint = action === 'create' ? '/ProyectoWeb/php/admin/CRUD/createRecord.php' : '/ProyectoWeb/php/admin/CRUD/updateRecord.php';
        
        if (action === 'edit') formData.append('recordId', recordId);

        fetch(endpoint, {
            method: 'POST',
            body: formData,
        })
            .then(response => response.json())
            .then(result => {
                if (result.success) {
                    alert(result.message);
                    crudModal.hide();
                    loadTableData(table); // Actualizar la tabla
                } else {
                    alert(result.message);
                }
            })
            .catch(error => console.error('Error:', error));
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
