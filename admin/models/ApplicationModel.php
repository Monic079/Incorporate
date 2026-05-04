<?php
require_once "../config/database.php";

class ApplicationModel {

    private $conn;

    public function __construct(){
        $db = new Database();
        $this->conn = $db->connect();
    }

    // 🔹 Obtener CV del estudiante
    public function getCvByUserId($user_id){
        $stmt = $this->conn->prepare("
            SELECT c.id
            FROM cvs c
            JOIN students s ON c.student_id = s.id
            WHERE s.user_id = ?
            LIMIT 1
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetch(PDO::FETCH_ASSOC);
    }

    // 🔹 Verificar si ya aplicó
    public function alreadyApplied($cv_id, $opportunity_id){
        $stmt = $this->conn->prepare("
            SELECT id FROM applications
            WHERE cv_id = ? AND opportunity_id = ?
        ");
        $stmt->execute([$cv_id, $opportunity_id]);
        return $stmt->fetch();
    }

    // 🔹 Crear aplicación
    public function apply($cv_id, $opportunity_id){
        $stmt = $this->conn->prepare("
            INSERT INTO applications (cv_id, opportunity_id)
            VALUES (?, ?)
        ");
        return $stmt->execute([$cv_id, $opportunity_id]);
    }

    //vER QUIÉN CHUCHAS APLICO 
    public function getApplicationsByCv($cv_id){
        $stmt = $this->conn->prepare("
            SELECT o.*, a.status
            FROM applications a
            JOIN opportunities o ON a.opportunity_id = o.id
            WHERE a.cv_id = ?
        ");
        $stmt->execute([$cv_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

//arreglar lo de aplicar
public function getCvByIdAndUser($cv_id, $user_id){

    $stmt = $this->conn->prepare("
        SELECT c.*
        FROM cvs c
        JOIN students s ON c.student_id = s.id
        WHERE c.id = ? AND s.user_id = ?
    ");

    $stmt->execute([$cv_id, $user_id]);

    return $stmt->fetch(PDO::FETCH_ASSOC);
}

//Debería obtener cvs para que se muestren en oportunidades_detail_view.php
public function getApplicationsByOpportunity($opportunity_id){

    $stmt = $this->conn->prepare("
        SELECT 
            a.id as application_id,
            a.status,
            a.applied_at,

            c.id as cv_id,
            c.full_name,
            c.photo,
            c.level,
            c.year

        FROM applications a
        JOIN cvs c ON a.cv_id = c.id
        WHERE a.opportunity_id = ?
        ORDER BY a.applied_at DESC
    ");

    $stmt->execute([$opportunity_id]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

//_____Probando a ver si furula


}