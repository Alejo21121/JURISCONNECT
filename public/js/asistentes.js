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

// ── Estado tabla asistentes ──
const stateAssistants = {
    search: '',
    sort_assistant: new URLSearchParams(window.location.search).get('sort_assistant') || 'id',
    dir_assistant: new URLSearchParams(window.location.search).get('dir_assistant') || 'asc',
    page: 1,
};

function cargarTablaAsistentes() {
    // ✅ ID correcto según el dashboard
    const container = document.getElementById('assistantsTableContainer');
    if (!container) return;

    container.style.opacity = '0.5';
    container.style.pointerEvents = 'none';

    const params = new URLSearchParams({
        search: stateAssistants.search,
        sort_assistant: stateAssistants.sort_assistant,
        dir_assistant: stateAssistants.dir_assistant,
        assistantsPage: stateAssistants.page,
        section: 'assistants',
    });

    fetch(`/dashboard?${params}`, {
        headers: { 'X-Requested-With': 'XMLHttpRequest' }
    })
        .then(res => res.json())
        .then(data => {
            if (data.success) {
                container.innerHTML = data.html;
                container.style.opacity = '1';
                container.style.pointerEvents = '';
                bindAssistantEvents();
            }
        })
        .catch(() => {
            container.style.opacity = '1';
            container.style.pointerEvents = '';
        });
}

function bindAssistantEvents() {
    // ✅ ID correcto
    document.querySelectorAll('#assistantsTableContainer .th-link').forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const url = new URL(this.href);
            stateAssistants.sort_assistant = url.searchParams.get('sort_assistant');
            stateAssistants.dir_assistant = url.searchParams.get('dir_assistant');
            stateAssistants.page = 1;
            cargarTablaAsistentes();
        });
    });

    // ✅ ID correcto
    document.querySelectorAll('#assistantsTableContainer .pagination-btn[href]').forEach(link => {
        link.addEventListener('click', function (e) {
            e.preventDefault();
            const url = new URL(this.href);
            stateAssistants.page = url.searchParams.get('assistantsPage') || 1;
            cargarTablaAsistentes();
        });
    });
}

// Buscador — usa el id correcto del dashboard: 'searchInput'
const searchAsistentes = document.getElementById('searchInput');
if (searchAsistentes) {
    let timer;
    searchAsistentes.addEventListener('input', function () {
        clearTimeout(timer);
        stateAssistants.search = this.value;
        stateAssistants.page = 1;
        timer = setTimeout(() => cargarTablaAsistentes(), 350);
    });
}

document.addEventListener('DOMContentLoaded', () => bindAssistantEvents());