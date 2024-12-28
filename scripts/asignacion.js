
//funcion para mostrar casilleros
document.addEventListener('DOMContentLoaded', () => {
    fetch('/ProyectoWeb/php/admin/casilleros.php')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('locker-container');

            data.forEach(locker => {
                const lockerDiv = document.createElement('div');
                lockerDiv.classList.add('locker');

                lockerDiv.setAttribute('data-id', locker.noCasillero);

                if (locker.estado === 'Disponible') {
                    lockerDiv.classList.add('disponible');
                    lockerDiv.addEventListener('click', () => {
                        showLockerModal(locker.noCasillero, locker.estado);
                    });
                } else {
                    lockerDiv.classList.add('asignado');
                    lockerDiv.addEventListener('click', () => {
                        showLockerModal(locker.noCasillero, locker.estado, locker.boleta, locker.nombre);
                    });
                }

            lockerDiv.addEventListener('click', () => {
                const noCasillero = lockerDiv.getAttribute('data-id');

                fetch(`/ProyectoWeb/php/admin/modal.php?noCasillero=${noCasillero}`)
                    .then(response => response.json())
                    .then(modalData => {
                        if (modalData.error) {
                            alert(modalData.error); 
                            return;
                        }
                        showLockerModal(modalData);
                    })
                    .catch(error => console.error('Error al cargar datos del casillero:', error));
            });

            lockerDiv.innerHTML = `<h2>${locker.noCasillero}</h2>`;
            container.appendChild(lockerDiv);
            });
        })
        .catch(error => console.error('Error al obtener los datos:', error));
});

//Funcion para el filtro
document.addEventListener('DOMContentLoaded', () => {
    const filterSelect = document.getElementById('filterSelect');
    const lockerContainer = document.getElementById('locker-container');

    filterSelect.addEventListener('change', () => {
        const filterValue = filterSelect.value;
        filterLockers(filterValue);
    });

    function filterLockers(filter) {
        const lockers = lockerContainer.querySelectorAll('.locker');

        lockers.forEach(locker => {
            if (filter === 'todos') {
                locker.style.display = 'block'; 
            } else if (filter === 'disponibles' && locker.classList.contains('disponible')) {
                locker.style.display = 'block'; 
            } else if (filter === 'asignados' && locker.classList.contains('asignado')) {
                locker.style.display = 'block'; 
            } else {
                locker.style.display = 'none';
            }
        });
    }
});

// Función para mostrar el modal con datos
function showLockerModal(data) {
    const modalElement = document.getElementById('lockerModal');
    const modalTitle = document.getElementById('lockerModalLabel');
    const modalBody = document.querySelector('.modal-body');
    const modalFooter = document.querySelector('.modal-footer');

    modalTitle.textContent = '';
    modalBody.innerHTML = '';

    const altura = data.altura > 0.55 ? 'Alto' : 'Bajo';
    const asignadoA = data.boleta
        ? `${data.nombre} ${data.primerAp} ${data.segundoAp} <br><strong>Boleta:</strong> ${data.boleta}`
        : 'Sin Asignar';

    modalTitle.textContent = `Casillero #${data.noCasillero}`;
    modalBody.innerHTML = `
        <p><strong>Estado:</strong> ${data.estado}</p>
        <p><strong>Altura:</strong> ${altura} (${data.altura}m)</p>
        <p><strong>Asignado a:</strong> ${asignadoA}</p>
    `;

    if (data.estado === 'Disponible') {
        modalFooter.innerHTML = `
            <button type="button" id="asignar-btn" class="btn btn-primary">Asignar</button>
        `;

        document.getElementById('asignar-btn').addEventListener('click', () => {
            listaAlumnos(data);
        });
    } else {
        modalFooter.innerHTML = `
            <button type="button" id="reasignar-btn" class="btn btn-primary">Revocar</button>
        `;

        // Lógica para Reasignar
        document.getElementById('reasignar-btn').addEventListener('click', () => {
            const confirmar = confirm(
                `¿Estás seguro de que deseas revocar el casillero #${data.noCasillero} asignado a ${data.nombre}?`
            );
            if (confirmar) {
                fetch('/ProyectoWeb/php/admin/reasignar.php', {
                    method: 'POST',
                    headers: { 'Content-Type': 'application/json' },
                    body: JSON.stringify({ noCasillero: data.noCasillero })
                })
                .then(response => response.json())
                .then(result => {
                    if (result.success) {
                        alert('El casillero ha sido revocado exitosamente.');
                        location.reload();
                    } else {
                        alert('Ocurrió un error al intentar revocar el casillero.');
                    }
                })
                .catch(error => console.error('Error al revocar el casillero:', error));
            }
        });
    }

    //Correccion de bug de modal
    const modalInstance = bootstrap.Modal.getInstance(modalElement);
    if (modalInstance) {
        modalInstance.dispose();
    }

    const modal = new bootstrap.Modal(modalElement);
    modal.show();
}

//Funcion para mostrar lista de alumnos
function listaAlumnos(data) {
    fetch(`/ProyectoWeb/php/admin/listaAlumnos.php`)
        .then((response) => response.json())
        .then((alumnos) => {    
            const modalElement = document.getElementById('lockerModal');
            const modalTitle = document.getElementById('lockerModalLabel');
            const modalBody = document.querySelector('.modal-body');
            const modalFooter = document.querySelector('.modal-footer');

            // Filtrar condiciones de altura
            const alumnosFiltrados = alumnos.filter(
                (alumno) =>
                    data.altura <= 0.55 || (data.altura > 0.55 && alumno.estatura > 1.60)
            );

            if (alumnos.length === 0) {
                modalBody.innerHTML = `
                    <h5>Lista de Alumnos</h5>
                    <div class="alert alert-warning text-center" role="alert">
                        Sin solicitudes pendientes.
                    </div>
                `;
            } else if (alumnosFiltrados.length === 0) {
                modalBody.innerHTML = `
                    <h5>Lista de Alumnos</h5>
                    <div class="alert alert-warning text-center" role="alert">
                        No hay alumnos que cumplan con las condiciones de altura.
                    </div>
                `;
            } else {
                modalBody.innerHTML = `
                    <h5>Lista de Alumnos</h5>
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Fecha de Solicitud</th>
                                <th>Boleta</th>
                                <th>Nombre</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody>
                            ${alumnosFiltrados
                                .map(
                                    (alumno) => alumno.solicitud === 'Renovacion' || alumno.casilleroAnt == data.noCasillero ?` 
                                    <tr>
                                        <td>${alumno.fechaRegistro}</td>
                                        <td>${alumno.boleta}</td>
                                        <td>${alumno.nombre}</td>
                                        <td>${alumno.solicitud}</td>
                                        <td>${alumno.estadoSolicitud}</td>
                                        <td>
                                            <button class="btn btn-outline-primary" onclick="mostrarDetallesAlumno(${alumno.boleta}, ${data.noCasillero})">Más</button>
                                        </td>
                                    </tr>
                                ` : ''
                                ) 
                                .join('')}

                            ${alumnosFiltrados
                                .map(
                                    (alumno) => alumno.solicitud === 'Primera vez' ?` 
                                    <tr>
                                        <td>${alumno.fechaRegistro}</td>
                                        <td>${alumno.boleta}</td>
                                        <td>${alumno.nombre}</td>
                                        <td>${alumno.solicitud}</td>
                                        <td>${alumno.estadoSolicitud}</td>
                                        <td>
                                            <button class="btn btn-outline-primary" onclick="mostrarDetallesAlumno(${alumno.boleta}, ${data.noCasillero})">Más</button>
                                        </td>
                                    </tr>
                                ` : ''
                                ) 
                                .join('')}
                        </tbody>
                    </table>
                `;
            }

            const tableBody = document.querySelector('table tbody');
            if (!tableBody || tableBody.children.length === 0) {
                modalBody.innerHTML = `
                    <h5>Lista de Alumnos</h5>
                    <div class="alert alert-warning text-center" role="alert">
                        Sin solicitudes pendientes.
                    </div>
                `;
            }

            modalFooter.innerHTML = '';
        });
}

//Funcion para mostrar detalles del alumno
function mostrarDetallesAlumno(boleta, noCasillero) {
    fetch(`/ProyectoWeb/php/admin/detallesAlumnos.php?boleta=${boleta}`)
        .then((response) => response.json())
        .then((alumno) => {
            const modalBody = document.querySelector('.modal-body');
            const modalFooter = document.querySelector('.modal-footer');

            modalBody.innerHTML = `
                <h5>Detalles del Alumno:<strong> ${alumno.nombre}</strong></h5>
                <table class="table table-bordered">
                    <tbody>
                        <tr><th>Boleta</th><td>${alumno.boleta}</td></tr>
                        <tr><th>Fecha de Solicitud</th><td>${alumno.fechaRegistro}</td></tr>
                        <tr><th>Estado de la Solicitud</th><td>${alumno.estadoSolicitud}</td></tr>
                        <tr><th>Nombre</th><td>${alumno.nombre}</td></tr>
                        <tr><th>Tipo de Solicitud</th><td>${alumno.solicitud}</td></tr>
                        ${alumno.solicitud === 'Renovación'
                                ? `<tr><th>No. de Casillero Anterior</th><td>#${alumno.casilleroAnt}</td></tr>`
                                : ''
                        }
                        <tr><th>Estatura</th><td>${alumno.estatura}m</td></tr>
                        <tr><th>Correo</th><td><a class="btn btn-link" href="mailto:${alumno.correo}">${alumno.correo}</td></tr>
                        <tr><th>Teléfono</th><td>${alumno.telefono}</td></tr>
                        <tr><th>CURP</th><td>${alumno.curp}</td></tr>
                        <tr>
                            <th>Documentos</th>
                            <td>
                                <a href="${alumno.credencial}" target="_blank" class="btn btn-link">Credencial</a>
                                <a href="${alumno.horario}" target="_blank" class="btn btn-link">Horario</a>
                            </td>
                        </tr>
                    </tbody>
                </table>
            `;

            modalFooter.innerHTML = `
                <button id="confirmar-asignar-btn" class="btn btn-primary">Asignar</button>
                <button id="regresar-btn" class="btn btn-secondary">Regresar</button>
            `;

            document
                .getElementById('confirmar-asignar-btn')
                .addEventListener('click', () => {
                    if (confirm('¿Estás seguro de asignar este casillero al alumno?')) {
                        fetch(`/ProyectoWeb/php/admin/asignarCasillero.php`, {
                            method: 'POST',
                            headers: {
                                'Content-Type': 'application/json',
                            },
                            body: JSON.stringify({
                                boleta: alumno.boleta,
                                noCasillero: noCasillero,
                            }),
                        })
                            .then((response) => response.json())
                            .then((resultado) => {
                                if (resultado.success) {
                                    alert('Casillero asignado exitosamente.');
                                    location.reload();
                                } else {
                                    alert('Error al asignar el casillero.');
                                }
                            });
                    }
                });

            document.getElementById('regresar-btn').addEventListener('click', () => {
                fetch(`/ProyectoWeb/php/admin/modal.php?noCasillero=${noCasillero}`)
                    .then(response => response.json())
                    .then(modalData => {listaAlumnos(modalData);});
            });
        })
        .catch((error) => {
            console.error('Error al obtener los detalles del alumno:', error);
            alert('No se pudieron cargar los detalles del alumno.');
        });
}

//Correccion de bug de modal
document.getElementById('lockerModal').addEventListener('hidden.bs.modal', () => {
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
    console.log('Modal cerrado y limpieza completada');
});