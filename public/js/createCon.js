// VARIABLES GLOBALES
let formToSubmit = null;

// FUNCIONES DEL MODAL
function showModal(form) {
    formToSubmit = form;
    const modal = document.getElementById("confirmModal");
    modal.classList.add("show");
    document.body.style.overflow = "hidden";
}

function closeModal() {
    const modal = document.getElementById("confirmModal");
    modal.classList.add("hiding");

    setTimeout(() => {
        modal.classList.remove("show", "hiding");
        document.body.style.overflow = "";
        formToSubmit = null;
    }, 200);
}

function confirmSubmit() {
    if (formToSubmit) {
        formToSubmit.removeEventListener("submit", handleFormSubmit);
        formToSubmit.submit();
    }
    closeModal();

    window.location.href = previousUrl;
}

function handleFormSubmit(e) {
    e.preventDefault();
    showModal(this);
}

// CÓDIGO PRINCIPAL
document.addEventListener("DOMContentLoaded", function () {
    const conceptoTextarea = document.getElementById("concepto");
    const counter = document.getElementById("conceptoCounter");
    const submitBtn = document.getElementById("submitBtn");
    const errorDiv = document.getElementById("conceptoError");
    const form = document.getElementById("conceptoForm");
    const modal = document.getElementById("confirmModal");

    // Contador de caracteres
    conceptoTextarea.addEventListener("input", function () {
        const length = this.value.length;
        counter.textContent = length + " caracteres";

        if (length < 50) {
            counter.className = "char-counter error";
            submitBtn.disabled = true;
            errorDiv.classList.add("show");
            this.classList.add("error");
        } else {
            counter.className = "char-counter success";
            submitBtn.disabled = false;
            errorDiv.classList.remove("show");
            this.classList.remove("error");
        }
    });

    // Trigger inicial
    conceptoTextarea.dispatchEvent(new Event("input"));

    // Evento del formulario
    if (form) {
        form.addEventListener("submit", handleFormSubmit);
    }

    // Cerrar modal al hacer clic fuera
    modal.addEventListener("click", function (e) {
        if (e.target === this) {
            closeModal();
        }
    });

    // Cerrar con tecla ESC
    document.addEventListener("keydown", function (e) {
        if (e.key === "Escape") {
            closeModal();
        }
    });
});