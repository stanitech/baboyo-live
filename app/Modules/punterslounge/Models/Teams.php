<?php

namespace PuntersLounge\Models;

use CodeIgniter\Model;

class Teams extends Model
{
  
	protected $table                = 'teams';
	protected $primaryKey           = 'id';
	protected $returnType           = 'object';
	protected $allowedFields        = ["id","name","point","img","link"];
	protected $useTimestamps        = true;

	
}