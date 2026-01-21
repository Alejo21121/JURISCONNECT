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
    const params = new URLSearchParams();
    if (searchTerm) params.append("search", searchTerm);
    params.append("ajax", "1");

    // Usar la ruta actual (o reemplaza por route('procesos.index'))
    fetch(`${window.location.pathname}?${params.toString()}`, {
        method: "GET",
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
        },
    })
        .then((res) => res.json())
        .then((data) => {
            if (data.success && data.html) {
                // Reemplaza el grid de tarjetas con el HTML devuelto
                const grid = document.querySelector(".process-grid");
                if (grid) {
                    grid.innerHTML = data.html;
                }

                // Si el backend devuelve un total, actualiza el contador si existe
                if (data.total !== undefined) {
                    const totalEl = document.getElementById("totalCount");
                    if (totalEl) totalEl.textContent = data.total;
                }

                // Actualizar URL sin recargar
                const newUrl = new URL(window.location);
                if (searchTerm) newUrl.searchParams.set("search", searchTerm);
                else newUrl.searchParams.delete("search");
                newUrl.searchParams.delete("page");
                window.history.replaceState({}, "", newUrl.toString());
            } else {
                console.error("Respuesta inválida de búsqueda", data);
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
        html: `¿Estás seguro de eliminar el proceso de <b>${nombre}</b>?<br>Esta acción no se puede deshacer.`,
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