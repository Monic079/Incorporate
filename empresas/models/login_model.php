<?php
require_once "../config/database.php";

class LoginModel {

    private $conn;

    public function __construct(){
        $db = new Database();
        $this->conn = $db->connect();
    }

    // 🔹 LOGIN
    public function login($email, $password){

        $sql = "SELECT * FROM users WHERE email = :email";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":email", $email);
        $stmt->execute();

        $user = $stmt->fetch(PDO::FETCH_ASSOC);

        if($user && password_verify($password, $user['password'])){
            return $user;
        }

        return false;
    }

    // 🔹 REGISTER
    public function registerCompanyUser($email, $password, $company_id, $new_company, $contact_name, $contact_position, $contact_phone){

        try{
            $this->conn->beginTransaction();

            // 1. Crear empresa si es nueva
            if($company_id == "new"){
                $sql = "INSERT INTO companies (name) VALUES (:name)";
                $stmt = $this->conn->prepare($sql);
                $stmt->bindParam(":name", $new_company);
                $stmt->execute();

                $company_id = $this->conn->lastInsertId();
            }

            // 2. Crear usuario
            $hashed = password_hash($password, PASSWORD_DEFAULT);

            $sql = "INSERT INTO users (email, password, role)
                    VALUES (:email, :password, 'company')";
            $stmt = $this->conn->prepare($sql);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":password", $hashed);
            $stmt->execute();

            $user_id = $this->conn->lastInsertId();

            // 3. Crear company_user
            $sql = "INSERT INTO company_users 
            (user_id, company_id, contact_name, contact_position, contact_email, contact_phone)
            VALUES (:user_id, :company_id, :name, :position, :email, :phone)";

            $stmt = $this->conn->prepare($sql);

            $stmt->bindParam(":user_id", $user_id);
            $stmt->bindParam(":company_id", $company_id);
            $stmt->bindParam(":name", $contact_name);
            $stmt->bindParam(":position", $contact_position);
            $stmt->bindParam(":email", $email);
            $stmt->bindParam(":phone", $contact_phone);

            $stmt->execute();

            $this->conn->commit();
            return true;

        } catch(Exception $e){
            $this->conn->rollback();

            echo "<pre>";
            echo "ERROR EN REGISTER:\n";
            echo $e->getMessage();
            exit;
            //return false;
        }
    }

    // 🔹 Obtener company_user por user_id
    public function getByUserId($user_id){
        $sql = "SELECT * FROM company_users WHERE user_id = :user_id LIMIT 1";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->execute();

        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 🔹 OBTENER EMPRESAS
    public function getCompanies(){
        $sql = "SELECT * FROM companies";
        $stmt = $this->conn->prepare($sql);
        $stmt->execute();
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}