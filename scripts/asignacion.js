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