<?php

class AuthController {



    public function logout(){
        session_start();

        // Vaciar variables de sesión
        $_SESSION = [];

        // Destruir sesión
        session_destroy();

        // Redirigir al login
        header("Location: ../views/login_view.php");
        exit();
    }

}

if(isset($_GET['action']) && $_GET['action'] === 'logout'){
    $controller = new AuthController();
    $controller->logout();
}