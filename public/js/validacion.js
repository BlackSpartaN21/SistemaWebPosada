//Validacion de Registrar Cliente
document.addEventListener("DOMContentLoaded", function () {
    const tipoDocumento = document.getElementById("tipo_documento_cliente");
    const documento = document.getElementById("documento_cliente");
    const nombres = document.getElementById("nombres_cliente");
    const apellidos = document.getElementById("apellidos_cliente");
    const telefono = document.getElementById("telefono_cliente");
    const correo = document.getElementById("correo_cliente");
    const descripcion = document.getElementById("descripcion_cliente");
  
    function validarDocumento() {
        const tipo = tipoDocumento.value;
        let valor = documento.value;
        let maxLength = tipo === "P" || tipo === "J" ? 10 : 8;
        let minLength = tipo === "J" ? 10 : 7;
  
        if (tipo === "J") {
            valor = valor.replace(/[^0-9-]/g, ""); // Solo números y "-"
        } else {
            valor = valor.replace(/\D/g, ""); // Solo números
        }
  
        if (valor.length < minLength || valor.length > maxLength) {
            documento.classList.add("is-invalid");
            documento.nextElementSibling.textContent = `Debe tener entre ${minLength} y ${maxLength} caracteres.`;
        } else {
            documento.classList.remove("is-invalid");
            documento.nextElementSibling.textContent = "";
        }
  
        documento.value = valor;
    }
  
    function validarTexto(input) {
        input.value = input.value.replace(/[^a-zA-ZáéíóúÁÉÍÓÚñÑ\s]/g, ""); // Solo letras y espacios
        if (input.value.length > 50) {
            input.classList.add("is-invalid");
            input.nextElementSibling.textContent = "Máximo 50 caracteres.";
        } else {
            input.classList.remove("is-invalid");
            input.nextElementSibling.textContent = "";
        }
    }
  
    function validarTelefono() {
        telefono.value = telefono.value.replace(/\D/g, ""); // Solo números
        if (telefono.value.length !== 11) {
            telefono.classList.add("is-invalid");
            telefono.nextElementSibling.textContent = "Debe contener exactamente 11 caracteres.";
        } else {
            telefono.classList.remove("is-invalid");
            telefono.nextElementSibling.textContent = "";
        }
    }
  
    function validarCorreo() {
        correo.value = correo.value.slice(0, 50); // Limita a 50 caracteres
        const regex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!regex.test(correo.value)) {
            correo.classList.add("is-invalid");
            correo.nextElementSibling.textContent = "Ingrese un correo válido.";
        } else {
            correo.classList.remove("is-invalid");
            correo.nextElementSibling.textContent = "";
        }
    }
  
    function validarDescripcion() {
        if (descripcion.value.length > 255) {
            descripcion.classList.add("is-invalid");
            descripcion.nextElementSibling.textContent = "Máximo 255 caracteres.";
        } else {
            descripcion.classList.remove("is-invalid");
            descripcion.nextElementSibling.textContent = "";
        }
    }
  
    // Eventos de validación en tiempo real
    tipoDocumento.addEventListener("change", validarDocumento);
    documento.addEventListener("input", validarDocumento);
    nombres.addEventListener("input", () => validarTexto(nombres));
    apellidos.addEventListener("input", () => validarTexto(apellidos));
    telefono.addEventListener("input", validarTelefono);
    correo.addEventListener("input", validarCorreo);
    descripcion.addEventListener("input", validarDescripcion);
  
    // Validación antes de enviar el formulario
    document.getElementById("clienteForm").addEventListener("submit", function (event) {
        validarDocumento();
        validarTelefono();
        validarCorreo();
        validarDescripcion();
  
        // Si hay algún error, evita el envío del formulario
        if (document.querySelector(".is-invalid")) {
            event.preventDefault();
        }
    });
  });
  
  function toggleMenu() {
    var menu = document.querySelector(".menu ul");
    menu.classList.toggle("show");
  }
  
  // Detectar clics fuera del menú para cerrarlo
  document.addEventListener("click", function(event) {
    var menu = document.querySelector(".menu ul");
    var hamburguesa = document.querySelector(".hamburguesa");
  
    // Verificar si el clic fue fuera del menú y fuera del ícono de hamburguesa
    if (!menu.contains(event.target) && !hamburguesa.contains(event.target)) {
      menu.classList.remove("show");
    }
  });
  
  document.addEventListener("DOMContentLoaded", function() {
    window.onscroll = function() {scrollFunction()};
  
    var menu = document.getElementById("menu");
    var sticky = menu.offsetTop;
  
    function scrollFunction() {
      if (window.pageYOffset > sticky) {
        menu.classList.add("sticky");
      } else {
        menu.classList.remove("sticky");
      }
    }
  });
  