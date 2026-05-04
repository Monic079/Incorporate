<?php
session_start();
require_once "../models/login_model.php";

$model = new LoginModel();
$action = $_POST['action'];

if($action == "register"){

    $email = $_POST['email'];
    $password = $_POST['password'];

    $company_id = $_POST['company_id'];
    $new_company = $_POST['new_company'];

    $contact_name = $_POST['contact_name'];
    $contact_position = $_POST['contact_position'];
    $contact_phone = $_POST['contact_phone'];

    $result = $model->registerCompanyUser(
        $email,
        $password,
        $company_id,
        $new_company,
        $contact_name,
        $contact_position,
        $contact_phone
    );

    if($result){
        echo "<script>alert('Cuenta creada'); window.location='../views/login_view.php';</script>";
    } else {
        echo "Error al registrar";
    }
}

if($action == "login"){

    $email = $_POST['email'];
    $password = $_POST['password'];

    $user = $model->login($email, $password);

    if($user){
        $_SESSION['user_id'] = $user['id'];
        $_SESSION['role'] = $user['role'];
        $_SESSION['email'] = $user['email'];

        // NUEVO: obtener company_user_id SOLO si es empresa
        if($user['role'] == 'company'){
            
        $companyUser = $model->getByUserId($user['id']);
        
            //  validación importante
            if($companyUser){
                $_SESSION['company_user_id'] = $companyUser['id'];

                //DEBUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUUG
                /*
                echo "<pre>";
                echo "SESSION en login:\n";
                print_r($_SESSION);
                exit;*/

            } else {
                // Esto no debería pasar si el registro está bien
                echo "Error: no existe company_user asociado";
                exit();
            }
        }

        header("Location: ../views/home_view.php"); //DEBERÍA LLEVARTE A HOME_VIEW.PHP PERO DE MOMENTO TE LLEVARÁ A OPORTUNIDADES_FORM_VIEW :D
    } else {
        echo "<script>alert('Credenciales incorrectas'); window.location='../views/login_view.php';</script>";
    }
}