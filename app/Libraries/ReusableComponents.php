<?php
namespace App\Libraries;

class ReusableComponents
{
    public function newsTicker(){
        $data['announcements'] = model("PuntersLounge\Models\Articles")->select('articles.name,articles.slug,articles.description')->join('article-category','article-category.id = articles.cat_id')->where(['article-category.status'=>'PUBLISHED'])->where(['article-category.name'=>'Announcements'])->orderBy('articles.id','asc')->findall(); 
        return view('partials/newsticker',$data); 
    }

    public function predictionTable($matches,$events,$slug=null){
        $data['match'] = $matches;
        $data['events'] = $events;
        $data['slug'] = $slug;
        $data['stat'] = prediction_statistics($matches);
        return view('partials/prediction-table',$data); 
    }
}