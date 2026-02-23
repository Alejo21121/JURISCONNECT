// Envío del formulario con AJAX después de validación
function submitFormularioAjax() {
    const formData = new FormData(document.getElementById("formProceso"));

    archivosSeleccionados.forEach((file) => {
        formData.append("documentos[]", file);
    });

    fetch(PROCESOS_CONFIG.storeUrl, {
        method: "POST",
        headers: {
            Accept: "application/json",
            "X-CSRF-TOKEN": PROCESOS_CONFIG.csrf,
        },
        body: formData,
    })
        .then((response) => response.json())
        .then((result) => {
            if (result.success) {
                showAlert(
                    "success",
                    "¡Éxito!",
                    "Proceso creado exitosamente. Redirigiendo...",
                );
                setTimeout(() => {
                    window.location.href = PROCESOS_CONFIG.indexUrl;
                }, 1500);
            } else {
                if (result.errors) {
                    let errores = [];
                    for (let campo in result.errors) {
                        errores.push(result.errors[campo].join(", "));
                    }
                    showAlert(
                        "error",
                        "Errores de validación",
                        errores.join("\n"),
                    );
                } else {
                    showAlert(
                        "error",
                        "Error",
                        result.message || "Error desconocido",
                    );
                }
            }
        })
        .catch((error) => {
            console.error("Error:", error);
            showAlert(
                "error",
                "Error de conexión",
                "No se pudo conectar con el servidor",
            );
        });
}

// Función para mostrar alertas personalizadas
function showAlert(type, title, message, buttons = null) {
    const overlay = document.getElementById("alertOverlay");
    const alert = document.getElementById("customAlert");
    const icon = document.getElementById("alertIcon");
    const alertTitle = document.getElementById("alertTitle");
    const alertMessage = document.getElementById("alertMessage");
    const alertButtons = document.getElementById("alertButtons");

    // Remover clases anteriores
    alert.className = "custom-alert";
    alert.classList.add(`alert-${type}`);

    // Configurar icono según el tipo
    const icons = {
        success: "✓",
        error: "✕",
        warning: "⚠",
        info: "ℹ",
    };
    icon.textContent = icons[type] || "✓";

    // Configurar contenido
    alertTitle.textContent = title;
    alertMessage.textContent = message;

    // Configurar botones
    if (buttons) {
        alertButtons.innerHTML = buttons;
    } else {
        alertButtons.innerHTML = `<button class="alert-button ${type}" onclick="closeAlert()">Aceptar</button>`;
    }

    // Mostrar overlay
    overlay.classList.add("show");
}

// Función para cerrar alerta
function closeAlert() {
    const overlay = document.getElementById("alertOverlay");
    overlay.classList.remove("show");
}

// Cerrar con ESC o click fuera
document.getElementById("alertOverlay").addEventListener("click", function (e) {
    if (e.target === this) closeAlert();
});

document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") closeAlert();
});

// Validación del formulario
function validarFormulario(e) {
    e.preventDefault();

    // Limpiar errores previos
    document
        .querySelectorAll(".field-error")
        .forEach((el) => (el.textContent = ""));
    document
        .querySelectorAll(".form-input, .form-select, .form-textarea")
        .forEach((el) => {
            el.classList.remove("error", "success");
        });

    let errores = [];
    let valido = true;

    // Validar tipo de proceso
    const tipoProceso = document.getElementById("tipo_proceso");
    if (!tipoProceso.value) {
        errores.push("Debe seleccionar un tipo de proceso");
        tipoProceso.classList.add("error");
        document.getElementById("error_tipo_proceso").textContent =
            "Campo requerido";
        valido = false;
    } else {
        tipoProceso.classList.add("success");
    }

    // Validar número de radicado
    const numeroRadicado = document.getElementById("numero_radicado");
    if (!numeroRadicado.value.trim()) {
        errores.push("El número de radicado es obligatorio");
        numeroRadicado.classList.add("error");
        document.getElementById("error_numero_radicado").textContent =
            "Campo requerido";
        valido = false;
    } else {
        numeroRadicado.classList.add("success");
    }

    // Validar demandante
    const demandante = document.getElementById("demandante");
    if (!demandante.value.trim()) {
        errores.push("El nombre del demandante es obligatorio");
        demandante.classList.add("error");
        document.getElementById("error_demandante").textContent =
            "Campo requerido";
        valido = false;
    } else {
        demandante.classList.add("success");
    }

    // Validar demandado
    const demandado = document.getElementById("demandado");
    if (!demandado.value.trim()) {
        errores.push("El nombre del demandado es obligatorio");
        demandado.classList.add("error");
        document.getElementById("error_demandado").textContent =
            "Campo requerido";
        valido = false;
    } else {
        demandado.classList.add("success");
    }

    // Validar
    const descripcion = document.getElementById("descripcion");
    if (!descripcion.value.trim()) {
        errores.push("La  del caso es obligatoria");
        descripcion.classList.add("error");
        document.getElementById("error_descripcion").textContent =
            "Campo requerido";
        valido = false;
    } else if (descripcion.value.trim().length < 20) {
        errores.push("La  debe tener al menos 20 caracteres");
        descripcion.classList.add("error");
        document.getElementById("error_descripcion").textContent =
            "Mínimo 20 caracteres";
        valido = false;
    } else {
        descripcion.classList.add("success");
    }

    // Validar documento
    const documento = document.getElementById("documento");
    if (documento.files.length > 0) {
        const allowedTypes = [
            "application/pdf",
            "application/msword",
            "application/vnd.openxmlformats-officedocument.wordprocessingml.document",
        ];

        const maxSize = 10 * 1024 * 1024; // 10MB

        for (let i = 0; i < documento.files.length; i++) {
            const file = documento.files[i];

            if (!allowedTypes.includes(file.type)) {
                errores.push(`El archivo "${file.name}" no es PDF, DOC o DOCX`);
                documento.classList.add("error");
                valido = false;
                break;
            }

            if (file.size > maxSize) {
                errores.push(`El archivo "${file.name}" supera los 10MB`);
                documento.classList.add("error");
                valido = false;
                break;
            }
        }

        if (valido) {
            documento.classList.add("success");
        }
    }

    // Mostrar resultado de la validación
    if (!valido) {
        let mensajeErrores =
            errores.length > 1
                ? `Se encontraron ${errores.length} errores:\n\n${errores.map((e, i) => `${i + 1}. ${e}`).join("\n")}`
                : errores[0];

        showAlert("error", "¡Error de Validación!", mensajeErrores);

        // Scroll al primer error
        const primerError = document.querySelector(".error");
        if (primerError) {
            primerError.scrollIntoView({
                behavior: "smooth",
                block: "center",
            });
        }
    } else {
        // Formulario válido - Enviar directamente
        enviarFormulario();
    }

    return false;
}

// Función para enviar el formulario
function enviarFormulario() {
    submitFormularioAjax();
}

// Asignar evento al formulario
document
    .getElementById("formProceso")
    .addEventListener("submit", validarFormulario);

// Limpiar errores al escribir
document
    .querySelectorAll(".form-input, .form-select, .form-textarea")
    .forEach((input) => {
        input.addEventListener("input", function () {
            this.classList.remove("error");
            const errorSpan = document.getElementById("error_" + this.id);
            if (errorSpan) {
                errorSpan.textContent = "";
            }
        });
    });

// Demo: Botones para probar las alertas
function testAlerts() {
    console.log("Alertas disponibles:");
    console.log('1. showAlert("success", "Éxito", "Operación completada")');
    console.log('2. showAlert("error", "Error", "Algo salió mal")');
    console.log('3. showAlert("warning", "Advertencia", "Tenga cuidado")');
    console.log('4. showAlert("info", "Información", "Datos importantes")');
}

let archivosSeleccionados = [];

const inputArchivo = document.getElementById("documento");
const preview = document.getElementById("filePreview");

inputArchivo.addEventListener("change", function () {
    for (let file of this.files) {
        archivosSeleccionados.push(file);
    }
    actualizarPreview();
    this.value = ""; // permite volver a elegir el mismo archivo
});

function actualizarPreview() {
    preview.innerHTML = "";

    archivosSeleccionados.forEach((file, index) => {
        const div = document.createElement("div");
        div.className = "file-item";

        div.innerHTML = `
            <div class="file-name">
                📄 ${file.name}
            </div>
            <button type="button" class="file-remove" onclick="eliminarArchivo(${index})">
                Eliminar
            </button>
        `;

        preview.appendChild(div);
    });
}

function eliminarArchivo(index) {
    archivosSeleccionados.splice(index, 1);
    actualizarPreview();
}

const requierePagoSelect = document.getElementById("requiere_pago");
const valorEstimadoDiv = document.getElementById("valorEstimadoDiv");
const valorEstimadoInput = valorEstimadoDiv.querySelector("input");

function toggleValorEstimado() {
    if (requierePagoSelect.value === "1") {
        valorEstimadoDiv.classList.remove("d-none");
        valorEstimadoInput.setAttribute("required", "required");
    } else {
        valorEstimadoDiv.classList.add("d-none");
        valorEstimadoInput.removeAttribute("required");
        valorEstimadoInput.value = "";
    }
}

// Al cambiar el select
requierePagoSelect.addEventListener("change", toggleValorEstimado);

// CLAVE: al cargar la página
document.addEventListener("DOMContentLoaded", toggleValorEstimado);

document
    .getElementById("valor_estimado")
    .addEventListener("input", function (e) {
        let value = e.target.value.replace(/\D/g, "");
        e.target.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    });
