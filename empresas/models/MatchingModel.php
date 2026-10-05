<?php
require_once "../config/database.php";

class MatchingModel {
    private $conn;

    public function __construct(){
        $db = new Database();
        $this->conn = $db->connect();
    }

    // Perfil de lo que busca la oportunidad
    public function getOpportunityProfile($id){

        $stmt = $this->conn->prepare("SELECT id, title, company_user_id FROM opportunities WHERE id = ?");
        $stmt->execute([$id]);
        $opp = $stmt->fetch(PDO::FETCH_ASSOC);
        if(!$opp) return null;

        // skills requeridas: id => nombre
        $stmt = $this->conn->prepare("
            SELECT s.id, s.name
            FROM opportunity_skills os
            JOIN skills s ON s.id = os.skill_id
            WHERE os.opportunity_id = ?
        ");
        $stmt->execute([$id]);
        $opp['skills'] = [];
        foreach($stmt->fetchAll(PDO::FETCH_ASSOC) as $r){
            $opp['skills'][(int)$r['id']] = $r['name'];
        }

        // carreras y sus categorías
        $stmt = $this->conn->prepare("
            SELECT ca.id, ca.category_id
            FROM opportunity_careers oc
            JOIN careers ca ON ca.id = oc.career_id
            WHERE oc.opportunity_id = ?
        ");
        $stmt->execute([$id]);
        $rows = $stmt->fetchAll(PDO::FETCH_ASSOC);
        $opp['career_ids']   = array_map('intval', array_column($rows, 'id'));
        $opp['category_ids'] = array_values(array_unique(array_map('intval', array_filter(array_column($rows, 'category_id')))));

        // nivel
        $stmt = $this->conn->prepare("SELECT level, year FROM opportunity_levels WHERE opportunity_id = ? LIMIT 1");
        $stmt->execute([$id]);
        $lvl = $stmt->fetch(PDO::FETCH_ASSOC);
        $opp['level'] = $lvl['level'] ?? null;
        $opp['year']  = $lvl['year']  ?? null;

        return $opp;
    }

    // Todos los CVs con sus ids de skills / carreras / categorías
    public function getCandidates($opportunityId){
        $stmt = $this->conn->prepare("
            SELECT
                c.id, c.full_name, c.photo, c.level, c.year,
                (SELECT GROUP_CONCAT(cs.skill_id) FROM cv_skills cs WHERE cs.cv_id = c.id) AS skill_ids,
                (SELECT GROUP_CONCAT(cc.career_id) FROM cv_careers cc WHERE cc.cv_id = c.id) AS career_ids,
                (SELECT GROUP_CONCAT(DISTINCT ca.category_id)
                   FROM cv_careers cc JOIN careers ca ON ca.id = cc.career_id
                  WHERE cc.cv_id = c.id) AS category_ids,
                (SELECT MIN(ca.name)
                   FROM cv_careers cc JOIN careers ca ON ca.id = cc.career_id
                  WHERE cc.cv_id = c.id) AS career_name,
                (SELECT COUNT(*) FROM applications a
                  WHERE a.cv_id = c.id AND a.opportunity_id = ?) AS applied
            FROM cvs c
        ");
        $stmt->execute([$opportunityId]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }
}