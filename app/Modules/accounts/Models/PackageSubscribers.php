<?php

namespace Accounts\Models;

use CodeIgniter\Model;

class PackageSubscribers extends Model
{
	protected $table                = 'package_subscribers';
	protected $primaryKey           = 'id';
	protected $returnType           = 'object';
	protected $useSoftDeletes       = false;
	protected $allowedFields        = ["package","user","expires_at","transaction_reference"];
	protected $useTimestamps        = true;


	public function getSubscriptions($where,$vebrose = false){
		$subscription = $this->select("transaction_reference,user,package_subscribers.created_at,expires_at,packages.name as package_name,accounts.name as expert_name, amount,duration")->join("packages","packages.id = package_subscribers.package","LEFT")->join("accounts","packages.expert = accounts.id","LEFT")->where($where)->orderBy('package_subscribers.id','desc')->paginate();
		if($vebrose){
			foreach ($subscription as $key) {
				$key->subscriber = model("Accounts\Models\Accounts")->getAccount(["id"=>$key->user]);
			}
		}
		return $subscription;
	}
}
