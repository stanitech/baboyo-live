<?= $this->extend('Accounts\Views\layouts\default-with-sidebar') ?>
<?= $this->section("section") ?>
<section class="row app d-none">
    <div class="col-lg-9 col-md-8">
        <h3 class="font-weight-lighter">Your Predictions</h3>
        <?php if($matches){ $events = array_keys($matches);}?>
        <div class="row">
            <div class="col-6 mb-3">
                <div class="input-group border rounded">
                    <input class="form-control border-0" type="text" name="" placeholder="Search...">
                    <div class="input-group-append">
                        <span class="input-group-text border-0 bg-transparent"><i class="la la-search"></i></span>
                    </div>
                </div>
            </div>
            <div class="form-group col-6">
            <select class="form-control" v-model="competition">
                <option :value="null" selected disabled>Scroll To Competition</option>
                <?php if(isset($events)):?>
                <?php foreach ($events as $key):?>
                <option value="<?=url_title($key)?>"><?=$key?></option>
                <?php endforeach?>
                <?php endif?>
            </select>
            </div>
        </div>
        <div class="betting-table">
            <div class="tab-content bet-tab-content" id="myTabContent">
                <div class="tab-pane fade show active" id="football">
                    <div class="sport-content-title">
                        <h3 class="d-flex justify-content-between">Football Matches
                        </h3>
                    </div>
                    <?php if($matches && count($matches) > 0):?>
                    
                    <?php foreach ($events as $event) : ?>
                    <div class="sports-list">
                        <h4 id="<?=url_title($event)?>" class="title"><?=$event?> [<?=count($matches[$event])?>]</h4>
                        <?php foreach($matches[$event] as $key):?>
                        <div class="pl-5 pl-md-0 single-sport-box flex-column flex-md-row justify-content-md-between justify-content-start align-items-start">
                            <?php if($key->result == "PENDING"):?>
                            <div class="part-icon " style="background: #e3e3e3">
                                <i class="flaticon-football"></i>
                                <span class="badge d-block text-left" style="font-size:0.7rem"><?=$key->result?></span>
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
                            
                                <div class="part-team border-0">
                                    <ul>
                                        <li>
                                            <span class="team-name"><?= $key->home_team ?></span>
                                            <span class="score-number"><?= $key->home_team_goals ?? "-" ?></span>
                                        </li>
                                        <li>
                                            <span class="team-name"><?= $key->away_team ?></span>
                                            <span class="score-number"><?= $key->away_team_goals ?? "-" ?></span>
                                        </li>
                                    </ul>
                                    <small class="small text-muted">[<?= date("h:i A",strtotime($key->match_time)) ?>]</small>
                                </div>
                                <div class="part-match">
                                    <div class="single-place-to-bet">
                                        <a href="#">
                                            <span class="bet-price"><?= $key->prediction ?></span>
                                            <span class="result-for-final">
                                                Prediction
                                            </span>
                                        </a>
                                    </div>
                                </div>
                         
                        </div>
                        <?php endforeach?>
                    </div>
                    <?php endforeach?>
                    <?php else:?>
                        <h3 class="text-uppercase text-center mt-3">No Result returned</h3>
                    <?php endif?>
                </div>
            </div>
        </div>
    </div>
    <div class="col-lg-3 col-md-4">
        <b-calendar hide-header selected-variant="dark" @selected="reload" v-model="current_date"></b-calendar>
    </div>
</section>
<script>
    new Vue({
        el:"section",
        data:{
            current_date:"<?=$date?>",
            competition:null,
        },
        mounted(){
            this.$el.classList.remove("d-none");
        },
        methods:{
            reload(){
                location = "<?=base_url("/account/your-predictions")?>"+`/${this.current_date}`
            },
        }
    })
</script>
<?= $this->endSection() ?>