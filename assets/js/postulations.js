document.addEventListener('DOMContentLoaded', function() {
    document.querySelectorAll('.despostular-form').forEach(function(form) {
        form.addEventListener('submit', function(event) {
            event.preventDefault();

            var formData = new FormData(form);

            fetch(form.action, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.status === 'success') {
                    window.location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                alert('Error al procesar la solicitud');
            });
        });
    });
});
