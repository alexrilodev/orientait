/* Función que engancha los listeners de validación y toggles */
function initForms() {
    // Validación de formularios
    const formRegistro = document.querySelector('#formRegistro');
    const formLogin    = document.querySelector('#formLogin');
    const formOferta   = document.querySelector('#formOferta');
  
    // Registro
    if (formRegistro) {
      formRegistro.addEventListener('submit', e => {
        const email    = document.querySelector('#emailRegistro').value.trim();
        const pass     = document.querySelector('#passwordRegistro').value.trim();
        const confirm  = document.querySelector('#confirmPasswordRegistro').value.trim();
        if (!validarEmail(email))      { alert('Email inválido'); e.preventDefault(); }
        else if (pass.length < 6)      { alert('La contraseña debe tener ≥6 caracteres'); e.preventDefault(); }
        else if (pass !== confirm)     { alert('Las contraseñas no coinciden'); e.preventDefault(); }
      });
    }
  
    // Login
    if (formLogin) {
      formLogin.addEventListener('submit', e => {
        const email = document.querySelector('#emailLogin').value.trim();
        const pass  = document.querySelector('#passwordLogin').value.trim();
        if (!validarEmail(email))  { alert('Email inválido'); e.preventDefault(); }
        else if (pass.length < 6)  { alert('La contraseña debe tener ≥6 caracteres'); e.preventDefault(); }
      });
    }
  
    // Oferta
    if (formOferta) {
      formOferta.addEventListener('submit', e => {
        const titulo = document.querySelector('#tituloOferta').value.trim();
        const desc   = document.querySelector('#descripcionOferta').value.trim();
        if (titulo.length < 5)        { alert('El título debe tener ≥5 caracteres'); e.preventDefault(); }
        else if (desc.length < 20)    { alert('La descripción debe tener ≥20 caracteres'); e.preventDefault(); }
      });
    }
  
    /* Toggles de mostrar/ocultar contraseña */
  
    // En Login
    const toggleLogin = document.getElementById('togglePasswordLogin');
    if (toggleLogin) {
      toggleLogin.addEventListener('click', function() {
        const input = document.getElementById('passwordLogin');
        if (input.type === 'password') {
          input.type = 'text';
          this.textContent = 'Ocultar';
        } else {
          input.type = 'password';
          this.textContent = 'Ver';
        }
      });
    }
  
    // En Registro
    const toggleReg = document.getElementById('togglePasswordRegistro');
    if (toggleReg) {
      toggleReg.addEventListener('click', function() {
        const input = document.getElementById('passwordRegistro');
        input.type = input.type === 'password' ? 'text' : 'password';
        this.textContent = input.type === 'password' ? 'Ver' : 'Ocultar';
      });
    }
  
    // En Confirmar registro
    const toggleConfirm = document.getElementById('toggleConfirmPasswordRegistro');
    if (toggleConfirm) {
      toggleConfirm.addEventListener('click', function() {
        const input = document.getElementById('confirmPasswordRegistro');
        input.type = input.type === 'password' ? 'text' : 'password';
        this.textContent = input.type === 'password' ? 'Ver' : 'Ocultar';
      });
    }
  
    // En Perfil
    const togglePerfil = document.getElementById('togglePasswordPerfil');
    if (togglePerfil) {
      togglePerfil.addEventListener('click', function() {
        const input = document.getElementById('passwordPerfil');
        input.type = input.type === 'password' ? 'text' : 'password';
        this.textContent = input.type === 'password' ? 'Ver' : 'Ocultar';
      });
    }
  }
  
  // Validación de email
  function validarEmail(email) {
    return /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(email);
  }
  
  if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initForms);
  } else {
    initForms();
  }
  