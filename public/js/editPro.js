let documentoAEliminar = null;

function eliminarDocumento(id) {
    documentoAEliminar = id;
    const modal = document.getElementById("modalEliminar");
    if (modal) modal.style.display = "flex";
}

function cerrarModal() {
    documentoAEliminar = null;
    const modal = document.getElementById("modalEliminar");
    if (modal) modal.style.display = "none";
}

function confirmarEliminar() {
    if (!documentoAEliminar) return;

    const token = document.querySelector('meta[name="csrf-token"]')?.content;
    if (!token) {
        alert("Error: Token CSRF no encontrado");
        return;
    }

    fetch(`/documentos/${documentoAEliminar}`, {
        method: "DELETE",
        headers: {
            "X-CSRF-TOKEN": token,
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

// ═══════════════════════════════════════
// INICIALIZACIÓN SEGURA
// ═══════════════════════════════════════
document.addEventListener("DOMContentLoaded", function () {
    // ──────────────────────────────────
    // FILE INPUT
    // ──────────────────────────────────
    const documentoInput = document.getElementById("documento");
    const fileInputLabel = document.querySelector(".file-input-label");
    const filePreview = document.getElementById("filePreview");

    if (documentoInput && fileInputLabel) {
        documentoInput.addEventListener("change", function (e) {
            const label = fileInputLabel.querySelector("span");
            if (!label) return;

            if (e.target.files.length > 0) {
                label.textContent = `${e.target.files.length} archivo(s) seleccionado(s)`;
                fileInputLabel.style.borderColor = "#28a745";
                fileInputLabel.style.background = "#e9f9ee";
                fileInputLabel.style.color = "#28a745";
            } else {
                label.textContent = "Seleccionar archivo";
                fileInputLabel.style.borderColor = "#d1d5db";
                fileInputLabel.style.background = "#f9fafb";
                fileInputLabel.style.color = "#6b7280";
            }
        });

        // DRAG AND DROP
        const fileInput = document.querySelector(".file-input");
        if (fileInput) {
            ["dragenter", "dragover", "dragleave", "drop"].forEach(
                (eventName) => {
                    fileInput.addEventListener(
                        eventName,
                        (e) => {
                            e.preventDefault();
                            e.stopPropagation();
                        },
                        false,
                    );
                },
            );

            ["dragenter", "dragover"].forEach((eventName) => {
                fileInput.addEventListener(
                    eventName,
                    () => {
                        fileInputLabel.style.borderColor = "#28a745";
                        fileInputLabel.style.background = "#e9f9ee";
                        fileInputLabel.style.color = "#28a745";
                    },
                    false,
                );
            });

            ["dragleave", "drop"].forEach((eventName) => {
                fileInput.addEventListener(
                    eventName,
                    () => {
                        fileInputLabel.style.borderColor = "#d1d5db";
                        fileInputLabel.style.background = "#f9fafb";
                        fileInputLabel.style.color = "#6b7280";
                    },
                    false,
                );
            });

            fileInput.addEventListener(
                "drop",
                (e) => {
                    const files = e.dataTransfer.files;
                    documentoInput.files = files;
                    documentoInput.dispatchEvent(new Event("change"));
                },
                false,
            );
        }
    }

    // ──────────────────────────────────
    // VALIDACIÓN EN TIEMPO REAL
    // ──────────────────────────────────
    document
        .querySelectorAll(
            "input[required], select[required], textarea[required]",
        )
        .forEach((field) => {
            field.addEventListener("blur", function () {
                if (!this.value.trim()) {
                    this.classList.add("is-invalid");
                } else {
                    this.classList.remove("is-invalid");
                }
            });

            field.addEventListener("input", function () {
                if (
                    this.classList.contains("is-invalid") &&
                    this.value.trim()
                ) {
                    this.classList.remove("is-invalid");
                }
            });
        });

    // ──────────────────────────────────
    // REQUIERE PAGO TOGGLE
    // ──────────────────────────────────
    const requierePagoSelect = document.getElementById("requiere_pago");
    const valorPagoBox = document.getElementById("valor_pago_box");
    const valorInput = document.getElementById("valor_estimado");

    if (requierePagoSelect && valorPagoBox && valorInput) {
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

        togglePago(); // ejecutar al cargar
        requierePagoSelect.addEventListener("change", togglePago);
    }

    // ──────────────────────────────────
    // FORMATO DE VALOR ESTIMADO
    // ──────────────────────────────────
    const valorEstimadoInput = document.getElementById("valor_estimado");
    if (valorEstimadoInput) {
        valorEstimadoInput.addEventListener("input", function (e) {
            let value = e.target.value.replace(/\D/g, "");
            e.target.value = value.replace(/\B(?=(\d{3})+(?!\d))/g, ".");
        });
    }

    // ──────────────────────────────────
    // ESTADO: ARCHIVADO CONFIRMACIÓN
    // ──────────────────────────────────
    const estadoSelect = document.getElementById("estadoSelect");
    const form = document.querySelector("form");

    if (estadoSelect && form) {
        let estadoOriginal = window.procesoEstado || estadoSelect.value;
        let archivadoConfirmado = false;

        estadoSelect.addEventListener("change", function () {
            if (this.value === "Archivado" && !archivadoConfirmado) {
                // Verificar si SweetAlert está disponible
                if (typeof Swal !== "undefined") {
                    Swal.fire({
                        icon: "warning",
                        title: "¿Archivar proceso?",
                        html: `
                            <div style="text-align:center;">
                                <p style="margin-bottom:10px; font-weight:600;">
                                    ⚠️ Esta acción es irreversible
                                </p>
                                <div style="text-align:left; display:inline-block; margin-top:10px;">
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
                        confirmButtonColor: "#28a745",
                        cancelButtonColor: "#6b7280",
                    }).then((result) => {
                        if (result.isConfirmed) {
                            archivadoConfirmado = true;
                        } else {
                            estadoSelect.value = estadoOriginal;
                        }
                    });
                } else {
                    // Fallback si no hay SweetAlert
                    const confirmed = confirm(
                        "⚠️ ¿Archivar proceso?\n\nEsta acción es irreversible.\n\n• No podrá volver a editarse\n• No se podrán modificar pagos ni documentos\n• El proceso quedará cerrado definitivamente",
                    );
                    if (confirmed) {
                        archivadoConfirmado = true;
                    } else {
                        estadoSelect.value = estadoOriginal;
                    }
                }
            }
        });

        form.addEventListener("submit", function (e) {
            if (estadoSelect.value === "Archivado" && !archivadoConfirmado) {
                e.preventDefault();
                estadoSelect.dispatchEvent(new Event("change"));
            }
        });
    }

    // ──────────────────────────────────
    // TRASLADO DE PROCESO
    // ──────────────────────────────────
    const trasladoBox = document.getElementById("trasladoBox");

    if (estadoSelect && trasladoBox) {
        function verificarEstado() {
            if (estadoSelect.value === "Trasladar") {
                trasladoBox.style.display = "block";
            } else {
                trasladoBox.style.display = "none";
            }
        }

        verificarEstado(); // ejecutar al cargar
        estadoSelect.addEventListener("change", verificarEstado);
    }

    // ──────────────────────────────────
    // NOTIFICACIONES DROPDOWN
    // ──────────────────────────────────
    const notificationBtn = document.getElementById("notificationBtn");
    const notificationDropdown = document.getElementById(
        "notificationDropdown",
    );

    if (notificationBtn && notificationDropdown) {
        notificationBtn.addEventListener("click", function (e) {
            e.stopPropagation();
            notificationDropdown.classList.toggle("show");
        });

        document.addEventListener("click", function (e) {
            if (
                !notificationBtn.contains(e.target) &&
                !notificationDropdown.contains(e.target)
            ) {
                notificationDropdown.classList.remove("show");
            }
        });

        document.querySelectorAll(".notification-item").forEach((item) => {
            item.addEventListener("click", function () {
                notificationDropdown.classList.remove("show");
            });
        });
    }

    console.log("✅ editPro.js cargado correctamente");
});
