<?php
namespace PuntersLounge\Models;
use CodeIgniter\Model;

class Predictions extends Model
{
	protected $table                = 'predictions';
	protected $primaryKey           = 'id';
	protected $returnType           = 'object';
	protected $allowedFields        = ["user","match_slug","prediction"];
	protected $useTimestamps        = true;

	public function getPredictions($date,$slug = null){
		$matches = model("PuntersLounge\Models\Predictions")->select("away_team,away_team_goals,event,home_team,home_team_goals,match_time,match_date,match_status,matches.slug,prediction")->join("matches","matches.slug = predictions.match_slug")->join("accounts","accounts.id = predictions.user","LEFT")->where(['match_date'=>$date]);
		if(is_null($slug)){
			$matches = $matches->groupStart()->where(["account_type" => 'ADMINISTRATOR'])->orWhere(['account_type' => 'SUPER USER'])->groupEnd();
		}else{
			$matches = $matches->where(['accounts.slug'=>$slug]);
		}
		$matches = $matches->groupBy('match_slug')->orderBy('matches.id','asc')->findall();

		foreach ($matches as $key) {
			if($key->prediction && $key->match_status == "FINISHED"){
				$key->result = winning_logic($key->prediction,$key->home_team_goals,$key->away_team_goals);
			 }else{
				 $key->result = 'PENDING';
			 }
		}
		
		$matches = array_reduce($matches,function($r, $a){
			$key = $a->event;
			if(!isset($r[$key])){
				$r[$key] = [];
			}
			array_push($r[$key],$a);
			return $r;
		});
		return $matches;
	}

	public function getSuccessRate($where = []){ 
		$matches = $this->select("prediction,home_team_goals,away_team_goals,match_status")->join("matches","matches.slug = predictions.match_slug")->where($where)->join("accounts","accounts.id = predictions.user")->findall(); 
		$selected = count($matches);
		$won = 0;
		$lost = 0;
		$unplayed = 0;

		foreach ($matches as $key) {
			$key->result = "PENDING";
			if ($key->match_status == "FINISHED") {
				$key->result = winning_logic($key->prediction,$key->home_team_goals,$key->away_team_goals);
			}
			if($key->result == 'WON'){$won++;}
			elseif($key->result == 'LOST'){$lost++;}
			if($key->match_status !== "FINISHED" && $key->match_status !== "PLAYING"){
				$unplayed ++;
			}
		}
		$win_rate = $won == 0 ? 0: round(($won/($selected - $unplayed))*100);
        return (object)['won'=>$won,'lost'=>$lost,'selected'=>$selected,'unplayed'=>$unplayed,'win_rate'=>$win_rate];
	}
} 
