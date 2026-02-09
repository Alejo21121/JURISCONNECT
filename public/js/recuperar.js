/* ---------- Funciones de manejo de alertas ---------- */
// Éxito
function showAlert() {
    const overlay = document.getElementById("alertOverlay");
    if (overlay) overlay.style.display = "flex";
}

function closeAlert() {
    const overlay = document.getElementById("alertOverlay");
    if (overlay) overlay.style.display = "none";
}

// Error / Throttle
function showErrorAlert(message) {
    const overlay = document.getElementById("alertErrorOverlay");
    const msgEl = document.getElementById("alertErrorMessage");
    if (message && msgEl) msgEl.textContent = message;
    if (overlay) overlay.style.display = "flex";
}

function closeErrorAlert() {
    const overlay = document.getElementById("alertErrorOverlay");
    if (overlay) overlay.style.display = "none";
}

// Email No Registrado
function showNotRegisteredAlert() {
    const overlay = document.getElementById("alertNotRegisteredOverlay");
    if (overlay) overlay.style.display = "flex";
}

function closeNotRegisteredAlert() {
    const overlay = document.getElementById("alertNotRegisteredOverlay");
    if (overlay) overlay.style.display = "none";
}

// Cierre al click fuera o Escape (aplica para todas)
document.addEventListener("click", function (e) {
    const overlay = document.getElementById("alertOverlay");
    const overlayError = document.getElementById("alertErrorOverlay");
    const overlayNotRegistered = document.getElementById(
        "alertNotRegisteredOverlay",
    );

    if (overlay && e.target === overlay) closeAlert();
    if (overlayError && e.target === overlayError) closeErrorAlert();
    if (overlayNotRegistered && e.target === overlayNotRegistered)
        closeNotRegisteredAlert();
});

document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
        closeAlert();
        closeErrorAlert();
        closeNotRegisteredAlert();
    }
});
