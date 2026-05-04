<?php
require_once "../models/ApplicationModel.php";

class ApplicationController {

    private $model;

    public function __construct(){
        $this->model = new ApplicationModel();
    }


//Función para permtiir que en cada oportunidad_detail_view.php se vea que cv a aplicado :D
    public function getByOpportunity($opportunity_id){
        return $this->model->getApplicationsByOpportunity($opportunity_id);
    }

    // num de apply
    public function getCount($opportunity_id) {
        // Asumiendo que tu controlador instancia el modelo como $this->model
        return $this->model->countApplications($opportunity_id);
    }

    public function updateStatus(){
        session_start();
        $model = new ApplicationModel();

        $id = $_POST['application_id'];
        $status = $_POST['status'];
        $company_user_id = $_SESSION['company_user_id']; // importante

        try{
            $model->updateStatus($id, $status, $company_user_id);

            //header("Location: ../views/oportunidades_detail_view.php?id=".$_POST['opportunity_id']);
            header("Location: ../views/oportunidades_detail_view.php?id=".$_POST['opportunity_id']."&status_updated=1");
        } catch(Exception $e){
            echo $e->getMessage();
        }
    }


}

if(isset($_GET['action'])){

    $controller = new ApplicationController();

    switch($_GET['action']){

        case "apply":
            $controller->apply();
            break;

        case "updateStatus":
                    $controller->updateStatus();
                    break;
                    
        default:
            echo "Acción no válida";
            break;
    }
}