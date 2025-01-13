document.addEventListener('DOMContentLoaded', function () {
    const renvDiv = document.getElementById('renv');
    const primeraVezDiv = document.getElementById('primera-vez');
    const usuarioE=document.getElementById('nombre');
    const tituloE=document.getElementById('titulo');

    // Ocultar ambas secciones inicialmente
    renvDiv.style.display = 'none';
    primeraVezDiv.style.display = 'none';

    fetch('/ProyectoWeb/php/acuse/validaSegA.php')
        .then(response => {
            if (!response.ok) {
                throw new Error('Error al obtener el estado de la solicitud');
            }
            return response.json();
        })
        .then(data => {
            if (data.error) {
                console.error(data.error);
                usuarioE.textContent = 'Usuario';
                return;
            }

            // Mostrar el nombre del usuario
            if (data.usuario) {
                usuarioE.textContent = `${data.usuario}`;
            } else {
                usuarioE.textContent = 'Usuario'; // Valor predeterminado si no hay nombre
            }
            
            if (data.solicitud) {
                tituloE.textContent = `${data.solicitud}`;
            } else {
                tituloE.textContent = 'Casilleros ESCOM'; // Valor predeterminado si no hay nombre
            }

            const estadoSolicitud = data.solicitud;
            

            // Mostrar la sección correspondiente según el estado
            if (estadoSolicitud === 'Renovación') {
                renvDiv.style.display = 'block';
                primeraVezDiv.style.display = 'none';
                cargarreno();
            } else if (estadoSolicitud === 'Primera vez') {
                primeraVezDiv.style.display = 'block';
                renvDiv.style.display = 'none';
                cargaprimv();
            } else {
                console.error('Estado de solicitud no válido:', estadoSolicitud);
            }
        })
        .catch(error => {
            console.error('Error al cargar el estado de solicitud:', error);
        });
});

// Función para cargar los modales de renovación
function cargarreno() {
    const inputComprobante = document.getElementById('comprobanteRenovacion');
    const checkConforme = document.getElementById('checkRenovacion');
    const btnGenerarAcuse = document.querySelector('#renovacion-boton button');
    const pdfDiv = document.getElementById('renovacion-pdf');
    const botonDiv = document.getElementById('renovacion-boton');

    pdfDiv.style.display = 'none';
    botonDiv.style.display = 'none';

    // Mostrar el PDF solo si se sube un archivo válido
    inputComprobante.addEventListener('change', function () {
        if (inputComprobante.files.length > 0) {
            const file = inputComprobante.files[0];

            // Validar que el archivo sea un PDF
            if (file.type === 'application/pdf') {
                pdfDiv.style.display = 'block';
            } else {
                alert('Por favor, suba un archivo en formato PDF.');
                inputComprobante.value = '';
                pdfDiv.style.display = 'none';
            }
        }
    });

    // Mostrar el botón de generar acuse solo si se acepta el checkbox
    checkConforme.addEventListener('change', function () {
        if (checkConforme.checked && inputComprobante.files.length > 0) {
            botonDiv.style.display = 'block';
        } else {
            botonDiv.style.display = 'none';
        }
    });

    // Lógica del botón Generar Acuse
    btnGenerarAcuse.addEventListener('click', function () {
        if (!checkConforme.checked) {
            alert('Debe aceptar los términos antes de continuar.');
            return;
        }

        if (!inputComprobante.files.length) {
            alert('Debe subir un comprobante de pago.');
            return;
        }

        // logica para enviar datos.
        alert('Renovación completada con éxito.');
    });
}

// Función para cargar los modales de PRIMERA VEZ
function cargaprimv(){
    const divboton = document.getElementById('primera-vez-boton');
    const divcompro = document.getElementById('primera-vez-subir-comprobante');
    const checkConforme = document.getElementById('checkPrimeraVez');
    const inputComprobante = document.getElementById('comprobantePrimeraVez');
    const btnGenerarAcuse = document.querySelector('#primera-vez-boton button');

    divcompro.style.display = 'none';
    divboton.style.display = 'none';
    
    checkConforme.addEventListener('change', function () {
        if (checkConforme.checked) {
            divcompro.style.display = 'block';
        } else {
            divcompro.style.display = 'none';
        }
    });

    inputComprobante.addEventListener('change', function () {
        if (inputComprobante.files.length > 0 && checkConforme.checked) {
            const file = inputComprobante.files[0];

            // Validar que el archivo sea un PDF
            if (file.type === 'application/pdf') {
                divboton.style.display = 'block';
            } else {
                alert('Por favor, suba un archivo en formato PDF.');
                inputComprobante.value = '';
                divboton.style.display = 'none';
            }
        }
    });

    btnGenerarAcuse.addEventListener('click', function () {
        if (!checkConforme.checked) {
            alert('Debe aceptar los términos antes de continuar.');
            return;
        }

        if (!inputComprobante.files.length) {
            alert('Debe subir un comprobante de pago.');
            return;
        }

        // logica para enviar datos.
        alert('Renovación completada con éxito.');
    });
}