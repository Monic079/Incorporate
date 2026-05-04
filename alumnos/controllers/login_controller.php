<?php
session_start();
//print_r($_POST);
//echo "Controller cargado<br>";
//exit;
require_once "../models/login_model.php";

$model = new LoginModel();
$action = $_POST['action'];

if($action == "register"){

    $email = $_POST['email'];
    $password = $_POST['password'];
    $institutional_email = $_POST['email'];

    $result = $model->registerStudent(
        $email,
        $password,
        $institutional_email
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

        // 🔹 SI ES ESTUDIANTE
        if($user['role'] == 'student'){
            
            $student = $model->getStudentByUserId($user['id']);
            
            if($student){
                $_SESSION['student_id'] = $student['id'];
            } else {
                echo "Error: no existe estudiante asociado";
                exit();
            }
        }

        // 🔹 REDIRECCIÓN SEGÚN ROL (MUY IMPORTANTE)
        if($user['role'] == 'student'){
            header("Location: ../views/home_view.php");
        } else if($user['role'] == 'company'){
            
            header("Location: ../../empresas/views/login_view.php");
        } else {
            header("Location: ../../admin/views/login_view.php");
        }

    } else {
        echo "<script>alert('Credenciales incorrectas'); window.location='../views/login_view.php';</script>";
    }
}