

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

    // validador del formulario de reserva
    const formReserva = document.getElementById('form-reserva');
    if (formReserva) {
        formReserva.addEventListener('submit', function(event) {
            // si la validacion propia del navegador falla, detenemos el envio
            if (!formReserva.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            
            // añadimos la clase de bootstrap para pintar los errores en rojo/verde
            formReserva.classList.add('was-validated');

            // si pasa la validacion nativa, comprobamos las reglas personalizadas
            if (formReserva.checkValidity()) {
                // obtener valores del formulario
                const datos = {
                    nombre: document.getElementById('nombre').value,
                    telefono: document.getElementById('telefono').value,
                    email: document.getElementById('email').value,
                    servicio: document.getElementById('servicio_id').value,
                    dispositivo: document.getElementById('dispositivo').value,
                    fecha: document.getElementById('fecha').value,
                    hora: document.getElementById('hora').value
                };

                // combinamos fecha y hora para la validacion general
                datos.fechaHora = (datos.fecha && datos.hora) ? datos.fecha + 'T' + datos.hora : '';

                // realizar la validacion con reglas personalizadas
                const resultado = validarCita(datos);
                if (!resultado.valido) {
                    alert(resultado.mensaje);
                    event.preventDefault();
                    event.stopPropagation();
                }
            }
        });
    }
});

// funcion para validar los datos del formulario de reserva con reglas personalizadas
function validarCita(datos) {
    const ahora = new Date();
    const fechaSeleccionada = new Date(datos.fechaHora);

    // validar que la fecha seleccionada no este en el pasado
    if (datos.fechaHora) {
        if (fechaSeleccionada < ahora) {
            return { valido: false, mensaje: 'la fecha y hora de la cita deben ser en el futuro.' };
        }
    }

    // validar formato del numero de telefono
    const regexTelefono = /^[0-9]{9,15}$/;
    if (datos.telefono && !regexTelefono.test(datos.telefono.trim())) {
        return { valido: false, mensaje: 'por favor, introduce un numero de telefono valido (solo digitos, entre 9 y 15).' };
    }

    return { valido: true };
}
