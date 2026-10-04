document.addEventListener('DOMContentLoaded', () => {

    // Mostrar / ocultar panel de filtros
    const toggle = document.getElementById('toggleFilters');
    const panel  = document.getElementById('filtersPanel');
    if (toggle && panel) {
        toggle.addEventListener('click', () => {
            panel.hidden = !panel.hidden;
        });
    }

    // Buscador de skills
    const skillSearch = document.getElementById('skillSearch');
    if (skillSearch) {
        skillSearch.addEventListener('input', function () {
            const q = this.value.toLowerCase();
            document.querySelectorAll('.chip').forEach(c => {
                c.style.display = c.dataset.name.includes(q) ? '' : 'none';
            });
        });
    }

    // Contador de skills seleccionadas
    const count = document.getElementById('skillsCount');
    document.querySelectorAll('.chip input').forEach(i => {
        i.addEventListener('change', () => {
            count.textContent = document.querySelectorAll('.chip input:checked').length;
        });
    });

    // Carreras dependientes de la categoría
    const selCat = document.getElementById('selCategory');
    const selCareer = document.getElementById('selCareer');
    if (selCat && selCareer) {
        const filterCareers = () => {
            const cat = selCat.value;
            [...selCareer.options].forEach(o => {
                if (o.value === '0') return;
                o.hidden = cat !== '0' && o.dataset.cat !== cat;
            });
            const cur = selCareer.selectedOptions[0];
            if (cur && cur.hidden) selCareer.value = '0';
        };
        selCat.addEventListener('change', filterCareers);
        filterCareers();
    }
});