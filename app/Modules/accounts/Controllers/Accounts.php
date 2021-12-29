<?php

namespace Accounts\Controllers;

use stdClass;

class Accounts extends \App\Controllers\BaseController
{
	use \CodeIgniter\API\ResponseTrait;

	public function login(){
		$request = $this->request->getPost();
		if(isset($request["redirect"])){
			$redirect = $request["redirect"];
			unset($request["redirect"]);
		}
		model('Accounts\Models\Accounts')->signInWithGoogle($request);
		return redirect()->to(isset($redirect) ? $redirect : base_url());
		
	}
	public function logout(){
		session()->remove(["user_id","logged_in","profile"]);
		return redirect()->to(base_url());
	}

	public function manageAccounts(){
		
		$data["all_users"] = model('Accounts\Models\Accounts')->getAccounts([],false);
		$data["users"] = model('Accounts\Models\Accounts')->getAccounts(); 
		$data["pager"] = model('Accounts\Models\Accounts')->pager;
		$data['newsletter_subscribers'] = model('NewsletterSubscribers')->countAllResults();
		return view('Accounts\Views\pages\manage-accounts',$data);
	}

	public function manageNewsletterSubscribers(){
		
		$data["users"] = model('Accounts\Models\NewsletterSubscribers')->orderBy('id','desc')->paginate(); 
		$data["pager"] = model('Accounts\Models\NewsletterSubscribers')->pager;
		return view('Accounts\Views\pages\manage-newsletter-subscribers',$data);
	}

	public function deleteNewsletterSubscribers(){
		$request = $this->request->getPost("id");
		model('Accounts\Models\NewsletterSubscribers')->delete($request);
		return redirect()->to(base_url('/settings/manage-newsletter-subscribers'));
	}


	public function saveAccount($isAJAX = false)
	{
		$request = $this->request->getPost();
		if(isset($request["redirect_to"])){
			$redirect = $request["redirect_to"];
			unset($request["redirect_to"]);
		}

		$result = model('Accounts\Models\Accounts')->saveAccount($request);
		if($isAJAX){
			return $this->respond(["status"=>$result ]);
		}
		return redirect()->to(isset($redirect) ? $redirect : base_url('/settings/manage-accounts'));
	} 
	public function deleteAccount()
	{
		model('Accounts\Models\Accounts')->delete($this->request->getPost(), true);
		return redirect()->to(base_url('/settings/manage-accounts'));
	}

	public function yourInfo(){
		$data["user"] = model('Accounts\Models\Accounts')->getAccount(["id"=>session("user_id")]);
		$data["subscriptions"] = model('Accounts\Models\PackageSubscribers')->getSubscriptions(["user"=>session("user_id")]);
		return view('Accounts\Views\pages\your-info',$data);
	}

	public function platformUpdates($date = null){
		$data["date"] = $date ? $date : date("Y-m-d"); 
		$data["recent_livescore_update"] = model("PuntersLounge\Models\UpdateHistory")->select("update_history.updated_at,name")->join("accounts","accounts.id = update_history.created_by")->where(["type"=>"LIVESCORES","target_date"=> $data["date"],"status"=>"SUCCESS"])->orderBy('update_history.id','desc')->first();
		$data["recent_prediction_tips_update"] = model("PuntersLounge\Models\UpdateHistory")->select("update_history.updated_at,name")->join("accounts","accounts.id = update_history.created_by")->where(["type"=>"PREDICTION TIPS","target_date"=> $data["date"],"status"=>"SUCCESS"])->orderBy('update_history.id','desc')->first();
		$data["user"] = model('Accounts\Models\Accounts')->where(["id"=>session("user_id")])->first();
		$data["options"] = model("Options")->getOption("sync_updates");
		return view('Accounts\Views\pages\platform-updates',$data);
	}
	

	public function updateHistory($date = null){
		$data["history"] = model("UpdateHistory")->getUpdates();
		$data["pager"] = model("UpdateHistory")->pager;
		return view('Accounts\Views\pages\update-history',$data);
	}

	public function clearHistory(){
		if(model("UpdateHistory")->where(["created_at <"=> date("Y-m-d",strtotime("- 3 day"))])->delete()){
			set_notification("success","Log has been cleared down to ".date("M d, Y",strtotime("- 3 day")));
		}
		return redirect()->to(base_url("settings/update-history"));
	}

	public function sendMessage(){
		if (! $this->validate(['to'	=> 'required'])) {
			set_notification("error",json_encode($this->validator->getError("to")));
			return redirect()->to(base_url('/settings/manage-accounts'));
		}
		if (! $this->validate(['message'=>"required"])) {
			set_notification("error",json_encode($this->validator->getError("message")));
			return redirect()->to(base_url('/settings/manage-accounts'));
		}
		$request = $this->request->getPost();
		$model = model("Accounts\Models\Accounts");
		$emails = [];		
		if(isset($request["to"]) && is_array($request["to"])){
			foreach ($request["to"] as $key) {
				if(is_numeric($key)){
					array_push($emails,$model->select("email,name")->where(['id'=>$key])->first());
				}elseif($key == 'newsletter-subscribers'){
					$subscribers = model('Accounts\Models\NewsletterSubscribers')->select('email')->findall();
					foreach ($subscribers as $user) {
						$u = new stdClass();
						$u->email = $user->email;
						$u->name = "";
						array_push($emails,$u);
					}
				}else{
					$emails = array_merge($emails,$model->select('email,name')->where('account_type',$key)->findall());
				}
			}
		} 
		$todays_prediction = model("PuntersLounge\Models\Predictions")->select("away_team,away_team_goals,event,home_team,home_team_goals,match_time,match_date,match_status,matches.slug,prediction")->join("matches","matches.slug = predictions.match_slug")->join("accounts","accounts.id = predictions.user","LEFT")->where(["match_date"=>date("Y-m-d")])->groupStart()->where(["account_type" => 'ADMINISTRATOR'])->orWhere(['account_type' => 'SUPER USER'])->groupEnd()->groupBy('match_slug')->orderBy('matches.id','asc')->findall();

		$todays_prediction_html = "<table style='border:2px dashed #dee2e6;border-collapse: collapse;font-family:monospace;width:400px;color: #212529'>";
		foreach ($todays_prediction as $key) {
			$date = date('d/m/Y',strtotime($key->match_date));
			$img = base_url('/assets/img/stadium.png');
			$todays_prediction_html.="<tr>
			<td style='padding: .75rem;vertical-align: top; border-top: 1px solid #dee2e6;width:30px'><img style='width:30px' src='$img'/></td>
			<td style='padding: .75rem;vertical-align: top; border-top: 1px solid #dee2e6'>
				<p style='color:grey;margin:0;font-size:10px'>{$date} {$key->match_time}</p>
				<ul style='list-style-type:none;padding:0;margin:0'>
					<li>{$key->home_team}</li>
					<li>{$key->away_team}</li>
				</ul>
			</td>
			<td style='padding: .75rem;vertical-align: top; border-top: 1px solid #dee2e6'>
				<span style='font-weight:bolder'>{{$key->prediction}}</span>
			</td>
		</tr>";
		
		}
			
		$todays_prediction_html.= "</table>";
		$todays_prediction_html.= "<small style='color:grey;margin-top:100px;text-align:center'>If you don't want to receive this type of email in the future, please [[unsubscribe link]] </small>";


		foreach (array_unique($emails,SORT_REGULAR) as $key) {
			$data["mail_body"] = str_ireplace("[[name]]",  ucwords($key->name), $request["message"]);
			$todays_prediction_html = str_ireplace("[[unsubscribe link]]", "<a href='".base_url('newsletter').'/'.base64_encode(base64_encode($key->email))."/unsubscribe'>unsubscribe<a>", $todays_prediction_html);
			$data["mail_body"] = str_ireplace("[[today predictions]]",  $todays_prediction_html, $data["mail_body"]);
			send_email($key->email, '['.env('app.name').'] '.$request['subject'],$data);
		}
		set_notification("success",'Message sent to '.counted(count($emails),"email"));
		return redirect()->to(base_url('/settings/manage-accounts'));
	}


	public function sendInvitation()
    {
		$request = $this->request->getPost();
        foreach ($request["invitees"] as $key => $value) {
            $email = $value["email"];
            $name = $value["name"] ? $value["name"] : str_replace(["_","-","."], " ", trim(explode("@", $email)[0]));
            $data["mail_body"]  = str_ireplace("[[name]]", ucwords($name), $request["template"]);
			$data["mail_body"]  = str_ireplace("[[newletter subscription link]]", "<a href='".base_url('newsletter').'/'.base64_encode(base64_encode($email))."/subscribe'>Click to join<a>", $data["mail_body"]);
            send_email($email, '['.env('app.name').'] Platform Invitation', $data);
        }
		set_notification("success",'Invitation Messages dispatched to '.counted(count($request["invitees"]),'email'));
		return redirect()->to(base_url('/settings/manage-accounts'));
    }
	public function manageSubscription(){
		$data["packages"] = model('Accounts\Models\Packages')->getPackages(["expert"=>session("user_id")]);
		return view('Accounts\Views\pages\manage-subscription',$data);
	}

	public function createPackage(){
		$request = $this->request->getPost();
		if(isset($request["redirect_to"])){
			$redirect = $request["redirect_to"];
			unset($request["redirect_to"]);
		}
		$request["expert"] = session("user_id");
		model('Accounts\Models\Packages')->save($request);
		return redirect()->to(isset($redirect) ? $redirect : base_url('/account/manage-subscription'));
	}
	
	public function subscribe(){
		$request = $this->request->getPost();
		$transaction_model = model("Accounts\Models\Transactions");
		$package = model("Accounts\Models\Packages")->select("amount,duration")->where(["id"=>$request["package"]])->first(); 
		if(!$package){
			set_notification("error",'Invalid Package');
			return redirect()->back();
		}
		$reference = $request["reference"];
		if ($transaction_model->where(["reference" => $reference])->countAllResults() > 0) {
			set_notification("error",'Transaction Error, Already existing transaction reference');
			return redirect()->back();
		}
		$curl = curl_init();
        curl_setopt_array($curl, array(
            CURLOPT_URL => "https://api.paystack.co/transaction/verify/$reference",
            CURLOPT_RETURNTRANSFER => true,
            CURLOPT_ENCODING => "",
            CURLOPT_MAXREDIRS => 10,
            CURLOPT_TIMEOUT => 30,
            CURLOPT_HTTP_VERSION => CURL_HTTP_VERSION_1_1,
            CURLOPT_CUSTOMREQUEST => "GET",
            CURLOPT_HTTPHEADER => array(
            "Authorization: Bearer ".env('app.apis.paystack-secretkey'),
            "Cache-Control: no-cache",
            ),
        ));
		$response = curl_exec($curl);
        $err = curl_error($curl);
		curl_close($curl);
		if ($err) {
			set_notification("error",'Transaction Error, '.$err);
			return redirect()->back();
		}
		$transaction = json_decode($response);

		$txn_ref = $transaction_model->insert([
			"reference" => $transaction->data->reference,
			"meta" => serialize($transaction),
			"user" => session("user_id"),
			"amount" => ($transaction->data->amount)/100,
			"status"=> $transaction->status ? "SUCCESS" : "FAILED",
		]);
		if(!$transaction->status){
			set_notification("error",'Payment Error! Transaction Not Successful');
			return redirect()->back();
		}
		if(model("PackageSubscribers")->save([
			"user"=>session("user_id"),
			"expires_at" => date("Y-m-d h:i:s",strtotime("+ {$package->duration} days")),
			"package"=>$request["package"],
			"transaction_reference"=>$transaction->data->reference
		])){
			set_notification("success",'Congratulations. Subscription expires on '.date("Y-m-d h:i:s",strtotime("+ {$package->duration} days")));
			return redirect()->to(isset($request["redirect_to"]) ? $request["redirect_to"] : base_url('/account/your-info'));
		}
	}
	public function subscribers(){
		$data["subscriptions"] = model('Accounts\Models\PackageSubscribers')->getSubscriptions([],true);
		return view('Accounts\Views\pages\packages-subscription',$data);
	}

	public function transaction($ref){
		$data["transaction"] = model('Accounts\Models\Transactions')->getTransaction($ref);
		return view('Accounts\Views\pages\transaction',$data);
	}
	public function transactions(){
		$data["transactions"] = model('Accounts\Models\Transactions')->transactions([]);
		return view('Accounts\Views\pages\transactions',$data);
	}

	public function subscribeToNewsLetter(){
		$request = $this->request->getPost("email");
		$model = model('NewsletterSubscribers');
		if($model->where("email",$request)->countAllResults() < 1){
			$model->save(['email'=>$request]);
		}
		return $this->response->setJSON(['status'=>true]);
	}

	public function subscribeToNewsLetterViaURL($encoded_email,$action='subscribe'){
		$email = base64_decode(base64_decode($encoded_email));
		$model = model('Accounts\Models\NewsletterSubscribers');
		if(filter_var($email,FILTER_VALIDATE_EMAIL)){
			if($action == 'subscribe'){
				if($model->where("email",$email)->countAllResults() < 1){
					$model->save(['email'=>$email]);
				}
				set_notification('success',"Thank you <strong>{$email}</strong> for joining baboyo newsletter mailing list");
			}
			elseif($action == 'unsubscribe'){
				$model->where('email',$email)->delete();
				set_notification('success',"Your email <strong>{$email}</strong> has been removed from baboyo's newsletter mailing list");
			}
		}else{
			set_notification('error',"Failed to add you to baboyo newsletter mailing list. Invalid Email Address");
		}
		return redirect()->to(base_url());
	}

	public function predictionStudio($date = null){
		if(!$date){
			$date = date("Y-m-d"); 
		}
		$matches = model("PuntersLounge\Models\Matches")->select('away_team,event,home_team,match_time,match_status,slug')->where("match_date = '{$date}'")->findall();
		array_map(function($r){
			$r->match_time = date('H:i',strtotime($r->match_time));
		},$matches);
		$matches = array_reduce($matches,function($r, $a){
			$key = $a->event;
			if(!isset($r[$key])){
				$r[$key] = [];
			}
			array_push($r[$key],$a);
			return $r;
		});

		$data["matches"] = $matches;

		$data['my_predictions'] = model("PuntersLounge\Models\Predictions")->select("prediction,home_team,away_team,match_slug,match_status,predictions.id")->join("matches","matches.slug = predictions.match_slug",'after')->where(['user'=>session("user_id")])->where(['matches.match_date'=>$date])->orderBy('predictions.id','desc')->groupBy('match_slug')->findall();
		$data["title"] = "Prediction Studio";
		$data['date'] = $date;
		return view('Accounts\Views\pages\prediction-studio',$data);
	}

	public function adManagement(){
		$data["title"] = "Ad Management";
		return view('Accounts\Views\pages\ads-management',$data);
	}
}
