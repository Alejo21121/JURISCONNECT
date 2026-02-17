let paginaAntesDeBuscar = null;

function closeAlert(alertId) {
    document.getElementById(alertId).classList.add("hidden");
}

// Ejemplo para mostrar alerta de éxito
function showSuccessAlert() {
    document.getElementById("success-alert").classList.remove("hidden");
}

// ===== FUNCIONALIDAD DE BÚSQUEDA AJAX =====
let searchTimeout;

function performSearch(searchTerm) {
    const currentUrl = new URL(window.location);

    // Guardar página actual SOLO la primera vez que se empieza a buscar
    if (searchTerm && !paginaAntesDeBuscar) {
        paginaAntesDeBuscar = currentUrl.searchParams.get("page") || "1";
    }

    const params = new URLSearchParams();

    if (searchTerm) {
        params.set("search", searchTerm);
        params.set("page", 1);
    } else {
        if (paginaAntesDeBuscar) {
            params.set("page", paginaAntesDeBuscar);
        }
    }

    fetch(`${window.location.pathname}?${params.toString()}`, {
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
        },
    })
        .then((res) => res.json())
        .then((data) => {
            if (data.success) {
                document.getElementById("processContainer").innerHTML =
                    data.html;

                // Actualizar URL real
                const newUrl = new URL(window.location);

                if (searchTerm) {
                    newUrl.searchParams.set("search", searchTerm);
                    newUrl.searchParams.set("page", 1);
                } else {
                    newUrl.searchParams.delete("search");

                    if (paginaAntesDeBuscar) {
                        newUrl.searchParams.set("page", paginaAntesDeBuscar);
                        paginaAntesDeBuscar = null;
                    }
                }

                window.history.replaceState({}, "", newUrl);
            }
        })
        .catch((err) => console.error("Error en búsqueda:", err));
}

// ===== ABRIR Y CERRAR MODAL DE PROCESO =====
function openProcessModal(id) {
    document.getElementById("viewProcessModal").style.display = "flex";
    const body = document.getElementById("processModalBody");
    body.innerHTML = "<p>Cargando datos...</p>";

    fetch(`/concepto_juridicos/${id}`)
        .then((res) => {
            if (!res.ok) throw new Error("No se pudo obtener el concepto");
            return res.json();
        })
        .then((data) => {
            // Ajusta las propiedades según tu modelo (titulo, descripcion, proceso, abogado...)
            const html = `
                <p><strong>Título:</strong> ${data.titulo ?? "—"}</p>
                <p><strong>Descripción:</strong> ${data.descripcion ?? data.concepto ?? "—"}</p>
                <p><strong>Proceso:</strong> ${data.proceso?.numero_radicado ?? "—"}</p>
                <p><strong>Abogado:</strong> ${data.abogado?.name ?? "—"}</p>
            `;
            body.innerHTML = html;
        })
        .catch(() => {
            body.innerHTML = "<p>Error al cargar los datos.</p>";
        });
}

function closeProcessModal() {
    document.getElementById("viewProcessModal").style.display = "none";
}

//  Cerrar modal con la tecla ESC
document.addEventListener("keydown", function (event) {
    const modal = document.getElementById("viewProcessModal");
    if (event.key === "Escape" && modal.style.display === "flex") {
        closeProcessModal();
    }
});

function confirmDelete(id, nombre) {
    Swal.fire({
        title: "Confirmar Eliminación",
        html: `o de <b>${nombre}</b>?<br>Esta acción no se puede deshacer.`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Eliminar",
        cancelButtonText: "Cancelar",
        reverseButtons: true,
        customClass: {
            popup: "custom-popup",
            title: "custom-title",
            htmlContainer: "custom-text",
            confirmButton: "custom-confirm",
            cancelButton: "custom-cancel",
            icon: "custom-icon",
        },
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(`delete-form-${id}`).submit();
        }
    });
}

document.addEventListener("click", function (e) {
    const link = e.target.closest(".pagination a");

    if (!link) return;

    e.preventDefault();

    const url = new URL(link.href);

    fetch(url.toString(), {
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
        },
    })
        .then((response) => {
            if (!response.ok) throw new Error("Respuesta no válida");
            return response.json();
        })
        .then((data) => {
            if (data.success) {
                document.getElementById("processContainer").innerHTML =
                    data.html;

                url.searchParams.delete("ajax");
                window.history.replaceState({}, "", url);
            }
        })
        .catch((error) => console.error("Error:", error));
});

document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchInput");

    searchInput.addEventListener("input", function () {
        clearTimeout(searchTimeout);

        searchTimeout = setTimeout(() => {
            performSearch(this.value.trim());
        }, 400);
    });
});

const notificationBtn = document.getElementById("notificationBtn");
const notificationDropdown = document.getElementById("notificationDropdown");

// Toggle dropdown
notificationBtn.addEventListener("click", function (e) {
    e.stopPropagation();
    notificationDropdown.classList.toggle("show");
});

// Cerrar al hacer clic fuera
document.addEventListener("click", function (e) {
    if (
        !notificationBtn.contains(e.target) &&
        !notificationDropdown.contains(e.target)
    ) {
        notificationDropdown.classList.remove("show");
    }
});

// Cerrar al hacer clic en una notificación
document.querySelectorAll(".notification-item").forEach((item) => {
    item.addEventListener("click", function () {
        notificationDropdown.classList.remove("show");
    });
});

