<?php

namespace Blog\Models;

use CodeIgniter\Model;

class Categories extends Model
{
	protected $table                = 'article-category';
	protected $primaryKey           = 'id';
	protected $returnType           = 'object';
	protected $useSoftDeletes       = false;
	protected $allowedFields        = ["name","description","options","slug","cover_img","created_by","created_at",'status','featured'];
	protected $useTimestamps        = true;

	public function getCategoriesWithStats($where,$keyword = ''){
		$article_model = model("PuntersLounge\Models\Articles"); 
		$posts = $this->where($where)->like('name',$keyword)->orderBy('id','desc')->paginate();
		foreach ($posts as $key) {
			$key->published_articles = $article_model->where(['status'=>'PUBLISHED','cat_id'=>$key->id])->countAllResults();
			$key->unpublished_articles = $article_model->where(['status'=>'UNPUBLISHED','cat_id'=>$key->id])->countAllResults();
			$key->featured_articles = $article_model->where(['featured'=>'YES'])->where('cat_id',$key->id)->countAllResults();
		}
		return $posts;
	}

	public function getCategory($where){
		$post = $this->where($where)->first();
		if($post){
			$options = json_decode($post->options);
			if($options->background_type == 'Image'){
				$options->cover_img = model("Media")->getFile($options->cover_img);
			}
			$post->options = $options;
		}
		return $post;

	}



	public function getCategoriesWithArticles($where=[],$post_where=[]){
		$category = $this->select('id,name,slug')->where($where)->orderBy('arrangement_order','asc')->findall();
		foreach ($category as $key) {
			$key->posts = model("Articles")->select('name,slug,created_at,options')->where('cat_id',$key->id)->where(['status'=>'PUBLISHED'])->where($post_where)->orderBy('created_at','desc')->paginate(5);
			foreach($key->posts as $post){
				$options = json_decode($post->options);
				$options->featured_image = isset($options->featured_image) && $options->featured_image ? model("Media")->getFile($options->featured_image) : null;
				$post->options = $options;
			}
		}
		return $category;
	}
}
