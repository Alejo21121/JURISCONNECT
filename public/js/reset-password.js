// ========== CONFIGURACIÓN DE TIEMPO DE EXPIRACIÓN ==========
const TOKEN_EXPIRATION_TIME = 1 * 60 * 1000; // 1 minuto
let tokenExpirationTimer;

// ========== FUNCIÓN PARA MOSTRAR ALERTAS ==========
function showAlert(message, type = "error") {
    const alertContainer = document.getElementById("alert-container");

    // Remover alertas anteriores
    alertContainer.innerHTML = "";

    const alertDiv = document.createElement("div");
    alertDiv.className = `alert alert-${type}`;

    const icon = type === "error" ? "✕" : "⚠";
    alertDiv.innerHTML = `
                <span class="alert-icon">${icon}</span>
                <span>${message}</span>
            `;

    alertContainer.appendChild(alertDiv);

    // Auto-remover después de 5 segundos
    setTimeout(() => {
        alertDiv.style.opacity = "0";
        setTimeout(() => alertDiv.remove(), 300);
    }, 5000);
}

// ========== VALIDAR REQUISITOS DE CONTRASEÑA ==========
function validatePasswordRequirements(password) {
    const requirements = {
        length: password.length >= 8,
        uppercase: /[A-Z]/.test(password),
        lowercase: /[a-z]/.test(password),
        number: /[0-9]/.test(password),
        special: /[!@#$%^&*()_+\-=\[\]{};':"\\|,.<>\/?]/.test(password),
    };

    // Actualizar UI de requisitos
    updateRequirement("req-length", requirements.length);
    updateRequirement("req-uppercase", requirements.uppercase);
    updateRequirement("req-lowercase", requirements.lowercase);
    updateRequirement("req-number", requirements.number);
    updateRequirement("req-special", requirements.special);

    // Retornar si todos los requisitos se cumplen
    return Object.values(requirements).every((req) => req === true);
}

// ========== ACTUALIZAR UI DE REQUISITO ==========
function updateRequirement(elementId, isValid) {
    const element = document.getElementById(elementId);
    const icon = element.querySelector(".requirement-icon");

    if (isValid) {
        element.classList.add("valid");
        element.classList.remove("invalid");
        icon.textContent = "✓";
    } else {
        element.classList.remove("valid");
        element.classList.add("invalid");
        icon.textContent = "○";
    }
}

// ========== INICIAR TEMPORIZADOR DE EXPIRACIÓN ==========
function startTokenExpirationTimer() {
    tokenExpirationTimer = setTimeout(() => {
        showAlert(
            "El enlace de restablecimiento ha expirado. Por favor, solicita uno nuevo.",
            "warning",
        );

        // Deshabilitar el formulario
        const form = document.getElementById("resetPasswordForm");
        const inputs = form.querySelectorAll("input");
        const submitBtn = form.querySelector('button[type="submit"]');

        inputs.forEach((input) => (input.disabled = true));
        submitBtn.disabled = true;
        submitBtn.textContent = "Enlace Expirado";

        // Redirigir después de 3 segundos
        setTimeout(() => {
            window.location.href = "/forgot-password";
        }, 3000);
    }, TOKEN_EXPIRATION_TIME);
}

document.addEventListener("DOMContentLoaded", function () {
    // ========== CÓDIGO ORIGINAL (Placeholder, efectos, etc.) ==========
    const emailInput = document.getElementById("email");
    const form = document.querySelector("form");
    const nextButton = document.querySelector(".btn-next");
    const backButton = document.querySelector(".btn-back");

    // Configuración de placeholders dinámicos
    let placeholderIndex = 0;
    const placeholders = [
        "xxxxxxxxxxxx",
        "tu.email@sena.edu.co",
        "correo@ejemplo.com",
        "usuario@dominio.com",
    ];

    // Función para cambiar placeholder
    function changePlaceholder() {
        if (
            emailInput &&
            !emailInput.value &&
            document.activeElement !== emailInput
        ) {
            placeholderIndex = (placeholderIndex + 1) % placeholders.length;
            emailInput.placeholder = placeholders[placeholderIndex];
        }
    }

    // Cambiar placeholder cada 3 segundos
    setInterval(changePlaceholder, 3000);

    // Eventos del campo email
    if (emailInput) {
        emailInput.addEventListener("focus", function () {
            this.placeholder = "Ingresa tu correo electrónico";
            this.style.textAlign = "left";
        });

        emailInput.addEventListener("blur", function () {
            if (!this.value) {
                this.placeholder = "xxxxxxxxxxxx";
                this.style.textAlign = "center";
            }
        });

        emailInput.addEventListener("input", function () {
            this.classList.remove("is-invalid");

            if (this.value && !isValidEmail(this.value)) {
                this.style.borderColor = "#ffc107";
            } else if (this.value && isValidEmail(this.value)) {
                this.style.borderColor = "#28a745";
            } else {
                this.style.borderColor = "#e9ecef";
            }
        });
    }

    function isValidEmail(email) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        return emailRegex.test(email);
    }

    function removeExistingAlerts() {
        const existingAlerts = document.querySelectorAll(".alert");
        existingAlerts.forEach((alert) => {
            if (
                !alert.querySelector("p")?.textContent.includes("Hemos enviado")
            ) {
                alert.remove();
            }
        });
    }

    function animateElements() {
        const leftSection = document.querySelector(".left-section");
        const formContainer = document.querySelector(".form-container");

        if (leftSection) {
            leftSection.style.opacity = "0";
            leftSection.style.transform = "translateX(-50px)";

            setTimeout(() => {
                leftSection.style.transition = "all 1s ease-out";
                leftSection.style.opacity = "1";
                leftSection.style.transform = "translateX(0)";
            }, 100);
        }

        if (formContainer) {
            formContainer.style.opacity = "0";
            formContainer.style.transform = "translateX(50px)";

            setTimeout(() => {
                formContainer.style.transition = "all 1s ease-out";
                formContainer.style.opacity = "1";
                formContainer.style.transform = "translateX(0)";
            }, 300);
        }
    }

    function addButtonEffects() {
        const buttons = document.querySelectorAll(".btn");

        buttons.forEach((btn) => {
            btn.addEventListener("mouseenter", function () {
                this.style.transform = "translateY(-2px)";
            });

            btn.addEventListener("mouseleave", function () {
                if (!this.disabled) {
                    this.style.transform = "translateY(0)";
                }
            });
        });
    }

    animateElements();
    addButtonEffects();

    // ========== NUEVAS FUNCIONALIDADES ==========
    const passwordInput = document.getElementById("password");
    const confirmPasswordInput = document.getElementById(
        "password_confirmation",
    );
    const resetForm = document.getElementById("resetPasswordForm");

    // Iniciar temporizador de expiración
    startTokenExpirationTimer();

    // Validar contraseña en tiempo real
    passwordInput.addEventListener("input", function () {
        validatePasswordRequirements(this.value);
    });

    // Validar coincidencia de contraseñas
    confirmPasswordInput.addEventListener("input", function () {
        if (passwordInput.value && this.value) {
            if (passwordInput.value !== this.value) {
                this.style.borderColor = "#dc3545";
            } else {
                this.style.borderColor = "#28a745";
            }
        }
    });

    // Manejar envío del formulario
    resetForm.addEventListener("submit", function (e) {
        const password = passwordInput.value;
        const confirmPassword = confirmPasswordInput.value;

        // Validar que las contraseñas coincidan
        if (password !== confirmPassword) {
            e.preventDefault();
            showAlert(
                "Las contraseñas no coinciden. Por favor, verifica que ambas contraseñas sean iguales.",
                "error",
            );
            confirmPasswordInput.focus();
            return;
        }

        // Validar requisitos de contraseña
        if (!validatePasswordRequirements(password)) {
            e.preventDefault();
            showAlert(
                "La contraseña no cumple con todos los requisitos de seguridad.",
                "error",
            );
            passwordInput.focus();
            return;
        }

        // Si todo está bien, el formulario se enviará normalmente
    });

    console.log(
        "JurisConnect - Recuperar Contraseña inicializado correctamente",
    );
});

// ========== FUNCIÓN TOGGLE PASSWORD (ORIGINAL) ==========
function togglePassword(inputId) {
    const input = document.getElementById(inputId);
    const eyeClosed = document.getElementById(`eyeClosed-${inputId}`);
    const eyeOpen = document.getElementById(`eyeOpen-${inputId}`);

    if (input.type === "password") {
        input.type = "text";
        eyeClosed.style.display = "none";
        eyeOpen.style.display = "inline";
    } else {
        input.type = "password";
        eyeClosed.style.display = "inline";
        eyeOpen.style.display = "none";
    }
}
window.togglePassword = togglePassword;

// Limpiar temporizador al salir
window.addEventListener("beforeunload", function () {
    if (tokenExpirationTimer) {
        clearTimeout(tokenExpirationTimer);
    }
});
