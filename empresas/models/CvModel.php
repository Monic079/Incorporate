<?php
require_once "../config/database.php";

class CvModel {
    private $conn;

    public function __construct(){
        $db = new Database();
        $this->conn = $db->connect();
    }

public function getById($id){

    // 🔹 CV BASE
    $stmt = $this->conn->prepare("
        SELECT * FROM cvs WHERE id = ?
    ");
    $stmt->execute([$id]);
    $cv = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$cv) return null;

    // 🔹 CAREERS
    $stmt = $this->conn->prepare("
        SELECT c.id, c.name
        FROM cv_careers cc
        JOIN careers c ON cc.career_id = c.id
        WHERE cc.cv_id = ?
    ");
    $stmt->execute([$id]);
    $cv['careers'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $cv['careers_ids'] = array_column($cv['careers'], 'id');

    // 🔹 SKILLS
    $stmt = $this->conn->prepare("
        SELECT s.id, s.name
        FROM cv_skills cs
        JOIN skills s ON cs.skill_id = s.id
        WHERE cs.cv_id = ?
    ");
    $stmt->execute([$id]);
    $cv['skills'] = $stmt->fetchAll(PDO::FETCH_ASSOC);
    $cv['skills_ids'] = array_column($cv['skills'], 'id');

    // 🔹 LINKS
    $stmt = $this->conn->prepare("
        SELECT * FROM cv_links WHERE cv_id = ?
    ");
    $stmt->execute([$id]);
    $cv['links'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 🔹 EXPERIENCIA
    $stmt = $this->conn->prepare("
        SELECT * FROM experiences WHERE cv_id = ?
    ");
    $stmt->execute([$id]);
    $cv['experiences'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    // 🔹 EDUCACIÓN
    $stmt = $this->conn->prepare("
        SELECT * FROM education WHERE cv_id = ?
    ");
    $stmt->execute([$id]);
    $cv['education'] = $stmt->fetchAll(PDO::FETCH_ASSOC);

    return $cv;
}

public function getAllCvs(){

    $stmt = $this->conn->prepare("
        SELECT 
            c.id,
            c.full_name,
            c.photo,

            -- UNA carrera (si hay varias, toma cualquiera)
            MIN(cr.name) AS career,

            -- skills concatenadas
            GROUP_CONCAT(DISTINCT sk.name SEPARATOR '||') AS skills,

            -- links concatenados
            GROUP_CONCAT(DISTINCT CONCAT(cl.type, '::', cl.url) SEPARATOR '||') AS links

        FROM cvs c

        LEFT JOIN cv_careers cc ON c.id = cc.cv_id
        LEFT JOIN careers cr ON cc.career_id = cr.id

        LEFT JOIN cv_skills cs ON c.id = cs.cv_id
        LEFT JOIN skills sk ON cs.skill_id = sk.id

        LEFT JOIN cv_links cl ON c.id = cl.cv_id

        GROUP BY c.id

        ORDER BY c.created_at DESC
    ");

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}
}