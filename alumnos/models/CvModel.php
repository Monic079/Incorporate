<?php
require_once "../config/database.php";

class CvModel {
    private $conn;

    public function __construct(){
        $db = new Database();
        $this->conn = $db->connect();
    }

public function create($data){

    try{
        $this->conn->beginTransaction();

        // 🔹 1. Obtener student_id desde user_id
        $stmt = $this->conn->prepare("SELECT id FROM students WHERE user_id = ?");
        $stmt->execute([$data['user_id']]);
        $student = $stmt->fetch(PDO::FETCH_ASSOC);

        if(!$student){
            throw new Exception("Student no encontrado");
        }

        $student_id = $student['id'];

        // 🔹 2. INSERT CV
        $sql = "INSERT INTO cvs 
            (student_id, photo, full_name, phone, email, gender, age, level, year, resume)
            VALUES 
            (:student_id, :photo, :full_name, :phone, :email, :gender, :age, :level, :year, :resume)";

        $cleanData = [
            ':student_id' => $student_id,
            ':photo'      => $data['photo'] ?? null,
            ':full_name'  => $data['full_name'],
            ':phone'      => $data['phone'] ?? null,
            ':email'      => $data['email'],
            ':gender'     => $data['gender'],
            ':age'        => $data['edad'] ?? null,
            ':level'      => $data['level'],
            ':year'       => $data['year'] ?? null,
            ':resume'     => $data['resume']
        ];

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($cleanData);

        $cv_id = $this->conn->lastInsertId();

        // 🔹 3. Relaciones
        $this->insertRelations($cv_id, $data);

        $this->conn->commit();

        return $cv_id;

    }catch(Exception $e){
        $this->conn->rollBack();
        throw $e;
    }
}

public function update($id, $data){

    try{
        $this->conn->beginTransaction();

        $sql = "UPDATE cvs SET
            full_name = :full_name,
            phone = :phone,
            email = :email,
            gender = :gender,
            age = :age,
            level = :level,
            year = :year,
            resume = :resume,
            photo = :photo
        WHERE id = :id";

        $cleanData = [
            ':id'        => $id,
            ':full_name' => $data['full_name'],
            ':phone'     => $data['phone'] ?? null,
            ':email'     => $data['email'],
            ':gender'    => $data['gender'],
            ':age'       => $data['edad'] ?? null,
            ':level'     => $data['level'],
            ':year'      => $data['year'] ?? null,
            ':resume'    => $data['resume'],
            ':photo' => $data['photo'] ?? null
        ];

        $stmt = $this->conn->prepare($sql);
        $stmt->execute($cleanData);

        // 🔥 MISMA LÓGICA QUE OPORTUNIDADES
        $this->deleteRelations($id);
        $this->insertRelations($id, $data);

        $this->conn->commit();

    }catch(Exception $e){
        $this->conn->rollBack();
        throw $e;
    }
}

public function delete($id){
    $stmt = $this->conn->prepare("DELETE FROM cvs WHERE id = ?");
    return $stmt->execute([$id]);
}

private function insertRelations($cv_id, $data){

    // 🔹 CAREERS
    if(!empty($data['careers'])){
        foreach($data['careers'] as $c){
            $this->conn->prepare("INSERT INTO cv_careers VALUES (?,?)")
                ->execute([$cv_id, $c]);
        }
    }

    // 🔹 SKILLS
    if(!empty($data['skills'])){
        foreach($data['skills'] as $s){
            $this->conn->prepare("INSERT INTO cv_skills VALUES (?,?)")
                ->execute([$cv_id, $s]);
        }
    }

    // 🔹 LINKS
    if(!empty($data['link_type'])){
        foreach($data['link_type'] as $i => $type){
            $url = $data['link_url'][$i] ?? null;

            if($url){
                $this->conn->prepare("
                    INSERT INTO cv_links (cv_id, type, url)
                    VALUES (?,?,?)
                ")->execute([$cv_id, $type, $url]);
            }
        }
    }

    // 🔹 EXPERIENCES
    if(!empty($data['exp_company'])){
        foreach($data['exp_company'] as $i => $company){

            if(empty($company)) continue;

            $this->conn->prepare("
                INSERT INTO experiences 
                (cv_id, company_name, position, start_date, end_date, description)
                VALUES (?,?,?,?,?,?)
            ")->execute([
                $cv_id,
                $company,
                $data['exp_position'][$i] ?? null,
                $data['exp_start'][$i] ?? null,
                $data['exp_end'][$i] ?? null,
                $data['exp_desc'][$i] ?? null
            ]);
        }
    }

    // 🔹 EDUCATION
    if(!empty($data['edu_institution'])){
        foreach($data['edu_institution'] as $i => $inst){

            if(empty($inst)) continue;

            $this->conn->prepare("
                INSERT INTO education
                (cv_id, institution, program_name, start_date, end_date)
                VALUES (?,?,?,?,?)
            ")->execute([
                $cv_id,
                $inst,
                $data['edu_program'][$i] ?? null,
                $data['edu_start'][$i] ?? null,
                $data['edu_end'][$i] ?? null
            ]);
        }
    }
}

private function deleteRelations($cv_id){

    $this->conn->prepare("DELETE FROM cv_careers WHERE cv_id=?")->execute([$cv_id]);
    $this->conn->prepare("DELETE FROM cv_skills WHERE cv_id=?")->execute([$cv_id]);
    $this->conn->prepare("DELETE FROM cv_links WHERE cv_id=?")->execute([$cv_id]);
    $this->conn->prepare("DELETE FROM experiences WHERE cv_id=?")->execute([$cv_id]);
    $this->conn->prepare("DELETE FROM education WHERE cv_id=?")->execute([$cv_id]);
}

/*
public function getById($id){
    // 🔹 CV BASE
    $stmt = $this->conn->prepare("
        SELECT * FROM cvs WHERE id = ?
    ");
    $stmt->execute([$id]);
    $cv = $stmt->fetch(PDO::FETCH_ASSOC);

    if(!$cv) return null;

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
}*/

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


    // =========================
    // LISTAS PARA FORM(YA ADAPTADO PARA ALUMNOS)
    // =========================
    public function getCareers(){
        $stmt = $this->conn->query("SELECT * FROM careers ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

    public function getSkills(){
        $stmt = $this->conn->query("SELECT * FROM skills ORDER BY name");
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }


        //obtener oportunidades para la feed
   /* public function getAll(){
        $stmt = $this->conn->prepare("
            SELECT o.id, o.title, o.type_opor, o.salary_min, o.salary_max,
                o.remuneration, o.salary_visible, o.modality, o.deadline
            FROM opportunities o
            ORDER BY o.created_at DESC
        ");

        $stmt->execute();

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }*/
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

            -- total aplicaciones
            (SELECT COUNT(*) 
             FROM applications a 
             WHERE a.opportunity_id = o.id) AS total_applications,

            -- aceptados
            (SELECT COUNT(*) 
             FROM applications a 
             WHERE a.opportunity_id = o.id 
             AND a.status = 'aceptado') AS accepted_count

        FROM opportunities o
        ORDER BY o.created_at DESC
    ");

    $stmt->execute();
    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

    //NUEVO MÉTODO PARA DARLE FUNCIONALIDAD AL BOTÓN APPLY
    public function getByUserId($user_id){
        $stmt = $this->conn->prepare("
            SELECT c.*
            FROM cvs c
            JOIN students s ON c.student_id = s.id
            WHERE s.user_id = ?
        ");
        $stmt->execute([$user_id]);
        return $stmt->fetchAll(PDO::FETCH_ASSOC);
    }

//Funcion para traer todos los cvs y mostrarlos en cvs_view.php creo 
    /*public function getByUser($user_id){
        $stmt = $this->conn->prepare("
            SELECT 
                c.id,
                c.full_name,
                c.photo,

                -- carrera (una)
                cr.name AS career,

                -- conteos
                COUNT(DISTINCT cs.skill_id) AS skills_count,
                COUNT(DISTINCT cl.id) AS links_count

            FROM cvs c

            INNER JOIN students s ON c.student_id = s.id
            INNER JOIN users u ON s.user_id = u.id

            LEFT JOIN cv_careers cc ON c.id = cc.cv_id
            LEFT JOIN careers cr ON cc.career_id = cr.id

            LEFT JOIN cv_skills cs ON c.id = cs.cv_id
            LEFT JOIN cv_links cl ON c.id = cl.cv_id

            WHERE u.id = :user_id

            GROUP BY c.id

            ORDER BY c.created_at DESC
        ");

        $stmt->execute(['user_id' => $user_id]);

        return $stmt->fetchAll(PDO::FETCH_ASSOC);
}*/
public function getByUser($user_id){
    $stmt = $this->conn->prepare("
        SELECT 
            c.id,
            c.full_name,
            c.photo,

            -- carrera (una)
            cr.name AS career,

            -- skills (nombres separados por coma)
            GROUP_CONCAT(DISTINCT sk.name SEPARATOR '||') AS skills,

            -- links: tipo + url
            GROUP_CONCAT(DISTINCT CONCAT(cl.type, '::', cl.url) SEPARATOR '||') AS links

        FROM cvs c

        INNER JOIN students s ON c.student_id = s.id
        INNER JOIN users u ON s.user_id = u.id

        LEFT JOIN cv_careers cc ON c.id = cc.cv_id
        LEFT JOIN careers cr ON cc.career_id = cr.id

        LEFT JOIN cv_skills cs ON c.id = cs.cv_id
        LEFT JOIN skills sk ON cs.skill_id = sk.id

        LEFT JOIN cv_links cl ON c.id = cl.cv_id

        WHERE u.id = :user_id

        GROUP BY c.id

        ORDER BY c.created_at DESC
    ");

    $stmt->execute(['user_id' => $user_id]);

    return $stmt->fetchAll(PDO::FETCH_ASSOC);
}

}