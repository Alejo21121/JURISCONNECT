let deleteConceptId = null;

function showDeleteModal(id, title) {
    deleteConceptId = id;
    document.getElementById("conceptTitle").textContent = title;
    document.getElementById("deleteModal").classList.add("active");
    document.body.style.overflow = "hidden";
}

function closeDeleteModal() {
    document.getElementById("deleteModal").classList.remove("active");
    document.body.style.overflow = "auto";
    deleteConceptId = null;
}

function confirmDelete() {
    if (deleteConceptId) {
        document.getElementById("delete-form-" + deleteConceptId).submit();
    }
}

// Cerrar modal al hacer clic fuera
document.getElementById("deleteModal").addEventListener("click", function (e) {
    if (e.target === this) {
        closeDeleteModal();
    }
});

// Cerrar modal con tecla ESC
document.addEventListener("keydown", function (e) {
    if (e.key === "Escape") {
        closeDeleteModal();
    }
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
