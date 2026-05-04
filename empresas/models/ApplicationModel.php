<?php
require_once "../config/database.php";

class ApplicationModel {

    private $conn;

    public function __construct(){
        $db = new Database();
        $this->conn = $db->connect();
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


//FUNCIÓN PARA CAMBIAR EL ESTADO DE UNA APLICACIÓN DE UN CV
public function updateStatus($application_id, $new_status, $company_user_id){
    try {
        $this->conn->beginTransaction();

        // 1. Obtener estado actual + opportunity
        $stmt = $this->conn->prepare("
            SELECT status, opportunity_id 
            FROM applications 
            WHERE id = ?
        ");
        $stmt->execute([$application_id]);
        $app = $stmt->fetch(PDO::FETCH_ASSOC);

        if(!$app) throw new Exception("Aplicación no encontrada");

        $old_status = $app['status'];
        $opportunity_id = $app['opportunity_id'];

        // 2. Validación: no aceptar si ya no hay vacantes
        if($new_status === 'aceptado'){
            $available = $this->getAvailableVacancies($opportunity_id);

            if($available <= 0){
                throw new Exception("No hay vacantes disponibles");
            }
        }

        // 3. Actualizar estado
        $stmt = $this->conn->prepare("
            UPDATE applications 
            SET status = ? 
            WHERE id = ?
        ");
        $stmt->execute([$new_status, $application_id]);

        // 4. Guardar historial
        $stmt = $this->conn->prepare("
            INSERT INTO application_status_history 
            (application_id, changed_by, old_status, new_status)
            VALUES (?, ?, ?, ?)
        ");
        $stmt->execute([$application_id, $company_user_id, $old_status, $new_status]);

        $this->conn->commit();
        return true;

    } catch(Exception $e){
        $this->conn->rollBack();
        throw $e;
    }
}

//FUNCIÓN PA CONTAR CUÁNTA GENTE HA SIDO ACEPTADA Y MOSTRAR EL NÚM DE VACANTES QUE SIGUE DISPO
public function countAccepted($opportunity_id){
    $stmt = $this->conn->prepare("
        SELECT COUNT(*) 
        FROM applications 
        WHERE opportunity_id = ? 
        AND status = 'aceptado'
    ");
    $stmt->execute([$opportunity_id]);
    return $stmt->fetchColumn();
}

//NO SÉ QUE HACE
public function getAvailableVacancies($opportunity_id){
    // total
    $stmt = $this->conn->prepare("
        SELECT vacancies 
        FROM opportunities 
        WHERE id = ?
    ");
    $stmt->execute([$opportunity_id]);
    $total = $stmt->fetchColumn();

    // aceptados
    $accepted = $this->countAccepted($opportunity_id);

    return $total - $accepted;
}

public function countApplications($opportunity_id){
    $stmt = $this->conn->prepare("
        SELECT COUNT(*) 
        FROM applications 
        WHERE opportunity_id = ?
    ");
    $stmt->execute([$opportunity_id]);
    return $stmt->fetchColumn();
}

}