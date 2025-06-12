document.addEventListener('DOMContentLoaded', function () {
    function obtenerNotificaciones() {
        fetch('../controllers/notificacionesController.php')
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    const notificacionIcono = document.getElementById('notificacion-icono');
                    const notificacionLista = document.getElementById('notificacion-lista');

                    if (data.notificaciones.length > 0) {
                        notificacionIcono.classList.add('notificaciones-nuevas'); // Indicador notificaciones
                        notificacionLista.innerHTML = ''; // Reset notificaciones anteriores

                        // Nuevas notificaciones en icono campana
                        data.notificaciones.forEach(notif => {
                            let li = document.createElement('li');
                            li.innerHTML = `<i class="bi bi-bell-fill"></i> ${notif.mensaje}`;
                            notificacionLista.appendChild(li);
                        });
                    } else {
                        notificacionIcono.classList.remove('notificaciones-nuevas');
                        notificacionLista.innerHTML = '<li>No tienes notificaciones nuevas.</li>';
                    }
                }
            })
            .catch(error => console.error('Error al obtener notificaciones:', error));
    }

    // Llamar a la función cada 10 seg
    setInterval(obtenerNotificaciones, 10000);
});
