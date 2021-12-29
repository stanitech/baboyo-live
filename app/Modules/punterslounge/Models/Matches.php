<?php

namespace PuntersLounge\Models;

use CodeIgniter\Model;

class Matches extends Model
{

	protected $table                = 'matches';
	protected $primaryKey           = 'id';
	protected $returnType           = 'object';
	protected $allowedFields        = ["id","event","slug","home_team","away_team","home_team_goals","away_team_goals","match_status","match_date","match_time"];
	protected $useTimestamps        = true;


	public function getMatches($where = []){
		$matches = $this->select('away_team,away_team_goals,event,home_team,home_team_goals,match_time,match_status,slug')->where($where)->findall();
		foreach ($matches as $key) {
			 $key->event = str_replace(["'",'"'],"",$key->event);
			 $key->event_slug = url_title($key->event,"-",true);
			 $key->home_team = str_replace(["'",'"'],"",$key->home_team);
			 $key->away_team = str_replace(["'",'"'],"",$key->away_team);
			 $key->result = "PENDING";
			 $key->users_who_predicted = model("PuntersLounge\Models\Predictions")->select('user')->where(["match_slug"=>$key->slug])->findColumn('user');
			 
			 $key->prediction = model("PuntersLounge\Models\Predictions")->select('prediction')->join("accounts","accounts.id = predictions.user","LEFT")->where(["match_slug"=>$key->slug])->groupStart()->where(["account_type" => 'ADMINISTRATOR'])->orWhere(['account_type' => 'SUPER USER'])->groupEnd()->first(); 
			 if($key->prediction && $key->match_status == "FINISHED"){
				$key->result = winning_logic($key->prediction->prediction,$key->home_team_goals,$key->away_team_goals);
			 }
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
