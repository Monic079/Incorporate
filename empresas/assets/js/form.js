document.addEventListener("DOMContentLoaded", () => {

    const stepIds = ["general", "detalles", "contacto"];

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
        const steps = ["general_f", "detalles_f", "contacto_f"];

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
    // LAYOUT MOCKUP - STEP GENERAL
    // ==========================================
    const generalF = document.getElementById('general_f');
    if (generalF) {

        // Campos simples - usar wrapField normal
        wrapField(generalF, '[name="title"]',    'Título de la oportunidad:');
        wrapField(generalF, '#tipo',             'Tipo de oportunidad:');
        wrapField(generalF, '[name="vacancies"]','Número de vacantes:');
        wrapField(generalF, '[name="deadline"]', 'Fecha límite de aplicación:');

        // Campos condicionales - agregar label DENTRO del div que ya se muestra/oculta
        addLabelInside('#campo_salario',      '*Rango salarial:');
        addLabelInside('#campo_remuneracion', '*Remuneración:');

    }

    // ==========================================
    // LAYOUT MOCKUP - STEP DETALLES
    // ==========================================
    const detallesF = document.getElementById('detalles_f');
    if (!detallesF) return;

    const fieldDefs = [
        { label: 'Carreras que aplican:', selector: '[name="careers[]"]' },
        { label: 'Niveles aceptados:',    selector: '#nivel' },
        { label: 'Funciones:',            selector: '[name="functions"]' },
        { label: 'Skills requeridas',     selector: '[name="skills[]"]' },
        { label: 'Modalidad de trabajo:', selector: '[name="modality"]' },
        { label: 'Horarios:',             selector: '[name="schedule"]' },
    ];

    fieldDefs.forEach(def => {
        const el = detallesF.querySelector(def.selector);
        if (!el) return;

        const row = document.createElement('div');
        row.className = 'field-row';

        const labelDiv = document.createElement('div');
        labelDiv.className = 'field-label';
        labelDiv.textContent = def.label;

        const inputDiv = document.createElement('div');
        inputDiv.className = 'field-input';

        el.parentNode.insertBefore(row, el);
        inputDiv.appendChild(el);
        row.appendChild(labelDiv);
        row.appendChild(inputDiv);
        detallesF.appendChild(row);

        if (el.tagName === 'SELECT' && !el.multiple) {
            const wrap = document.createElement('div');
            wrap.className = 'custom-select-wrap';
            el.parentNode.insertBefore(wrap, el);
            wrap.appendChild(el);
        }

        if (el.tagName === 'SELECT' && el.multiple) {
            buildMultiSelect(el, inputDiv);
        }
    });

    // Mover campo_anio dentro de la fila de nivel
    const campoAnio = document.getElementById('campo_anio');
    const nivelRow = document.getElementById('nivel')?.closest('.field-row');
    if (campoAnio && nivelRow) {
        nivelRow.querySelector('.field-input').appendChild(campoAnio);
    }
    
// ==========================================
// LAYOUT MOCKUP - STEP CONTACTO
// ==========================================
const contactoF = document.getElementById('contacto_f');
if (contactoF) {
    const contactDefs = [
        { label: 'Nombre de contacto:', selector: '[name="contact_name"]' },
        { label: 'Cargo de contacto:',  selector: '[name="contact_position"]' },
        { label: 'Email de contacto:',  selector: '[name="contact_email"]' },
        { label: 'Número de contacto:', selector: '[name="contact_phone"]' },
    ];

    contactDefs.forEach(def => {
        const el = contactoF.querySelector(def.selector);
        if (!el) return;

        const row = document.createElement('div');
        row.className = 'field-row';

        const labelDiv = document.createElement('div');
        labelDiv.className = 'field-label';
        labelDiv.textContent = def.label;

        const inputDiv = document.createElement('div');
        inputDiv.className = 'field-input';

        el.parentNode.insertBefore(row, el);
        inputDiv.appendChild(el);
        row.appendChild(labelDiv);
        row.appendChild(inputDiv);
        contactoF.appendChild(row);
    });
}

});

// ==========================================
// BUILDMULTI-SELECT 
// ==========================================
function addLabelInside(containerSelector, labelText) {
    const container = document.querySelector(containerSelector);
    if (!container) return;

    // Convertir el div condicional en una field-row
    container.classList.add('field-row');
    container.style.alignItems = 'center';

    const labelDiv = document.createElement('div');
    labelDiv.className = 'field-label';
    labelDiv.textContent = labelText;

    // Insertar el label antes del input/select dentro del div
    container.insertBefore(labelDiv, container.firstChild);

    // Wrap al input interno en field-input
    const inner = container.querySelector('input, select, label');
    if (inner) {
        const inputDiv = document.createElement('div');
        inputDiv.className = 'field-input';
        container.appendChild(inputDiv);
        inputDiv.appendChild(inner);
    }
}

// ==========================================
// FUNCION AUXILIAR
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

    // Insertar la fila EN EL LUGAR del elemento, no al final
    el.parentNode.insertBefore(row, el);
    inputDiv.appendChild(el);
    row.appendChild(labelDiv);
    row.appendChild(inputDiv);
    // ← Ya NO hay parent.appendChild(row)

    // Wrap selects simples con flecha custom
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

    const wrapper = document.createElement('div');
    wrapper.className = 'custom-multi-select';

    const searchBar = document.createElement('div');
    searchBar.className = 'cms-search-bar no-tags';
    searchBar.innerHTML = `<input class="cms-search-input" type="text" placeholder="Busca...">`;

    const tagsDiv = document.createElement('div');
    tagsDiv.className = 'cms-tags';

    const dropdown = document.createElement('div');
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

    searchInput.addEventListener('input', () => {
        renderOptions(searchInput.value);
    });

    document.addEventListener('click', (e) => {
        if (!wrapper.contains(e.target)) {
            dropdown.classList.remove('open');
        }
    });

    wrapper.appendChild(searchBar);
    wrapper.appendChild(tagsDiv);
    wrapper.appendChild(dropdown);
    container.insertBefore(wrapper, selectEl);

    renderTags();

    
}