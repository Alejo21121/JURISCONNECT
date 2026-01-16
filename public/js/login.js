function togglePasswordVisibility() {
    const passwordInput = document.getElementById("password");
    const eyeOpen = document.getElementById("eyeOpen");
    const eyeClosed = document.getElementById("eyeClosed");

    if (passwordInput.type === "password") {
        // Mostrar contraseña
        passwordInput.type = "text";
        eyeClosed.style.display = "none";
        eyeOpen.style.display = "inline";
    } else {
        // Ocultar contraseña
        passwordInput.type = "password";
        eyeClosed.style.display = "inline";
        eyeOpen.style.display = "none";
    }

    passwordInput.focus();
}

document.addEventListener("DOMContentLoaded", function () {
    const toggleButton = document.getElementById("togglePassword");
    const eyeClosed = document.getElementById("eyeClosed");
    const eyeOpen = document.getElementById("eyeOpen");

    //  Aseguramos que cargue con ojo cerrado
    eyeClosed.style.display = "inline";
    eyeOpen.style.display = "none";

    // Accesibilidad con teclado
    toggleButton.addEventListener("keydown", function (e) {
        if (e.code === "Space" || e.code === "Enter") {
            e.preventDefault();
            togglePasswordVisibility();
        }
    });
});
