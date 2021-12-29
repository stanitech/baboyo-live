<?php

if (!function_exists("get_livescores")) {
    function get_livescores($date,$use_dummy = false,$sync = false)
    {
        $history["status"] = "SUCCESS";
        $history["type"] = "LIVESCORES";
        $history["target_date"] = $date;
        $history["created_by"] = session("user_id");

        $results = [];
        include_once(APPPATH . 'ThirdParty/simple_html_dom.php');
        if($use_dummy){
            $url = APPPATH . 'ThirdParty/dummy_page_livescores.html';
        }
        else {
            $url = 'https://www.statarea.com/livescore/date/'.$date;
        }
        try {
            $html = file_get_html($url);
            foreach ($html->find('.competition') as $element) {
                $competition_name = $element->find('.header .name', 0)->plaintext;
                if ($competition_name !== "Advertisement") {
                    $competition["name"] = $competition_name;
                    $competition["matches"] = [];
                    foreach ($element->find(".body .match") as $match) {
                        $m = new stdClass();
                        $m->time = $match->find(".startblock .time", 0)->plaintext;
                        $m->status = $match->find(".startblock .status", 0)->plaintext ?? "PLAYING";
                        $m->hostteam['goals'] = $match->find('.hostteam .goals', 0)->plaintext;
                        $m->hostteam['name'] = $match->find('.hostteam .name', 0)->plaintext;
                        $m->guestteam['goals'] = $match->find('.guestteam .goals', 0)->plaintext;
                        $m->guestteam['name'] = $match->find('.guestteam .name', 0)->plaintext;
                        $m->slug = strtolower(md5($m->hostteam['name'] . $m->guestteam['name']));
                        $competition["matches"][] = $m;
                    }
                    $data[] = $competition;
                }
            }
            $matches_model = model("PuntersLounge\Models\Matches");
            $matches_model->where("match_date",$date)->delete();
            foreach ($data as $event) {
                foreach ($event["matches"] as $key){
                    $match_status = "PLAYING";
                    if($key->status == "fin"){
                        $match_status = "FINISHED";
                    }
                    elseif($key->status == "sched"){
                        $match_status = "SCHEDULED";
                    }
                    elseif($key->status == "canc"){
                        $match_status = "CANCELLED";
                    }
                    elseif($key->status == "HT"){
                        $match_status = "HALF TIME";
                    }
                    elseif($key->status == "post"){
                        $match_status = "POSTPONED";
                    }
                    $matches_model->save([
                        "event"=> str_replace("'",'',$event["name"]),
                        "slug"=>$key->slug,
                        "home_team"=>str_replace("'",'',$key->hostteam["name"]),
                        "away_team"=>str_replace("'",'',$key->guestteam["name"]),
                        "home_team_goals"=>$key->hostteam["goals"] == "-" ? null : $key->hostteam["goals"],
                        "away_team_goals"=>$key->guestteam["goals"] == "-" ? null : $key->guestteam["goals"],
                        
                        "match_status"=>$match_status,
                        "match_date"=> $date ?? date("Y-m-d"),
                        "match_time"=>$key->time
                    ]);
                }
            }
            
            
            $results =  $data;

 
            $html->clear();
        } catch (\Throwable $th) {
            $history["status"] = "FAILED";
            $history["message"] = $th->getMessage();
        }
        finally{
           
            model("PuntersLounge\Models\UpdateHistory")->save($history);
            unset($history,$url,$html,$competition_name,$competition,$m,$key,$event,$data,$matches_model);
            if($sync){
                get_assisted_predictions($date);
            }
            return $results;
        }
    }
}

if (!function_exists("get_assisted_predictions")) {
    function get_assisted_predictions($date,$use_dummy = false,$sync = false)
    {  

        $history["status"] = "SUCCESS";
        $history["type"] = "PREDICTION TIPS";
        $history["target_date"] = $date;
        $history["created_by"] = session("user_id");

        $results = [];
        $bet_tags = ["1","X","2","HT1","HTX","HT2","1.5","2.5","3.5","BTS","OTS"];
        include_once(APPPATH . 'ThirdParty/simple_html_dom.php');

        if($use_dummy){
            $url = APPPATH . 'ThirdParty/dummy_page_predictions.html';
        }
        else {
            $url = "https://www.statarea.com/predictions/date/$date/competition/";
        }

        try {
            $html = file_get_html($url);
            foreach ($html->find('.competition') as $element) {
                $competition_name = $element->find('.header .name', 0)->plaintext;
                if ($competition_name !== "Advertisement") {
                    $competition["name"] = $competition_name;
                    $competition['logo'] = $element->find('.header .logo img', 0)->src;
                    $competition["matches"] = [];

                     foreach ($element->find(".body .match") as $match) {
                        $m = new stdClass();
                        $m->time = $match->find(".date", 0)?->plaintext;
                        
                         $m->hostteam['goals'] = $match->find('.hostteam .goals', 0)->plaintext;
                         $m->hostteam['name'] = $match->find('.hostteam .name', 0)->plaintext;
                         $m->guestteam['goals'] = $match->find('.guestteam .goals', 0)->plaintext;
                         $m->guestteam['name'] = $match->find('.guestteam .name', 0)->plaintext;
                         $m->slug = strtolower(md5($m->hostteam['name'] . $m->guestteam['name']));
                        $i = 0;
                        $info = [];
                         foreach($match->find(".coefbox .value") as $tt){
                            $info[$bet_tags[(string)$i]] = $tt->plaintext;
                            $i++;
                         }
                         $info['likepositive'] = $match->find(".likepositive .value", 0)->plaintext;
                         $info['likenegative'] = $match->find(".likenegative .value", 0)->plaintext;
                         $info['tip'] = $match->find(".tip .value", 0)->plaintext;

                         $m->info = $info;
                         $competition["matches"][] = $m;
                     }
                    $data[] = $competition;
                }
            }
            $matches_model = model("PuntersLounge\Models\PredictionsTip");
            $matches_model->where("match_date",$date)->delete();
            foreach ($data as $event) {
                foreach ($event["matches"] as $key)
                $matches_model->save([
                    "event"=> str_replace("'",'',$event["name"]),
                    "slug"=>$key->slug,
                    "home_team"=>str_replace("'",'',$key->hostteam["name"]),
                    "away_team"=>str_replace("'",'',$key->guestteam["name"]),
                    "home_team_goals"=>$key->hostteam["goals"] == "-" ? null : $key->hostteam["goals"],
                    "away_team_goals"=>$key->guestteam["goals"] == "-" ? null : $key->guestteam["goals"],
                    "logo"=>$event["logo"],
                    "more_info"=>serialize($key->info),
                    "match_date"=> $date,
                    "match_time"=>$key->time
                ]);
            }
            $results =  $data;
            $html->clear();
            
        } catch (\Throwable $th) {
            $history["status"] = "FAILED";
            $history["message"] = $th->getMessage();
        }
        finally{
            model("PuntersLounge\Models\UpdateHistory")->save($history);
            unset($updates,$url,$html,$competition_name,$competition,$m,$key,$event,$data);
            if($sync){
                get_assisted_predictions($date);
            }
            return $results;
        }
    }
}

if (! function_exists('is_logged_in')) {
    function is_logged_in()
    {
        return session()->has("logged_in") && session("logged_in") == true;
    }
}
if (! function_exists('can_access')) {
    function can_access(array $account_types = [])
    {
        return session()->has("logged_in") && session("logged_in") == true && in_array(session("account_type"), $account_types);
    }
}
if (! function_exists('humanize_time')) {
    function humanize_time($time)
    {
        return CodeIgniter\I18n\Time::parse($time,'Africa/Lagos')->humanize();
    }
}

if (! function_exists('winning_logic')){
    function winning_logic($prediction,$home_team_goals,$away_team_goals)
    {
        if ($prediction === "BTS" && $home_team_goals > 0 && $away_team_goals > 0) {
            $winning_status = "WON";
        }
        elseif ($prediction === "OTS" && (($home_team_goals == 0 && $away_team_goals == 0) || ($home_team_goals == 0 && $away_team_goals > 0) || ($away_team_goals == 0 && $home_team_goals > 0))) {
            $winning_status = "WON";
        }
        elseif ($prediction === "1" && $home_team_goals > $away_team_goals) {
            $winning_status = "WON";
        }
        elseif ($prediction === "1X" && $home_team_goals >= $away_team_goals) {
            $winning_status = "WON";
        }
        elseif ($prediction === "X" && $home_team_goals === $away_team_goals) {
            $winning_status = "WON";
        }
        elseif ($prediction === "2" && $home_team_goals < $away_team_goals) {
                    $winning_status = "WON"; 
        }
        elseif ($prediction === "2X" && $home_team_goals <= $away_team_goals) {
            $winning_status = "WON";
        }
        elseif ($prediction === "12" && $home_team_goals !== $away_team_goals) {
            $winning_status = "WON";
        }
        elseif ($prediction === "Over 1.5" && ($home_team_goals + $away_team_goals) >= 2) {
            $winning_status = "WON";
        }
        elseif ($prediction === "Over 2.5" && ($home_team_goals + $away_team_goals) >= 3) {
            $winning_status = "WON";
        }
        elseif ($prediction === "Over 3.5" && ($home_team_goals + $away_team_goals) >= 4) {
            $winning_status = "WON";
        }
        elseif ($prediction === "Under 2.5" && ($home_team_goals + $away_team_goals) < 3) {
            $winning_status = "WON";
        }
        elseif ($prediction === "Under 3.5" && ($home_team_goals + $away_team_goals) < 4) {
            $winning_status = "WON";
        }elseif ($prediction === "Under 4.5" && ($home_team_goals + $away_team_goals) < 5) {
            $winning_status = "WON";
        }else{
            $winning_status = "LOST";
        }
        return  $winning_status;
    }
}

if (!function_exists('send_email')) {
    function send_email(string $to, string $subject,array $data = [])
    {

        $mail = service("phpmailer", false);
        $site_name      = env('app.name');
        $site_email     = env('app.email');
        try {

            if (env('app.email.type') == "isSMTP") {
                $mail->isSMTP();
            } else {
                $mail->isMail();
            }

            $mail->Host = env('app.email.host');
            $mail->SMTPAuth = env('app.email.auth');
            $mail->Username = env('app.email.username');
            $mail->Password = env('app.email.password');
            $mail->SMTPSecure = env('app.email.secure');
            $mail->Port = env('app.email.port');
            $mail->setFrom($site_email, $site_name);
            $mail->addReplyTo($site_email, $site_name);
            $mail->addAddress($to);
            $mail->Subject = $subject;
            $mail->isHTML(true);
            $mail->Body = view("email/index", $data);
            $mail->send();
            return ["status" => true, "message" => "Email Sent to $to"];
        } catch (\Throwable $th) {
            return ["status" => false, "message" => $th->getMessage()];
        } finally {
            $mail->smtpClose();
        }
    }
}
if (!function_exists('humanize_currency')) {
    function humanize_currency($amount)
    {
        return "₦ " . number_format($amount);
    }
}
if (! function_exists('set_notification')) {
    function set_notification($status="success",$text="I am a notification bar")
    {
        $color = "bg-success";
        if($status == "error"){
            $color = "bg-danger";
        }elseif ($status == "warning") {
            $color = "bg-warning";
        }
        elseif ($status == "success") {
            $color = "bg-success";
        }
        elseif ($status == "info") {
            $color = "bg-info";
        }
        $alert = "<script> Snackbar.show({text:'$text',pos: 'top-center',customClass:'$color',duration:20000,actionTextColor:'#666'});</script>";
        session()->setFlashdata('notification', $alert);
    }
}
if (!function_exists('can_access_prediction_tip')) {
    function can_access_prediction_tip($package,$expert_id,$date)
    {
        if(env("app.setting.activate_package_mode") == "true" && count($package) > 0){
            if(strtotime($date) < strtotime(date("Y-m-d"))){
                return true;
            }
            if(is_logged_in() && $expert_id == session("user_id")){
                return true;
            }
            $my_subs = model("PackageSubscribers")->join("packages","packages.id = package_subscribers.package","LEFT")->where("expert",$expert_id)->where("user",session("user_id"))->where(["expires_at >"=>date("Y-m-d h:i:s")])->countAllResults();
            if($my_subs > 0){
                return true;
            }
            return false;
        }else{
            return true;
        }
    }
}
if (! function_exists('setPath')) {
    function setPath(string $path): string
    {
        if (! is_dir($path)) {
            mkdir($path, 0777, true);
            //create the index.html file
            if (! is_file($path . 'index.html')) {
                $file = fopen($path . 'index.html', 'x+');
                fclose($file);
            }
        }
        return $path;
    }
}

 if (! function_exists('upload_file')) {
     function upload_file($file, $width = 800)
     {
         list($w, $h) = getimagesize($file);
         $folderName = rtrim(date('Ymd'), '/') . '/';
         setPath(WRITEPATH . 'uploads/'.$folderName) ;
         $filepath   = $folderName.$file->getRandomName();

         $img = service("image")->withFile($file->getTempName());
         if ($w > $width) {
             $img = $img->resize($width, $width, true, 'width');
         }
         $img = $img->/*convert(IMAGETYPE_JPEG)->*/save(WRITEPATH . 'uploads/'.$filepath, 70);
         return $img ? $filepath : null;
     }

    if (! function_exists('prediction_statistics')) {
        function prediction_statistics($matches)
        {
            $events = array_keys($matches ?? []);
            $selected = 0;
            $won = 0;
            $lost = 0;
            $unplayed = 0;

            foreach ($events as $event){
                foreach($matches[$event] as $key){
                   if($key->result == 'WON'){$won++;}
                   elseif($key->result == 'LOST'){$lost++;}
                    if($key->match_status !== "FINISHED" && $key->match_status !== "PLAYING"){
                        $unplayed ++;
                    }
                   $selected++;
                }

            }
            $win_rate = $won == 0 ? '--': round(($won/($selected - $unplayed))*100).'%';
            return (object)['won'=>$won,'lost'=>$lost,'selected'=>$selected,'unplayed'=>$unplayed,'win_rate'=>$win_rate];
        }
    }

 }
 if (! function_exists('get_ad_code')) {
        function get_ad_code($section,$script=true){
            $code =  model("Options")->getOption("ad_management")?->ads[$section];
            if(str_contains($code,'adsbygoogle') && $script){
                return 
                '<script async src="https://pagead2.googlesyndication.com/pagead/js/adsbygoogle.js?client=ca-pub-4333731149536913"crossorigin="anonymous"></script>'.$code.'<script>(adsbygoogle = window.adsbygoogle || []).push({});</script>';
            }
            
            return $code;
        }
 }



 if (!function_exists("scrape_teams")) {
    function scrape_teams()
    {
        include_once(APPPATH . 'ThirdParty/simple_html_dom.php');
      
           $url = APPPATH . 'ThirdParty/league_dummy_page.html';
        
            //$url = 'http://www.statarea.com/rankings/world/';
        
        $html = file_get_html($url);
        $data = [];
     
                foreach ($html->find(".teamitem") as $item) {
                    $m = new stdClass();
                    
                    $m->name = $item->find(".name ", 0)->plaintext;
                    $m->point = $item->find(".points", 0)->plaintext; 
                    $m->link = $item->find(".name a", 0)->href;
                    $m->img = $item->find(".logo img[src]", 0)->attr['src'];  
                             
                    $data[] = $m;
                }
                
               


        $html->clear();

        //$item_model = model("Teams");
       
            // foreach ($data as $items) {
            //     $item_model->save([
            //         "name"=> $items->name,
            //         "point"=>$items->point,
            //         "img"=>$items->img,
            //         "link"=>$items->link,
                   
            //     ]);
            // }
        

          //dd($data);
        return $data;
    }
  
}



if (!function_exists("scrap_match")) {
    function scrape_match()
    {
        include_once(APPPATH . 'ThirdParty/simple_html_dom.php');
      
           $url = APPPATH . 'ThirdParty/team_compare.html';
        
            //$url = 'http://www.statarea.com/rankings/world/';
        
        $html = file_get_html($url);
        $data = [];
     
                foreach ($html->find(".container .datacotainer") as $item) {
                    $m = new stdClass();
                    
                    $m->match = $item->find(".datacotainerheader h1", 0)->plaintext;
                    $m->img = $item->find(".teamsinfo .thumb img[src]", 0)->attr['src'];
                    $m->team_name = $item->find(".datarow .value", 0)->plaintext;
                    $m->team_found = $item->find(".datarow #teamfound", 0)->plaintext;
                    $m->team_country = $item->find(".datarow #teamcountry", 0)->plaintext;
                    $m->team_country_img = $item->find(".datarow #teamcountry img[src]", 0)->attr['src'];
                    $m->team_site = $item->find(".datarow #teamwebsite", 0)->plaintext;
                    $m->team_rank = $item->find(".datarow .value", 4)->plaintext;

                   
                    $m->away_img = $item->find(".teamsinfo .thumb img[src]", 1)->attr['src'];
                    $m->away_team_found = $item->find(".datarow .value", 6)->plaintext;
                    $m->away_team_country = $item->find(".datarow .value", 7)->plaintext;
                    $m->away_team_country_img = $item->find(".datarow #teamcountry img[src]", 1)->attr['src'];
                    $m->away_team_site = $item->find(".datarow .value", 8)->plaintext;
                    $m->away_team_rank = $item->find(".datarow .value", 9)->plaintext;
                    
                    
                   
                    
                    // $m->link = $item->find(".name a", 0)->href;
                    // $m->img = $item->find(".logo img[src]", 0)->attr['src'];  
                             
                    $data[] = $m;
                }
                
            

        $html->clear();

        //$item_model = model("Teams");
       
            // foreach ($data as $items) {
            //     $item_model->save([
            //         "name"=> $items->name,
            //         "point"=>$items->point,
            //         "img"=>$items->img,
            //         "link"=>$items->link,
                   
            //     ]);
            // }
        

          dd($data);
        return $data;
    }
  
}
