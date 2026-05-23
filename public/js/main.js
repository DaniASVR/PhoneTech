

//buscador del catalogo
document.addEventListener('DOMContentLoaded', function() {
    const buscador = document.getElementById('buscar-input');
    const tarjetas = document.querySelectorAll('.servicio-tarjeta');
    const mensajeSinResultados = document.getElementById('sin-resultados');

    // comprobamos que el buscador esta inicializado y existen tarjetas
    if (buscador && tarjetas.length > 0) {
        // listener de que el usuario escribe en el buscador
        buscador.addEventListener('input', function() {
            // convertimos el texto a minusculas y quitamos espacios sobrantes
            const textoBusqueda = buscador.value.toLowerCase().trim();
            let encontradas = 0;

            // recorremos las tarjetas para ver si coinciden
            tarjetas.forEach(function(tarjeta) {
                // obtenemos titulo y descripcion
                const titulo = tarjeta.querySelector('.card-title').textContent.toLowerCase();
                const descripcion = tarjeta.querySelector('.card-text').textContent.toLowerCase();

                // comprobamos si coinciden
                if (textoBusqueda === "" || titulo.includes(textoBusqueda) || descripcion.includes(textoBusqueda)) {
                    tarjeta.style.display = "";
                    encontradas++;
                } else {
                    tarjeta.style.display = "none";
                }
            });

            // mostramos u ocultamos el mensaje de error
            if (encontradas === 0) {
                mensajeSinResultados.style.display = "block";
            } else {
                mensajeSinResultados.style.display = "none";
            }
        });
    }
});
