document.addEventListener("DOMContentLoaded", () => {

// Seleccionamos los botones por su ID
    const btnLogin = document.getElementById('btnLogin');
    const btnRegister = document.getElementById('btnRegister');

    // Asignamos el evento 'click' a la función correspondiente
    btnLogin.addEventListener('click', showLogin);
    btnRegister.addEventListener('click', showRegister);

function showLogin(){
    console.log("La función showLogin se está ejecutando"); // Mensaje de control
    document.getElementById('loginForm').classList.remove('hidden');
    //document.getElementById('registerForm').classList.add('hidden');
    // Oculta el contenedor que tiene ambos botones
    document.getElementById('containerButtons').classList.add('hidden');
}

function showRegister(){
    console.log("La función showRegister se está ejecutando"); // Mensaje de control
    document.getElementById('registerForm').classList.remove('hidden');
    //document.getElementById('loginForm').classList.add('hidden');
    // Oculta el contenedor que tiene ambos botones
    document.getElementById('containerButtons').classList.add('hidden');
}

function checkCompany(){
    let select = document.getElementById('companySelect');
    let input = document.getElementById('newCompany');

    if(select.value === "new"){
        input.classList.remove('hidden');
        input.required = true;
    } else {
        input.classList.add('hidden');
        input.required = false;
    }
}



});
