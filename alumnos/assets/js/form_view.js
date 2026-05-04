document.addEventListener("DOMContentLoaded", () => {
    // Todo tu código actual aquí adentro

const today = new Date().toISOString().split("T")[0];

document.querySelectorAll('input[type="date"]').forEach(input => {
    input.max = today;
});

toggleNivel();

let step = 0;
const steps = ["contacto_f", "general_f", "detalles_f", ];
const phoneRegex = /^[267][0-9]{7}$/;
const nextBtn = document.getElementById("nextBtn");
const prevBtn = document.getElementById("prevBtn");
const btnText = document.getElementById("btnText");

//Botones para más de una experiencia

const container = document.getElementById("experiences_container");
const btnAdd = document.getElementById("btnAddExperience");

const eduContainer = document.getElementById("education_container");
const btnAddEdu = document.getElementById("btnAddEdu");

const linksContainer = document.getElementById("links_container");
const btnAddLink = document.getElementById("btnAddLink");

document.getElementById("nivel").addEventListener("change", toggleNivel);

const stepFields = {
    0: ["full_name", "email", "phone", "edad", "gender"],
    1: ["careers[]", "level", "resume"],
    2: [] // ← IMPORTANTE
};

// Función para actualizar la vista
function updateForm() {
    // Ocultar todas las secciones
    steps.forEach((s) => {
        document.getElementById(s).style.display = "none";
    });

    // Mostrar la sección actual
    document.getElementById(steps[step]).style.display = "block";

    // Lógica del botón VOLVER (ocultar si estamos en el paso 0)
    prevBtn.style.display = (step === 0) ? "none" : "inline-block";

    // Lógica del botón SIGUIENTE / ENVIAR
    if (step === steps.length - 1) {
        btnText.innerText = "Enviar";
        //BRO CAMBIA ESTO CREO PA QUE NO QUEDE ASÍ
        nextBtn.type = "button";//ACÁ HAY QUE CAMBIAR ESTO 
    } else {
        btnText.innerText = "Next";
        nextBtn.type = "button";
    }
}

function validateStep() {
    let valid = true;

    // Limpiar mensajes de error previos
    document.querySelectorAll(".error-msg").forEach(el => el.remove());

    const fields = stepFields[step];

    fields.forEach(name => {
        let input;

        // Manejar arreglos como careers[] o similares
        if(name.includes("[]")){
            input = document.getElementsByName(name)[0];
        } else {
            input = document.querySelector(`[name="${name}"]`);
        }

        // Si el campo no existe en el DOM, lo saltamos
        if (!input) return;

        let isEmpty = false;

        // 1. SELECT MULTIPLE
        if (input.multiple) {
            isEmpty = input.selectedOptions.length === 0;
        }

        // 2. SELECT NORMAL
        else if (input.tagName === "SELECT") {
            isEmpty = input.value === "";
        }

        // 3. INPUT NUMBER (edad)
        else if (name === "edad") {
            isEmpty = input.value === "" || parseInt(input.value) <= 0;
        }

        // 4. DEFAULT (Text, Textarea, Email, etc.)
        else {
            //isEmpty = input.value.trim() === "";
            const value = input.value.trim();

            isEmpty = value === "";

            // 🔥 VALIDACIÓN ESPECÍFICA PARA TELÉFONO
            if (name === "phone" && value !== "") {
                if (!phoneRegex.test(value)) {
                    valid = false;

                    const error = document.createElement("div");
                    error.className = "error-msg";
                    error.style.color = "white";
                    error.style.backgroundColor = "red";
                    error.style.padding = "5px";
                    error.style.marginTop = "5px";
                    error.style.borderRadius = "5px";

                    error.innerText = "Teléfono es obligatorio y debe tener 8 dígitos y comenzar con 2, 6 o 7.";
                    

                    input.parentNode.appendChild(error);
                }
            }
        }

        if (isEmpty) {
            valid = false;

            const error = document.createElement("div");
            error.className = "error-msg";
            error.style.color = "white";
            error.style.backgroundColor = "red";
            error.style.padding = "5px";
            error.style.marginTop = "5px";
            error.style.borderRadius = "5px";

            error.innerText = getErrorMessage(name);

            input.parentNode.appendChild(error);
        }
    });

    return valid;
}


function getErrorMessage(name) {
    const messages = {
        full_name: "El nombre es obligatorio.",
        email: "El correo es obligatorio.",
        phone: "El teléfono es obligatorio.",
        phone: "Teléfono inválido (8 dígitos, inicia con 2, 6 o 7)",
        edad: "Debe ingresar una edad válida.",
        gender: "Debe seleccionar un género.",

        "careers[]": "Debe seleccionar al menos una carrera.",
        level: "Debe seleccionar un nivel.",
        resume: "Debe escribir un resumen profesional."
    };

    return messages[name] || "Campo obligatorio";
}

function validateDateGroup(containerSelector, startName, endName) {
    let valid = true;

    const items = document.querySelectorAll(containerSelector);

    items.forEach(item => {
        const start = item.querySelector(`[name="${startName}"]`);
        const end = item.querySelector(`[name="${endName}"]`);

        if (!start || !end) return;

        // limpiar errores previos SOLO de fechas
        item.querySelectorAll(".date-error").forEach(e => e.remove());

        if (start.value && end.value) {
            if (end.value < start.value) {
                valid = false;

                const error = document.createElement("div");
                error.className = "error-msg date-error";
                error.style.color = "white";
                error.style.backgroundColor = "red";
                error.style.padding = "5px";
                error.style.marginTop = "5px";
                error.style.borderRadius = "5px";

                error.innerText = "La fecha de finalización no puede ser menor que la de inicio.";

                end.parentNode.appendChild(error);
            }
        }
    });

    return valid;
}

// Evento Siguiente
nextBtn.addEventListener("click", (e) => {

    const isValid = validateStep();

    // 🔥 VALIDACIÓN EXTRA SOLO EN EL PASO 2
let datesValid = true;

if (step === 2) {
    const eduValid = validateDateGroup(".edu-item", "edu_start[]", "edu_end[]");
    const expValid = validateDateGroup(".exp-item", "exp_start[]", "exp_end[]");

    datesValid = eduValid && expValid;
}

if (!isValid || !datesValid) return;

    if (!isValid || !datesValid) return;

    if (step === steps.length - 1) {
        const form = document.getElementById("formCv");
        form.submit(); 
        return;
    }

    if (step < steps.length - 1) {
        step++;
        updateForm();
    }
});

document.addEventListener("change", function(e){
    if (
        e.target.name === "edu_start[]" ||
        e.target.name === "edu_end[]" ||
        e.target.name === "exp_start[]" ||
        e.target.name === "exp_end[]"
    ) {
        validateDateGroup(".edu-item", "edu_start[]", "edu_end[]");
        validateDateGroup(".exp-item", "exp_start[]", "exp_end[]");
    }
});

document.querySelectorAll("input, select, textarea").forEach(input => {
    input.addEventListener("input", () => {
        const error = input.parentNode.querySelector(".error-msg");
        if (error) error.remove();
    });
});

// Evento Volver
prevBtn.addEventListener("click", () => {
    if (step > 0) {
        step--;
        updateForm();
    }
});



function toggleNivel() {
    let nivel = document.getElementById("nivel").value;

    if(nivel === "estudiante"){
        document.getElementById("campo_anio").style.display = "block";
    } else {
        document.getElementById("campo_anio").style.display = "none";
    }
}

/*
// 🔹 Escuchar el clic en el botón "Agregar"
    if (btnAdd) {
        btnAdd.addEventListener("click", function() {
            const div = document.createElement("div");
            div.classList.add("exp-item");

            div.innerHTML = `
                <hr>
                <input type="text" name="exp_company[]" placeholder="Empresa">
                <input type="text" name="exp_position[]" placeholder="Cargo">

                <label>Fecha de inicio</label>
                <input type="date" name="exp_start[]">

                <label>Fecha de finalización</label>
                <input type="date" name="exp_end[]">

                <label>Descripción</label>
                <textarea name="exp_desc[]" placeholder="Descripción breve"></textarea>

                <button type="button" class="btn-remove">Eliminar</button>
            `;

            container.appendChild(div);
        });
    }

//educacion
    if (btnAddEdu) {
        btnAddEdu.addEventListener("click", function() {
            const div = document.createElement("div");
            div.classList.add("edu-item");
            div.innerHTML = `
                <hr>
                <input type="text" name="edu_institution[]" placeholder="Institución">
                <input type="text" name="edu_program[]" placeholder="Programa">
                <label>Fecha de inicio</label>
                <input type="date" name="edu_start[]">
                <label>Fecha de finalización</label>
                <input type="date" name="edu_end[]">
                <button type="button" class="btn-remove">Eliminar</button>
            `;
            eduContainer.appendChild(div);
        });
    }

    if (btnAddLink) {
        btnAddLink.addEventListener("click", function() {
            const div = document.createElement("div");
            div.classList.add("link-item");
            div.innerHTML = `
                <hr>
                <select name="link_type[]">
                    <option value="linkedin">LinkedIn</option>
                    <option value="github">GitHub</option>
                    <option value="portfolio">Portfolio</option>
                    <option value="website">Website</option>
                    <option value="other">Otro</option>
                </select>
                <input type="url" name="link_url[]" placeholder="https://ejemplo.com">
                <button type="button" class="btn-remove">Eliminar</button>
            `;
            linksContainer.appendChild(div);
        });
    }

// --- DELEGACIÓN DE EVENTOS PARA ELIMINAR (GENERAL) ---
    // Escuchamos clics en todo el documento y filtramos por la clase 'btn-remove'
    document.addEventListener("click", function(e) {
        if (e.target && e.target.classList.contains("btn-remove")) {
            e.target.parentElement.remove();
        }
    });

*/
});

