<?= $this->extend("layouts/core") ?>
<?= $this->section("layout") ?>

<?php 
if($matches){
    $events = array_keys($matches);
}
?>
<?=$this->include("partials/preloader")?>
<div class="betting bg-white  pt-2 ">
    <div class=" py-2 mb-3" id="filter-menu" >
        <div  class="container">
            <div class="row">
                <div class="col-md-6 col-12">
                    <b-form-datepicker today-button @input="loadMatch" v-model="current_date" locale="en"></b-form-datepicker>
                </div>
                <div class="form-group  col-md-6 col-12">
                    <b-form-input debounce="100000" autocomplete="off" autofocus v-model.lazy.trim="competition" type="search" class="form-control" list="competitions" placeholder="Scroll To Competition" ></b-form-input>
                    <datalist id="competitions">
                        <?php if(isset($events)):?>
                        <?php foreach ($events as $key):?>
                        <option value="<?=url_title($key,"-",true)?>"></option>
                        <?php endforeach?>
                        <?php endif?>
                    </datalist>
                </div>
            </div>
        </div>
    </div>
    <div class="container-fluid">
        <div class="betting-table">
            <div class="row">
                <div class="col-12">
                    <div class="tab-content bet-tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="football">
                            <div class="sport-content-title">
                                <h3 class="d-flex justify-content-between">Football Matches
                                </h3>
                            </div>
                            <?php if($matches && count($matches) > 0):?>
                            <?php foreach ($events as $event) : ?>
                                <table id="<?=url_title($event,"-",true)?>" class="table table-borderless table-sm table-responsive animated">
                                    <thead class="thead-light">
                                        <tr>
                                            <th style="width:2rem"><img src="<?=$matches[$event][0]->logo?>"></th>
                                            <th style="width:100%" colspan="2" class="small font-weight-bold text-center"><?=$event?></th>
                                            <th style="width:5rem">Tip</th>
                                            <th class="text-center" style="width:1rem">1</th>
                                            <th class="text-center" style="width:1rem">X</th>
                                            <th class="text-center" style="width:1rem">2</th>
                                            <th style="width:1rem">HT1</th>
                                            <th style="width:1rem">HTX</th>
                                            <th style="width:1rem">HT2</th>
                                            <th style="width:1rem">1.5</th>
                                            <th style="width:1rem">2.5</th>
                                            <th style="width:1rem">3.5</th>
                                            <th style="width:1rem">BTS</th>
                                            <th style="width:1rem">OTS</th>
                                        </tr>
                                    </thead>
                                        <tbody>
                                            <?php foreach($matches[$event] as $key):?>
                                            <tr>
                                                <td class=""> 
                                                    <span class="badge p-0 font-weight-normal">
                                                    <?php if (env('CI_ENVIRONMENT') == 'production') : ?>
                                                    <?= date("h:i A",strtotime('+ 5 hour',strtotime($key->match_time)))?>
                                                    <?php else:?>
                                                    <?=date("h:i A",strtotime($key->match_time))?>
                                                    <?php endif?>
                                                    </span>
                                                </td>
                                                <td style="width:50%" class="text-right">
                                                    <div class="d-flex justify-content-end align-items-center">
                                                        <div class="small mr-2"><?= $key->home_team ?></div>
                                                        <div class="info-box"><?= $key->home_team_goals ?? "-" ?></div>
                                                    </div>
                                                </td>
                                                <td style="width:50%" class="text-left">
                                                    <div class="d-flex justify-content-start align-items-center">
                                                        <div class="info-box"><?= $key->away_team_goals ?? "-" ?></div>
                                                        <div class="small ml-2"><?= $key->away_team ?></div>
                                                    </div>
                                                </td>
                                                <td class="d-flex">
                                                    <div class=""><span class="info-box"><?= $key->more_info["tip"] ?? "-" ?></span></div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="info-box"><i class="fa fa-thumbs-up text-success"></i></div>
                                                        <div class="info-box"><?= $key->more_info["likepositive"] ?? "-" ?></div>
                                                    </div>
                                                    <div class="d-flex align-items-center">
                                                        <div class="info-box"><i class="fa fa-thumbs-down text-danger"></i></div>
                                                        <div class="info-box"><?= $key->more_info["likenegative"] ?? "-" ?></div>
                                                    </div>
                                                </td>
                                                <td>
                                                    <div class="info-box color-1"><?= $key->more_info["1"] ?? "-" ?></div>
                                                </td>
                                                <td>
                                                    <div class="info-box color-2"><?= $key->more_info["X"] ?? "-" ?></div>
                                                </td>
                                                <td>
                                                    <div class="info-box color-3"><?= $key->more_info["2"] ?? "-" ?></div>
                                                </td>
                                                <td>
                                                    <div class="info-box color-4"><?= $key->more_info["HT1"] ?? "-" ?></div>
                                                </td>
                                                <td>
                                                    <div class="info-box color-5"><?= $key->more_info["HTX"] ?? "-" ?></div>
                                                </td>
                                                <td>
                                                    <div class="info-box color-6"><?= $key->more_info["HT2"] ?? "-" ?></div>
                                                </td>
                                                <td>
                                                    <div class="info-box color-7"><?= $key->more_info["1.5"] ?? "-" ?></div>
                                                </td>
                                                <td>
                                                    <div class="info-box color-8"><?= $key->more_info["2.5"] ?? "-" ?></div>
                                                </td>
                                                <td>
                                                    <div class="info-box color-9"><?= $key->more_info["3.5"] ?? "-" ?></div>
                                                </td>
                                                <td>
                                                    <div class="info-box color-10"><?= $key->more_info["BTS"] ?? "-" ?></div>
                                                </td>
                                                <td>
                                                    <div class="info-box color-11"><?= $key->more_info["OTS"] ?? "-" ?></div>
                                                </td>
                                            </tr>
                                            <?php endforeach?>
                                        </tbody>
                                    </table>
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
</div>
<script>
    new Vue({
        el:".betting",
        data(){
            return {
                competition:null,
                current_date: '<?=$date?>',
            }
        },
        watch:{
            competition(value){
                this.scrollElementToBottom("#"+value);
            }
        },
        mounted(){
            this.$el.classList.remove("d-none");
            setTimeout(() => {
                this.highlightCompetition();
            }, 1000);
        },
        methods:{
            loadMatch(){
                location = "<?=base_url("get-predictions-tip")?>"+`/${this.current_date}`
            },
            scrollElementToBottom(el) {
                let element = document.querySelector(el)
                let styles = ["flash","infinite","animated"];
				element.scrollIntoView({ behavior: "smooth", block: "center", inline: "nearest" });
                setTimeout(() => {
                    element.classList.remove(...styles)
                }, 3000);
                element.classList.add(...styles)
            },
            highlightCompetition(){
                let competition = new URLSearchParams(window.location.search).get("competition");
                let styles = ["shake","infinite"];
                if(competition){
                    let element = document.querySelector("#"+competition.toLowerCase());
                    if(element){

                        this.scrollElementToBottom("#"+competition.toLowerCase());
                        setTimeout(() => {
                            element.classList.remove(...styles)
                        }, 3000);
                        element.classList.add(...styles)
                    }

                }
            }
        }
    })
</script>
<?= $this->endSection() ?>