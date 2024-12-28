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
                    lockerDiv.addEventListener('click', () => {
                        showLockerModal(locker.noCasillero, locker.estado);
                    });
                } else {
                    lockerDiv.classList.add('asignado');
                    lockerDiv.addEventListener('click', () => {
                        showLockerModal(locker.noCasillero, locker.estado, locker.boleta, locker.nombre);
                    });
                }

                  // Evento al hacer clic
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
            <button type="button" class="btn btn-primary">Asignar</button>
        `;
    } else {
        modalFooter.innerHTML = `
            <button type="button" id="reasignar-btn" class="btn btn-primary">Reasignar</button>
        `;

        // Lógica para el botón "Reasignar"
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
                            location.reload(); // Recargar la página para reflejar los cambios
                        } else {
                            alert('Ocurrió un error al intentar revocar el casillero.');
                        }
                    })
                    .catch(error => console.error('Error al revocar el casillero:', error));
            }
        });
    }

    const modalInstance = bootstrap.Modal.getInstance(modalElement);
    if (modalInstance) {
        modalInstance.dispose();
    }

    const modal = new bootstrap.Modal(modalElement);
    modal.show();
}

document.getElementById('lockerModal').addEventListener('hidden.bs.modal', () => {
    document.body.classList.remove('modal-open');
    document.body.style.overflow = '';
    document.body.style.paddingRight = '';
    console.log('Modal cerrado y limpieza completada');
});