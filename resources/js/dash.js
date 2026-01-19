/* ========= utils y constantes ========= */
const getCsrfToken = () => {
    const el = document.querySelector('meta[name="csrf-token"]');
    return el ? el.getAttribute("content") : null;
};

const hamburgerBtn = document.getElementById("hamburgerBtn");
const sidebar = document.getElementById("sidebar");
const overlay = document.getElementById("overlay");
const mainContent = document.getElementById("mainContent");
const createLawyerModal = document.getElementById("createLawyerModal");
const createBtn = document.getElementById("createBtn");
const closeModal = document.getElementById("closeModal");
const cancelBtn = document.getElementById("cancelBtn");

// Variables para modal de edición
const editLawyerModal = document.getElementById("editLawyerModal");
const editLawyerForm = document.getElementById("editLawyerForm");
const closeEditModalBtn = document.getElementById("closeEditModal");
const cancelEditBtn = document.getElementById("cancelEditBtn");

/* ========= ALERTAS PERSONALIZADAS (VERSIÓN MEJORADA) ========= */

function showCustomAlert(
    type,
    title = "",
    message = "",
    showCancel = false,
    confirmText = "Aceptar",
    cancelText = "Cancelar"
) {
    return new Promise((resolve) => {
        // Crear overlay si no existe
        let alertOverlay = document.getElementById("alertOverlay");
        if (alertOverlay) {
            alertOverlay.remove();
        }

        // Crear nuevo overlay
        alertOverlay = document.createElement("div");
        alertOverlay.id = "alertOverlay";
        alertOverlay.className = "alert-overlay";

        // Configurar iconos según el tipo
        const icons = {
            success: "✓",
            error: "✕",
            warning: "⚠",
            info: "ℹ",
        };

        // Configurar títulos por defecto
        const defaultTitles = {
            success: "¡Éxito!",
            error: "¡Error!",
            warning: "¡Atención!",
            info: "Información",
        };

        // Configurar mensajes por defecto
        const defaultMessages = {
            success: "Operación completada exitosamente",
            error: "Algo salió mal. Inténtalo de nuevo.",
            warning: "Verifica la información antes de continuar.",
            info: "Proceso en desarrollo.",
        };

        const icon = icons[type] || icons.info;
        const alertTitle = title || defaultTitles[type] || defaultTitles.info;
        const alertMessage =
            message || defaultMessages[type] || defaultMessages.info;

        // Crear botones
        const buttonsHTML = showCancel
            ? `<div class="alert-buttons">
                <button class="alert-button secondary" id="cancelAlertBtn">${cancelText}</button>
                <button class="alert-button ${type}" id="confirmAlertBtn">${confirmText}</button>
               </div>`
            : `<div class="alert-buttons">
                <button class="alert-button ${type}" id="confirmAlertBtn">${confirmText}</button>
               </div>`;

        alertOverlay.innerHTML = `
            <div class="custom-alert alert-${type}" id="customAlert" role="dialog" aria-modal="true">
                <div class="alert-icon">${icon}</div>
                <div class="alert-title">${alertTitle}</div>
                <div class="alert-message">${alertMessage}</div>
                ${buttonsHTML}
            </div>
        `;

        document.body.appendChild(alertOverlay);

        // Mostrar con animación
        setTimeout(() => alertOverlay.classList.add("show"), 10);

        const cleanup = () => {
            alertOverlay.classList.remove("show");
            setTimeout(() => {
                if (alertOverlay && alertOverlay.parentNode) {
                    alertOverlay.parentNode.removeChild(alertOverlay);
                }
            }, 350);
        };

        // Event listeners para botones
        const confirmBtn = document.getElementById("confirmAlertBtn");
        const cancelBtn = document.getElementById("cancelAlertBtn");

        if (confirmBtn) {
            confirmBtn.onclick = () => {
                cleanup();
                resolve(true);
            };
        }

        if (cancelBtn) {
            cancelBtn.onclick = () => {
                cleanup();
                resolve(false);
            };
        }

        // Cerrar con ESC
        const escHandler = (e) => {
            if (e.key === "Escape") {
                cleanup();
                resolve(false);
                document.removeEventListener("keydown", escHandler);
            }
        };
        document.addEventListener("keydown", escHandler);

        // Cerrar al hacer click fuera del modal
        alertOverlay.addEventListener("click", function (e) {
            if (e.target === alertOverlay) {
                cleanup();
                resolve(false);
            }
        });
    });
}

function showAlert(type, title, message, buttons = null) {
    // Si se proporcionan botones personalizados, usar la versión antigua
    if (buttons) {
        return showCustomAlert(
            type,
            title,
            message,
            false,
            "Aceptar",
            "Cancelar"
        );
    }

    // Usar la nueva versión mejorada
    return showCustomAlert(type, title, message, false, "Aceptar", "Cancelar");
}

/**
 * Cerrar alerta manualmente
 */
function closeAlert() {
    const overlayEl = document.getElementById("alertOverlay");
    if (overlayEl) {
        overlayEl.classList.remove("show");
        setTimeout(() => {
            if (overlayEl && overlayEl.parentNode) {
                overlayEl.parentNode.removeChild(overlayEl);
            }
        }, 350);
    }
}

/**
 * Alias de closeAlert para compatibilidad
 */
function hideCustomAlert() {
    closeAlert();
}

// ========================================
// FUNCIONES DE CONVENIENCIA
// ========================================

/**
 * Alerta de éxito rápida
 */
function alertSuccess(message, title = "¡Éxito!") {
    return showCustomAlert("success", title, message, false, "Aceptar");
}

/**
 * Alerta de error rápida
 */
function alertError(message, title = "¡Error!") {
    return showCustomAlert("error", title, message, false, "Entendido");
}

/**
 * Alerta de advertencia rápida
 */
function alertWarning(message, title = "¡Atención!") {
    return showCustomAlert("warning", title, message, false, "De acuerdo");
}

/**
 * Alerta de información rápida
 */
function alertInfo(message, title = "Información") {
    return showCustomAlert("info", title, message, false, "Ok");
}

/**
 * Alerta de confirmación con botones Sí/No
 */
async function alertConfirm(message, title = "¿Continuar?") {
    return await showCustomAlert(
        "info",
        title,
        message,
        true,
        "Confirmar",
        "Cancelar"
    );
}

/**
 * Alerta de eliminación peligrosa
 */
async function alertDelete(message, title = "¿Eliminar?") {
    return await showCustomAlert(
        "error",
        title,
        message,
        true,
        "Eliminar",
        "Cancelar"
    );
}

/* ========= MANEJO DE DUPLICADOS Y VALIDACIONES ========= */
async function handleDuplicateError(error, status, context = "create") {
    if (status !== 422) return false;

    // ✅ PRIORIDAD: duplicados enviados desde el backend (assistant, lawyers, etc.)
    if (error.type === "duplicate" && Array.isArray(error.duplicates)) {
        await showCustomAlert(
            "warning",
            "Datos duplicados",
            error.duplicates.join("\n")
        );
        return true;
    }

    // ⬇️ RESPALDO: validaciones estándar de Laravel
    const errorMessage = error.message || "";
    const errors = error.errors || {};

    if (errors.numero_documento) {
        await showCustomAlert(
            "error",
            "Número de documento duplicado",
            errors.numero_documento.join("\n")
        );
        return true;
    }

    if (errors.correo) {
        await showCustomAlert(
            "error",
            "Correo electrónico duplicado",
            errors.correo.join("\n")
        );
        return true;
    }

    // ⬇️ Último respaldo (muy genérico)
    if (
        errorMessage.toLowerCase().includes("unique") ||
        errorMessage.toLowerCase().includes("duplicado") ||
        errorMessage.toLowerCase().includes("ya existe")
    ) {
        const actionText = context === "create" ? "crear" : "actualizar";
        await showCustomAlert(
            "error",
            "Información duplicada",
            `No se pudo ${actionText} el registro porque existen datos repetidos.`
        );
        return true;
    }

    // ⬇️ Validación genérica
    await showCustomAlert(
        "warning",
        "Error de validación",
        errorMessage || "Los datos ingresados no son válidos."
    );

    return true;
}

async function checkForDuplicates(formData, currentId = null) {
    try {
        const body = {
            numero_documento: formData.get("numero_documento"),
            correo: formData.get("correo"),
            current_id: currentId,
        };

        const csrf = getCsrfToken();
        const response = await fetch("/lawyers/check-duplicates", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": csrf,
                "Content-Type": "application/json",
                Accept: "application/json",
            },
            body: JSON.stringify(body),
        });

        if (response.ok) {
            const result = await response.json();
            if (result.duplicates && result.duplicates.length > 0) {
                const duplicateMessages = result.duplicates.map((duplicate) => {
                    if (duplicate.field === "numero_documento")
                        return `Número de documento ${duplicate.value} ya está registrado`;
                    if (duplicate.field === "correo")
                        return `Correo electrónico ${duplicate.value} ya está registrado`;
                    return `• ${duplicate.field}: ${duplicate.value} ya existe`;
                });
                await showCustomAlert(
                    "warning",
                    "Información Duplicada Detectada",
                    `Se encontraron los siguientes duplicados:\n\n${duplicateMessages.join(
                        "\n"
                    )}\n\nPor favor, modifica estos campos antes de continuar.`
                );
                return true;
            }
        }
    } catch (err) {
        console.log("No se pudo verificar duplicados:", err);
        // no forzamos nada aquí
    }
    return false;
}

/* ========= VALIDACIONES ========= */
function validateForm(formData) {
    const errors = [];

    if (!formData.get("nombre")?.trim())
        errors.push("El nombre es obligatorio");

    if (!formData.get("apellido")?.trim())
        errors.push("El apellido es obligatorio");

    if (!formData.get("tipo_documento")?.trim())
        errors.push("El tipo de documento es obligatorio");

    if (!formData.get("numero_documento")?.trim())
        errors.push("El número de documento es obligatorio");

    if (!formData.get("correo")?.trim())
        errors.push("El correo electrónico es obligatorio");

    if (!formData.get("telefono")?.trim())
        errors.push("El teléfono es obligatorio");

    if (!formData.get("especialidad")?.trim())
        errors.push("La especialidad es obligatoria");

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (formData.get("correo") && !emailRegex.test(formData.get("correo")))
        errors.push("El formato del correo electrónico no es válido");

    return errors;
}

function validateEditForm(formData) {
    // idéntica a validateForm en este caso, pero separada por claridad
    return validateForm(formData);
}

function validateRegisterForm(formData) {
    const errors = [];
    if (!formData.get("correo") || formData.get("correo").trim() === "")
        errors.push("El correo electrónico es obligatorio");
    if (!formData.get("telefono") || formData.get("telefono").trim() === "")
        errors.push("El teléfono es obligatorio");
    if (
        !formData.get("especialidad") ||
        formData.get("especialidad").trim() === ""
    )
        errors.push("La especialidad es obligatoria");

    const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
    if (formData.get("correo") && !emailRegex.test(formData.get("correo")))
        errors.push("El formato del correo electrónico no es válido");

    return errors;
}

/* ========= VALIDACIÓN EN TIEMPO REAL (debounce) ========= */
function setupRealTimeValidation(fieldName, inputElement) {
    let timeoutId;
    inputElement.addEventListener("input", function () {
        clearTimeout(timeoutId);
        timeoutId = setTimeout(async () => {
            const value = this.value.trim();
            if (value.length < 3) {
                inputElement.classList.remove("error", "success");
                hideFieldError(inputElement);
                return;
            }
            try {
                const csrf = getCsrfToken();
                const response = await fetch("/lawyers/check-field", {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": csrf,
                        "Content-Type": "application/json",
                        Accept: "application/json",
                    },
                    body: JSON.stringify({ field: fieldName, value }),
                });
                if (response.ok) {
                    const result = await response.json();
                    if (result.exists) {
                        inputElement.classList.add("error");
                        inputElement.classList.remove("success");
                        showFieldError(
                            inputElement,
                            `Este ${
                                fieldName === "numero_documento" ||
                                fieldName === "numero_documento"
                                    ? "número de documento"
                                    : "correo"
                            } ya está registrado`
                        );
                    } else {
                        inputElement.classList.add("success");
                        inputElement.classList.remove("error");
                        hideFieldError(inputElement);
                    }
                }
            } catch (err) {
                console.log("Error en validación en tiempo real:", err);
            }
        }, 800);
    });
}

function showFieldError(inputElement, message) {
    let errorElement = inputElement.parentNode.querySelector(".field-error");
    if (!errorElement) {
        errorElement = document.createElement("div");
        errorElement.className = "field-error";
        errorElement.style.color = "#e74c3c";
        errorElement.style.fontSize = "12px";
        errorElement.style.marginTop = "4px";
        inputElement.parentNode.appendChild(errorElement);
    }
    errorElement.textContent = message;
}

function hideFieldError(inputElement) {
    const errorElement = inputElement.parentNode.querySelector(".field-error");
    if (errorElement) errorElement.remove();
}

/* ========= LÓGICA DE UI: sidebar, modales, tablas ========= */
function toggleSidebar() {
    if (!sidebar || !overlay) return;
    sidebar.classList.toggle("active");
    overlay.classList.toggle("active");
}
function closeSidebar() {
    if (!sidebar || !overlay) return;
    sidebar.classList.remove("active");
    overlay.classList.remove("active");
}
function openModal() {
    if (!createLawyerModal) return;
    createLawyerModal.classList.add("active");
    document.body.style.overflow = "hidden";
}
function closeModalFunction() {
    if (!createLawyerModal) return;
    createLawyerModal.classList.remove("active");
    document.body.style.overflow = "auto";
    const f = createLawyerModal.querySelector("form");
    if (f) f.reset();
}
function openEditModal(lawyerData) {
    if (!editLawyerModal) return;
    document.getElementById("editNombre").value = lawyerData.nombre || "";
    document.getElementById("editApellido").value = lawyerData.apellido || "";
    document.getElementById("editTipoDocumento").value =
        lawyerData.tipo_documento || "";
    document.getElementById("editNumeroDocumento").value =
        lawyerData.numero_documento || "";
    document.getElementById("editCorreo").value = lawyerData.correo || "";
    document.getElementById("editTelefono").value = lawyerData.telefono || "";
    document.getElementById("editEspecialidad").value =
        lawyerData.especialidad || "";

    editLawyerForm.action = "/lawyers/" + lawyerData.id;
    editLawyerModal.classList.add("active");
    document.body.style.overflow = "hidden";
}
function closeEditModal() {
    if (!editLawyerModal) return;
    editLawyerModal.classList.remove("active");
    document.body.style.overflow = "auto";
    editLawyerForm.reset();
}

function saveCurrentSection() {
    const activeSection = document.querySelector(".section-content.active");
    if (activeSection) {
        localStorage.setItem("activeSection", activeSection.id);
    }
}

function updateRowInTable(id, updatedData) {
    const row = document.querySelector(`tr[data-id='${id}']`);
    if (!row) return;
    // Asumo orden: nombre, apellido, tipo_documento, numero_documento, correo, telefono, especialidad
    row.children[0].textContent = updatedData.nombre || "";
    row.children[1].textContent = updatedData.apellido || "";
    row.children[2].textContent = updatedData.tipo_documento || "";
    row.children[3].textContent = updatedData.numero_documento || "";
    row.children[4].textContent = updatedData.correo || "";
    row.children[5].textContent = updatedData.telefono || "";
    row.children[6].textContent = updatedData.especialidad || "";
}

/* ========= PAGINACIÓN AJAX ========= */
function handleAjaxPagination() {
    // Aplicar a AMBAS secciones: lawyers y assistants
    const sections = [
        document.querySelector("#lawyers-section"),
        document.querySelector("#assistants-section"),
        document.querySelector("#lawyersTableWrapper"),
        document.querySelector("#assistantsTableWrapper"),
        document.querySelector("#procesosTableWrapper"), // 👈 AÑADIDO
    ];

    sections.forEach((section) => {
        if (!section) return;

        section.addEventListener("click", function (e) {
            const link = e.target.closest(".pagination-btn.ajax-page");
            if (!link) return;
            e.preventDefault();

            const url = link.getAttribute("href");
            if (!url || url === "#") return;

            // Buscar el contenedor de tabla (puede ser .table-container o .table-wrapper)
            const container =
                section.querySelector(".table-container") ||
                section.querySelector(".table-wrapper") ||
                section;

            if (container) {
                container.style.opacity = "0.5";
                container.style.pointerEvents = "none";
            }

            fetch(url, {
                method: "GET",
                headers: {
                    "X-Requested-With": "XMLHttpRequest",
                    Accept: "application/json",
                },
            })
                .then(async (resp) => {
                    if (!resp.ok)
                        throw new Error(`HTTP error! status: ${resp.status}`);
                    return resp.json();
                })
                .then((data) => {
                    if (data.success && data.html) {
                        // Reemplazar TODO el contenedor (tabla + paginación)
                        const newContainer = document.createElement("div");
                        newContainer.innerHTML = data.html;
                        const newContent = newContainer.querySelector(
                            ".table-container, .table-wrapper"
                        );

                        if (container && newContent) {
                            container.replaceWith(newContent);
                        }

                        // Actualizar URL sin recargar
                        if (window.history && window.history.pushState)
                            window.history.pushState({}, "", url);

                        // Re-inicializar paginación para el nuevo contenido
                        handleAjaxPagination();
                    } else {
                        throw new Error(
                            data.message || "Formato de respuesta inválido"
                        );
                    }
                })
                .catch((error) => {
                    console.error("Error en paginación AJAX:", error);
                    if (container) {
                        container.innerHTML = `<div class="alert alert-danger" style="padding: 20px; color: red;"><strong>Error:</strong> ${error.message}</div>`;
                        container.style.opacity = "1";
                        container.style.pointerEvents = "auto";
                    }
                })
                .finally(() => {
                    if (container) {
                        container.style.opacity = "1";
                        container.style.pointerEvents = "auto";
                    }
                });
        });
    });
}

/* ========= EVENTOS GLOBALES (inicialización única) ========= */
document.addEventListener("DOMContentLoaded", function () {
    // Inicial UI
    if (hamburgerBtn) hamburgerBtn.addEventListener("click", toggleSidebar);
    if (overlay) overlay.addEventListener("click", closeSidebar);

    if (createBtn) createBtn.addEventListener("click", openModal);
    if (closeModal) closeModal.addEventListener("click", closeModalFunction);
    if (cancelBtn) cancelBtn.addEventListener("click", closeModalFunction);

    if (closeEditModalBtn)
        closeEditModalBtn.addEventListener("click", closeEditModal);
    if (cancelEditBtn) cancelEditBtn.addEventListener("click", closeEditModal);

    // Cerrar con ESC (global)
    document.addEventListener("keydown", (e) => {
        if (e.key === "Escape") {
            closeSidebar();
            closeModalFunction();
            closeEditModal();
            hideCustomAlert();
        }
    });

    // Delegación: eliminar abogado con AJAX (SIN RECARGAR)
    document.addEventListener("submit", async function (e) {
        if (!e.target.classList.contains("delete-lawyer-form")) return;

        e.preventDefault();

        const form = e.target;
        const lawyerName = form.dataset.name || "";

        const confirmed = await showCustomAlert(
            "warning",
            "Confirmar Eliminación",
            `¿Estás seguro de eliminar al abogado ${lawyerName}? Esta acción no se puede deshacer.`,
            true,
            "Eliminar",
            "Cancelar"
        );

        if (!confirmed) return;

        try {
            const response = await fetch(form.action, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": getCsrfToken(),
                    Accept: "application/json",
                },
                body: new FormData(form),
            });

            const result = await response.json();

            if (!result.success) {
                throw new Error(result.message || "Error al eliminar");
            }

            // 🔥 quitar fila de la tabla
            const row = form.closest("tr");
            if (row) row.remove();

            await showCustomAlert(
                "success",
                "Eliminado",
                `El abogado ${lawyerName} fue eliminado exitosamente.`
            );
        } catch (error) {
            console.error(error);
            await showCustomAlert(
                "error",
                "Error",
                "Ocurrió un error al eliminar."
            );
        }
    });

    // Delegación: eliminar proceso con confirmación personalizada
    document.addEventListener("submit", async function (e) {
        if (e.target.classList.contains("delete-proceso-form")) {
            e.preventDefault();
            const confirmed = await showCustomAlert(
                "warning",
                "Confirmar Eliminación",
                "¿Seguro que deseas eliminar este proceso?",
                true,
                "Eliminar",
                "Cancelar"
            );
            if (confirmed) e.target.submit();
        }
    });

    // Delegación: abrir modal de edición
    document.addEventListener("click", function (e) {
        if (e.target.classList.contains("btn-edit")) {
            const row = e.target.closest("tr");
            const lawyerData = {
                id: row.dataset.id,
                nombre: row.children[0].textContent,
                apellido: row.children[1].textContent,
                tipo_documento: row.children[2].textContent,
                numero_documento: row.children[3].textContent,
                correo: row.children[4].textContent,
                telefono: row.children[5].textContent,
                especialidad: row.children[6].textContent,
            };
            openEditModal(lawyerData);
        }
    });

    // Edición: envío del formulario
    if (editLawyerForm) {
        editLawyerForm.addEventListener("submit", async function (e) {
            e.preventDefault();
            const form = e.target;
            const data = new FormData(form);
            const lawyerId = form.action.split("/").pop();

            const validationErrors = validateEditForm(data);
            if (validationErrors.length > 0) {
                await showCustomAlert(
                    "warning",
                    "Campos Incompletos",
                    validationErrors.join("\n")
                );
                return;
            }

            const hasDuplicates = await checkForDuplicates(data, lawyerId);
            if (hasDuplicates) return;

            try {
                const csrf = getCsrfToken();
                const response = await fetch(form.action, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": csrf,
                        Accept: "application/json",
                    },
                    body: (() => {
                        data.append("_method", "PUT");
                        return data;
                    })(),
                });

                if (response.ok) {
                    const updatedLawyer = {
                        nombre: data.get("nombre"),
                        apellido: data.get("apellido"),
                        tipo_documento: data.get("tipo_documento"),
                        numero_documento: data.get("numero_documento"),
                        correo: data.get("correo"),
                        telefono: data.get("telefono"),
                        especialidad: data.get("especialidad"),
                    };

                    updateRowInTable(lawyerId, updatedLawyer);

                    const row = document.querySelector(
                        `tr[data-id="${lawyerId}"]`
                    );
                    if (row) {
                        row.children[0].textContent = updatedLawyer.nombre;
                        row.children[1].textContent = updatedLawyer.apellido;
                        row.children[2].textContent =
                            updatedLawyer.tipo_documento;
                        row.children[3].textContent =
                            updatedLawyer.numero_documento;
                        row.children[4].textContent = updatedLawyer.correo;
                        row.children[5].textContent = updatedLawyer.telefono;
                        row.children[6].textContent =
                            updatedLawyer.especialidad;
                    }

                    await showCustomAlert(
                        "success",
                        "¡Perfecto!",
                        `El abogado ${updatedLawyer.nombre} ${updatedLawyer.apellido} ha sido actualizado exitosamente.`
                    );
                    closeEditModal();
                } else {
                    const error = await response.json();
                    const handled = await handleDuplicateError(
                        error,
                        response.status,
                        "edit"
                    );
                    if (!handled)
                        await showCustomAlert(
                            "error",
                            "Error de Actualización",
                            "Error al actualizar: " +
                                (error.message ||
                                    "Verifica que todos los campos estén correctos.")
                        );
                }
            } catch (err) {
                console.error(err);
                await showCustomAlert(
                    "error",
                    "Error Inesperado",
                    "Ocurrió un error inesperado. Inténtalo de nuevo."
                );
            }
        });
    }

    // Creación: envío del formulario
    const createForm = document
        .getElementById("createLawyerModal")
        ?.querySelector("form");
    if (createForm) {
        createForm.addEventListener("submit", async function (e) {
            e.preventDefault();
            const form = e.target;
            const data = new FormData(form);

            const validationErrors = validateForm(data);
            if (validationErrors.length > 0) {
                await showCustomAlert(
                    "warning",
                    "Campos Incompletos",
                    "Por favor, completa todos los campos obligatorios:\n\n" +
                        validationErrors.join("\n")
                );
                return;
            }

            const hasDuplicates = await checkForDuplicates(data);
            if (hasDuplicates) return;

            try {
                const csrf = getCsrfToken();
                const response = await fetch("/lawyers", {
                    method: "POST",
                    headers: { "X-CSRF-TOKEN": csrf },
                    body: data,
                });

                if (response.ok) {
                    await showCustomAlert(
                        "success",
                        "¡Excelente!",
                        `El abogado ${data.get("nombre")} ${data.get(
                            "apellido"
                        )} ha sido registrado exitosamente.`
                    );
                    form.reset();
                    closeModalFunction();
                } else {
                    const error = await response.json();
                    const handled = await handleDuplicateError(
                        error,
                        response.status,
                        "create"
                    );
                    if (!handled)
                        await showCustomAlert(
                            "error",
                            "Error al Crear",
                            "Error al guardar: " +
                                (error.message || "Verifica los campos.")
                        );
                }
            } catch (err) {
                console.error(err);
                await showCustomAlert(
                    "error",
                    "Error de Conexión",
                    "No se pudo crear el abogado. Verifica tu conexión e inténtalo de nuevo."
                );
            }
        });
    }

    // Inicializar validaciones en tiempo real (si existen inputs)
    const createNumeroDocumento = document.getElementById("numero_documento");
    const createCorreo = document.getElementById("correo");
    if (createNumeroDocumento)
        setupRealTimeValidation("numero_documento", createNumeroDocumento);
    if (createCorreo) setupRealTimeValidation("correo", createCorreo);

    // Búsqueda en tiempo real
    const searchInput = document.getElementById("searchInput");
    if (searchInput) {
        searchInput.addEventListener("input", function () {
            clearTimeout(searchTimeout);
            const searchTerm = this.value.trim();
            searchTimeout = setTimeout(() => performSearch(searchTerm), 300);
        });
    }

    // Navegación entre secciones
    const navButtons = document.querySelectorAll(".nav-btn");
    const sections = document.querySelectorAll(".section-content");
    navButtons.forEach((button) =>
        button.addEventListener("click", function (e) {
            e.preventDefault();
            const sectionId = this.getAttribute("data-section");
            navButtons.forEach((btn) => btn.classList.remove("active"));
            sections.forEach((section) => section.classList.remove("active"));
            this.classList.add("active");
            const targetSection = document.getElementById(
                sectionId + "-section"
            );
            if (targetSection) targetSection.classList.add("active");
        })
    );

    // Inicializar paginación AJAX
    handleAjaxPagination();

    console.log("Sistema de alertas y validaciones inicializado correctamente");
});

// ===== FUNCIONALIDAD DE SUBIDA DE IMAGEN DE PERFIL =====
function setupImageUpload() {
    const fileInput = document.getElementById("fileInput");
    const profileImage = document.getElementById("profileImage");
    const loadingIndicator = document.getElementById("loadingIndicator");

    if (!fileInput || !profileImage) {
        console.warn("Elementos para subida de imagen no encontrados.");
        return;
    }

    profileImage.dataset.originalSrc = profileImage.src;

    fileInput.addEventListener("change", async function (e) {
        const file = e.target.files[0];
        if (!file) return;

        // Validar tipo de archivo
        const allowedTypes = ["image/jpeg", "image/jpg", "image/png"];
        if (!allowedTypes.includes(file.type)) {
            await showCustomAlert(
                "error",
                "Archivo no válido",
                "Solo se permiten archivos JPG, JPEG y PNG."
            );
            fileInput.value = "";
            return;
        }

        // Validar tamaño (2MB máximo)
        const maxSize = 2 * 1024 * 1024;
        if (file.size > maxSize) {
            await showCustomAlert(
                "error",
                "Archivo muy grande",
                "El archivo debe ser menor a 2MB."
            );
            fileInput.value = "";
            return;
        }

        // Mostrar preview inmediato
        const reader = new FileReader();
        reader.onload = function (e) {
            profileImage.src = e.target.result;
        };
        reader.readAsDataURL(file);

        // Mostrar indicador de carga
        if (loadingIndicator) {
            loadingIndicator.style.display = "block";
        }

        // Obtener CSRF token
        const csrfToken = getCsrfToken();
        if (!csrfToken) {
            await showCustomAlert(
                "error",
                "Error de seguridad",
                "Token CSRF no encontrado."
            );
            profileImage.src = profileImage.dataset.originalSrc;
            if (loadingIndicator) loadingIndicator.style.display = "none";
            return;
        }

        // Crear FormData
        const formData = new FormData();
        formData.append("profile_photo", file);

        try {
            const response = await fetch("/perfil/foto", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": csrfToken,
                    Accept: "application/json",
                    // NO pongas Content-Type aquí cuando usas FormData
                },
                body: formData,
            });

            const data = await response.json();

            if (response.ok && data.success) {
                profileImage.src = data.url + "?t=" + new Date().getTime();
                profileImage.dataset.originalSrc = data.url;
                await showCustomAlert(
                    "success",
                    "¡Perfecto!",
                    "Imagen actualizada correctamente."
                );
            } else {
                profileImage.src = profileImage.dataset.originalSrc;
                await showCustomAlert(
                    "error",
                    "Error",
                    data.message || "No se pudo actualizar la imagen."
                );
            }
        } catch (error) {
            profileImage.src = profileImage.dataset.originalSrc;
            console.error("Error al subir imagen:", error);
            await showCustomAlert(
                "error",
                "Error de conexión",
                "No se pudo conectar con el servidor."
            );
        } finally {
            if (loadingIndicator) {
                loadingIndicator.style.display = "none";
            }
            fileInput.value = "";
        }
    });
}

// Inicializar subida de imagen
setupImageUpload();

/* =============================
   =    DASHBOARD SLIDE TOGGLE
   ============================= */

document.addEventListener("DOMContentLoaded", function () {
    const lawyersCard = document.getElementById("lawyersStatCard");
    const assistantsCard = document.getElementById("assistantsStatCard");
    const casesCard = document.getElementById("casesStatCard");

    const lawyersWrapper = document.getElementById("lawyersTableWrapper");
    const assistantsWrapper = document.getElementById("assistantsTableWrapper");
    const procesosWrapper = document.getElementById("procesosTableWrapper");

    function isVisible(element) {
        return window.getComputedStyle(element).display !== "none";
    }

    function slideToggle(element) {
        if (!isVisible(element)) {
            element.style.display = "block";
            element.style.overflow = "hidden";
            element.style.maxHeight = "0px";
            element.style.transition = "max-height 0.4s ease";

            requestAnimationFrame(() => {
                element.style.maxHeight = element.scrollHeight + "px";
            });
        } else {
            element.style.maxHeight = "0px";
            setTimeout(() => {
                element.style.display = "none";
            }, 400);
        }
    }

    function closeOthers(current) {
        [lawyersWrapper, assistantsWrapper, procesosWrapper].forEach((el) => {
            if (el !== current && isVisible(el)) {
                slideToggle(el);
            }
        });
    }

    lawyersCard.addEventListener("click", () => {
        closeOthers(lawyersWrapper);
        slideToggle(lawyersWrapper);
    });

    assistantsCard.addEventListener("click", () => {
        closeOthers(assistantsWrapper);
        slideToggle(assistantsWrapper);
    });

    casesCard.addEventListener("click", () => {
        closeOthers(procesosWrapper);
        slideToggle(procesosWrapper);
    });
});

/* =============================    
     REABRIR PROCESO MODAL  
    ============================= */
let procesoAReabrir = null;

document.addEventListener("click", function (e) {
    if (e.target.classList.contains("reopen-process-btn")) {
        procesoAReabrir = e.target.dataset.id;

        document.getElementById("reabrirOverlay").classList.remove("hidden");
        document.getElementById("reabrirModal").classList.remove("hidden");
    }
});

// Cancelar
document.getElementById("cancelReabrir").addEventListener("click", () => {
    cerrarModalReabrir();
});

// Confirmar
document.getElementById("confirmReabrir").addEventListener("click", () => {
    fetch(`/procesos/${procesoAReabrir}/reabrir`, {
        method: "POST",
        headers: {
            "X-CSRF-TOKEN": document.querySelector('meta[name="csrf-token"]')
                .content,
            Accept: "application/json",
        },
    })
        .then((res) => res.json())
        .then((data) => {
            if (data.success) {
                location.reload();
            } else {
                alert("Error al reabrir el proceso");
            }
        })
        .catch(() => alert("Error de servidor"));
});

function cerrarModalReabrir() {
    document.getElementById("reabrirOverlay").classList.add("hidden");
    document.getElementById("reabrirModal").classList.add("hidden");
    procesoAReabrir = null;
}

document.addEventListener("DOMContentLoaded", function () {
    const lawyerContainer = document.getElementById("lawyerSelectContainer");
    const lawyerList = document.getElementById("lawyerList");
    const addLawyerBtn = document.getElementById("addLawyerBtn");

    const lawyerTemplate = document.querySelector(".lawyer-select");

    function addLawyerSelect() {
        const wrapper = document.createElement("div");
        wrapper.style.display = "flex";
        wrapper.style.gap = "10px";
        wrapper.style.marginBottom = "8px";

        const newSelect = lawyerTemplate.cloneNode(true);
        newSelect.style.display = "block";
        newSelect.name = "lawyers[]";

        const deleteBtn = document.createElement("button");
        deleteBtn.type = "button";
        deleteBtn.textContent = "Eliminar";
        deleteBtn.classList.add("btn-cancel");

        deleteBtn.addEventListener("click", () => wrapper.remove());

        wrapper.appendChild(newSelect);
        wrapper.appendChild(deleteBtn);

        lawyerList.appendChild(wrapper);
    }

    addLawyerBtn.addEventListener("click", addLawyerSelect);
});

const btnOpenAsistente = document.getElementById("btnOpenAsistente");
const modalAsistente = document.getElementById("modalAsistente");
const btnCloseAsistente = document.getElementById("closeAsistente");
const btnCancelAsistente = document.getElementById("cancelAsistente");

function openAssistantModal() {
    modalAsistente.classList.add("active");
    document.body.style.overflow = "hidden";
}

function closeAssistantModal() {
    modalAsistente.classList.remove("active");
    document.body.style.overflow = "auto";
}

if (btnOpenAsistente) {
    btnOpenAsistente.addEventListener("click", openAssistantModal);
}

if (btnCloseAsistente) {
    btnCloseAsistente.addEventListener("click", closeAssistantModal);
}

if (btnCancelAsistente) {
    btnCancelAsistente.addEventListener("click", closeAssistantModal);
}

/* =============================
   =    EDITAR ASISTENTE
   ============================= */
document.addEventListener("click", function (e) {
    // ABRIR MODAL
    if (e.target.classList.contains("btn-edit-assistant")) {
        const btn = e.target;
        const id = btn.dataset.id;

        // Llenar campos
        editAssistantNombre.value = btn.dataset.nombre;
        editAssistantApellido.value = btn.dataset.apellido;
        editAssistantTipoDocumento.value = btn.dataset.tipo_documento;
        editAssistantNumeroDocumento.value = btn.dataset.numero_documento;
        editAssistantCorreo.value = btn.dataset.correo;
        editAssistantTelefono.value = btn.dataset.telefono || "";

        // Action del form
        editAssistantForm.action = `/assistants/${id}`;

        // Limpiar abogados asignados
        const container = document.getElementById("assignedLawyersContainer");
        container.innerHTML = "";

        // Cargar abogados asignados
        const lawyers = JSON.parse(btn.dataset.lawyers || "[]");
        lawyers.forEach((lawyerId) => addLawyerSelect(lawyerId));

        // Mostrar modal
        editAssistantModal.style.display = "flex";
    }

    // CERRAR MODAL
    if (
        e.target.id === "closeEditAssistantModal" ||
        e.target.id === "cancelEditBtn"
    ) {
        editAssistantModal.style.display = "none";
    }

    // AGREGAR SELECT ABOGADO
    if (e.target.id === "addLawyerBtn") {
        addLawyerSelect();
    }

    // ELIMINAR SELECT ABOGADO
    if (e.target.classList.contains("remove-lawyer")) {
        e.target.parentElement.remove();
    }
});

/* ===========================================
   =   FUNCIÓN AGREGAR SELECT DE ABOGADO
   =========================================== */
function addLawyerSelect(selectedId = null) {
    const baseSelect = document.querySelector(".lawyer-select");
    const container = document.getElementById("assignedLawyersContainer");

    const select = baseSelect.cloneNode(true);
    select.style.display = "block";
    select.style.flex = "1";
    select.name = "lawyers[]";

    if (selectedId) {
        select.value = selectedId;
    }

    const wrapper = document.createElement("div");
    wrapper.classList.add("lawyer-wrapper");
    wrapper.style.display = "flex";
    wrapper.style.alignItems = "center";
    wrapper.style.gap = "10px";
    wrapper.style.marginBottom = "10px";

    const removeBtn = document.createElement("button");
    removeBtn.type = "button";
    removeBtn.textContent = "Eliminar";
    removeBtn.classList.add("remove-lawyer", "btn-cancel");

    wrapper.appendChild(select);
    wrapper.appendChild(removeBtn);
    container.appendChild(wrapper);
}

/* ===========================================
   =    ENVÍO AJAX UPDATE (MISMO ESTILO ABOGADO)
   =========================================== */
if (editAssistantForm) {
    editAssistantForm.addEventListener("submit", async function (e) {
        e.preventDefault();

        const form = this;
        const data = new FormData(form);
        const assistantId = form.action.split("/").pop();

        // ✅ VALIDACIÓN
        const validationErrors = validateEditAssistantForm(data);
        if (validationErrors.length > 0) {
            await showCustomAlert(
                "warning",
                "Campos Incompletos",
                validationErrors.join("\n")
            );
            return;
        }

        // ✅ DUPLICADOS
        const hasDuplicates = await checkForAssistantDuplicates(
            data,
            assistantId
        );
        if (hasDuplicates) return;

        // ✅ ENVÍO
        try {
            const response = await fetch(form.action, {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": getCsrfToken(),
                    Accept: "application/json",
                },
                body: data,
            });

            const result = await response.json();

            if (result.success) {
                updateAssistantRowInTable(result.assistant);

                // 🔥 ACTUALIZAR DATASETS DEL BOTÓN EDITAR
                const editBtn = document.querySelector(
                    `.btn-edit-assistant[data-id="${result.assistant.id}"]`
                );

                if (editBtn) {
                    editBtn.dataset.nombre = result.assistant.nombre ?? "";
                    editBtn.dataset.apellido = result.assistant.apellido ?? "";
                    editBtn.dataset.tipo_documento =
                        result.assistant.tipo_documento ?? "";
                    editBtn.dataset.numero_documento =
                        result.assistant.numero_documento ?? "";
                    editBtn.dataset.correo = result.assistant.correo ?? "";
                    editBtn.dataset.telefono = result.assistant.telefono ?? "";

                    // abogados asignados (MUY IMPORTANTE)
                    editBtn.dataset.lawyers = JSON.stringify(
                        (result.assistant.lawyers || []).map((l) => l.id)
                    );
                }

                const nombreCompleto = `${result.assistant.nombre} ${result.assistant.apellido}`;

                await showCustomAlert(
                    "success",
                    "¡Perfecto!",
                    `El asistente ${nombreCompleto} fue actualizado correctamente.`
                );

                editAssistantModal.style.display = "none";
            } else {
                await showCustomAlert(
                    "error",
                    "Error",
                    result.message || "No se pudo actualizar."
                );
            }
        } catch (err) {
            console.error(err);
            await showCustomAlert(
                "error",
                "Error inesperado",
                "Inténtalo nuevamente."
            );
        }
    });

    /* ===========================================
   = VERIFICAR DUPLICADOS ASISTENTE (AJAX)
   =========================================== */
    async function checkForAssistantDuplicates(data, assistantId) {
        try {
            const response = await fetch("/lawyers/check-duplicates", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": getCsrfToken(),
                    Accept: "application/json",
                },
                body: new URLSearchParams({
                    numero_documento: data.get("numero_documento"),
                    correo: data.get("correo"),
                    telefono: data.get("telefono"),
                    current_id: assistantId,
                }),
            });

            const result = await response.json();

            if (result.has_duplicates) {
                const messages = result.duplicates.map(
                    (d) => `• ${d.message.replace(":value", d.value)}`
                );

                await showCustomAlert(
                    "warning",
                    "Datos duplicados",
                    messages.join("\n")
                );

                return true; // 🚫 detener submit
            }

            return false; // ✅ continuar
        } catch (error) {
            console.error(error);
            await showCustomAlert(
                "error",
                "Error",
                "No se pudo verificar duplicados."
            );
            return true;
        }
    }

    /* ========= FUNCIONES ESPECÍFICAS PARA ASISTENTES JURÍDICOS ========= */

    /* ========= VALIDACIONES PARA ASISTENTES ========= */
    function validateAssistantForm(formData) {
        const errors = [];
        if (!formData.get("nombre") || formData.get("nombre").trim() === "")
            errors.push("El nombre es obligatorio");
        if (!formData.get("apellido") || formData.get("apellido").trim() === "")
            errors.push("El apellido es obligatorio");
        if (
            !formData.get("tipo_documento") ||
            formData.get("tipo_documento").trim() === ""
        )
            errors.push("El tipo de documento es obligatorio");
        if (
            !formData.get("numero_documento") ||
            formData.get("numero_documento").trim() === ""
        )
            errors.push("El número de documento es obligatorio");
        if (!formData.get("correo") || formData.get("correo").trim() === "")
            errors.push("El correo electrónico es obligatorio");
        if (!formData.get("telefono") || formData.get("telefono").trim() === "")
            errors.push("El teléfono es obligatorio");

        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (formData.get("correo") && !emailRegex.test(formData.get("correo")))
            errors.push("El formato del correo electrónico no es válido");

        return errors;
    }

    function validateEditAssistantForm(formData) {
        return validateAssistantForm(formData);
    }

    /* ========= VERIFICACIÓN DE DUPLICADOS PARA ASISTENTES ========= */
    async function checkAssistantDuplicates(formData, currentId = null) {
        try {
            const response = await fetch("/assistants/check-duplicates", {
                method: "POST",
                headers: {
                    "X-CSRF-TOKEN": getCsrfToken(),
                    "Content-Type": "application/json",
                    Accept: "application/json",
                },
                body: JSON.stringify({
                    numero_documento: formData.get("numero_documento"),
                    correo: formData.get("correo"),
                    current_id: currentId,
                }),
            });

            if (!response.ok) return false;

            const result = await response.json();

            if (result.duplicates && result.duplicates.length > 0) {
                const list = result.duplicates
                    .map((d) => `• ${d.message}`)
                    .join("\n");

                await showCustomAlert("warning", "Información Duplicada", list);

                return true; // ⛔ detener submit
            }

            return false;
        } catch (e) {
            console.error(e);
            await showCustomAlert(
                "error",
                "Error",
                "No se pudo verificar duplicados."
            );
            return true;
        }
    }

    /* ========= ACTUALIZAR FILA EN TABLA DE ASISTENTES ========= */
    function updateAssistantRowInTable(assistant) {
        const row = document.querySelector(
            `tr[data-assistant-id='${assistant.id}']`
        );
        if (!row) return;

        // Datos básicos
        row.children[0].textContent = assistant.nombre ?? "";
        row.children[1].textContent = assistant.apellido ?? "";
        row.children[2].textContent = assistant.tipo_documento ?? "";
        row.children[3].textContent = assistant.numero_documento ?? "";
        row.children[4].textContent = assistant.correo ?? "";
        row.children[5].textContent = assistant.telefono ?? "N/A";

        // 🔥 Abogados asignados
        const lawyersCell = row.children[6];
        lawyersCell.innerHTML = "";

        if (assistant.lawyers && assistant.lawyers.length > 0) {
            const ul = document.createElement("ul");
            ul.style.margin = "0";
            ul.style.paddingLeft = "20px";

            assistant.lawyers.forEach((lawyer) => {
                const li = document.createElement("li");
                li.textContent = `${lawyer.nombre} ${lawyer.apellido}`;
                ul.appendChild(li);
            });

            lawyersCell.appendChild(ul);
        } else {
            lawyersCell.innerHTML = `<span style="color:#999;">Sin abogados asignados</span>`;
        }
    }

    /* ========= VALIDACIÓN EN TIEMPO REAL PARA ASISTENTES ========= */
    function setupAssistantRealTimeValidation(fieldName, inputElement) {
        let timeoutId;
        inputElement.addEventListener("input", function () {
            clearTimeout(timeoutId);
            timeoutId = setTimeout(async () => {
                const value = this.value.trim();
                if (value.length < 3) {
                    inputElement.classList.remove("error", "success");
                    hideFieldError(inputElement);
                    return;
                }
                try {
                    const csrf = getCsrfToken();
                    const response = await fetch("/assistants/check-field", {
                        method: "POST",
                        headers: {
                            "X-CSRF-TOKEN": csrf,
                            "Content-Type": "application/json",
                            Accept: "application/json",
                        },
                        body: JSON.stringify({ field: fieldName, value }),
                    });
                    if (response.ok) {
                        const result = await response.json();
                        if (result.exists) {
                            inputElement.classList.add("error");
                            inputElement.classList.remove("success");
                            showFieldError(
                                inputElement,
                                `Este ${
                                    fieldName === "numeroDocumento" ||
                                    fieldName === "numero_documento"
                                        ? "número de documento"
                                        : "correo"
                                } ya está registrado`
                            );
                        } else {
                            inputElement.classList.add("success");
                            inputElement.classList.remove("error");
                            hideFieldError(inputElement);
                        }
                    }
                } catch (err) {
                    console.log("Error en validación en tiempo real:", err);
                }
            }, 800);
        });
    }

    /* ========= INICIALIZACIÓN DE EVENTOS PARA ASISTENTES ========= */
    document.addEventListener("DOMContentLoaded", function () {
        // ===== CREAR ASISTENTE =====
        const createAssistantForm = document.querySelector(
            "#modalAsistente form"
        );
        if (createAssistantForm) {
            createAssistantForm.addEventListener("submit", async function (e) {
                e.preventDefault();
                const form = e.target;
                const data = new FormData(form);

                const validationErrors = validateAssistantForm(data);
                if (validationErrors.length > 0) {
                    await showCustomAlert(
                        "warning",
                        "Campos Incompletos",
                        "Por favor, completa todos los campos obligatorios:\n\n" +
                            validationErrors.join("\n")
                    );
                    return;
                }

                const hasDuplicates = await checkAssistantDuplicates(data);
                if (hasDuplicates) return;

                try {
                    const csrf = getCsrfToken();
                    const response = await fetch("/lawyers", {
                        method: "POST",
                        headers: { "X-CSRF-TOKEN": csrf },
                        body: data,
                    });

                    if (response.ok) {
                        await showCustomAlert(
                            "success",
                            "¡Excelente!",
                            `El asistente jurídico ${data.get(
                                "nombre"
                            )} ${data.get(
                                "apellido"
                            )} ha sido registrado exitosamente.`
                        );
                        form.reset();
                        closeAssistantModal();
                    } else {
                        const error = await response.json();
                        const handled = await handleDuplicateError(
                            error,
                            response.status,
                            "create"
                        );
                        if (!handled)
                            await showCustomAlert(
                                "error",
                                "Error al Crear",
                                "Error al guardar: " +
                                    (error.message || "Verifica los campos.")
                            );
                    }
                } catch (err) {
                    console.error(err);
                    await showCustomAlert(
                        "error",
                        "Error de Conexión",
                        "No se pudo crear el asistente. Verifica tu conexión e inténtalo de nuevo."
                    );
                }
            });
        }

        // ===== ELIMINAR ASISTENTE =====
        document.addEventListener("submit", async function (e) {
            if (!e.target.classList.contains("delete-assistant-form")) return;

            e.preventDefault();

            const form = e.target;
            const assistantName = form.dataset.name || "";

            const confirmed = await showCustomAlert(
                "warning",
                "Confirmar Eliminación",
                `¿Estás seguro de eliminar al asistente ${assistantName}? Esta acción no se puede deshacer.`,
                true,
                "Eliminar",
                "Cancelar"
            );

            if (!confirmed) return;

            try {
                const response = await fetch(form.action, {
                    method: "POST",
                    headers: {
                        "X-CSRF-TOKEN": getCsrfToken(),
                        Accept: "application/json",
                    },
                    body: new FormData(form),
                });

                const result = await response.json();

                if (!result.success) {
                    throw new Error(result.message || "Error al eliminar");
                }

                // 🔥 QUITAR FILA SIN RECARGAR
                const row = form.closest("tr");
                if (row) row.remove();

                await showCustomAlert(
                    "success",
                    "Eliminado",
                    `El asistente ${assistantName} fue eliminado exitosamente.`
                );
            } catch (error) {
                console.error(error);
                await showCustomAlert(
                    "error",
                    "Error",
                    "Ocurrió un error al eliminar el asistente."
                );
            }
        });

        // ===== VALIDACIÓN EN TIEMPO REAL =====
        const createNumeroDocumentoAssistant = document.getElementById(
            "numeroDocumento_assistant"
        );
        const createCorreoAssistant =
            document.getElementById("correo_assistant");

        if (createNumeroDocumentoAssistant)
            setupAssistantRealTimeValidation(
                "numeroDocumento",
                createNumeroDocumentoAssistant
            );
        if (createCorreoAssistant)
            setupAssistantRealTimeValidation("correo", createCorreoAssistant);

        const editNumeroDocumentoAssistant = document.getElementById(
            "editAssistantNumeroDocumento"
        );
        const editCorreoAssistant = document.getElementById(
            "editAssistantCorreo"
        );

        if (editNumeroDocumentoAssistant)
            setupAssistantRealTimeValidation(
                "numeroDocumento",
                editNumeroDocumentoAssistant
            );
        if (editCorreoAssistant)
            setupAssistantRealTimeValidation("correo", editCorreoAssistant);

        console.log(
            "Sistema de alertas para asistentes jurídicos inicializado correctamente"
        );
    });

    /* ========= Exponer funciones útiles globalmente (si las necesitas) ========= */
    window.showCustomAlert = showCustomAlert;
    window.hideCustomAlert = hideCustomAlert;
    window.handleDuplicateError = handleDuplicateError;
    window.checkForDuplicates = checkForDuplicates;
    window.performSearch = performSearch;
    window.clearSearch = clearSearch;
}
