<?php
require_once "../config/database.php";

class OpportunityModel {
    private $conn;

    public function __construct(){
        $db = new Database();
        $this->conn = $db->connect();
    }

    // =========================
    // INSERT
    // =========================
    /*
    public function create($data){
        $sql = "INSERT INTO opportunities 
        (company_user_id, title, type_opor, salary_min, salary_max, remuneration, salary_visible, vacancies, deadline, functions, modality, schedule)
        VALUES (:company_user_id, :title, :type_opor, :salary_min, :salary_max, :remuneration, :salary_visible, :vacancies, :deadline, :functions, :modality, :schedule)";

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($data);

        $opportunity_id = $this->conn->lastInsertId();

        $this->insertRelations($opportunity_id, $data);

        return $opportunity_id;
    }*/
        public function create($data) {
    // 1. Definimos exactamente qué columnas van a la tabla 'opportunities'
    $sql = "INSERT INTO opportunities 
            (company_user_id, title, type_opor, salary_min, salary_max, remuneration, salary_visible, vacancies, deadline, functions, modality, schedule)
            VALUES 
            (:company_user_id, :title, :type_opor, :salary_min, :salary_max, :remuneration, :salary_visible, :vacancies, :deadline, :functions, :modality, :schedule)";

    // 2. Creamos un array limpio solo con esos datos
    // Esto evita que campos como 'careers' o 'skills' rompan el execute
    $cleanData = [
        ':company_user_id' => $data['company_user_id'],
        ':title'           => $data['title'],
        ':type_opor'        => $data['type_opor'],
        ':salary_min'      => $data['salary_min'] ?? null,
        ':salary_max'      => $data['salary_max'] ?? null,
        ':remuneration'    => $data['remuneration'] ?? null,
        ':salary_visible'  => isset($data['salary_visible']) ? 1 : 0,
        ':vacancies'       => $data['vacancies'],
        ':deadline'        => $data['deadline'],
        ':functions'       => $data['functions'],
        ':modality'        => $data['modality'],
        ':schedule'        => $data['schedule']
    ];

    $stmt = $this->conn->prepare($sql);
    
    // 3. Ejecutamos con el array limpio
    $stmt->execute($cleanData);

    $opportunity_id = $this->conn->lastInsertId();

    // 4. Pasamos el $data original a las relaciones (porque ahí sí ocupamos careers, skills, etc)
    $this->insertRelations($opportunity_id, $data);

    return $opportunity_id;
}

    // =========================
    // UPDATE
    // =========================
    /*public function update($id, $data){
        $sql = "UPDATE opportunities SET
            title=:title,
            type_opor=:type_opor,
            salary_min=:salary_min,
            salary_max=:salary_max,
            remuneration=:remuneration,
            salary_visible=:salary_visible,
            vacancies=:vacancies,
            deadline=:deadline,
            functions=:functions,
            modality=:modality,
            schedule=:schedule
        WHERE id=:id";

        $data['id'] = $id;

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($data);

        // limpiar relaciones
        $this->deleteRelations($id);
        $this->insertRelations($id, $data);
    }*/

    public function update($id, $data){

        $sql = "UPDATE opportunities SET
            title=:title,
            type_opor=:type_opor,
            salary_min=:salary_min,
            salary_max=:salary_max,
            remuneration=:remuneration,
            salary_visible=:salary_visible,
            vacancies=:vacancies,
            deadline=:deadline,
            functions=:functions,
            modality=:modality,
            schedule=:schedule
        WHERE id=:id";

        $cleanData = [
            ':id' => $id,
            ':title' => $data['title'],
            ':type_opor' => $data['type_opor'],
            ':salary_min' => $data['salary_min'] ?? null,
            ':salary_max' => $data['salary_max'] ?? null,
            ':remuneration' => $data['remuneration'] ?? null,
            ':salary_visible' => $data['salary_visible'],
            ':vacancies' => $data['vacancies'],
            ':deadline' => $data['deadline'],
            ':functions' => $data['functions'],
            ':modality' => $data['modality'],
            ':schedule' => $data['schedule']
        ];

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($cleanData);

        // 🔥 IMPORTANTE
        $this->deleteRelations($id);
        $this->insertRelations($id, $data);
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
    // DELETE
    // =========================
    public function delete($id){
        $stmt = $this->conn->prepare("DELETE FROM opportunities WHERE id=?");
        return $stmt->execute([$id]);
    }

    // =========================
    // RELACIONES
    // =========================
    private function insertRelations($id, $data){

        // careers
        if(!empty($data['careers'])){
            foreach($data['careers'] as $c){
                $this->conn->prepare("INSERT INTO opportunity_careers VALUES (?,?)")
                    ->execute([$id, $c]);
            }
        }

        // skills
        if(!empty($data['skills'])){
            foreach($data['skills'] as $s){
                $this->conn->prepare("INSERT INTO opportunity_skills VALUES (?,?)")
                    ->execute([$id, $s]);
            }
        }

        // level
        if(!empty($data['level'])){
            $this->conn->prepare("INSERT INTO opportunity_levels (opportunity_id, level, year) VALUES (?,?,?)")
                ->execute([$id, $data['level'], $data['year'] ?? null]);
        }
    }

    private function deleteRelations($id){
        $this->conn->prepare("DELETE FROM opportunity_careers WHERE opportunity_id=?")->execute([$id]);
        $this->conn->prepare("DELETE FROM opportunity_skills WHERE opportunity_id=?")->execute([$id]);
        $this->conn->prepare("DELETE FROM opportunity_levels WHERE opportunity_id=?")->execute([$id]);
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


    
    //obtener oportunidades para la home
    /*public function getAll(){
        $stmt = $this->conn->prepare("
            SELECT o.id, o.title, o.type_opor, o.salary_min, o.salary_max,
                o.remuneration, o.salary_visible, o.modality, o.deadline, o.vacancies
            FROM opportunities o
            ORDER BY o.created_at DESC
        ");

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }*/
        /*
        public function getAll(){
    $stmt = $this->conn->prepare("
        SELECT 
            o.id, 
            o.title, 
            o.type_opor, 
            o.salary_min, 
            o.salary_max,
            o.remuneration, 
            o.salary_visible, 
            o.modality, 
            o.deadline, 
            o.vacancies,

            -- TOTAL APLICACIONES
            (SELECT COUNT(*) 
             FROM applications a 
             WHERE a.opportunity_id = o.id) AS total_applications,

            -- TOTAL ACEPTADOS
            (SELECT COUNT(*) 
             FROM applications a 
             WHERE a.opportunity_id = o.id 
             AND a.status = 'aceptado') AS accepted_count

        FROM opportunities o
        ORDER BY o.created_at DESC
    ");

    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}*/

public function getByCompanyUser($company_user_id){
    $stmt = $this->conn->prepare("
        SELECT 
            o.id, 
            o.title, 
            o.type_opor, 
            o.salary_min, 
            o.salary_max,
            o.remuneration, 
            o.salary_visible, 
            o.modality, 
            o.deadline, 
            o.vacancies,

            (SELECT COUNT(*) 
             FROM applications a 
             WHERE a.opportunity_id = o.id) AS total_applications,

            (SELECT COUNT(*) 
             FROM applications a 
             WHERE a.opportunity_id = o.id 
             AND a.status = 'aceptado') AS accepted_count

        FROM opportunities o
        WHERE o.company_user_id = :company_user_id
        ORDER BY o.created_at DESC
    ");

    $stmt->bindParam(":company_user_id", $company_user_id);
    $stmt->execute();

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    public function getAllWithStats(){
    $stmt = $this->conn->prepare("
        SELECT 
            o.*,

            -- total aplicaciones
            (SELECT COUNT(*) FROM applications a WHERE a.opportunity_id = o.id) AS total_applications,

            -- aceptados
            (SELECT COUNT(*) FROM applications a WHERE a.opportunity_id = o.id AND a.status='aceptado') AS accepted_count

        FROM opportunities o
    ");

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}


}