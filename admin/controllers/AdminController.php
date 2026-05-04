<?php
require_once "../models/AdminModel.php";

class AdminController {
    private $model;

    public function __construct(){
        $this->model = new AdminModel();
    }

    // 🔹 CREAR CAREER
    public function createCareer(){
        if($_POST){
            $name = $_POST['name'];
            $category_id = $_POST['category_id'];
            $this->model->insertCareer($name, $category_id);
            //header("Location: ../views/home_view.php");
            header("Location: ../views/home_view.php?success=career");
        }
    }

    // 🔹 CREAR SKILL
    public function createSkill(){
        if($_POST){
            $name = $_POST['name'];
            $category_id = $_POST['category_id'];
            $this->model->insertSkill($name, $category_id);
             header("Location: ../views/home_view.php?success=skill");
        }
    }

    // 🔹 CREAR EMPRESA
    public function createCompany(){
        if($_POST){
            $name = $_POST['name'];
            $this->model->insertCompany($name);
            header("Location: ../views/home_view.php?success=company");
        }
    }

    // 🔹 CREAR USUARIO
public function createUser(){
    if($_POST){
        $email = $_POST['email'];
        $password = password_hash($_POST['password'], PASSWORD_DEFAULT);
        $role = $_POST['role'];
        $company_id = $_POST['company_id'] ?? null;
        $contact_name = $_POST['contact_name'] ?? null;
        $contact_position = $_POST['contact_position'] ?? null;
        $contact_email = $_POST['email'] ?? null;
        $contact_phone = $_POST['contact_phone'] ?? null;

        // 1. crear usuario
        $this->model->insertUser($email, $password, $role);

        // 2. obtener ID recién creado
        $user_id = $this->model->getLastUserId();

        // 3. crear relación
        if($role == "student"){
            $this->model->createStudent($user_id);
        }

        if($role == "company"){
            if(!$company_id){
                header("Location: ../views/home_view.php?error=company_required");
                return;
            }

            $this->model->createCompanyUser(
                $user_id,
                $company_id,
                $contact_name,
                $contact_position,
                $contact_email,
                $contact_phone
            );
        }

        header("Location: ../views/home_view.php?success=user");
    }
}
    

public function changeRole(){
    if($_POST){
        $user_id = $_POST['user_id'];
        $new_role = $_POST['role'];
        $company_id = $_POST['company_id'] ?? null;

        try {
            $this->model->beginTransaction();

            // eliminar relaciones
            $this->model->deleteStudent($user_id);
            $this->model->deleteCompanyUser($user_id);

            // actualizar rol
            $this->model->updateRole($user_id, $new_role);

            // crear nuevas relaciones
            if($new_role == "student"){
                if(!$this->model->studentExists($user_id)){
                    $this->model->createStudent($user_id);
                }
            }

            if($new_role == "company"){
                if(!$company_id){
                    throw new Exception("company_required");
                }

                if(!$this->model->companyUserExists($user_id)){
                    $this->model->createCompanyUser($user_id, $company_id);
                }
            }

            $this->model->commit();

            header("Location: ../views/home_view.php?success=role");

        } catch(Exception $e){
            $this->model->rollback();
            header("Location: ../views/home_view.php?error=".$e->getMessage());
        }
    }
}

public function deleteUser(){
    if($_POST){
        $user_id = $_POST['user_id'];

        $this->model->deleteUser($user_id);

        header("Location: ../views/home_view.php?success=delete");
    }
}


    // 🔹 LISTADOS
    public function getData(){
        return [
            "careers" => $this->model->getCareers(),
            "skills" => $this->model->getSkills(),
            "career_categories" => $this->model->getCareerCategories(),
            "skill_categories" => $this->model->getSkillCategories(),
            "companies" => $this->model->getCompanies(),
            "users" => $this->model->getUsers()
        ];
    }
}

// 🔥 ROUTER SIMPLE
$controller = new AdminController();

if(isset($_GET['action'])){
    switch($_GET['action']){
        case "career": $controller->createCareer(); break;
        case "skill": $controller->createSkill(); break;
        case "company": $controller->createCompany(); break;
        case "user": $controller->createUser(); break;
        case "role": $controller->changeRole(); break;
        case "delete": $controller->deleteUser(); break;
    }
}