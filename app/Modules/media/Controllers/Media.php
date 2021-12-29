<?php

namespace Media\Controllers;

class Media extends \App\Controllers\BaseController
{
	use \CodeIgniter\API\ResponseTrait;
	public function index()
	{
		$request = $this->request->getGet();
		$model = model("Media");
		
		$data["files"] = $model->getFiles(isset($request['type']) ? $request['type'] : null,isset($request['search']),isset($request['search'])? $request['search'] : null );
		$data["pager"] = $model->pager;
		return view('Media\Views\pages\library',$data);
	}

	public function getMediaAjax($row_no = 0){
		$request = $this->request->getGet();
		$model = model("Media");
		$total_rows = $model->countAllResults();
		$row_per_page = 60;
		if($row_no != 0){
			$row_no = ($row_no - 1) * $row_per_page;
		}
		$data["files"] = $model->getMediaAjax(isset($request['type']) ? $request['type'] : "image",$row_no,$row_per_page);
		
		$data['page_count'] = service('pager')->getPageCount();
		$data["total_rows"] = $total_rows;

		return $this->respond($data);
	}
	public function changeFile(){
		$model = model("Media");
		$id = $this->request->getPost("id");
		if($id){
			$file = $this->request->getFile("file");
			$item = $model->select("file")->where(["id"=>$id])->first();
			if ($item && $item->file !== "") {
				file_exists(WRITEPATH."uploads/".$item->file) ? unlink(WRITEPATH."uploads/$item->file"):"";
			}
			$model->save([
				"id"=>$id,
				"file"=>$file->store(),
				"type"=>$file->getClientMimeType(),
				"size"=>$file->getSize(),
				"created_by"=>session("user_id")
			]); 
		}
		return redirect()->to(base_url("administrator/media"));
	}
	public function apiHandleFileUpload(){
		$files = $this->request->getFileMultiple("files");
		foreach ($files as $key) {
			$file_type = $key->getClientMimeType();
			$model = model("Media");
			$status = $model->save([
				"name"=>$key->getName(),
				"file"=> $file_type && (strpos($file_type,'image') !== false) ?  upload_file($key) : $key->store() , 
				"type"=>$key->getClientMimeType(),
				"size"=>$key->getSize(),
				"created_by"=>session("user_id")
			]);
		}
		return $this->respond(["status"=>$status]);
	}
	public function updateFile($mode="normal"){
		$status = model("Media")->save($this->request->getPost());
		if($mode == 'normal'){
			return redirect()->to(base_url("administrator/media"));
		}
		return $this->respond(["status"=>$status]);
	}
	public function deleteFile(){
		$request = $this->request->getPost();
		$model = model("Media");
        $item = $model->where(["id"=>$request["id"]])->first();
		
		if ($item && $item->file !== "") {
			file_exists(WRITEPATH."uploads/".$item->file) ? unlink(WRITEPATH."uploads/$item->file"):"";
		}
        $model->delete($request["id"], true);
		return redirect()->back();
	}
	public function apiDeleteFile(){
		$request = $this->request->getPost();
		$model = model("Media");
        $item = $model->where(["id"=>$request["id"]])->first();
		
		if ($item && $item->file !== "") {
			file_exists(WRITEPATH."uploads/".$item->file) ? unlink(WRITEPATH."uploads/$item->file"):"";
		}
		return $this->respond(["status"=>$model->delete($request["id"], true)]);
	}
}
