document.addEventListener("DOMContentLoaded", () => {

    const stepIds = ["contacto", "general", "detalles"];

    function updateStepIndicator(currentStep) {
        stepIds.forEach((id, index) => {
            const el = document.getElementById(id);

            if (!el) return;

            el.classList.remove("step-active", "step-done");

            if (index === currentStep) {
                el.classList.add("step-active");
            } else if (index < currentStep) {
                el.classList.add("step-done");
            }
        });
    }

    // Detectar cambios automáticamente del form_view.js
    const observer = new MutationObserver(() => {
        const steps = ["contacto_f", "general_f", "detalles_f"];

        let current = 0;

        steps.forEach((id, index) => {
            const el = document.getElementById(id);
            if (el && el.style.display !== "none") {
                current = index;
            }
        });

        updateStepIndicator(current);
    });

    observer.observe(document.body, {
        attributes: true,
        subtree: true,
        attributeFilter: ["style"]
    });

    // Estado inicial
    updateStepIndicator(0);
   
    // ==========================================
    // STEP CONTACTO
    // ==========================================
    const contactoF = document.getElementById('contacto_f');
    if (contactoF) {
        wrapField(contactoF, '[name="full_name"]', 'Nombre completo:');
        wrapField(contactoF, '[name="phone"]',     'Teléfono:');
        wrapField(contactoF, '[name="email"]',     'Email:');
        wrapField(contactoF, '[name="gender"]',    'Género:');
        wrapField(contactoF, '[name="edad"]',      'Edad:');
    }

    // ==========================================
    // STEP GENERAL
    // ==========================================
    const generalF = document.getElementById('general_f');
    if (generalF) {
        wrapField(generalF, '[name="careers[]"]', 'Carreras:');
        wrapField(generalF, '#nivel',             'Nivel:');

        // Mover campo_anio dentro de la fila de nivel
        const campoAnio = document.getElementById('campo_anio');
        const nivelRow  = document.getElementById('nivel')?.closest('.field-row');
        if (campoAnio && nivelRow) {
            nivelRow.querySelector('.field-input').appendChild(campoAnio);
            campoAnio.querySelector('input').style.margin = '8px 0 0 0';
        }

        // Sacar textarea del div#resume y ponerlo directo en generalF ANTES de wrapField
        const resumeDiv = document.getElementById('resume');
        if (resumeDiv) {
            const ta = resumeDiv.querySelector('textarea');
            if (ta) {
                resumeDiv.parentNode.insertBefore(ta, resumeDiv); // mover textarea fuera
                resumeDiv.remove();                                // eliminar div vacío
            }
        }

        // Ahora sí wrapField lo encuentra
        wrapField(generalF, '[name="resume"]', 'Resumen:');

        // Multi-select carreras
        const careersSelect = generalF.querySelector('[name="careers[]"]');
        if (careersSelect) {
            const fi = careersSelect.closest('.field-row')?.querySelector('.field-input');
            if (fi) buildMultiSelect(careersSelect, fi);
        }
    }

    // ==========================================
    // STEP DETALLES - agrupar fechas lado a lado
    // ==========================================
    groupDates('experiences_container', 'exp_start[]', 'exp_end[]');
    groupDates('education_container',   'edu_start[]', 'edu_end[]');

    // Multi-select skills
    const skillsSelect = document.querySelector('#detalles_f [name="skills[]"]');
    if (skillsSelect) {
        const skillWrapper = document.createElement('div');
        skillsSelect.parentNode.insertBefore(skillWrapper, skillsSelect);
        skillWrapper.appendChild(skillsSelect);
        buildMultiSelect(skillsSelect, skillWrapper);
    }

    // Botones agregar — clonan el primer item
    setupAddBtn('btnAddExperience', 'exp-item', 'experiences_container');
    setupAddBtn('btnAddEdu',        'edu-item',  'education_container');
    setupAddBtn('btnAddLink',       'link-item', 'links_container');
    
});

// ==========================================
// AGRUPAR FECHAS INICIO/FIN LADO A LADO
// ==========================================
function groupDates(containerId, startName, endName) {
    const container = document.getElementById(containerId);
    if (!container) return;

    container.querySelectorAll('.exp-item, .edu-item').forEach(item => {
        // Evitar procesar items que ya tienen date-row
        if (item.querySelector('.date-row')) return;

        const startInput = item.querySelector(`[name="${startName}"]`);
        const endInput   = item.querySelector(`[name="${endName}"]`);
        if (!startInput || !endInput) return;

        const allLabels  = Array.from(item.querySelectorAll('label'));
        const startLabel = allLabels.find(l => l.textContent.toLowerCase().includes('inicio'));
        const endLabel   = allLabels.find(l => l.textContent.toLowerCase().includes('finaliz'));

        const dateRow = document.createElement('div');
        dateRow.className = 'date-row';

        const startCol = document.createElement('div');
        startCol.style.flex = '1';
        if (startLabel) startCol.appendChild(startLabel);
        startCol.appendChild(startInput);

        const endCol = document.createElement('div');
        endCol.style.flex = '1';
        if (endLabel) endCol.appendChild(endLabel);
        endCol.appendChild(endInput);

        dateRow.appendChild(startCol);
        dateRow.appendChild(endCol);

        // Insertar dateRow en el item directamente
        item.appendChild(dateRow);
    });
}

// ==========================================
// BOTÓN AGREGAR ITEM (exp / edu / link)
// ==========================================
function setupAddBtn(btnId, itemClass, containerId) {
    const btn       = document.getElementById(btnId);
    const container = document.getElementById(containerId);

    if (!btn || !container) return;

    btn.addEventListener('click', () => {
        const first = container.querySelector('.' + itemClass);
        if (!first) return;

        const clone = first.cloneNode(true);

        // Limpiar valores
        clone.querySelectorAll('input, textarea').forEach(el => el.value = '');
        clone.querySelectorAll('select').forEach(el => el.selectedIndex = 0);

        // 🔥 CREAR BOTÓN ELIMINAR AQUÍ (correcto)
        const removeBtn = document.createElement('button');
        removeBtn.type = 'button';
        removeBtn.textContent = 'Eliminar';
        removeBtn.className = 'btn-remove';

        removeBtn.addEventListener('click', () => {
            clone.remove();
        });

        clone.appendChild(removeBtn);

        container.appendChild(clone);

        // Reaplicar agrupación de fechas
        groupDates(containerId,
            itemClass === 'exp-item' ? 'exp_start[]' : 'edu_start[]',
            itemClass === 'exp-item' ? 'exp_end[]'   : 'edu_end[]'
        );
    });
}
// ==========================================
// WRAP FIELD - label + input en fila
// ==========================================
function wrapField(parent, selector, labelText) {
    const el = parent.querySelector(selector);
    if (!el) return;

    const row = document.createElement('div');
    row.className = 'field-row';

    const labelDiv = document.createElement('div');
    labelDiv.className = 'field-label';
    labelDiv.textContent = labelText;

    const inputDiv = document.createElement('div');
    inputDiv.className = 'field-input';

    el.parentNode.insertBefore(row, el);
    inputDiv.appendChild(el);
    row.appendChild(labelDiv);
    row.appendChild(inputDiv);

    if (el.tagName === 'SELECT' && !el.multiple) {
        const wrap = document.createElement('div');
        wrap.className = 'custom-select-wrap';
        el.parentNode.insertBefore(wrap, el);
        wrap.appendChild(el);
    }
}

// ==========================================
// MULTI-SELECT CON BUSCADOR
// ==========================================
function buildMultiSelect(selectEl, container) {
    selectEl.style.display = 'none';

    const options = Array.from(selectEl.options).map(o => ({
        value: o.value,
        text: o.text,
        selected: o.selected
    }));

    const wrapper    = document.createElement('div');
    wrapper.className = 'custom-multi-select';

    const searchBar  = document.createElement('div');
    searchBar.className = 'cms-search-bar no-tags';
    searchBar.innerHTML = `<input class="cms-search-input" type="text" placeholder="Busca...">`;

    const tagsDiv    = document.createElement('div');
    tagsDiv.className = 'cms-tags';

    const dropdown   = document.createElement('div');
    dropdown.className = 'cms-dropdown';

    const searchInput = searchBar.querySelector('.cms-search-input');

    function renderOptions(filter = '') {
        dropdown.innerHTML = '';
        options
            .filter(o => o.text.toLowerCase().includes(filter.toLowerCase()))
            .forEach(o => {
                const div = document.createElement('div');
                div.className = 'cms-option' + (o.selected ? ' selected' : '');
                div.textContent = o.text;
                div.addEventListener('click', () => {
                    o.selected = !o.selected;
                    Array.from(selectEl.options).forEach(opt => {
                        if (opt.value == o.value) opt.selected = o.selected;
                    });
                    renderTags();
                    renderOptions(searchInput.value);
                });
                dropdown.appendChild(div);
            });
    }

    function renderTags() {
        tagsDiv.innerHTML = '';
        const selected = options.filter(o => o.selected);
        selected.forEach(o => {
            const tag = document.createElement('div');
            tag.className = 'cms-tag';
            tag.innerHTML = `${o.text} <span data-val="${o.value}">×</span>`;
            tag.querySelector('span').addEventListener('click', () => {
                o.selected = false;
                Array.from(selectEl.options).forEach(opt => {
                    if (opt.value == o.value) opt.selected = false;
                });
                renderTags();
                renderOptions(searchInput.value);
            });
            tagsDiv.appendChild(tag);
        });

        if (selected.length > 0) {
            searchBar.classList.remove('no-tags');
        } else {
            searchBar.classList.add('no-tags');
        }
    }

    searchInput.addEventListener('focus', () => {
        renderOptions(searchInput.value);
        dropdown.classList.add('open');
    });

    searchInput.addEventListener('input', () => renderOptions(searchInput.value));

    document.addEventListener('click', (e) => {
        if (!wrapper.contains(e.target)) dropdown.classList.remove('open');
    });

    wrapper.appendChild(searchBar);
    wrapper.appendChild(tagsDiv);
    wrapper.appendChild(dropdown);
    if (selectEl.parentNode === container) {
    container.insertBefore(wrapper, selectEl);
    } else {
        container.appendChild(wrapper);
    }

    renderTags(); 
}