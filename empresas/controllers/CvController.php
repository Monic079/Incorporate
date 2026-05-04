<?php

require_once "../models/CvModel.php";
require_once "../models/login_model.php";

class CvController {

    private $model;
    private $loginModel;

    public function __construct(){
        $this->model = new CvModel();
        $this->loginModel = new LoginModel(); //
    }
    
// =========================
    // SHOW (GET CV)
    // =========================
    public function show(){

        if(!isset($_GET['id'])){
            exit("ID no proporcionado");
        }

        $id = $_GET['id'];

        $cv = $this->model->getById($id);

        if(!$cv){
            exit("CV no encontrado");
        }

        return $cv;
    }

 
    // =========================
    // DATA PARA FORM
    // =========================
    public function getFormData(){

        //session_start();

        if(!isset($_SESSION['user_id'])){
            header("Location: ../views/login_view.php");
            exit();
        }

        $data = [];

        // 🔹 estudiante (opcional si luego lo necesitas)
        $student = $this->loginModel->getStudentByUserId($_SESSION['user_id']);
        $data['student'] = $student;

        // 🔹 listas dinámicas
        $data['careers'] = $this->model->getCareers();
        $data['skills']  = $this->model->getSkills();

        return $data;
    }




  public function allCvs(){

    session_start();

    if(!isset($_SESSION['user_id'])){
        header("Location: ../views/login_view.php");
        exit();
    }

    //  opcional: solo empresas
    if($_SESSION['role'] != 'company'){
        exit("No autorizado");
    }

    return $this->model->getAllCvs();
}



    
}


// =========================
// ROUTER SIMPLE
// =========================
if(isset($_GET['action'])){

    $controller = new CvController();

    switch($_GET['action']){

        case "save":
            $controller->save();
            break;

        case "delete":
            $controller->delete();
            break;

        default:
            echo "Acción no válida";
            break;
    }
}