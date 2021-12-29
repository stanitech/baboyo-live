<?php 
if($matches){
    $events = array_keys($matches);
}
?>
<div class="container">
    <?=get_ad_code('header')?>
    <div class="betting-table">
        <div class="row">
            <div class="col-lg-2 d-none d-lg-block">
                <div class="bett-menu">
                    <ul class="nav nav-tabs" id="myTab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active">
                                <i class="flaticon-football"></i>
                                <span class="text">
                                    football
                                </span>
                            </a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link text-reset">
                                <span class="text mb-0">Prediction Stats</span>
                                <div >
                                    <h1 class='display-4 text-dark my-0'><?=$stat->win_rate?></h1> 
                                </div>
                                <div class='p-2 text-uppercase text'>
                                    <table class='table table-borderless table-sm small'>
                                        <tr>
                                            <td class='text-left'>Selected</td>
                                            <td class='text-right'><?=$stat->selected?></td>
                                        </tr>
                                        <tr>
                                            <td class='text-left'>Won</td>
                                            <td class='text-right'><?=$stat->won?></td>
                                        </tr>
                                        <tr>
                                            <td class='text-left'>Lost</td>
                                            <td class='text-right'><?=$stat->lost?></td>
                                        </tr>
                                        <tr>
                                            <td class='text-left'>Unplayed</td>
                                            <td class='text-right'><?=$stat->unplayed?></td>
                                        </tr>
                                    </table>
                                </div>
                            </a>
                            <a target="_blank" href="/predictions-statistics/<?=is_null($slug) ? '':$slug?>" class='small p-2 text-right'>More Stats</a>
                        </li>
                    </ul>
                </div>
            </div>
            <div class="col-lg-10">
                <div class="tab-content bet-tab-content">
                    <div class="tab-pane fade show active" id="football">
                        <div class="sport-content-title">
                            <h3 class="d-flex justify-content-between mb-2">Predicted Matches</h3>
                        </div>
                        <?php if($matches && count($matches) > 0):?>
                        <?php foreach ($events as $event) : ?>
                        <div class="sports-list shadow-none" id="<?=url_title($event)?>">
                            <h4  class="title"><?=$event?></h4>
                            <?php foreach($matches[$event] as $key):?>
                            <div class="single-sport-box border-0" >
                                <div class="d-flex flex-column">    
                                    <small class="small d-inline-block d-md-none"><?php if (env('CI_ENVIRONMENT') == 'production') : ?>
                                        <span class="bet-price"><?= date("h:i A",strtotime('+ 5 hour',strtotime($key->match_time))) ?></span>
                                        <?php else:?>
                                        <span class="bet-price"><?= date("h:i A",strtotime($key->match_time)) ?></span>
                                        <?php endif?>
                                    </small>
                                    <div class='d-flex'>
                                        <?php if($key->result == "PENDING"):?>
                                        <div class="part-icon" style="background: #ffd65e"> 
                                            <i class="flaticon-football"></i>
                                        </div>
                                        <?php elseif($key->result == "WON"):?>
                                        <div class="part-icon " style="background: #73ab73">
                                            <i class="fa fa-check-circle "></i>
                                            <span class="badge d-block text-left" style="font-size:0.7rem"><?=$key->result?></span>
                                        </div>
                                        <?php else:?>
                                        <div class="part-icon " style="background: #ff5757">
                                            <i class="fa fa-times-circle "></i>
                                            <span class="badge d-block text-left" style="font-size:0.7rem"><?=$key->result?></span>
                                        </div>
                                        <?php endif?>
                                        <div class="part-team ml-2">
                                            <ul>
                                                <li>
                                                    <span class="team-name"><a href="/tips"><?= $key->home_team ?></a></span>
                                                    <span class="score-number"><a href="/tips"><?= $key->home_team_goals ?? "-" ?></a> </span>
                                                </li>
                                                <li>
                                                    <span class="team-name"><a href="/tips"><?= $key->away_team ?></a></span>
                                                    <span class="score-number"><a href="/tips"><?= $key->away_team_goals ?? "-" ?></a></span>
                                                </li>
                                            </ul>
                                            
                                        </div>
                                    </div>
                                </div>
                                <div class="part-match">
                                    <div class='d-flex justify-content-between'>
                                        <div class="single-place-to-bet animated tada mb-2" >
                                            <a class="py-3" style="border:1px dashed #ffad1f;background:#ffad1f">
                                                <span class="bet-price"><?=$key->prediction?></span>
                                            </a>
                                        </div>
                                        <div class="single-place-to-bet d-none d-md-block">
                                            <a >
                                                <?php if (env('CI_ENVIRONMENT') == 'production') : ?>
                                                <span class="bet-price"><?= date("h:i A",strtotime('+ 5 hour',strtotime($key->match_time))) ?></span>
                                                <?php else:?>
                                                <span class="bet-price"><?= date("h:i A",strtotime($key->match_time)) ?></span>
                                                <?php endif?>
                                                <span class="result-for-final">
                                                    Match Time
                                                </span> 
                                            </a>
                                        </div>
                                        <div class="single-place-to-bet">
                                            <a >
                                                <span class="bet-price"><?= $key->match_status  ? $key->match_status : "PLAYING"?></span>
                                                <span class="result-for-final">
                                                    Match Status
                                                </span>
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <?php endforeach?>
                            
                        </div>
                        <?php if(mt_rand(0,100) % 5 == 0):?>
                            <div class="sports-list shadow-none" >
                                <?=get_ad_code('prediction')?> 
                            </div>
                        <?php endif?>
                        <?php endforeach?>
                        <?php else:?>
                            <h3 class="text-uppercase text-center mt-3">No Result returned</h3>
                        <?php endif?>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>