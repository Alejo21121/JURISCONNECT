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
