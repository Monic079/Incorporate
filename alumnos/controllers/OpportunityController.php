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