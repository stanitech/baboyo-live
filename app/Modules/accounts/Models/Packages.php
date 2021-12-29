<?php

namespace Accounts\Models;

use CodeIgniter\Model;

class Packages extends Model
{
	protected $table                = 'packages';
	protected $primaryKey           = 'id';
	protected $returnType           = 'object';
	protected $useSoftDeletes       = false;
	protected $allowedFields        = ["name","amount","description","duration","refundable","notification","expert"];
	protected $useTimestamps        = true;

	public function getPackages($where,$detailed = true){
        $packages = $this->select("id,name,amount,description,duration,refundable,notification")->where($where)->orderBy("id","desc")->findall();
		if($detailed){
			foreach ($packages as $key) {
				$key->all_time_subscribers = model("PackageSubscribers")->where(["package"=>$key->id])->countAllResults();
				$key->active_subscribers = model("PackageSubscribers")->where(["package"=>$key->id,"expires_at >"=>date('Y-m-d h:i:s')])->countAllResults();
				$key->revenue = model("PackageSubscribers")->join("transactions","transactions.reference = package_subscribers.transaction_reference")->selectSum('transactions.amount',"amount_paid")->where(["package"=>$key->id])->first()->amount_paid;
			}
		}
        return $packages;
	}
}
