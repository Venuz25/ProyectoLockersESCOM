document.addEventListener("DOMContentLoaded", () => {
    const radioRenovacion = document.getElementById("renovacion");
    const radioPrimeraVez = document.getElementById("p_vez");
    const casilleroAnterior = document.getElementById("casillero-anterior");
    const formulario = document.forms.locker;
    const datos = document.getElementById("datos");
    const divResumen = document.getElementById("resumen");
    const divFormulario = document.getElementById("formulario");
    const enviarButton = document.getElementById("enviar");
    const guardarButton = document.getElementById("guardar");

    const actualizarVisibilidad = () => {
        if (radioRenovacion.checked) {
            casilleroAnterior.style.display = "block";
            cuestionario.style.display = "block";
        } else if (radioPrimeraVez.checked) {
            casilleroAnterior.style.display = "none";
            cuestionario.style.display = "block";
        }
    };

    const validarFormulario = () => {
        const archivoCredencial = document.getElementById("credencial").files[0];
        const archivoHorario = document.getElementById("horario").files[0];
    
        // Validar número de casillero
        if (radioRenovacion.checked) {
            var numeroCasillero = document.forms.locker['numero-casillero'].value.trim();
            if (!numeroCasillero) {
                alert("Ingresa un numero de casillero anterior.");
                return false;
            }
        }
    
        // Validar nombre (acepta acentos y compuestos)
        var nombre = document.forms.locker['nombre'].value.trim();
        if (!/^[A-ZÁÉÍÓÚÜÑa-záéíóúüñ]+(?: [A-ZÁÉÍÓÚÜÑa-záéíóúüñ]+)*$/.test(nombre)) {
            alert("El nombre solo debe contener letras y puede ser compuesto.");
            return false;
        }
    
        // Validar primer apellido (acepta apellidos largos y compuestos)
        var apellidop = document.forms.locker['p_apellido'].value.trim();
        if (!/^[A-ZÁÉÍÓÚÜÑa-záéíóúüñ]+(?: [A-ZÁÉÍÓÚÜÑa-záéíóúüñ]+)*$/.test(apellidop)) {
            alert("El primer apellido solo debe contener letras y puede ser compuesto.");
            return false;
        }
    
        // Validar segundo apellido (acepta apellidos largos y compuestos)
        var apellidos = document.forms.locker['s_apellido'].value.trim();
        if (!/^[A-ZÁÉÍÓÚÜÑa-záéíóúüñ]+(?: [A-ZÁÉÍÓÚÜÑa-záéíóúüñ]+)*$/.test(apellidos)) {
            alert("El segundo apellido solo debe contener letras y puede ser compuesto.");
            return false;
        }
    
        // Validar teléfono (10 dígitos)
        var telefono = document.forms.locker['telefono'].value.trim();
        if (!/^\d{10}$/.test(telefono)) {
            alert("El teléfono debe contener exactamente 10 dígitos.");
            return false;
        }
    
        // Validar correo (formato institucional)
        var correo = document.forms.locker['correo'].value.trim();
        if (!/^[a-z0-9._%+-]+@alumno\.ipn\.mx$/.test(correo)) {
            alert("El correo no es válido.");
            return false;
        }
    
        // Validar boleta (10 dígitos)
        var boleta = document.forms.locker['boleta'].value.trim();
        if (!/^\d{10}$/.test(boleta)) {
            alert("El número de boleta debe contener 10 dígitos.");
            return false;
        }
    
        // Validar CURP (18 caracteres, años 1900-2000)
        var curp = document.forms.locker['curp'].value.trim();
        if (!/^[A-Z]{4}\d{6}[HM][A-Z]{5}[A-Z0-9]\d$/.test(curp)) {
            alert("El CURP debe contener exactamente 18 caracteres válidos.");
            return false;
        }
    
        // Validar estatura (en metros, formato 0.00 o 1.00+)
        var estatura = document.forms.locker['estatura'].value.trim();
        if (!/^([1-2]\.\d{2})$/.test(estatura)) {
            alert("La estatura debe estar en metros.");
            return false;
        }
    
        // Validar que se haya seleccionado un archivo y que sea PDF
        if (!archivoCredencial || archivoCredencial.type !== "application/pdf") {
            alert("Debes subir un archivo PDF válido para la credencial.");
            return false;
        }
    
        if (!archivoHorario || archivoHorario.type !== "application/pdf") {
            alert("Debes subir un archivo PDF válido para el horario.");
            return false;
        }
    
        // Valida que se ingrese algo en usuario y contraseña
        var usuario = document.forms.locker['user'].value.trim();
        if (usuario === "") {
            alert("Ingresa un usuario.");
            return false;
        }
    
        var contraseña = document.forms.locker['contraseña'].value.trim();
        if (contraseña === "") {
            alert("Ingresa una contraseña.");
            return false;
        }
    
        // Si todo está bien
        return true;
    };
        
    const mostrarResumen = () => {
        const tipoSolicitud = radioRenovacion.checked ? "Renovación" : "Primera vez";
        const numeroCasillero = document.getElementById("numero-casillero").value.trim();
        const nombre = formulario['nombre'].value.trim();
        const pApellido = formulario['p_apellido'].value.trim();
        const sApellido = formulario['s_apellido'].value.trim();
        const telefono = formulario['telefono'].value.trim();
        const correo = formulario['correo'].value.trim();
        const boleta = formulario['boleta'].value.trim();
        const curp = formulario['curp'].value.trim();
        const estatura = formulario['estatura'].value.trim();
        const archivoCredencial = document.getElementById("credencial").files[0];
        const archivoHorario = document.getElementById("horario").files[0];
        var usuario = document.forms.locker['user'].value.trim();

        var contraseña = document.forms.locker['contraseña'].value.trim();
        const contraseñaOculta = "*".repeat(contraseña.length - 3) + contraseña.slice(-3);

        const resumen = `
            <h4>Resumen de la solicitud de</h4> <span>${usuario}</span>
            <p><strong>Tipo de solicitud:</strong> ${tipoSolicitud}</p>
            ${tipoSolicitud === "Renovación" ? `<p><strong>Casillero anterior:</strong> ${numeroCasillero}</p>` : ""}
            <p><strong>Nombre completo:</strong> ${nombre} ${pApellido} ${sApellido}</p>
            <p><strong>Teléfono:</strong> ${telefono}</p>
            <p><strong>Correo:</strong> ${correo}</p>
            <p><strong>Boleta:</strong> ${boleta}</p>
            <p><strong>CURP:</strong> ${curp}</p>
            <p><strong>Estatura:</strong> ${estatura}</p>
            <p><strong>Archivo Credencial:</strong> ${archivoCredencial ? archivoCredencial.name : "No seleccionado"}</p>
            <p><strong>Archivo Horario:</strong> ${archivoHorario ? archivoHorario.name : "No seleccionado"}</p>
            <p><strong>Contraseña:</strong> ${contraseñaOculta}</p>
        `;

        datos.innerHTML = resumen;
        divResumen.style.display = "block";
        divFormulario.style.display = "none";
    };

    regresar = () => {
        divResumen.style.display = "none";
        divFormulario.style.display = "block";
    }

    radioRenovacion.addEventListener("change", actualizarVisibilidad);
    radioPrimeraVez.addEventListener("change", actualizarVisibilidad);

    enviarButton.addEventListener("click", () => {
        if (validarFormulario()) {
            mostrarResumen();
        }
    });

    // Enviar los datos al servidor
    guardarButton.addEventListener("click", () => {
        const formData = new FormData(formulario); 
        fetch("/ProyectoWeb/php/registro/guardarDatos.php", {
            method: "POST",
            body: formData,
        })
            .then((response) => {
                if (response.ok) {
                    window.location.href = "confirmacion.html";
                } else {
                    alert("Error al procesar la solicitud.");
                }
            })
            .catch((error) => {
                console.error("Error:", error);
                alert("Ocurrió un error al enviar los datos.");
            });
    });

});
