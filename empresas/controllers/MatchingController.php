<?php
require_once "../models/MatchingModel.php";

class MatchingController {

    private $model;

    // Pesos (suman 100). Ajustalos a gusto.
    const W_SKILLS = 50;
    const W_CAREER = 30;
    const W_LEVEL  = 20;

    public function __construct(){
        $this->model = new MatchingModel();
    }

    private function ids($csv){
        return $csv === null || $csv === '' ? [] : array_map('intval', explode(',', $csv));
    }

    // Devuelve ['opp'=>..., 'criteria'=>[...], 'results'=>[...]]
    public function forOpportunity($opportunityId, $limit = 10){

        if(session_status() === PHP_SESSION_NONE) session_start();

        $opp = $this->model->getOpportunityProfile((int)$opportunityId);
        if(!$opp) return null;

        // Solo la empresa dueña de la oportunidad puede ver el matching
        if(isset($_SESSION['company_user_id']) && $opp['company_user_id'] != $_SESSION['company_user_id']){
            return null;
        }

        // Criterios que la oportunidad realmente define
        $criteria = [];
        if(!empty($opp['skills']))     $criteria[] = 'skills';
        if(!empty($opp['career_ids'])) $criteria[] = 'carrera';
        if(!empty($opp['level']))      $criteria[] = 'nivel';

        $out = ['opp' => $opp, 'criteria' => $criteria, 'results' => []];
        if(empty($criteria)) return $out;

        $results = [];
        foreach($this->model->getCandidates($opp['id']) as $cv){
            $results[] = $this->score($opp, $cv);
        }

        // Mayor porcentaje primero
        usort($results, fn($a, $b) => $b['score'] <=> $a['score']);

        $out['results'] = array_slice($results, 0, $limit);
        return $out;
    }

    private function score($opp, $cv){

        $cvSkills     = $this->ids($cv['skill_ids']);
        $cvCareers    = $this->ids($cv['career_ids']);
        $cvCategories = $this->ids($cv['category_ids']);

        $total  = 0;   // suma de pesos activos
        $earned = 0;   // puntos obtenidos
        $matchedSkills = [];
        $missingSkills = [];

        // SKILLS
        if(!empty($opp['skills'])){
            foreach($opp['skills'] as $sid => $name){
                if(in_array($sid, $cvSkills, true)) $matchedSkills[] = $name;
                else                                $missingSkills[] = $name;
            }
            $total  += self::W_SKILLS;
            $earned += self::W_SKILLS * (count($matchedSkills) / count($opp['skills']));
        }

        // CARRERA
        if(!empty($opp['career_ids'])){
            $total += self::W_CAREER;
            if(array_intersect($opp['career_ids'], $cvCareers))          $earned += self::W_CAREER;
            elseif(array_intersect($opp['category_ids'], $cvCategories)) $earned += self::W_CAREER * 0.4;
        }

        // NIVEL
        if(!empty($opp['level'])){
            $total += self::W_LEVEL;
            if($cv['level'] === $opp['level']){
                $v = 1;
                if(!empty($opp['year']) && !empty($cv['year']) && $opp['year'] != $cv['year']) $v = 0.75;
                $earned += self::W_LEVEL * $v;
            }
        }

        return [
            'cv_id'    => $cv['id'],
            'name'     => $cv['full_name'],
            'career'   => $cv['career_name'],
            'level'    => $cv['level'],
            'year'     => $cv['year'],
            'applied'  => (int)$cv['applied'] > 0,
            'matched'  => $matchedSkills,
            'missing'  => $missingSkills,
            'score'    => $total > 0 ? (int)round($earned / $total * 100) : 0,
        ];
    }
}