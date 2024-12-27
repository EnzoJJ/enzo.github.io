document.addEventListener('DOMContentLoaded', function() {
    const fechaInput = document.getElementById('fecha');
    const horarioSelect = document.getElementById('horario');

    // Define los horarios disponibles
    const horariosDisponibles = [
        "14:00",
        "14:40",
        "15:20",
        "16:00",
        "16:40",
        "17:20",
        "18:00",
        "18:40",
        "19:20",
        "20:00",
    ];

    function establecerFechaMinima() {
        const hoy = new Date();
        const dia = hoy.getDate().toString().padStart(2, '0');
        const mes = (hoy.getMonth() + 1).toString().padStart(2, '0');
        const anio = hoy.getFullYear();
        const fechaMinima = `${anio}-${mes}-${dia}`;
        fechaInput.setAttribute('min', fechaMinima);
    }

    establecerFechaMinima();

    fechaInput.addEventListener('change', function() {
        const fechaSeleccionada = this.value;

        // Realiza una solicitud AJAX para obtener los horarios reservados
        fetch(`index.php?action=obtenerHorariosReservados&fecha=${fechaSeleccionada}`)
            .then(response => response.json())
            .then(horariosReservados => {
                console.log('Horarios reservados:', horariosReservados); // Verifica la respuesta
                horarioSelect.innerHTML = '';

                horariosDisponibles.forEach(horario => {
                    const option = document.createElement('option');
                    option.value = horario;
                    option.textContent = horario;

                    // Si el horario está reservado, deshabilitarlo
                    if (horariosReservados.includes(horario)) {
                        option.disabled = true; // Deshabilitar opción
                    }

                    horarioSelect.appendChild(option); // Agregar la opción al select
                });
            })
            .catch(error => {
                console.error('Error al obtener los horarios:', error);
            });
    });
});

