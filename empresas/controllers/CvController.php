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


   // funciones para los filtros
    public function getFilters(){
        return [
            'career_category' => (int)($_GET['career_category'] ?? 0),
            'career'          => (int)($_GET['career'] ?? 0),
            'level'           => in_array($_GET['level'] ?? '', ['estudiante','egresado']) ? $_GET['level'] : '',
            'age_min'         => (int)($_GET['age_min'] ?? 0),
            'age_max'         => (int)($_GET['age_max'] ?? 0),
            'skills'          => array_values(array_filter(array_map('intval', (array)($_GET['skills'] ?? [])))),
            'skills_mode'     => ($_GET['skills_mode'] ?? 'all') === 'any' ? 'any' : 'all',
            'order'           => ($_GET['order'] ?? '') === 'old' ? 'old' : 'recent',
        ];
    }

    public function filterOptions(){
        return $this->model->getFilterOptions();
    }

    public function allCvs(){

        session_start();

        if(!isset($_SESSION['user_id'])){
            header("Location: ../views/login_view.php");
            exit();
        }

        if($_SESSION['role'] != 'company'){
            exit("No autorizado");
        }

        return $this->model->getFilteredCvs($this->getFilters());
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