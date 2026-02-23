// ==========================================
// BUSCADOR REAL AJAX (consulta al servidor)
// ==========================================
const searchInputAjax = document.getElementById("searchInput");
if (searchInputAjax) {
    let typingTimer;

    searchInputAjax.addEventListener("input", function () {
        clearTimeout(typingTimer);

        typingTimer = setTimeout(() => {
            const search = this.value;

            fetch(
                `/dashboard?search=${encodeURIComponent(
                    search
                )}&assistantsPage=1`,
                {
                    headers: { "X-Requested-With": "XMLHttpRequest" },
                }
            )
                .then((res) => res.json())
                .then((data) => {
                    if (data.success && data.html) {
                        document.querySelector(
                            "#assistantsTableContainer"
                        ).innerHTML = data.html;
                    }
                })
                .catch((err) =>
                    console.error("Error AJAX búsqueda asistentes:", err)
                );
        }, 250); 
    });
}