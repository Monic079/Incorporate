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
// SAVE (CREATE + UPDATE)
// =========================
public function save(){

    session_start();

    if(!isset($_SESSION['user_id'])){
        header("Location: ../views/login_view.php");
        exit();
    }

    $data = $_POST;
    $data['user_id'] = $_SESSION['user_id'];

    // 🔹 LÓGICA DE FOTO MEJORADA
    $photoName = null;

    // 1. Verificamos si se subió una nueva foto
    if(isset($_FILES['photo']) && $_FILES['photo']['error'] === 0){
        
        $filename = time() . "_" . $_FILES['photo']['name'];
        // Usamos la ruta que definimos antes para que coincida con tu estructura
        $path = "../assets/img/uploads/" . $filename;

        if(move_uploaded_file($_FILES['photo']['tmp_name'], $path)){
            $photoName = $filename;
        }

    } else {
        // 2. 🔥 IMPORTANTE: Si no se subió nada, mantenemos la actual
        // Debes asegurarte de tener un <input type="hidden" name="current_photo" value="..."> en tu formulario
        $photoName = $_POST['current_photo'] ?? null;
    }

    // Asignamos el nombre resultante (sea el nuevo o el anterior) al array de datos
    $data['photo'] = $photoName;

    // 🔹 PROCESO DE GUARDADO (CREATE O UPDATE)
    if(!empty($_POST['id'])){
        // UPDATE
        $id = $_POST['id'];
        $this->model->update($id, $data);
    } else {
        // CREATE
        $id = $this->model->create($data);
    }

    // 🔹 REDIRECT
    header("Location: ../views/cv_detail_view.php?id=".$id);
    exit();
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
    // DELETE
    // =========================
    public function delete(){

        if(!isset($_GET['id'])){
            exit("ID no proporcionado");
        }

        $id = $_GET['id'];

        $this->model->delete($id);

        header("Location: ../views/cvs_view.php");
        exit();
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


    //función para mandar a llamar y mostrar las oportunidades en home bro
    public function feed(){
        return $this->model->getAll();
    }

    //función para mostrar todos los cvs bro 
    public function myCvs(){
        session_start();

        if(!isset($_SESSION['user_id'])){
            header("Location: ../views/login_view.php");
            exit();
        }

        return $this->model->getByUser($_SESSION['user_id']);
    }

    //FUNCIÓN NUEVO PARA DARLE FUNCIONALIDAD AL BOTÓN APPLY DESDE FEED DONDE HAY VARIAS OPORTUNIDADES Y PODER SELECCIONAR QUÉ CV APLICA
    public function getUserCvs(){
        session_start();
        return $this->model->getByUserId($_SESSION['user_id']);
    }


    

    public function getApplications($cv_id){
        require_once "../models/ApplicationModel.php";
        $appModel = new ApplicationModel();
        return $appModel->getApplicationsByCv($cv_id);
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