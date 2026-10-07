<?php

require_once "../models/OpportunityModel.php";
require_once "../models/login_model.php";

class OpportunityController {

    private $model;
    private $loginModel;

    public function __construct(){
        $this->model = new OpportunityModel();
        $this->loginModel = new LoginModel(); //
    }
    
    // =========================
    // SAVE (CREATE + UPDATE)
    // =========================
    public function save(){

        if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

if(!isset($_SESSION['company_user_id'])){
    header("Location: ../views/login_view.php");
    exit();
}

        $data = $_POST;
        $errors = [];

        // checkbox fix
        $data['salary_visible'] = isset($_POST['salary_visible']) ? 1 : 0;
        $data['company_user_id'] = $_SESSION['company_user_id'];

            // 🔴 VALIDACIONES
        if(empty(trim($data['title']))) {
            $errors['title'] = "El título es obligatorio.";
        }

        if(empty($data['type_opor'])) {
            $errors['type_opor'] = "Debe seleccionar un tipo de oportunidad.";
        }

        if(empty($data['vacancies'])) {
            $errors['vacancies'] = "Debe colocar el número de vacantes disponibles.";
        }

        if(empty($data['deadline'])) {
            $errors['deadline'] = "Coloque una fecha límite de aplicación.";
        }

        if(empty(trim($data['functions']))) {
            $errors['functions'] = "Debe colcoar una breve descripción de las funciones.";
        }

        if(empty($data['modality'])) {
            $errors['modality'] = "Debe seleccionar un tipo de modalidad.";
        }

        if(empty(trim($data['schedule']))) {
            $errors['schedule'] = "El horario es obligatorio.";
        }

        // 🔴 SI HAY ERRORES → REGRESAR AL FORM
        if(!empty($errors)){
            $_SESSION['errors'] = $errors;
            $_SESSION['old'] = $data;

            header("Location: ../views/oportunidades_form_view.php");
            exit();
        }

        if(!empty($_POST['id'])){
            // UPDATE
            $id = $_POST['id'];
            $this->model->update($id, $data);
        }else{
            // CREATE
            //$data['company_user_id'] = 1; // ejemplo  QUÉ ES ESTO BRO Y POR QUÉ MADRES 1 

            $id = $this->model->create($data);
        }

        // REDIRECCIÓN AL DETAIL
        header("Location: ../views/oportunidades_detail_view.php?id=".$id);
        exit();
    }

    // =========================
    // GET DETAIL
    // =========================
    public function show(){
        //$id = $_GET['id'];
        //return $this->model->getById($id);
        if(!isset($_GET['id'])){
            exit("ID no proporcionado");
            echo("Bro no hay id");
        }

        $id = $_GET['id'];
        $op = $this->model->getById($id);

        if(!$op){
            exit("Oportunidad no encontrada");
        }

        return $op;
    }

    // =========================
    // DELETE
    // =========================
    public function delete(){
        $id = $_POST['id'];
        $this->model->delete($id);

        header("Location: ../views/oportunidades_view.php");
        exit();
    }

        // =========================
    // DATA PARA FORM
    // =========================
    public function getFormData(){

        //session_start();

        $data = [];

        // 🔹 VALIDACIÓN DE SESIÓN
        if(!isset($_SESSION['user_id'])){
            header("Location: login_view.php");
            exit();
        }

        // 🔹 obtener company_user desde login_model
        $companyUser = $this->loginModel->getByUserId($_SESSION['user_id']);

        $data['company'] = $companyUser;

        // 🔹 listas dinámicas
        $data['careers'] = $this->model->getCareers();
        $data['skills'] = $this->model->getSkills();

        return $data;
    }

    public function index(){
    if (session_status() === PHP_SESSION_NONE) {
        session_start();
    }

    if(!isset($_SESSION['company_user_id'])){
        header("Location: ../views/login_view.php");
        exit();
    }

    $company_user_id = $_SESSION['company_user_id'];

    return $this->model->getByCompanyUser($company_user_id);
}


    
}


// =========================
// ROUTER SIMPLE
// =========================
if(isset($_GET['action'])){

    $controller = new OpportunityController();

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