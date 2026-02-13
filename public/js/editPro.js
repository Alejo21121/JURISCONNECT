let documentoAEliminar = null;

function eliminarDocumento(id) {
    documentoAEliminar = id;
    document.getElementById("modalEliminar").style.display = "flex";
}

function cerrarModal() {
    documentoAEliminar = null;
    document.getElementById("modalEliminar").style.display = "none";
}

function confirmarEliminar() {
    fetch(`/documentos/${documentoAEliminar}`, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
            Accept: "application/json",
        },
    }).then((res) => {
        if (res.ok) {
            location.reload();
        } else {
            alert("Error al eliminar el documento");
        }
    });
}
// Función para mostrar/ocultar alertas
function showErrorAlert() {
    document.getElementById("error-alert").style.display = "block";
    document.getElementById("error-alert").scrollIntoView({
        behavior: "smooth",
    });
}

// Función para mostrar estado de carga
function showLoading(button) {
    button.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Actualizando...';
    button.disabled = true;
    // Simular carga (en aplicación real esto se manejaría con el envío del formulario)
    setTimeout(() => {
        button.innerHTML = '<i class="fas fa-check"></i> ¡Actualizado!';
        setTimeout(() => {
            button.innerHTML = '<i class="fas fa-save"></i> Actualizar Proceso';
            button.disabled = false;
        }, 1000);
    }, 2000);
}

// Mejorar la experiencia del file input
document.getElementById("documento").addEventListener("change", function (e) {
    const label = document.querySelector(".file-input-label span");
    if (e.target.files.length > 0) {
        label.textContent = `Archivo seleccionado: ${e.target.files[0].name}`;
        // Cuando selecciona archivo
        label.parentElement.style.borderColor = "#28a745";
        label.parentElement.style.background = "#e9f9ee";
        label.parentElement.style.color = "#28a745";
    } else {
        label.textContent = "Seleccionar nuevo archivo o arrastra aquí";
    }
});

// Drag and drop para el file input
const fileInput = document.querySelector(".file-input");
const fileInputLabel = document.querySelector(".file-input-label");

["dragenter", "dragover", "dragleave", "drop"].forEach((eventName) => {
    fileInput.addEventListener(eventName, preventDefaults, false);
    document.body.addEventListener(eventName, preventDefaults, false);
});

["dragenter", "dragover"].forEach((eventName) => {
    fileInput.addEventListener(eventName, highlight, false);
});

["dragleave", "drop"].forEach((eventName) => {
    fileInput.addEventListener(eventName, unhighlight, false);
});

fileInput.addEventListener("drop", handleDrop, false);

function preventDefaults(e) {
    e.preventDefault();
    e.stopPropagation();
}

function highlight(e) {
    fileInputLabel.style.borderColor = "#28a745";
    fileInputLabel.style.background = "#e9f9ee";
    fileInputLabel.style.color = "#28a745";
}

function unhighlight(e) {
    fileInputLabel.style.borderColor = "#d1d5db";
    fileInputLabel.style.background = "#f9fafb";
    fileInputLabel.style.color = "#6b7280";
}

function handleDrop(e) {
    const files = e.dataTransfer.files;
    document.getElementById("documento").files = files;

    if (files.length > 0) {
        const label = document.querySelector(".file-input-label span");
        label.textContent = `Archivo seleccionado: ${files[0].name}`;
    }
}

// Validación en tiempo real
document
    .querySelectorAll("input[required], select[required], textarea[required]")
    .forEach((field) => {
        field.addEventListener("blur", function () {
            if (!this.value.trim()) {
                this.classList.add("is-invalid");
            } else {
                this.classList.remove("is-invalid");
            }
        });

        field.addEventListener("input", function () {
            if (this.classList.contains("is-invalid") && this.value.trim()) {
                this.classList.remove("is-invalid");
            }
        });
    });

let archivosNuevos = [];

const inputArchivo = document.getElementById("documento");
const preview = document.getElementById("filePreview");

inputArchivo.addEventListener("change", function () {
    for (let file of this.files) {
        archivosNuevos.push(file);
    }
    actualizarPreview();
    this.value = ""; // permite volver a elegir el mismo archivo
});

function actualizarPreview() {
    preview.innerHTML = "";

    archivosNuevos.forEach((file, index) => {
        const div = document.createElement("div");
        div.className = "file-item";

        div.innerHTML = `
            <span>📄 ${file.name}</span>
            <button type="button" onclick="eliminarArchivoNuevo(${index})">
                Eliminar
            </button>
        `;

        preview.appendChild(div);
    });
}

function eliminarArchivoNuevo(index) {
    archivosNuevos.splice(index, 1);
    actualizarPreview();
}

document.querySelector("form").addEventListener("submit", function (e) {
    if (archivosNuevos.length === 0) return;

    const formData = new FormData(this);

    archivosNuevos.forEach((file) => {
        formData.append("documentos[]", file);
    });

    e.preventDefault();

    fetch(this.action, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": "{{ csrf_token() }}",
        },
        body: formData,
    }).then((res) =>
        res.redirected ? (window.location.href = res.url) : location.reload(),
    );
});

const requierePagoSelect = document.getElementById("requiere_pago");
const valorPagoBox = document.getElementById("valor_pago_box");
const valorInput = document.getElementById("valor_estimado");

if (requierePagoSelect) {
    function togglePago() {
        if (requierePagoSelect.value === "1") {
            valorPagoBox.style.display = "block";
            valorInput.required = true;
        } else {
            valorPagoBox.style.display = "none";
            valorInput.required = false;
            valorInput.value = "";
        }
    }

    document.addEventListener("DOMContentLoaded", togglePago);
    requierePagoSelect.addEventListener("change", togglePago);
}

const estadoSelect = document.getElementById("estadoSelect");
const form = document.querySelector("form");

let estadoOriginal = window.procesoEstado;
let archivadoConfirmado = false;

if (estadoSelect) {
    estadoSelect.addEventListener("change", function () {
        if (this.value === "Archivado" && !archivadoConfirmado) {
            Swal.fire({
                icon: "warning",
                title: "¿Archivar proceso?",
                html: `
        <div style="text-align:center;">
            <p style="margin-bottom:10px; font-weight:600;">
                ⚠️ Esta acción es irreversible
            </p>

            <div style="
                text-align:left;
                display:inline-block;
                margin-top:10px;
            ">
                <p>Al archivar este proceso:</p>
                <ul style="margin-top:8px;">
                    <li>❌ No podrá volver a editarse</li>
                    <li>❌ No se podrán modificar pagos ni documentos</li>
                    <li>✔️ El proceso quedará cerrado definitivamente</li>
                </ul>
            </div>
        </div>
    `,
                showCancelButton: true,
                confirmButtonText: "Sí, archivar",
                cancelButtonText: "Cancelar",
                confirmButtonColor: "#28a745", // ahora verde 👌
                cancelButtonColor: "#6b7280",
            }).then((result) => {
                if (result.isConfirmed) {
                    archivadoConfirmado = true;
                } else {
                    estadoSelect.value = estadoOriginal;
                }
            });
        }
    });
}

form.addEventListener("submit", function (e) {
    if (estadoSelect.value === "Archivado" && !archivadoConfirmado) {
        e.preventDefault();
    }
});

form.addEventListener("submit", function (e) {
    if (estadoSelect.value === "Archivado" && !archivadoConfirmado) {
        e.preventDefault();
    }
});

document
    .getElementById("valor_estimado")
    .addEventListener("input", function (e) {
        let value = e.target.value.replace(/\D/g, "");
        e.target.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
    });
