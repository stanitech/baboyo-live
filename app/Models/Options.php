<?php

namespace App\Models;

use CodeIgniter\Model;

class Options extends Model
{
	protected $table                = 'options';
	protected $primaryKey           = 'id';
	protected $returnType           = 'object';
	protected $useSoftDeletes       = false;
	protected $allowedFields        = ["property","value"];
	protected $useTimestamps        = true;

    public function getOption($field){
        $cached_name = 'cached_option_'.$field;
		if (!cache($cached_name)){
			$option = $this->select("value")->where(["property"=>$field])->first();
            $value = [];
            if(isset($option->value) && $option->value){
				$value = unserialize($option->value);
				return (object)$value;
			}
			return null;
            cache()->save($cached_name, $value, DAY);
		}
		return cache($cached_name);
    }
}
