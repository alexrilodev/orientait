document.addEventListener('DOMContentLoaded', () => {
    import('./postulations.js')
      .then(module => {
      })
      .catch(e => console.error(e));

    import('./forms.js')
      .catch(e => console.error(e));

    import('./notifications.js')
      .catch(e => console.error(e));

    console.log('app.js cargado correctamente');
});
