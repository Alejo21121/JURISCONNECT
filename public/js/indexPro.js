let searchTimeout;

// Inicializar eventos de búsqueda
document.addEventListener("DOMContentLoaded", function () {
    const searchInput = document.getElementById("searchInput");
    const searchBtn = document.getElementById("searchBtn");

    if (searchInput) {
        searchInput.addEventListener("input", function () {
            clearTimeout(searchTimeout);
            const searchTerm = this.value.trim();
            searchTimeout = setTimeout(() => {
                performSearch(searchTerm);
            }, 300);
        });

        searchInput.addEventListener("keypress", function (event) {
            if (event.key === "Enter") {
                clearTimeout(searchTimeout);
                performSearch(this.value.trim());
            }
        });
    }

    if (searchBtn) {
        searchBtn.addEventListener("click", function () {
            const searchTerm = searchInput.value.trim();
            performSearch(searchTerm);
        });
    }
});

function performSearch(searchTerm) {
    const params = new URLSearchParams();
    if (searchTerm) {
        params.append("search", searchTerm);
    }
    params.append("ajax", "1");

    fetch(`${window.routes.procesosIndex}?${params.toString()}`, {
        method: "GET",
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
        },
    })
        .then((response) => response.json())
        .then((data) => {
            if (data.success && data.html) {
                document.getElementById("procesosTableContainer").innerHTML =
                    data.html;
                if (data.total !== undefined) {
                    document.getElementById("totalCount").textContent =
                        data.total;
                }
            } else {
                console.error(
                    "Error en búsqueda:",
                    data.message || "Error desconocido",
                );
            }
        })
        .catch((error) => {
            console.error("Error en la petición AJAX:", error);
        });
}

function clearSearch() {
    const searchInput = document.getElementById("searchInput");
    if (searchInput) {
        searchInput.value = "";
        performSearch("");
    }
}

// ========================================
// MODAL DE PROCESO - VERSIÓN MEJORADA
// ========================================
window.openProcessModal = function (id) {
    const modal = document.getElementById("viewProcessModal");
    const body = document.getElementById("processModalBody");

    modal.style.display = "flex";
    body.innerHTML =
        '<p style="text-align:center; padding:2rem; color:#64748b;">⏳ Cargando datos...</p>';

    fetch(`/procesos/${id}`)
        .then((res) => {
            if (!res.ok) throw new Error("Error al cargar datos");
            return res.json();
        })
        .then((data) => {
            console.log("Datos recibidos:", data);

            body.innerHTML = `
<div class="process-details">

    <!-- INFORMACIÓN BÁSICA -->
    <div class="detail-row">
        <span class="label">📋 Radicado</span>
        <span class="value"><strong>${data.numero_radicado}</strong></span>
    </div>

    <div class="detail-row">
        <span class="label">📅 Fecha radicación</span>
        <span class="value">${data.created_at}</span>
    </div>

    <div class="detail-row">
        <span class="label">📅 Fecha vencimiento</span>
        <span class="value">${data.fecha_vencimiento}</span>
    </div>

    <div class="detail-row">
        <span class="label">📊 Estado</span>
        <span class="value">
            <span class="badge estado-${data.estado.toLowerCase().replace(/ /g, "-")}">
                ${getEstadoIcon(data.estado)} ${data.estado}
            </span>
        </span>
    </div>

    <div class="detail-row">
        <span class="label">⚖️ Tipo de proceso</span>
        <span class="value">${data.tipo_proceso}</span>
    </div>

    <div class="detail-row">
        <span class="label">👤 Demandante</span>
        <span class="value">${data.demandante}</span>
    </div>

    <div class="detail-row">
        <span class="label">👥 Demandado</span>
        <span class="value">${data.demandado}</span>
    </div>

    <!-- DESCRIPCIÓN -->
    <div class="detail-box">
        <span class="label">📝 Detalle del caso</span>
        <p style="white-space: pre-wrap; word-wrap: break-word;">${data.descripcion || "Sin descripción"}</p>
    </div>

    <div class="section-divider"></div>

    <!-- INFORMACIÓN DE PAGO -->
    <h4 class="section-title">💰 Información de Pago</h4>

    <div class="detail-row">
        <span class="label">¿Requiere pago?</span>
        <span class="value">
            ${data.requiere_pago == 1
                    ? '<strong style="color:#059669;">✅ Sí</strong>'
                    : '<strong style="color:#6b7280;">❌ No</strong>'
                }
        </span>
    </div>

${data.requiere_pago == 1
                    ? `
            <div class="detail-row">
                <span class="label">Valor estimado</span>
                <span class="value">
                    <strong style="color:#2563eb; font-size:1.125rem;">
                        $ ${Number(data.valor_estimado || 0).toLocaleString("es-CO")}
                    </strong>
                </span>
            </div>

<div class="detail-row">
                <span class="label">Estado del pago</span>
                <span class="value">
                    ${data.porcentaje >= 100
                        ? '<span class="badge pago-realizado"><i class="fas fa-check-circle"></i> Pago completado</span>'
                        : data.porcentaje > 0
                            ? '<span class="badge pago-tramite"><i class="fas fa-spinner"></i> Pago en trámite</span>'
                            : '<span class="badge pago-pendiente"><i class="fas fa-clock"></i> Pago pendiente</span>'
                    }
                    <button 
                        class="badge pago-btn"
                        onclick="irAPago(${data.id})"
                        style="margin-left:8px; cursor:pointer; border:none;"
                    >
                        💳 Ir a pagos
                    </button>
                </span>
            </div>

            ${data.porcentaje >= 100 && data.pago
                        ? `
        <div class="pago-card">
            <h5 style="color:#065f46; font-size:1rem; margin:0 0 1rem 0; font-weight:700; display:flex; align-items:center; gap:0.5rem;">
                <i class="fas fa-money-bill-wave"></i>
                Detalles del Pago Realizado
            </h5>
            <div class="pago-grid">
                <div class="pago-item">
                    <span class="label">Valor Pagado</span>
                    <span class="value">$ ${Number(data.pago.valor_pagado || 0).toLocaleString("es-CO")}</span>
                </div>
                <div class="pago-item">
                    <span class="label">Fecha de Pago</span>
                    <span class="value"><i class="fas fa-calendar-check"></i> ${data.pago.fecha_pago || "N/A"}</span>
                </div>
                <div class="pago-item">
                    <span class="label">Forma de Pago</span>
                    <span class="value">${data.pago.forma_pago || "N/A"}</span>
                </div>
                ${data.pago.observaciones
                            ? `
                            <div class="pago-item" style="grid-column: 1 / -1;">
                                <span class="label">Observaciones</span>
                                <span class="value" style="font-size:0.9rem; font-weight:500; color:#374151;">${data.pago.observaciones}</span>
                            </div>
                        `
                            : ""
                        }
            </div>
        </div>
    `
                        : ""
                    }
        `
                    : `
    <div class="detail-row">
        <span class="label">Estado del pago</span>
        <span class="value">
            <span class="badge pago-no-aplica">— No aplica</span>
            <button 
                class="badge pago-btn"
                onclick="irAPago(${data.id})"
                style="margin-left:8px; cursor:pointer; border:none;"
            >
                💳 Ir a pagos
            </button>
        </span>
    </div>
`
                }

    <div class="section-divider"></div>

    <!-- DOCUMENTOS -->
    <h4 class="section-title">
        <i class="fas fa-paperclip"></i>
        Documentos del proceso
    </h4>
    ${renderDocuments(data.documentos)}

</div>
`;
        })
        .catch((error) => {
            console.error("Error:", error);
            body.innerHTML = `
                    <div style="text-align:center; padding:2rem;">
                        <i class="fas fa-exclamation-triangle" style="font-size:3rem; color:#dc2626; margin-bottom:1rem;"></i>
                        <p style="color:#dc2626; font-weight:600;">❌ Error al cargar los datos</p>
                        <p style="color:#64748b; font-size:0.875rem; margin-top:0.5rem;">${error.message}</p>
                    </div>
                `;
        });
};

// Cerrar modal
window.closeProcessModal = function () {
    document.getElementById("viewProcessModal").style.display = "none";
};

//  CERRAR MODAL AL HACER CLIC FUERA
document.addEventListener("click", function (event) {
    const modal = document.getElementById("viewProcessModal");

    // Si el modal está visible
    if (modal && modal.style.display === "flex") {
        // Si el clic fue en el fondo negro (no en el contenido)
        if (event.target === modal) {
            closeProcessModal();
        }
    }
});

// Cerrar modal con ESC
document.addEventListener("keydown", function (event) {
    const modal = document.getElementById("viewProcessModal");
    if (event.key === "Escape" && modal.style.display === "flex") {
        closeProcessModal();
    }
});

// Helper para iconos de estado
function getEstadoIcon(estado) {
    const icons = {
        Pendiente: "⏳",
        Radicado: "📋",
        Archivado: "📁",
        Finalizado: "✅",
        "En proceso": "🔄",
        Reabierto: "🔓",
        Admisión: "📝",
        Traslado: "📤",
        Audiencia: "👨‍⚖️",
        "Fallo favorable": "✅",
        "Fallo desfavorable": "❌",
        Apelación: "⚖️",
        Ejecutoria: "🔨",
        "Pago en trámite": "💰",
        Conciliado: "🤝",
    };
    return icons[estado] || "📄";
}

// Renderizar documentos
function renderDocuments(documentos) {
    if (!documentos || documentos.length === 0) {
        return `<p class="text-muted">📄 No hay documentos asociados a este proceso.</p>`;
    }

    return `
            <ul class="documents-list">
                ${documentos
            .map(
                (doc) => `
                                            <li>
                                                <a href="/storage/${doc.ruta}" target="_blank">
                                                    <i class="fas fa-file-pdf"></i>
                                                    ${doc.nombre}
                                                </a>
                                            </li>
                                        `,
            )
            .join("")}
            </ul>
        `;
}

// Cerrar modal
function closeProcessModal() {
    document.getElementById("viewProcessModal").style.display = "none";
}

// Cerrar modal con ESC
document.addEventListener("keydown", function (event) {
    const modal = document.getElementById("viewProcessModal");
    if (event.key === "Escape" && modal.style.display === "flex") {
        closeProcessModal();
    }
});

// Confirmar eliminación
function confirmDelete(id, nombre) {
    Swal.fire({
        title: "Confirmar Eliminación",
        html: `¿Estás seguro de eliminar el proceso de <b>${nombre}</b>?<br>Esta acción no se puede deshacer.`,
        icon: "warning",
        showCancelButton: true,
        confirmButtonText: "Eliminar",
        cancelButtonText: "Cancelar",
        reverseButtons: true,
        confirmButtonColor: "#dc2626",
        cancelButtonColor: "#6b7280",
    }).then((result) => {
        if (result.isConfirmed) {
            document.getElementById(`delete-form-${id}`).submit();
        }
    });
}

// ========================================
// PAGINACIÓN AJAX (mantiene la búsqueda)
// ========================================
document.addEventListener("click", function (e) {
    const link = e.target.closest(".pagination a");
    if (!link) return;

    e.preventDefault();

    const url = link.getAttribute("href");

    // Agregar el parámetro ajax
    const separator = url.includes("?") ? "&" : "?";
    const finalUrl = `${url}${separator}ajax=1`;

    fetch(finalUrl, {
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
        },
    })
        .then((res) => res.json())
        .then((data) => {
            if (data.success && data.html) {
                document.getElementById("procesosTableContainer").innerHTML =
                    data.html;

                if (data.total !== undefined) {
                    document.getElementById("totalCount").textContent =
                        data.total;
                }
            }
        })
        .catch((err) => console.error("Error paginación AJAX:", err));
});

function irAPago(procesoId) {
    window.location.href = `/pagos?proceso=${procesoId}`;
}

// ── Estado global ──
const state = {
    search: new URLSearchParams(window.location.search).get('search') || '',
    sort: new URLSearchParams(window.location.search).get('sort') || 'id',
    dir: new URLSearchParams(window.location.search).get('dir') || 'asc',
    page: new URLSearchParams(window.location.search).get('page') || 1,
};

// ── Cargar tabla vía AJAX ──
function cargarTabla(pushHistory = true) {
    const container = document.getElementById('procesosTableContainer');
    container.style.opacity = '0.5';
    container.style.pointerEvents = 'none';

    const params = new URLSearchParams({
        search: state.search,
        sort: state.sort,
        dir: state.dir,
        page: state.page,
        ajax: 1,
    });

    fetch(`${window.routes.procesosIndex}?${params}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                container.innerHTML = data.html;
                container.style.opacity = '1';
                container.style.pointerEvents = '';

                // Actualizar contador total
                const totalEl = document.getElementById('totalCount');
                if (totalEl) totalEl.textContent = data.total;

                // Actualizar URL sin recargar
                if (pushHistory) {
                    const url = new URL(window.location.href);
                    url.searchParams.set('search', state.search);
                    url.searchParams.set('sort', state.sort);
                    url.searchParams.set('dir', state.dir);
                    url.searchParams.set('page', state.page);
                    window.history.pushState(state, '', url);
                }

                // Reasignar eventos después de reemplazar el HTML
                bindTableEvents();
            }
        })
        .catch(() => {
            container.style.opacity = '1';
            container.style.pointerEvents = '';
        });
}

// ── Clicks en th (sort) y paginación — delegación de eventos ──
function bindTableEvents() {
    // Ordenamiento — interceptar links de los th
    document.querySelectorAll('#procesosTableContainer .th-link').forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const url = new URL(this.href);
            state.sort = url.searchParams.get('sort');
            state.dir = url.searchParams.get('dir');
            state.page = 1;
            cargarTabla();
        });
    });

    // Paginación — interceptar links de página
    document.querySelectorAll('#procesosTableContainer .pagination-btn[href]').forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const url = new URL(this.href);
            state.page = url.searchParams.get('page') || 1;
            cargarTabla();
        });
    });

    // Re-bind modales de eliminar (se pierden al reemplazar HTML)
    document.querySelectorAll('.form-delete-proceso').forEach(form => {
        form.addEventListener('submit', function (e) {
            e.preventDefault();
            const nombre = form.dataset.nombre || 'este proceso';
            showDeleteConfirm(nombre, () => form.submit());
        });
    });
}

// ── Inicializar al cargar ──
document.addEventListener('DOMContentLoaded', () => {
    // Setear valor del buscador si viene en la URL
    const inputSearch = document.getElementById('searchInput');
    if (inputSearch && state.search) inputSearch.value = state.search;

    bindTableEvents();
});

// ── Botón atrás/adelante del navegador ──
window.addEventListener('popstate', (e) => {
    if (e.state) {
        Object.assign(state, e.state);
        cargarTabla(false);
    }
});