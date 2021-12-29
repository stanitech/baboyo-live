<?php

namespace PuntersLounge\Controllers;

class PuntersLounge extends \App\Controllers\BaseController
{

	public function index($date = null){
		//session()->set(['user_id'=> 5,"logged_in"=>true,"account_type"=>'SUPER USER']);
		if(!$date){
			$date = date("Y-m-d"); 
			return redirect()->to(base_url("livescore")."/$date");
		}
		$data["date"] = $date;
		$data["matches"] = model("PuntersLounge\Models\Predictions")->getPredictions($date,null);
		$data['stat'] = prediction_statistics($data["matches"]);
		$data["title"] = "Live Football Score $date";
           

		//dd($data);
		return view('PuntersLounge\Views\pages\livescores',$data);
	}

	public function tips(){

	  $data['team_compare'] = scrape_match();
		return view('PuntersLounge\Views\pages\tips', $data);
	}


	public function teams(){

		$data['leagues'] = scrape_teams();

	

		return view('PuntersLounge\Views\pages\teams', $data);
	}


	
	public function team(){

	
		return view('PuntersLounge\Views\pages\team-single');
	}

	public function predictionsStatistics($slug = null){

		$data["title"] = "Prediction Stats";
		$data['stats'] = [];
		for ($i=1; $i <= 10 ; $i++) { 
			$data['stats'][date("Y-m-d",strtotime("- $i day"))] = prediction_statistics(model("PuntersLounge\Models\Predictions")->getPredictions(["match_date"=>date("Y-m-d",strtotime("- $i day"))],$slug));
		}
		$data['slug'] = $slug;
		return view('PuntersLounge\Views\pages\predictions-statistics',$data);
	}

	public function experts(){
		$request = $this->request->getGet();
		$data["experts"] = model('Accounts\Models\Accounts')->getExpertsWithSuccessRate(isset($request["search"])? $request["search"] : null); 
		$data["search"] = isset($request["search"]) ? $request["search"] : null;
		$data["title"] = "Expert soccer football predictions and live reviews";
		return view('PuntersLounge\Views\pages\experts',$data); 
	}
	public function expertPrediction($user_slug,$date){
		$data["date"] = $date ? $date : date("Y-m-d"); 
		$data["expert"] = model('Accounts\Models\Accounts')->getAccount(["slug"=>$user_slug]);
		$data["packages"] = model('Accounts\Models\Packages')->getPackages(["expert"=>$data["expert"]->id],false);
		$data["matches"] = model("PuntersLounge\Models\Predictions")->getPredictions($data["date"],$user_slug);
		$data['stat'] = prediction_statistics($data["matches"]);
		// model("PuntersLounge\Models\Predictions")->getPredictions(["match_date"=>$data["date"],"accounts.slug"=>$user_slug]);
		$data["can_access"] = can_access_prediction_tip($data["packages"],$data["expert"]->id,$data["date"]);
		if(!$data["can_access"]){
			$data["user"] =  model('Accounts\Models\Accounts')->getAccount(["id"=>session("user_id")]);
		}
		$data["title"] = $data["expert"]->name." Predictions for ". $date;
		return view('PuntersLounge\Views\pages\expert-prediction',$data);
	}

	public function resyncLivescore(){
		$request = $this->request->getPost();
		if(isset($request["redirect"])){
			$redirect = $request["redirect"];
		}
		$option = model("Options")->getOption("sync_updates");
		get_livescores($request["date"],false,$option->sync_updates == 'true'? true:false);
		return redirect()->to(isset($redirect) ? $redirect : base_url("livescore")."/{$request['date']}");
	}


	public function getPredictionsTip($date = null){
		if(!$date){
			$date = date("Y-m-d"); 
		}
		$data["date"] = $date;
		$data["matches"] = model("PuntersLounge\Models\PredictionsTip")->getPredictionTips(["match_date"=>$date]);
		return view('PuntersLounge\Views\pages\predictions',$data);
	}

	public function resyncPredictions(){
		$request = $this->request->getPost();
		if(isset($request["redirect"])){
			$redirect = $request["redirect"];
		}
		$option = model("Options")->getOption("sync_updates");
		get_assisted_predictions($request["date"],false,$option->sync_updates == 'true'? true:false);
		return redirect()->to(isset($redirect) ? $redirect : base_url("get-predictions-tip")."/{$request['date']}");
	}

	public function setPrediction(){
		$request = $this->request->getPost();
		$to = $request["redirect_back"];
		$model = model("PuntersLounge\Models\Predictions");
		unset($request["redirect_back"]);
		$predictions_to_remove = json_decode($request['predictions_to_remove']);
		if(is_array($predictions_to_remove) && count($predictions_to_remove) > 0){
			$model->delete($predictions_to_remove);
		}

		foreach ($request['predictions'] as $key) {
			$game = json_decode($key);
			$game->user = session("user_id");
			$model->save($game);
		}
		return redirect()->to($to);
	}
	public function posts($slug){
		$request = $this->request->getGet();
		$category = model("PuntersLounge\Models\Categories")->getCategory(["slug"=>$slug]);
		if (!$category) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}
		$data["category"] = $category;
		$data["title"] = $category->name;
		$data["posts"] = model('PuntersLounge\Models\Articles')->select('articles.name,articles.slug,articles.created_at,accounts.name as author,articles.options')->join("accounts", "accounts.id = articles.created_by")->join('article-category','articles.cat_id = article-category.id')->where(['cat_id'=>$category->id])->where(['articles.status'=>'PUBLISHED'])->where(['article-category.status'=>'PUBLISHED'])->orderBy('articles.created_at','desc')->paginate();
		foreach($data["posts"] as $post){
			$options = json_decode($post->options);
			$options->featured_image = $options->featured_image ? model("Media")->getFile($options->featured_image) : null;
			$post->options = $options;
		}
		// if(isset($request["search"])){
		// 	$data["title"] = "Search results for [{$request['search']}]"; 
		// 	$data["posts"] = model("PuntersLounge\Models\Posts")->searchPosts($request["search"]);
		// 	$data["search"] = $request["search"];
		// }else{

		// 	$data["title"] = "Football news, latest transfers, predictions tips, livescores and transfers"; 
		// 	$data["posts"] = model("PuntersLounge\Models\Posts")->getPosts(['status'=>"PUBLISHED"]);
		// }
		$data["pager"] =  model("PuntersLounge\Models\Articles")->pager;
		return view('PuntersLounge\Views\pages\posts',$data);
	}

	public function post($slug){
		$article_model = model("PuntersLounge\Models\Articles");
		$post = $article_model->getArticle(["articles.slug"=>$slug]);
		if (!$post) {
			throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
		}
		if ($post->options->featured_image) {
			$data["image"] = ($post->options->featured_image->file);
		}
		$data["post"] = $post;
		$data["title"] = $post->name;
		$data["posts"] = $article_model->where(['cat_id'=>$post->cat_id])->orderBy('featured','asc')->where("id != {$post->id}")->orderBy('created_at','asc')->paginate(4);
		foreach($data["posts"] as $p){
			$options = json_decode($p->options);
			$options->featured_image = $options->featured_image ? model("Media")->getFile($options->featured_image) : null;
			$p->options = $options;
		}
		
		$article_model->set('hits','hits+1',false)->where(['id'=>$post->id])->update();

		return view('PuntersLounge\Views\pages\post', $data);
	}

	public function managePosts($action = null,$slug=null){
		$data['action'] = $action;
		$data['categories'] = model("PuntersLounge\Models\Categories")->select('name,id')->where([])->findall();
		if($action == 'new'){
			$data['title'] = "New Post";
			return view('PuntersLounge\Views\pages\manage-news-posts', $data);
		}elseif($action == 'edit' && $slug){
			$post = model("PuntersLounge\Models\Articles")->getArticle(["articles.slug"=>$slug]);
			if (!$post) {
				throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
			}
			$data["post"] = $post;
			$data['title'] = "Edit Post";
			return view('PuntersLounge\Views\pages\manage-news-posts', $data);
		}
		$data['title'] = "All Posts";
		$data["posts"] = model("PuntersLounge\Models\Articles")->select('articles.id,articles.name,articles.slug,articles.status,articles.featured,articles.created_at,hits,accounts.name as author,article-category.name as category_name')->like('articles.name',$this->request->getGet('search')??'')->join("accounts", "accounts.id = articles.created_by")->join("article-category", "article-category.id = articles.cat_id")->orderBy('created_at','desc')->paginate();
		$data['pager'] = model("PuntersLounge\Models\Articles")->pager;
		return view('PuntersLounge\Views\pages\manage-news-posts', $data);
	}

	public function savePost()
	{
		$request = $this->request->getPost();
		$action = $request['action'];
		unset($request['action']);
		if(isset($request['created_at'])){

			if(empty($request['slug'])){
				$request['slug'] = url_title($request['name'],'-',true);
			}else{
				$request['slug'] = url_title($request['slug'],'-',true);
			}
			if(empty($request['created_at'])){
				$request['created_at'] = date("Y-m-d H:i:s");
			}
			$request['created_by'] = session("user_id");
			$request['options'] = json_encode($request['options']);
		}

		if(model("PuntersLounge\Models\Articles")->save($request)){
			set_notification("success", "Article Saved");

			if($action == 'save'){
				return redirect()->to(base_url("/settings/manage-posts/edit/{$request['slug']}"));
			}elseif($action == 'save-and-close'){
				return redirect()->to(base_url("/settings/manage-posts"));
			}elseif($action == 'save-and-new'){
				return redirect()->to(base_url("/settings/manage-posts/new"));
			}
		}
	}
	public function batchArticlesOperations(){
		$request = $this->request->getPost();
		$id = $request['id'];
		unset($request['id']);
		if(isset($request['trash']) && $request['trash'] == 'YES' ){
			model("PuntersLounge\Models\Articles")->delete($id);
		}else {
			model("PuntersLounge\Models\Articles")->update($id,$request);
		}
		return redirect()->to(base_url("/settings/manage-posts"));
	}

	public function postCategories($action = null,$slug=null){
		$data['action'] = $action;
		if($action == 'new'){
			$data['title'] = "New Category";
			return view('PuntersLounge\Views\pages\manage-categories', $data);
		}elseif($action == 'edit' && $slug){
			$post = model("PuntersLounge\Models\Categories")->getCategory(["slug"=>$slug]);
			if (!$post) {
				throw \CodeIgniter\Exceptions\PageNotFoundException::forPageNotFound();
			}
			$data["post"] = $post;
			$data['title'] = "Edit Categories";
			return view('PuntersLounge\Views\pages\manage-categories', $data);
		}

		$data['title'] = "All Categories";
		$data["posts"] = model("PuntersLounge\Models\Categories")->getCategoriesWithStats([],$this->request->getGet('search')??'');
		$data['pager'] = model("PuntersLounge\Models\Categories")->pager;
		return view('PuntersLounge\Views\pages\manage-categories', $data);
	}

	public function batchPostCategoryOperations(){
		$request = $this->request->getPost();
		$id = $request['id'];
		$base_category_id = null;
		unset($request['id']);
		if(isset($request['trash']) && $request['trash'] == 'YES' ){
			//CHECK IF THEIR ARE ARTICLES UNDER THAT CATEGORY
			foreach ($id as $i) {
				if(model("PuntersLounge\Models\Articles")->where('cat_id',$i)->countAllResults() > 0){
					if(!$base_category_id){
						if(model("PuntersLounge\Models\Categories")->where('name','Uncategorized')->countAllResults() > 0) {
							$base_category_id = model("PuntersLounge\Models\Categories")->select('id')->where('name','Uncategorized')->first()->id;
						}else{
							$base_category_id = model("Categories")->insert([
								'name'=>'Uncategorized',
								'slug'=>'uncategorized',
								'status'=>'UNPUBLISHED',
								'options'=>json_encode(['background_type'=>'None','cover_img'=>'','icon'=>'icon-reading']),
								'created_by'=>session('user_id')
							]);
						}
					}
					model("PuntersLounge\Models\Articles")->set(['cat_id'=>$base_category_id])->where(['cat_id'=>$i])->update();
				}
				model("PuntersLounge\Models\Categories")->where(['id'=>$i])->delete();
			}
			
		}else {
			model("PuntersLounge\Models\Categories")->update($id,$request);
		}
		return redirect()->to(base_url("/settings/manage-post-category"));
	}

	public function savePostCategory(){
		$request = $this->request->getPost();
		$action = $request['action'];
		unset($request['action']);
		if(isset($request['created_at'])){
			if(empty($request['slug'])){
				$request['slug'] = url_title($request['name'],'-',true);
			}else{
				$request['slug'] = url_title($request['slug'],'-',true);
			}
			if(empty($request['created_at'])){
				$request['created_at'] = date("Y-m-d H:i:s");
			}
			$request['created_by'] = session("user_id");
			$request['options'] = json_encode($request['options']);
		}

		if(model("PuntersLounge\Models\Categories")->save($request)){
			set_notification("success", "Category Saved");

			if($action == 'save'){
				return redirect()->to(base_url("/settings/manage-post-category/edit/{$request['slug']}"));
			}elseif($action == 'save-and-close'){
				return redirect()->to(base_url("/settings/manage-post-category"));
			}elseif($action == 'save-and-new'){
				return redirect()->to(base_url("/settings/manage-post-category/new"));
			}
		}
	}

}
