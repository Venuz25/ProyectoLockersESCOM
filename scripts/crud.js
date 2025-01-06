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
            } else if (table === 'casilleros') {
                document.getElementById('estado').addEventListener('change', function () {
                    const estado = this.value;
                    const divBoleta = document.getElementById('divBoleta');
                
                    if (estado !== 'Asignado') {
                        divBoleta.style.display = 'none';
                    } else {
                        divBoleta.style.display = 'block';
                    }
                });
            }else if (table === 'solicitudes'){
                document.getElementById('estadoSolicitud').addEventListener('change', function () {
                    const estadoSolicitud = this.value;
                    const casilleroAsignado = document.getElementById('casilleroAsignado');
                
                    if (estadoSolicitud === 'Aprobada') {
                        casilleroAsignado.style.display = 'block';
                    } else {
                        casilleroAsignado.style.display = 'none';
                    }
                });
            }

            document.getElementById('submitBtn').onclick = function () {
                if (validateForm()) {
                    handleSubmit('create', table);
                } else {
                    alert('Formulario no válido. Verifica los campos.');
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

    // Función para validar campos antes de enviar
    function validateForm() {
        const form = document.getElementById('crudForm');
        const inputs = form.querySelectorAll('input, select');
        let isValid = true;

        inputs.forEach(input => {
            if (input.validity.valid === false) {
                isValid = false;
                alert(`Por favor, revisa el campo: ${input.name} ${input.value} (${input.validationMessage})`);
            }
        });

        return isValid;
    }

    // Generar contenido dinámico del formulario
    function contForms(recordId, table, data = {}) {
        switch (table) {
            case 'administradores':
                return `
                    <div class="mb-3">
                        <label for="id" class="form-label">Id</label>
                        <input type="number" min="1" class="form-control" id="id" name="id" value="${data.id || ''}" required>
                    </div>
                    <div class="mb-3">
                        <label for="usuario" class="form-label">Usuario</label>
                        <input type="text" class="form-control" id="usuario" name="usuario" value="${data.usuario || ''}" required>
                    </div>
                    <div class="mb-3">
                        <label for="contrasena" class="form-label">Contraseña</label>
                        <input type="password" class="form-control" id="contrasena" name="contrasena" value="${data.contrasena || ''}" required>
                    </div>`;
            case 'alumnos':
                return `
                     <fieldset class="border p-3 mb-4">
                        <div class="mb-3">
                            <label for="tipo_solicitud" class="form-label">Selecciona el tipo de solicitud</label>
                            <select id="tipo_solicitud" name="tipo_solicitud" class="form-select" required>
                                <option value="Renovación" ${data.solicitud === 'Renovación' ? "selected" : ""}>Renovación</option>
                                <option value="Primera vez" ${data.solicitud === 'Primera vez' ? "selected" : ""}>Primera vez</option>
                            </select>
                        </div>
                        <div id="casillero-anterior" class="mt-3">
                            <label for="numero-casillero">Número de Casillero Anterior:</label>
                            <input type="number" name="numero-casillero" id="numero-casillero" class="form-control" min="1" title="Solo se permiten números" value="${data.casilleroAnt || ''}">
                        </div>
                    </fieldset>

                    <fieldset class="border p-3 mb-4">
                        <div class="mb-3">
                            <label for="nombre" class="form-label">Nombre</label>
                            <input type="text" name="nombre" id="nombre" class="form-control" pattern="[A-Za-záéíóúÁÉÍÓÚñÑ ]+" title="Solo se permiten letras" value="${data.nombre || ''}" required>
                        </div>

                        <div class="mb-3">
                            <label for="p_apellido" class="form-label">Primer Apellido</label>
                            <input type="text" name="p_apellido" id="p_apellido" class="form-control" pattern="[A-Za-záéíóúÁÉÍÓÚñÑ ]+" title="Solo se permiten letras" value="${data.primerAp || ''}" required>
                        </div>

                        <div class="mb-3">
                            <label for="s_apellido" class="form-label">Segundo Apellido</label>
                            <input type="text" name="s_apellido" id="s_apellido" class="form-control" pattern="[A-Za-záéíóúÁÉÍÓÚñÑ ]+" title="Solo se permiten letras" value="${data.segundoAp || ''}" required>
                        </div>

                        <div class="mb-3">
                            <label for="telefono" class="form-label">Teléfono</label>
                            <input type="text" name="telefono" id="telefono" class="form-control" pattern="^[0-9]{10}"  title="El teléfono debe contener exactamente 10 dígitos" value="${data.telefono || ''}" required>
                        </div>

                        <div class="mb-3">
                            <label for="correo" class="form-label">Correo Institucional</label>
                            <input type="text" name="correo" id="correo" class="form-control" pattern="[a-zA-Z0-9._%+-áéíóúÁÉÍÓÚñÑ]+@alumno\.ipn\.mx" title="El correo debe ser el institucional" value="${data.correo || ''}" required>
                        </div>

                        <div class="mb-3">
                            <label for="boleta" class="form-label">Número de Boleta</label>
                            <input type="text" name="boleta" id="boleta" class="form-control" pattern="^[0-9]{10}"  title="Número de boleta de 10 dígitos" value="${data.boleta || ''}" required>
                        </div>

                        <div class="mb-3">
                            <label for="curp" class="form-label">CURP</label>
                            <input type="text" name="curp" id="curp" class="form-control" pattern="[A-Z0-9]{18}" title="El CURP debe tener 18 caracteres validos" value="${data.curp || ''}" required>
                        </div>

                        <div class="mb-3">
                            <label for="estatura" class="form-label">Estatura</label>
                            <input type="text" name="estatura" id="estatura" class="form-control" min="1" max="3" step="0.01" title="La estatura debe estar en metros" value="${data.estatura || ''}" required>
                        </div>
                    </fieldset>

                    <fieldset class="border p-3 mb-4">
                        <div class="mb-3">
                            <label for="credencial" class="form-label">Credencial <i>(nombre: boleta_credencial)</i></label>
                            <input type="file" name="credencial" id="credencial" class="form-control" accept=".pdf" value="${data.credencial || ''}" required>
                        </div>

                        <div class="mb-3">
                            <label for="horario" class="form-label">Horario <i>(nombre: boleta_horario)</i></label>
                            <input type="file" name="horario" id="horario" class="form-control" accept=".pdf" value="${data.horario || ''}" required>
                        </div>
                    </fieldset>

                    <fieldset class="border p-3 mb-4">
                        <div class="mb-3">
                            <label for="user" class="form-label">Usuario</label>
                            <input type="text" id="user" name="usuario" class="form-control" value="${data.usuario || ''}" required autocomplete="off">
                        </div>

                        <div class="mb-3">
                            <label for="contraseña" class="form-label">Contraseña</label>
                            <input type="password" id="contraseña" name="contraseña" class="form-control" pattern=".{6,}" title="La contraseña debe tener al menos 6 caracteres" value="${data.contrasena || ''}" required autocomplete="off">
                        </div>
                    </fieldset>
                    `;
            case 'casilleros':
                return `
                    <fieldset class="border p-3 mb-4">
                        <div class="mb-3">
                            <label for="noCasillero" class="form-label">Número de Casillero</label>
                            <input type="number" id="noCasillero" name="noCasillero" class="form-control" min="1" value="${data.noCasillero || ''}" required>
                        </div>
                        <div class="mt-3">
                            <label for="altura" class="form-label">Altura</label>
                            <input type="text" id="altura" name="altura" class="form-control" pattern="^([1-2]\.[0-9]{2})?$" value="${data.altura || ''}" required>
                        </div>
                    </fieldset>
                    
                    <fieldset class="border p-3 mb-4">
                        <div class="mb-3">
                            <label for="estado" class="form-label">Estado</label>
                            <select id="estado" name="estado" class="form-select" required>
                                <option value="Asignado" ${data.estado === 'Asignado' ? "selected" : ""}>Asignado</option>
                                <option value="Disponible" ${data.estado === 'Disponible' ? "selected" : ""}>Disponible</option>
                            </select>
                        </div>
                        <div id="divBoleta" class="mt-3">
                            <label for="boletaAsignada" class="form-label">Boleta Asignada</label>
                            <input type="text" id="boletaAsignada" name="boletaAsignada" class="form-control" pattern="^[0-9]{10}$" value="${data.boletaAsignada || ''}">
                        </div>
                    </fieldset>
                    `;
            case 'solicitudes':
                return `
                    <fieldset class="border p-3 mb-4">
                        <div class="mb-3">
                            <label for="id" class="form-label">Id</label>
                            <input type="number" min="1" id="user" name="id" class="form-control" value="${data.id || ''}" required autocomplete="off">
                        </div>

                        <div class="mb-3">
                            <label for="noBoleta" class="form-label">Boleta Asociada</label>
                            <input type="text" id="noBoleta" name="noBoleta" class="form-control" pattern="^[0-9]{10}$" value="${data.noBoleta || ''}">
                        </div>

                        <div class="mb-3">
                            <label for="fechaRegistro" class="form-label">Fecha de Registro</label>
                            <input type="datetime" id="fechaRegistro" name="fechaRegistro" class="form-control" value="${data.fechaRegistro || ''}">
                        </div>
                    </fieldset>

                    <fieldset class="border p-3 mb-4">
                        <div class="mb-3">
                            <label for="estadoSolicitud" class="form-label">Estado de la Solicitud</label>
                            <select id="estadoSolicitud" name="estadoSolicitud" class="form-select" required>
                                <option value="Aprobada" ${data.estadoSolicitud === 'Aprobada' ? "selected" : ""}>Aprobada</option>
                                <option value="Pendiente" ${data.estadoSolicitud === 'Pendiente' ? "selected" : ""}>Pendiente</option>
                                <option value="Lista de espera" ${data.estadoSolicitud === 'Lista de espera' ? "selected" : ""}>Lista de espera</option>
                            </select>
                        </div>

                        <div id="casilleroAsignado" class="mb-3">
                            <label for="noCasillero" class="form-label">Número de Casillero Asignado</label>
                            <input type="number" id="noCasillero" name="noCasillero" class="form-control" min="1" required>
                        </div>
                    </fieldset>
                    
                    <fieldset class="border p-3 mb-4">
                        <div class="mb-3">
                            <label for="comprobantePago" class="form-label">Comprobante de Pago</label>
                            <input type="file" name="comprobantePago" id="comprobantePago" class="form-control" accept=".pdf" value="${data.comprobantePago || ''}" required>
                        </div>
                    </fieldset>
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
