<?php
require_once "../models/ApplicationModel.php";

class ApplicationController {

    private $model;

    public function __construct(){
        $this->model = new ApplicationModel();
    }

    public function apply(){

        session_start();

        if(!isset($_SESSION['user_id'])){
            exit("Debes iniciar sesión");
        }

        if(!isset($_GET['id'])){
            exit("ID de oportunidad no proporcionado");
        }

        $user_id = $_SESSION['user_id'];
        $opportunity_id = $_GET['id'];

        // 🔹 1. Obtener CV del estudiante
        //$cv = $this->model->getCvByUserId($user_id);
        if(!isset($_GET['cv_id'])){
            exit("Debes seleccionar un CV");
        }

        $cv_id = $_GET['cv_id'];

            $cv = $this->model->getCvByIdAndUser($cv_id, $user_id);

            if(!$cv){
                exit("CV no válido");
            }

        if(!$cv){
            exit("Debes crear tu CV antes de aplicar");
        }



        // 🔹 2. Evitar duplicados
        if($this->model->alreadyApplied($cv_id, $opportunity_id)){
            exit("Ya aplicaste a esta oportunidad");
        }

        // 🔹 3. Aplicar
        $this->model->apply($cv_id, $opportunity_id);

        // 🔹 4. Redirección
        //header("Location: ../views/oportunidades_detail_view.php?id=".$opportunity_id);
        // 🔹 4. Redirección
        if(($_GET['back'] ?? '') === 'cv'){
            header("Location: ../views/cv_detail_view.php?id=".(int)$cv_id."#matching_section");
        } else {
            header("Location: ../views/oportunidades_detail_view.php?id=".$opportunity_id);
        }
        exit();
    }

//Función para permitir que en cada oportunidad_detail_view.php se vea que cv a aplicado :D
    public function getByOpportunity($opportunity_id){
        return $this->model->getApplicationsByOpportunity($opportunity_id);
    }


}

if(isset($_GET['action'])){

    $controller = new ApplicationController();

    switch($_GET['action']){

        case "apply":
            $controller->apply();
            break;

        default:
            echo "Acción no válida";
            break;
    }
}