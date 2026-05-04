<?php
session_start();
require_once "../models/login_model.php";

$model = new LoginModel();
$action = $_POST['action'];

if($action == "login"){

    $email = $_POST['email'];
    $password = $_POST['password'];

    $user = $model->login($email, $password);

    if($user){

        if($user['role'] !== 'admin'){
            header("Location: ../views/login_view.php?error=not_admin");
            exit();
        }
        
        session_start();

        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['email'] = $user['email'];

        


        header("Location: ../views/home_view.php"); //DEBERÍA LLEVARTE A HOME_VIEW.PHP PERO DE MOMENTO TE LLEVARÁ A OPORTUNIDADES_FORM_VIEW :D
    } else {
        echo "<script>alert('Credenciales incorrectas'); window.location='../views/login_view.php';</script>";
    }
}