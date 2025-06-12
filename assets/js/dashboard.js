document.addEventListener('DOMContentLoaded', () => {
    document.querySelectorAll('.cancelarCita').forEach(btn => {
      btn.addEventListener('click', () => {
        if(confirm('¿Estás seguro de cancelar la cita?')){
          fetch('../controllers/citasController.php?action=cancel', {
            method: 'POST',
            headers: {'Content-Type':'application/x-www-form-urlencoded'},
            body: `fecha=${btn.dataset.fecha}&hora=${btn.dataset.hora}`
          })
          .then(r => r.json())
          .then(res => {
            if (res.success) location.reload();
            else alert(res.error);
          });
        }
      });
    });
  });
  