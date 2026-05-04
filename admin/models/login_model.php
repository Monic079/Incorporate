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