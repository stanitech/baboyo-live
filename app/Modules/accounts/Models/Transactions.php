<?php

namespace Accounts\Models;

use CodeIgniter\Model;

class Transactions extends Model
{
	protected $table                = 'transactions';
	protected $primaryKey           = 'id';
	protected $returnType           = 'object';
	protected $useSoftDeletes       = false;
	protected $allowedFields        = ["meta","user","amount","status","reference"];
	protected $useTimestamps        = true;


	public function getTransaction($reference){
		$transaction = $this->select("meta")->where(["reference"=>$reference])->first();
		if($transaction){
			$transaction = unserialize(($transaction->meta));
		}
		return $transaction;
	}
	public function transactions($where){
		return $this->select("user,amount,status,reference,name,email,transactions.created_at")->join('accounts',"accounts.id = transactions.user")->where($where)->paginate();
	}
}
