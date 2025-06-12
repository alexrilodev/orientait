document.addEventListener('DOMContentLoaded', () => {
    const input = document.getElementById('selectorCita');
    const fp = flatpickr(input, {
        enableTime: true,
        dateFormat: "Y-m-d H:i",
        minDate: "2025-01-01",
        maxDate: "2025-12-31",
        time_24hr: true,
        onReady: fetchSlots
    });
  
    function fetchSlots() {
        fetch('../controllers/citasController.php?action=slots')
            .then(r => r.json())
            .then(slots => {
                const disabled = slots.map(slot => {
                    return slot.hora;
                });
                fp.set('disable', disabled);
            })
            .catch(e => console.error(e));
    }
  
    document.getElementById('btnReservar').addEventListener('click', () => {
        const fh = input.value;
        if (!fh) return alert('Selecciona fecha y hora');
  
        // Validación rápida previo a enviar
        const fechaHora = new Date(fh);
        const dia = fechaHora.getDay();
        const hora = fechaHora.getHours();
        const minutos = fechaHora.getMinutes();
  
        if (dia === 0 || dia === 6) {
            return alert('Solo puedes reservar de lunes a viernes.');
        }
        if (!((hora >= 8 && hora < 14) || (hora >= 15 && hora < 17))) {
            return alert('Solo puedes reservar entre 8-14h o 15-17h.');
        }
        if (minutos !== 0) {
            return alert('Solo puedes reservar en horas exactas (en punto).');
        }
  
        fetch('../controllers/citasController.php?action=book', {
            method: 'POST',
            headers: {'Content-Type':'application/x-www-form-urlencoded'},
            body: `fecha_hora=${encodeURIComponent(fh)}`
        })
        .then(r => r.json())
        .then(res => {
            const st = document.getElementById('statusCita');
            if (res.success) {
                st.innerHTML = `<div class="alert alert-success">${res.success}</div>`;
            } else {
                st.innerHTML = `<div class="alert alert-danger">${res.error}</div>`;
            }
            fetchSlots(); // Actualizar la lista de slots deshabilitados
        });
    });
  });
  