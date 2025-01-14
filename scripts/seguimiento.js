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
            const boleta = data.boleta;
            

            // Mostrar la sección correspondiente según el estado
            if (estadoSolicitud === 'Renovación') {
                renvDiv.style.display = 'block';
                primeraVezDiv.style.display = 'none';
                cargarreno(boleta);
            } else if (estadoSolicitud === 'Primera vez') {
                primeraVezDiv.style.display = 'block';
                renvDiv.style.display = 'none';
                cargaprimv(boleta);
            } else {
                console.error('Estado de solicitud no válido:', estadoSolicitud);
            }
        })
        .catch(error => {
            console.error('Error al cargar el estado de solicitud:', error);
        });
});

//Función para el envio de datos
function procesarAcuse(inputComprobante, urlSubir, urlPDF, urlExito) {
    if (!inputComprobante.files.length) {
        alert('Debe subir un comprobante de pago.');
        return;
    }

    const file = inputComprobante.files[0];
    const formData = new FormData();
    formData.append('comprobante', file);

    //Enviar información a la base de datos
    fetch(urlSubir, {
        method: 'POST',
        body: formData,
    })
        .then(response => {
            console.log(response); // Para ver si la respuesta es OK
            if (!response.ok) {
                throw new Error('Error al guardar los datos en la base de datos.');
            }
            return response.json();
        })
        .then(data => {
            console.log(data); // Para inspeccionar los datos recibidos
            if (data.error) {
                alert('Error: ' + data.error);
                return;
            }
    
            // Generar el PDF
            window.open(urlPDF, '_blank');
    
            // Redirigir a la página de éxito
            window.location.href = urlExito;
        })
        .catch(error => {
            console.error('Error:', error);
            alert('Ocurrió un error. Por favor, inténtelo de nuevo.');
        });
}

// Función para cargar los modales de renovación
function cargarreno(boleta) {
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
            const nombreArchivo = file.name.trim().toLowerCase();
            const regex = /^\d{10}_comprobante\.pdf$/i;
            
            if (!regex.test(nombreArchivo)) {
                alert(`El archivo debe tener el formato: "${boleta}_comprobante.pdf".`);
                inputComprobante.value = '';
                divboton.style.display = 'none';
                return;
            }

            const boletaArchivo = nombreArchivo.trim().substring(0, 10);

            /* Comparar con la boleta del usuario
            if (boletaArchivo !== boleta.trim()) {
                alert(`El número de boleta en el archivo (${boletaArchivo}) no coincide con la boleta del usuario (${boleta}).`);
                inputComprobante.value = '';
                divboton.style.display = 'none';
                return;
            }
            */

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

        // Llamar a la función genérica
        procesarAcuse(
            inputComprobante,
            '/ProyectoWeb/php/acuse/subCompro.php',
            '/ProyectoWeb/php/acuse/Pdf_acuse.php',
            '/ProyectoWeb/exito.html'
        );
    });
    
}

// Función para cargar los modales de PRIMERA VEZ
function cargaprimv(boleta){
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
            const nombreArchivo = file.name.trim().toLowerCase();
            const regex = /^\d{10}_comprobante\.pdf$/i;
            
            if (!regex.test(nombreArchivo)) {
                alert(`El archivo debe tener el formato: "${boleta}_comprobante.pdf".`);
                inputComprobante.value = '';
                divboton.style.display = 'none';
                return;
            }

            const boletaArchivo = nombreArchivo.trim().substring(0, 10);

            /* Comparar con la boleta del usuario
            if (boletaArchivo !== boleta.trim()) {
                alert(`El número de boleta en el archivo (${boletaArchivo}) no coincide con la boleta del usuario (${boleta}).`);
                inputComprobante.value = '';
                divboton.style.display = 'none';
                return;
            }
            */

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

        // Llamar a la función genérica
        procesarAcuse(
            inputComprobante,
            '/ProyectoWeb/php/acuse/subCompro.php',
            '/ProyectoWeb/php/acuse/Pdf_acuse.php',
            '/ProyectoWeb/exito.html'
        );
    });
}