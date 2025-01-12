document.addEventListener('DOMContentLoaded', function () {
    const btnGenerarAcuse = document.querySelector('#primera-vez-boton button');
    const checkConforme = document.getElementById('checkPrimeraVez');
    const inputComprobante = document.getElementById('comprobantePrimeraVez');

    btnGenerarAcuse.addEventListener('click', function () {
        if (!checkConforme.checked) {
            alert('Debe aceptar los términos antes de continuar.');
            return;
        }

        if (!inputComprobante.files.length) {
            alert('Debe subir un comprobante de pago.');
            return;
        }

        // Aquí puedes agregar la lógica para enviar los datos al servidor
        alert('Registro completado con éxito.');
    });
});
