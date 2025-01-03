document.addEventListener("DOMContentLoaded", function () {
    const casillerosButton = document.getElementById("btnCasilleros");
    const crudButton = document.getElementById("btnCrud");
    const casillerosDiv = document.getElementById("casilleros");
    const crudDiv = document.getElementById("crud");

    // Función estilos de los botones
    function actualizarEstilos(activo, inactivo) {
        activo.style.backgroundColor = "#225560";
        activo.style.color = "white";
        inactivo.style.backgroundColor = "white";
        inactivo.style.color = "#333";
    }

    // Mostrar el div de casilleros 
    casillerosButton.addEventListener("click", function () {
        casillerosDiv.style.display = "block"; 
        crudDiv.style.display = "none"; 
        actualizarEstilos(casillerosButton, crudButton);
    });

    // Mostrar el div de CRUD 
    crudButton.addEventListener("click", function () {
        crudDiv.style.display = "block"; 
        casillerosDiv.style.display = "none"; 
        actualizarEstilos(crudButton, casillerosButton);
    });
});
