<?php

namespace Blog\Models;

use CodeIgniter\Model;

class Articles extends Model
{
	protected $table                = 'articles';
	protected $primaryKey           = 'id';
	protected $returnType           = 'object';
	protected $useSoftDeletes       = true;
	protected $allowedFields        = ["name","description","options","slug","cover_img","created_by","created_at",'status','featured','cat_id','hits'];
	protected $useTimestamps        = true;

	public function getArticle($where){
		$post = $this->select('articles.*,article-category.name as cat_name,article-category.slug as cat_slug,accounts.name as author')->join('article-category','article-category.id = articles.cat_id')->join("accounts", "accounts.id = articles.created_by")->where($where)->first();
		if($post){
			$options = json_decode($post->options);
			$options->featured_image = isset($options->featured_image) && $options->featured_image ? model("Media")->getFile($options->featured_image) : null;
			if($options->post_format == 'Gallery'){
				$options->gallery_images = isset($options->gallery_images) && $options->gallery_images ? model("Media")->getFilesByID($options->gallery_images):null;
			}
			$post->options = $options;
		}
		return $post; 

	}
}
