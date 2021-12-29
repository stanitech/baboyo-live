<?php

namespace App\Controllers;

class Home extends BaseController
{
	public function index()
	{
		
		//symlink(WRITEPATH.'assets','assets');
		symlink(WRITEPATH.'uploads','uploads');

	}
    public function saveOption($field){
		cache()->deleteMatching("cached_option_$field*");
		$request = $this->request->getPost();
		if(isset($request["redirect"])){
			$redirect = $request["redirect"];
			unset($request["redirect"]);
		}
		$options_model = model("Options");
		if($options_model->where(["property"=>$field])->countAllResults() > 0){
			$options_model->set(["value"=>serialize($request)])->where(["property"=>$field])->update();
		}else{
			$options_model->save(["property"=>$field,"value"=>serialize($request)]);
		}
		if(isset($request)){
			return redirect()->to($redirect);
		}
		return redirect()->back();
	}
}
