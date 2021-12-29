<?php

namespace PuntersLounge\Models;

use CodeIgniter\Model;

class PredictionsTip extends Model
{

	protected $table                = 'prediction_tips';
	protected $primaryKey           = 'id';
	protected $returnType           = 'object';
	protected $allowedFields        = ["event","slug","home_team","away_team","home_team_goals","away_team_goals","logo","match_date","match_time","more_info"];
	protected $useTimestamps        = true;

	public function getPredictionTips($where = []){
		$matches = $this->where($where)->findall();
		foreach ($matches as $key) {
			 $key->more_info = unserialize($key->more_info);
		}
		$matches_sorted_by_event = array_reduce($matches,function($r, $a){
			$key = $a->event;
			if(!isset($r[$key])){
				$r[$key] = [];
			}
			array_push($r[$key],$a);
			return $r;
		});
		return $matches_sorted_by_event;
	}
}
