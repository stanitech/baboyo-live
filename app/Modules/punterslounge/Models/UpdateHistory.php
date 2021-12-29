<?php

namespace PuntersLounge\Models;

use CodeIgniter\Model;

class UpdateHistory extends Model
{

	protected $table                = 'update_history';
	protected $primaryKey           = 'id';
	protected $returnType           = 'object';
	protected $allowedFields        = ["type","target_date","status","message","created_by"];
	protected $useTimestamps        = true;

	
	public function getUpdates($where = [],$limit = 40,$order_by='id',$order="desc"){ 
		$cached_name = 'cached_updates_'.str_replace(['{','}','(',')','/','\\','@',':'],'',serialize($where)).'_'.$limit;
		if (!cache($cached_name)){
			$updates = $this->select("update_history.*,name")->join("accounts","accounts.id = update_history.created_by","LEFT")->where($where)->orderBy($order_by,$order)->paginate($limit);
			//cache()->save($cached_name, $posts, 30);
			return $updates;
		}
		return cache($cached_name);
	}
}
