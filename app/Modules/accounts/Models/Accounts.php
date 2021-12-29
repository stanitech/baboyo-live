<?php

namespace Accounts\Models;

use CodeIgniter\Model;

class Accounts extends Model
{
	protected $table                = 'accounts';
	protected $primaryKey           = 'id';
	protected $returnType           = 'object';
	protected $useSoftDeletes       = false;
	protected $allowedFields        = ["name","email","slug","login_at","account_type","cover_img","description"];
	protected $useTimestamps        = true;

	public function signInWithGoogle($data){
		$data["login_at"]  = date("Y-m-d H:i:s");
		
		if($user = $this->select("account_type,id")->where(["email"=>$data["email"]])->first()){
			session()->set(['user_id'=> $user->id,"logged_in"=>true,"account_type"=>$user->account_type]);
			return $this->update($user->id, $data);
		}else{
			$data["slug"] = url_title($data["name"],"-","true");
			$user_id = $this->insert($data);
			if($user_id){
				session()->set(['user_id'=> $user_id,"logged_in"=>true,"account_type"=>"STANDARD"]);
			}
			return $user_id;
		}
	}

	public function saveAccount($data){
		
        if(!isset($data["id"])){
            $data["slug"] = url_title($data["name"],"-",true);
        }
        return $this->save($data);
    }
    public function getAccount($where){
        $account = $this->where($where)->first();
        if($account){
            $account->cover_img = $account->cover_img ? model("Media")->getFile($account->cover_img):null;
        }
        return $account;
	}
	public function getAccounts($where = [],$paginate = true){
		$accounts = $this->where($where)->orderBy("name","asc");
		if($paginate){
			$accounts = $accounts->paginate(10);
		}else{
			$accounts = $accounts->findall();
		}
        foreach($accounts as $account){
            $account->description = str_replace(["'",'"'],'',$account->description);
            $account->cover_img = $account->cover_img ? model("Media")->getFile($account->cover_img):null;
        }
        return $accounts;
	}
	
	public function getExpertsWithSuccessRate($keyword=null ){
		if($keyword){
			$accounts = $this->where(["account_type"=>"EXPERT"])->like("name",$keyword)->orderBy("name","asc")->paginate();
		}else{
			$accounts = $this->where(["account_type"=>"EXPERT"])->orderBy("name","asc")->paginate();
		}
		foreach($accounts as $account){
			$account->cover_img = $account->cover_img ? model("Media")->getFile($account->cover_img):null;
			$account->stats = model("PuntersLounge\Models\Predictions")->getSuccessRate(["predictions.user"=>$account->id,"predictions.created_at >="=>date('c',strtotime("- 7 day"))]); 
        }
        return $accounts;
	}
}
