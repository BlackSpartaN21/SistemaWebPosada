

document.addEventListener("DOMContentLoaded", () => {
  const form = document.getElementById("loginForm");
  const emailInput = document.getElementById("email");
  const passwordInput = document.getElementById("password");
  const emailError = document.getElementById("emailError");
  const passwordError = document.getElementById("passwordError");

  // Validación en tiempo real
  emailInput.addEventListener("input", () => {
    if (emailInput.value.length > 30) {
      emailError.textContent = "El correo no puede exceder los 30 caracteres.";
    } else {
      emailError.textContent = "";
    }
  });

  passwordInput.addEventListener("input", () => {
    if (passwordInput.value.length > 30) {
      passwordError.textContent = "La contraseña no puede exceder los 30 caracteres.";
    } else {
      passwordError.textContent = "";
    }
  });

  // Validación al enviar el formulario
  form.addEventListener("submit", (event) => {
    let isValid = true;

    if (emailInput.value.trim() === "") {
      emailError.textContent = "Debes llenar el campo de correo.";
      isValid = false;
    }

    if (passwordInput.value.trim() === "") {
      passwordError.textContent = "Debes llenar el campo de contraseña.";
      isValid = false;
    }

    if (!isValid) {
      event.preventDefault(); // Evita que se envíe el formulario
    }
  });
});

