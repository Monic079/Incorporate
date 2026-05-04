<?php
require_once "../config/database.php";

class OpportunityModel {
    private $conn;

    public function __construct(){
        $db = new Database();
        $this->conn = $db->connect();
    }
    

public function getById($id){

    // 🔹 oportunidad base
    $stmt = $this->conn->prepare("
        SELECT o.*, cu.contact_name, cu.contact_email, cu.contact_phone, cu.contact_position
        FROM opportunities o
        LEFT JOIN company_users cu ON o.company_user_id = cu.id
        WHERE o.id = ?
    ");
    $stmt->execute([$id]);
    $op = $stmt->fetch(PDO::FETCH_ASSOC);

    // 🔹 careers (CON NOMBRE)
    $stmt = $this->conn->prepare("
        SELECT c.name, c.id   
        FROM opportunity_careers oc
        JOIN careers c ON oc.career_id = c.id
        WHERE oc.opportunity_id = ?
    ");
    $stmt->execute([$id]);
    $op['careers'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
    $op['careers_ids'] = array_column($op['careers'], 'id');

    // 🔹 skills (CON NOMBRE)
    $stmt = $this->conn->prepare("
        SELECT s.name, s.id  
        FROM opportunity_skills os
        JOIN skills s ON os.skill_id = s.id
        WHERE os.opportunity_id = ?
    ");
    $stmt->execute([$id]);
    $op['skills'] = $stmt->fetchAll(PDO::FETCH_COLUMN);
    //NUEVO TOO
    $op['skills_ids'] = array_column($op['skills'], 'id');
    // 🔹 level
    $stmt = $this->conn->prepare("
        SELECT level, year 
        FROM opportunity_levels
        WHERE opportunity_id = ?
        LIMIT 1
    ");
    $stmt->execute([$id]);
    $op['level_data'] = $stmt->fetch(PDO::FETCH_ASSOC);

    return $op;
}


    // =========================
    // LISTAS PARA FORM
    // =========================
    public function getCareers(){
        $stmt = $this->conn->query("SELECT * FROM careers ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSkills(){
        $stmt = $this->conn->query("SELECT * FROM skills ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }




}