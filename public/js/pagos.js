// Variables globales
let procesos = window.procesosData || [];
let filtroActual = "todos";
let procesoSeleccionado = null;

// Token CSRF para peticiones AJAX
const csrfToken = document.querySelector('meta[name="csrf-token"]')?.content;

/**
 * Formatear peso colombiano
 */
function formatearPeso(valor) {
    if (!valor) return "$0";
    return new Intl.NumberFormat("es-CO", {
        style: "currency",
        currency: "COP",
        minimumFractionDigits: 0,
    }).format(valor);
}

/**
 * Formatear fecha
 */
function formatearFecha(fecha) {
    if (!fecha) return "";
    return new Date(fecha).toLocaleDateString("es-CO");
}

/**
 * Actualizar estadísticas del dashboard
 */
function actualizarEstadisticas() {
    const pendientes = procesos.filter(
        (p) => p.requiere_pago && !p.pago_realizado
    ).length;
    const realizados = procesos.filter((p) => p.pago_realizado).length;
    const totalPagado = procesos
        .filter((p) => p.pago_realizado)
        .reduce((sum, p) => sum + (parseFloat(p.valor_sentencia) || 0), 0);

    document.getElementById("totalProcesos").textContent = procesos.length;
    document.getElementById("pagosPendientes").textContent = pendientes;
    document.getElementById("pagosRealizados").textContent = realizados;
    document.getElementById("totalPagado").textContent =
        formatearPeso(totalPagado);
}

/**
 * Renderizar lista de procesos
 */
function renderizarProcesos() {
    const lista = document.getElementById("procesosList");
    const emptyState = document.getElementById("emptyState");

    let procesosFiltrados = procesos;

    if (filtroActual === "pendientes") {
        procesosFiltrados = procesos.filter(
            (p) => p.requiere_pago && !p.pago_realizado
        );
    } else if (filtroActual === "pagados") {
        procesosFiltrados = procesos.filter((p) => p.pago_realizado);
    }

    if (procesosFiltrados.length === 0) {
        lista.style.display = "none";
        emptyState.style.display = "block";
        return;
    }

    lista.style.display = "grid";
    emptyState.style.display = "none";

    lista.innerHTML = procesosFiltrados
        .map(
            (proceso) => `
        <div class="proceso-card">
            <div class="proceso-header">
                <div class="proceso-info">
                    <h3>${proceso.nombre}</h3>
                    <div class="proceso-details">
                        <div class="detail-item">
                            <i class="fas fa-file-alt"></i>
                            <span><strong>Radicado:</strong> ${
                                proceso.radicado
                            }</span>
                        </div>
                        <div class="detail-item">
                            <span><strong>Demandante:</strong> ${
                                proceso.demandante
                            }</span>
                        </div>
                    </div>
                </div>
                <div>
                    ${
                        proceso.requiere_pago
                            ? proceso.pago_realizado
                                ? `<span class="status-badge pagado">
                                <i class="fas fa-check-circle"></i>
                                Pago Realizado
                               </span>`
                                : `<span class="status-badge pendiente">
                                <i class="fas fa-clock"></i>
                                Pago Pendiente
                               </span>`
                            : `<span class="status-badge no-requiere">No requiere pago</span>`
                    }
                </div>
            </div>
            
            ${
                proceso.requiere_pago
                    ? `
                <div class="pago-section">
                    ${
                        proceso.pago_realizado
                            ? `
                        <div class="pago-realizado">
                            <div class="pago-item destacado">
                                <div class="label">Valor Pagado</div>
                                <div class="value">${formatearPeso(
                                    proceso.valor_sentencia
                                )}</div>
                            </div>
                            <div class="pago-item">
                                <div class="label">Forma de Pago</div>
                                <div class="value">${proceso.forma_pago}</div>
                            </div>
                            <div class="pago-item">
                                <div class="label">Fecha de Pago</div>
                                <div class="value">
                                    <i class="fas fa-calendar"></i>
                                    ${formatearFecha(proceso.fecha_pago)}
                                </div>
                            </div>
                            ${
                                proceso.observaciones
                                    ? `
                                <div class="pago-item" style="grid-column: 1 / -1;">
                                    <div class="label">Observaciones</div>
                                    <div class="value">${proceso.observaciones}</div>
                                </div>
                            `
                                    : ""
                            }
                        </div>
                    `
                            : `
                        <div class="pago-pendiente">
                            <div class="pago-item destacado">
                                <div class="label">Valor Estimado</div>
                                <div class="value">${formatearPeso(
                                    proceso.valor_estimado
                                )}</div>
                            </div>
                            <button class="btn-registrar" onclick="abrirModal(${
                                proceso.id
                            })">
                                <i class="fas fa-dollar-sign"></i>
                                Registrar Pago
                            </button>
                        </div>
                    `
                    }
                </div>
            `
                    : ""
            }
        </div>
    `
        )
        .join("");
}

/**
 * Abrir modal de registro de pago
 */
function abrirModal(id) {
    procesoSeleccionado = procesos.find((p) => p.id === id);

    if (!procesoSeleccionado) {
        alert("Error: Proceso no encontrado");
        return;
    }

    document.getElementById("procesoInfoModal").innerHTML = `
        <h3>${procesoSeleccionado.nombre}</h3>
        <p><strong>Radicado:</strong> ${procesoSeleccionado.radicado}</p>
        <p><strong>Demandante:</strong> ${procesoSeleccionado.demandante}</p>
    `;

    document.getElementById("valorSentencia").value =
        procesoSeleccionado.valor_estimado || "";
    document.getElementById("formaPago").value = "Transferencia";
    document.getElementById("fechaPago").value = new Date()
        .toISOString()
        .split("T")[0];
    document.getElementById("observaciones").value = "";

    document.getElementById("modalPago").classList.add("show");
}

/**
 * Cerrar modal
 */
function cerrarModal() {
    document.getElementById("modalPago").classList.remove("show");
    procesoSeleccionado = null;
}

/**
 * Registrar pago (enviar a Laravel)
 */
async function registrarPago() {
    const valor = document.getElementById("valorSentencia").value;
    const forma = document.getElementById("formaPago").value;
    const fecha = document.getElementById("fechaPago").value;
    const obs = document.getElementById("observaciones").value;
    const archivo = document.getElementById("comprobantePago").files[0];

    if (!valor || !fecha) {
        Swal.fire("Error", "Completa los campos obligatorios", "warning");
        return;
    }

    if (!archivo) {
        Swal.fire("Error", "Debes subir el comprobante de pago", "warning");
        return;
    }

    const formData = new FormData();
    formData.append("proceso_id", procesoSeleccionado.id);
    formData.append("valor_pagado", valor);
    formData.append("forma_pago", forma);
    formData.append("fecha_pago", fecha);
    formData.append("observaciones", obs);
    formData.append("comprobante", archivo);

    try {
        const response = await fetch("/pagos", {
            method: "POST",
            headers: {
                "X-CSRF-TOKEN": csrfToken,
                Accept: "application/json",
            },
            body: formData,
        });

        const data = await response.json();

        if (data.success) {
            Swal.fire("Éxito", data.message, "success");

            procesos = procesos.map((p) =>
                p.id === procesoSeleccionado.id
                    ? {
                          ...p,
                          pago_realizado: true,
                          valor_sentencia: valor,
                          forma_pago: forma,
                          fecha_pago: fecha,
                          observaciones: obs,
                      }
                    : p
            );

            cerrarModal();
            actualizarEstadisticas();
            renderizarProcesos();
        } else {
            Swal.fire("Error", "No se pudo registrar el pago", "error");
        }
    } catch (error) {
        console.error(error);
        Swal.fire("Error", "Error de conexión", "error");
    }
}

/**
 * Inicialización
 */
document.addEventListener("DOMContentLoaded", function () {
    // Event listeners para filtros
    document.querySelectorAll(".filter-btn").forEach((btn) => {
        btn.addEventListener("click", function () {
            document
                .querySelectorAll(".filter-btn")
                .forEach((b) => b.classList.remove("active"));
            this.classList.add("active");
            filtroActual = this.dataset.filtro;
            renderizarProcesos();
        });
    });

    // Cerrar modal al hacer clic fuera
    document
        .getElementById("modalPago")
        .addEventListener("click", function (e) {
            if (e.target === this) {
                cerrarModal();
            }
        });

    // Renderizar inicial
    actualizarEstadisticas();
    renderizarProcesos();
});
