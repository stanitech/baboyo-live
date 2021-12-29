<?php

namespace Media\Models;

use CodeIgniter\Model;

class Media extends Model
{
	protected $table                = 'media';
	protected $primaryKey           = 'id';
	protected $returnType           = 'object';
	protected $useSoftDeletes       = false;
	protected $allowedFields        = ["id","name","file","type","size","source","created_by"];
	protected $useTimestamps        = true;


	public function getFiles($type=null,$is_search = false,$keyword='',$limit=100){
		$media = model("Media");
		if($type){
			$media = $media->like('type',$type);
		}
		if($is_search){
			$media = $media->like('name',$keyword);
		}
		$media = $media->orderBy("id","desc")->paginate($limit);
		foreach ($media as $key) {
			$key->name =  $key->name ? str_replace("'",'',$key->name): null;
			$key->file = $key->source === "EXTERNAL" ? $key->file :  base_url('/uploads').'/'.$key->file; 
		}
		return $media;
	}

	public function getFilesByID($ids){
		$media = $this->orderBy("id","desc")->find($ids);
		foreach ($media as $key) {
			$key->name =  $key->name ? str_replace("'",'',$key->name): null;
			$key->file = $key->source === "EXTERNAL" ? $key->file :  base_url('/uploads').'/'.$key->file; 
		}
		return $media;
	}

	public function getMediaAjax($type="null",$offset=0,$limit=20){
		$media = $this->like('type',$type)->orderBy("id","desc")->findall($limit,$offset);
		foreach ($media as $key) {
			$key->name =  $key->name ? str_replace("'",'',$key->name): null;
			$key->file = $key->source === "EXTERNAL" ? $key->file :  base_url('/uploads').'/'.$key->file; 
		}
		return $media;
	}

	public function getFile($id){
		$media = $this->find($id);
		if($media) {
			$media->file = $media->source === "EXTERNAL" ? $media->file :  base_url('/uploads').'/'.$media->file; 
		}
		return $media; 
	}

}
