<?php

namespace Accounts\Models;

use CodeIgniter\Model;

class NewsletterSubscribers extends Model
{
	protected $table                = 'newsletter_subscribers';
	protected $primaryKey           = 'id';
	protected $returnType           = 'object';
	protected $useSoftDeletes       = false;
	protected $allowedFields        = ["email"];
	protected $useTimestamps        = true;
}
