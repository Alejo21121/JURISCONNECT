let searchTimeout;

function buscarProcesos(page = 1) {
    const buscador = document.getElementById("buscadorProcesos");
    if (!buscador) return;

    const termino = buscador.value.trim();

    const params = new URLSearchParams();
    params.append("radicado", termino);
    params.append("procesosSimplePage", page);

    fetch(`/dashboard?${params.toString()}`, {
        headers: {
            "X-Requested-With": "XMLHttpRequest",
            Accept: "application/json",
        },
    })
        .then((r) => r.json())
        .then((data) => {
            if (!data.success || !data.html) return;

            const temp = document.createElement("div");
            temp.innerHTML = data.html;

            const nuevoTbody = temp.querySelector("#procesosTableBody");
            if (nuevoTbody) {
                document.getElementById("procesosTableBody").innerHTML =
                    nuevoTbody.innerHTML;
            }

            const nuevaPag = temp.querySelector("#paginationContainer");
            if (nuevaPag) {
                document.getElementById("paginationContainer").innerHTML =
                    nuevaPag.innerHTML;
            }
        })
        .catch(console.error);
}

// Delegación global (una sola vez)
document.body.addEventListener("input", (e) => {
    if (e.target.id === "buscadorProcesos") {
        clearTimeout(searchTimeout);
        searchTimeout = setTimeout(() => buscarProcesos(1), 500);
    }
});

document.body.addEventListener("click", (e) => {
    const pag = e.target.closest("#paginationContainer .pagination a");
    if (pag) {
        e.preventDefault();
        const url = new URL(pag.href);
        buscarProcesos(url.searchParams.get("procesosSimplePage") || 1);
    }

    const reopen = e.target.closest(".reopen-process-btn");
    if (reopen) {
        console.log("Reabrir proceso:", reopen.dataset.id);
    }
});
