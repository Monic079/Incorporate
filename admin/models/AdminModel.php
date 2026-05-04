<?php
require_once "../config/database.php";

class AdminModel {
    private $conn;

    public function __construct(){
        $db = new Database();
        $this->conn = $db->connect();
    }

    // INSERTS
    public function insertCareer($name, $category_id){
        $stmt = $this->conn->prepare("INSERT INTO careers (name, category_id) VALUES (?, ?)");
        $stmt->execute([$name, $category_id]);
    }

    public function insertSkill($name, $category_id){
        $stmt = $this->conn->prepare("INSERT INTO skills (name, category_id) VALUES (?, ?)");
        $stmt->execute([$name, $category_id]);
    }

    public function insertCompany($name){
        $stmt = $this->conn->prepare("INSERT INTO companies (name) VALUES (?)");
        $stmt->execute([$name]);
    }

    public function beginTransaction(){
    $this->conn->beginTransaction();
}

public function commit(){
    $this->conn->commit();
}

public function rollback(){
    $this->conn->rollBack();
}

// 🔍 verificar existencia
public function studentExists($user_id){
    $stmt = $this->conn->prepare("SELECT id FROM students WHERE user_id=?");
    $stmt->execute([$user_id]);
    return $stmt->fetch();
}

public function companyUserExists($user_id){
    $stmt = $this->conn->prepare("SELECT id FROM company_users WHERE user_id=?");
    $stmt->execute([$user_id]);
    return $stmt->fetch();
}




    public function insertUser($email, $password, $role){
        $stmt = $this->conn->prepare("INSERT INTO users (email, password, role) VALUES (?, ?, ?)");
        $stmt->execute([$email, $password, $role]);
    }

        

    // UPDATE
    public function updateRole($user_id, $role){
        $stmt = $this->conn->prepare("UPDATE users SET role=? WHERE id=?");
        $stmt->execute([$role, $user_id]);
    }

    
public function getLastUserId(){
    return $this->conn->lastInsertId();
}



// 🔹 CREAR STUDENT
public function createStudent($user_id){
    $stmt = $this->conn->prepare("
        INSERT INTO students (user_id) VALUES (?)
    ");
    $stmt->execute([$user_id]);
}

// 🔹 CREAR COMPANY_USER
public function createCompanyUser($user_id, $company_id, $name, $position, $email, $phone){
    $stmt = $this->conn->prepare("
        INSERT INTO company_users 
        (user_id, company_id, contact_name, contact_position, contact_email, contact_phone)
        VALUES (?, ?, ?, ?, ?, ?)
    ");
    $stmt->execute([$user_id, $company_id, $name, $position, $email, $phone]);
}
// 🔹 ELIMINAR RELACIONES
public function deleteStudent($user_id){
    $stmt = $this->conn->prepare("DELETE FROM students WHERE user_id=?");
    $stmt->execute([$user_id]);
}

public function deleteCompanyUser($user_id){
    $stmt = $this->conn->prepare("DELETE FROM company_users WHERE user_id=?");
    $stmt->execute([$user_id]);
}

public function deleteUser($user_id){
    $this->deleteStudent($user_id);
    $this->deleteCompanyUser($user_id);

    $stmt = $this->conn->prepare("DELETE FROM users WHERE id=?");
    $stmt->execute([$user_id]);
}


public function getUsers(){
    return $this->conn->query("
        SELECT u.*, cu.company_id
        FROM users u
        LEFT JOIN company_users cu ON u.id = cu.user_id
    ")->fetchAll(PDO::FETCH_ASSOC);
}

    // GETS
    public function getCareers(){
        return $this->conn->query("SELECT * FROM careers")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSkills(){
        return $this->conn->query("SELECT * FROM skills")->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCareerCategories(){
    return $this->conn->query("SELECT * FROM career_categories")
        ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSkillCategories(){
        return $this->conn->query("SELECT * FROM skill_categories")
            ->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getCompanies(){
        return $this->conn->query("SELECT * FROM companies")->fetchAll(PDO::FETCH_ASSOC);
    }


}