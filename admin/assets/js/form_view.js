document.addEventListener("DOMContentLoaded", () => {
    // Todo tu código actual aquí adentro
    toggleTipo();
    toggleNivel();


let step = 0;
const steps = ["general_f", "detalles_f", "contacto_f"];

const nextBtn = document.getElementById("nextBtn");
const prevBtn = document.getElementById("prevBtn");
const btnText = document.getElementById("btnText");

document.getElementById("tipo").addEventListener("change", toggleTipo);
document.getElementById("nivel").addEventListener("change", toggleNivel);

//PARA VALIDACIONES
const stepFields = {
    0: ["title", "type_opor"],                  // general_f
    1: ["vacancies", "deadline", "functions"],  // detalles_f
    2: ["modality", "schedule"]                 // contacto_f
};

function validateStep() {
    let valid = true;

    // limpiar errores previos
    document.querySelectorAll(".error-msg").forEach(el => el.remove());

    const fields = stepFields[step];

    fields.forEach(name => {
        const input = document.querySelector(`[name="${name}"]`);

        if (!input) return;

        if (input.value.trim() === "") {
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
        title: "El título es obligatorio.",
        type_opor: "Debe seleccionar un tipo de oportunidad.",
        vacancies: "Debe colocar el número de vacantes disponibles.",
        deadline: "Coloque una fecha límite de aplicación.",
        functions: "Debe colocar una breve descripción de las funciones.",
        modality: "Debe seleccionar un tipo de modalidad.",
        schedule: "El horario es obligatorio."
    };

    return messages[name] || "Campo obligatorio";
}

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

// Evento Siguiente
nextBtn.addEventListener("click", (e) => {

    //VALIDACIONES PRIMERO
        const isValid = validateStep();

    if (!isValid) return; // ❌ NO avanza si hay errores

   // Si estamos en el último paso (Contacto)
    if (step === steps.length - 1) {
        // Obtenemos el formulario y lo enviamos manualmente
        const form = document.getElementById("formOportunidad");
        form.submit(); 
        return;
    }

    // Si no es el último, simplemente avanzamos de pestaña
    if (step < steps.length - 1) {
        step++;
        updateForm();
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


function toggleTipo() {
    let tipo = document.getElementById("tipo").value;

    document.getElementById("campo_remuneracion").style.display = "none";
    document.getElementById("campo_salario").style.display = "none";
    document.getElementById("campo_visibilidad").style.display = "none";

    if(tipo === "pasantia"){
        document.getElementById("campo_remuneracion").style.display = "block";
        document.getElementById("campo_visibilidad").style.display = "block";
    }

    if(tipo === "trabajo"){
        document.getElementById("campo_salario").style.display = "block";
        document.getElementById("campo_visibilidad").style.display = "block";
    }
}

function toggleNivel() {
    let nivel = document.getElementById("nivel").value;

    if(nivel === "estudiante"){
        document.getElementById("campo_anio").style.display = "block";
    } else {
        document.getElementById("campo_anio").style.display = "none";
    }
}






});

