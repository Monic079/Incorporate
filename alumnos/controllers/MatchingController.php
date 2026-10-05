<?php
require_once "../models/MatchingModel.php";

class MatchingController {

    private $model;

    const W_SKILLS = 50;
    const W_CAREER = 30;
    const W_LEVEL  = 20;

    public function __construct(){
        $this->model = new MatchingModel();
    }

    private function ids($csv){
        return $csv === null || $csv === '' ? [] : array_map('intval', explode(',', $csv));
    }

    // ['cv'=>..., 'results'=>[...]] o null si el CV no es del usuario
    public function forCv($cvId, $limit = 10){

        if(session_status() === PHP_SESSION_NONE) session_start();
        if(!isset($_SESSION['user_id'])) return null;

        $cv = $this->model->getCvProfile((int)$cvId, $_SESSION['user_id']);
        if(!$cv) return null;

        $results = [];
        foreach($this->model->getOpenOpportunities($cv['id']) as $op){
            $r = $this->score($cv, $op);
            if($r) $results[] = $r;
        }

        usort($results, fn($a, $b) => $b['score'] <=> $a['score']);

        return ['cv' => $cv, 'results' => array_slice($results, 0, $limit)];
    }

    private function score($cv, $op){

        // skills que pide la oportunidad: id => nombre
        $reqSkills = [];
        if(!empty($op['skills_raw'])){
            foreach(explode('||', $op['skills_raw']) as $pair){
                [$sid, $name] = array_pad(explode(':', $pair, 2), 2, '');
                $reqSkills[(int)$sid] = $name;
            }
        }
        $reqCareers    = $this->ids($op['career_ids']);
        $reqCategories = $this->ids($op['category_ids']);

        // Si la oportunidad no define ningún criterio, no se puede comparar
        if(empty($reqSkills) && empty($reqCareers) && empty($op['req_level'])) return null;

        $total = 0; $earned = 0;
        $matched = []; $missing = [];

        // SKILLS
        if(!empty($reqSkills)){
            foreach($reqSkills as $sid => $name){
                if(in_array($sid, $cv['skill_ids'], true)) $matched[] = $name;
                else                                       $missing[] = $name;
            }
            $total  += self::W_SKILLS;
            $earned += self::W_SKILLS * (count($matched) / count($reqSkills));
        }

        // CARRERA
        if(!empty($reqCareers)){
            $total += self::W_CAREER;
            if(array_intersect($reqCareers, $cv['career_ids']))         $earned += self::W_CAREER;
            elseif(array_intersect($reqCategories, $cv['category_ids'])) $earned += self::W_CAREER * 0.4;
        }

        // NIVEL
        if(!empty($op['req_level'])){
            $total += self::W_LEVEL;
            if($cv['level'] === $op['req_level']){
                $v = 1;
                if(!empty($op['req_year']) && !empty($cv['year']) && $op['req_year'] != $cv['year']) $v = 0.75;
                $earned += self::W_LEVEL * $v;
            }
        }

        return [
            'id'       => $op['id'],
            'title'    => $op['title'],
            'type'     => $op['type_opor'],
            'company'  => $op['company'],
            'modality' => $op['modality'],
            'deadline' => $op['deadline'],
            'applied'  => (int)$op['applied'] > 0,
            'matched'  => $matched,
            'missing'  => $missing,
            'score'    => $total > 0 ? (int)round($earned / $total * 100) : 0,
        ];
    }
}