(function validarSesion() {
    fetch('/ProyectoWeb/php/acuse/validaSesA.php')
        .then(response => {
            if (!response.ok) {
                // Si no está autorizado
                window.location.href = '/ProyectoWeb/acuse.html';
            }
        })
        .catch(() => {
            window.location.href = '/ProyectoWeb/acuse.html';
        });
})();
