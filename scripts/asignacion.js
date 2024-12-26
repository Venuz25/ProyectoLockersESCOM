document.addEventListener('DOMContentLoaded', () => {
    fetch('/ProyectoWeb/php/admin/asignacion.php')
        .then(response => response.json())
        .then(data => {
            const container = document.getElementById('locker-container');

            data.forEach(locker => {
                const lockerDiv = document.createElement('div');
                lockerDiv.classList.add('locker');

                if (locker.estado !== 'Disponible') {
                    lockerDiv.classList.add('asignado');
                } else {
                    // Agregar evento para los casilleros disponibles
                    lockerDiv.addEventListener('click', () => {
                        showLockerModal(locker.noCasillero);
                    });
                }

                lockerDiv.innerHTML = `<span>${locker.noCasillero}</span>`;
                container.appendChild(lockerDiv);
            });
        })
        .catch(error => console.error('Error al obtener los datos:', error));
});

// Función para mostrar el modal con información del casillero
function showLockerModal(lockerNumber) {
    fetch('/ProyectoWeb/php/admin/solicitudes.php?action=solicitudes')
        .then(response => response.json())
        .then(solicitudes => {
            const modalTitle = document.getElementById('lockerModalLabel');
            const modalBody = document.querySelector('#lockerModal .modal-body');

            // Configurar contenido dinámico
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
                                    </tr>`).join('')
                                : '<tr><td colspan="4">No hay solicitudes pendientes.</td></tr>'
                        }
                    </tbody>
                </table>
            `;

            // Mostrar el modal usando Bootstrap
            const modal = new bootstrap.Modal(document.getElementById('lockerModal'));
            modal.show();
        })
        .catch(error => console.error('Error al obtener las solicitudes:', error));
}


// Ocultar el modal al hacer clic fuera de él
document.addEventListener('click', (event) => {
    const modal = document.getElementById('locker-modal');
    if (event.target === modal) {
        modal.style.display = 'none';
    }
});
