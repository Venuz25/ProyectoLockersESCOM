document.addEventListener('DOMContentLoaded', () => {
    fetch('/ProyectoWeb/php/admin/asignacion.php')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('locker-container');

            data.forEach(locker => {
                const lockerDiv = document.createElement('div');
                lockerDiv.classList.add('locker');

                if (locker.estado === 'Disponible') {
                    lockerDiv.addEventListener('click', () => {
                        showLockerModal(locker.noCasillero, locker.estado);
                    });
                } else {
                    lockerDiv.classList.add('asignado');
                    lockerDiv.addEventListener('click', () => {
                        showLockerModal(locker.noCasillero, locker.estado, locker.boleta, locker.nombre);
                    });
                }

                lockerDiv.innerHTML = `<span>${locker.noCasillero}</span>`;
                container.appendChild(lockerDiv);
            });
        })
        .catch(error => console.error('Error al obtener los datos:', error));
});

// Función para mostrar el modal con información del casillero
function showLockerModal(lockerNumber, estado, boleta = null, nombre = null) {
    const modalTitle = document.getElementById('lockerModalLabel');
    const modalBody = document.querySelector('#lockerModal .modal-body');

    if (estado === 'Disponible') {
        fetch('/ProyectoWeb/php/admin/solicitudes.php?action=solicitudes')
            .then(response => response.json())
            .then(solicitudes => {
                // Configurar contenido dinámico para casillero disponible
                modalTitle.textContent = `Casillero #${lockerNumber}`;
                modalBody.innerHTML = `
                    <p><strong>Altura del casillero:</strong> ${lockerNumber <= 50 ? 'Bajo (1-50)' : 'Alto (51-100)'}</p>
                    <p><strong>Estado:</strong> Disponible</p>
                    <h5>Solicitudes Pendientes o en Lista de Espera:</h5>
                    <table class="table">
                        <thead>
                            <tr>
                                <th>Boleta</th>
                                <th>Nombre</th>
                                <th>Tipo</th>
                                <th>Estado</th>
                                <th>Estatura</th>
                            </tr>
                        </thead>
                        <tbody>
                            ${
                                solicitudes.length > 0
                                    ? solicitudes.map(s => `
                                        <tr>
                                            <td>${s.boleta}</td>
                                            <td>${s.nombre}</td>
                                            <td>${s.solicitud}</td>
                                            <td>${s.estadoSolicitud}</td>
                                            <td>${s.estatura}</td>
                                        </tr>`).join('')
                                    : '<tr><td colspan="5">No hay solicitudes pendientes.</td></tr>'
                            }
                        </tbody>
                    </table>
                `;

                const modal = new bootstrap.Modal(document.getElementById('lockerModal'));
                modal.show();
            })
            .catch(error => console.error('Error al obtener las solicitudes:', error));
    } else {
        fetch(`/ProyectoWeb/php/admin/detalleCasillero.php?noCasillero=${lockerNumber}`)
            .then(response => response.json())
            .then(data => {
                modalTitle.textContent = `Casillero #${lockerNumber}`;
                modalBody.innerHTML = `
                    <p><strong>Estado:</strong> Asignado</p>
                    <p><strong>Altura del casillero:</strong> ${lockerNumber <= 50 ? 'Bajo (1-50)' : 'Alto (51-100)'}</p>
                    <h5>Asignado a:</h5>
                    <p><strong>Nombre:</strong> ${data.nombre}</p>
                    <p><strong>Boleta:</strong> ${data.boleta}</p>
                    <button id="reasignarCasillero" class="btn btn-warning">Reasignar Casillero</button>
                    <button id="cerrarModal" class="btn btn-secondary">Cerrar</button>
                `;

                document.getElementById('reasignarCasillero').addEventListener('click', () => {
                    fetch('/ProyectoWeb/php/admin/listaSolicitudes.php')
                        .then(response => response.json())
                        .then(solicitudes => {
                            let solicitudesHTML = '<table class="table"><thead><tr><th>No Boleta</th><th>Nombre</th><th>Tipo de Solicitud</th><th>Altura</th><th>Teléfono</th><th>Correo</th><th>CURP</th><th>Estado</th><th>Fecha</th><th>Acción</th></tr></thead><tbody>';
                            solicitudes.forEach(solicitud => {
                                solicitudesHTML += `
                                    <tr>
                                        <td>${solicitud.boleta}</td>
                                        <td>${solicitud.nombre}</td>
                                        <td>${solicitud.tipoSolicitud}</td>
                                        <td>${solicitud.altura}</td>
                                        <td>${solicitud.telefono}</td>
                                        <td>${solicitud.correo}</td>
                                        <td>${solicitud.curp}</td>
                                        <td>${solicitud.estado}</td>
                                        <td>${solicitud.fecha}</td>
                                        <td><button class="btn btn-primary" onclick="asignarCasillero(${lockerNumber}, ${solicitud.boleta})">Asignar Casillero</button></td>
                                    </tr>
                                `;
                            });
                            solicitudesHTML += '</tbody></table>';
                            modalBody.innerHTML = solicitudesHTML;
                        })
                        .catch(error => console.error('Error al obtener las solicitudes:', error));
                });

                document.getElementById('cerrarModal').addEventListener('click', () => {
                    modal.hide();
                });
            })
            .catch(error => {
                console.error('Error al obtener los datos del casillero:', error);
                modalBody.innerHTML = `<p>Error al cargar los datos del casillero asignado.</p>`;
            });
    }

    const modal = new bootstrap.Modal(document.getElementById('lockerModal'));
    modal.show();
}

function asignarCasillero(noCasillero, boleta) {
    fetch(`/ProyectoWeb/php/admin/asignarCasillero.php?noCasillero=${noCasillero}&boleta=${boleta}`, { method: 'POST' })
        .then(response => response.json())
        .then(data => {
            if (data.success) {
                alert('Casillero asignado correctamente');
                modal.hide();
            } else {
                alert('Error al asignar el casillero');
            }
        })
        .catch(error => console.error('Error al asignar el casillero:', error));
}
