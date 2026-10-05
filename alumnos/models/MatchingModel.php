<?php
require_once "../config/database.php";

class MatchingModel {
    private $conn;

    public function __construct(){
        $db = new Database();
        $this->conn = $db->connect();
    }

    // Perfil del CV (solo si pertenece al usuario logueado)
    public function getCvProfile($cvId, $userId){

        $stmt = $this->conn->prepare("
            SELECT c.id, c.full_name, c.level, c.year
            FROM cvs c
            JOIN students s ON s.id = c.student_id
            WHERE c.id = ? AND s.user_id = ?
        ");
        $stmt->execute([$cvId, $userId]);
        $cv = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$cv) return null;

        $stmt = $this->conn->prepare("SELECT skill_id FROM cv_skills WHERE cv_id = ?");
        $stmt->execute([$cvId]);
        $cv['skill_ids'] = array_map('intval', $stmt->fetchAll(PDO::FETCH_COLUMN));

        $stmt = $this->conn->prepare("
            SELECT ca.id, ca.category_id
            FROM cv_careers cc
            JOIN careers ca ON ca.id = cc.career_id
            WHERE cc.cv_id = ?
        ");
        $stmt->execute([$cvId]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $cv['career_ids']   = array_map('intval', array_column($rows, 'id'));
        $cv['category_ids'] = array_values(array_unique(array_map('intval', array_filter(array_column($rows, 'category_id')))));

        return $cv;
    }

    // Oportunidades abiertas con lo que piden
    public function getOpenOpportunities($cvId){
        $stmt = $this->conn->prepare("
            SELECT
                o.id, o.title, o.type_opor, o.modality, o.deadline,
                co.name AS company,

                (SELECT GROUP_CONCAT(CONCAT(s.id, ':', s.name) SEPARATOR '||')
                   FROM opportunity_skills os JOIN skills s ON s.id = os.skill_id
                  WHERE os.opportunity_id = o.id) AS skills_raw,

                (SELECT GROUP_CONCAT(oc.career_id)
                   FROM opportunity_careers oc
                  WHERE oc.opportunity_id = o.id) AS career_ids,

                (SELECT GROUP_CONCAT(DISTINCT ca.category_id)
                   FROM opportunity_careers oc JOIN careers ca ON ca.id = oc.career_id
                  WHERE oc.opportunity_id = o.id) AS category_ids,

                (SELECT ol.level FROM opportunity_levels ol
                  WHERE ol.opportunity_id = o.id LIMIT 1) AS req_level,

                (SELECT ol.year FROM opportunity_levels ol
                  WHERE ol.opportunity_id = o.id LIMIT 1) AS req_year,

                (SELECT COUNT(*) FROM applications a
                  WHERE a.opportunity_id = o.id AND a.cv_id = ?) AS applied

            FROM opportunities o
            LEFT JOIN company_users cu ON cu.id = o.company_user_id
            LEFT JOIN companies co     ON co.id = cu.company_id
            WHERE (o.deadline IS NULL OR o.deadline >= CURDATE())
              AND o.vacancies - (SELECT COUNT(*) FROM applications a2
                                  WHERE a2.opportunity_id = o.id
                                    AND a2.status = 'aceptado') > 0
        ");
        $stmt->execute([$cvId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}