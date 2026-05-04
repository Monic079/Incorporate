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

    // 🔹 REGISTER STUDENT
   public function registerStudent($email, $password, $institutional_email){

    try{
        $this->conn->beginTransaction();

        // 1. Crear usuario
        $hashed = password_hash($password, PASSWORD_DEFAULT);

        $sql = "INSERT INTO users (email, password, role)
                VALUES (:email, :password, 'student')";
        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":email", $email);
        $stmt->bindParam(":password", $hashed);
        $stmt->execute();

        $user_id = $this->conn->lastInsertId();

        // 2. Crear estudiante
        $sql = "INSERT INTO students (user_id, institutional_email)
                VALUES (:user_id, :institutional_email)";

        $stmt = $this->conn->prepare($sql);
        $stmt->bindParam(":user_id", $user_id);
        $stmt->bindParam(":institutional_email", $institutional_email);
        $stmt->execute();

        $this->conn->commit();
        return true;

        } catch(Exception $e){
            $this->conn->rollback();
            //return false;
            die("ERROR SQL: " . $e->getMessage());
        }
    }   

    
public function getStudentByUserId($user_id){

    $sql = "SELECT * FROM students WHERE user_id = :user_id";
    $stmt = $this->conn->prepare($sql);
    $stmt->bindParam(":user_id", $user_id);
    $stmt->execute();

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

}