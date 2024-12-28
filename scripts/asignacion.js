document.addEventListener('DOMContentLoaded', () => {
    //funcion para mostrar casilleros
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

        // Lógica para Asignar
        document.getElementById('asignar-btn').addEventListener('click', () => {
            fetch(`/ProyectoWeb/php/admin/listaAlumnos.php`)
                .then((response) => response.json())
                .then((alumnos) => {
                    const modalBody = document.querySelector('.modal-body');
        
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
                                        <th>Acción</th>
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
                    // Verificar si el tbody está vacío
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



//Correccion de bug de modal
document.getElementById('lockerModal').addEventListener('hidden.bs.modal', () => {
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
    console.log('Modal cerrado y limpieza completada');
});