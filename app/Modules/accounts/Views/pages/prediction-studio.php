<?= $this->extend('Accounts\Views\layouts\default-with-sidebar') ?>
<?= $this->section("section") ?>
<style>
    .tab-selected{
        border-bottom: 2px solid orangered !important;
        font-weight:bolder;
    }
</style>
<ul class="nav nav-tabs border-0 small">
    <?php
        $date_range = [date("Y-m-d"),date("Y-m-d",strtotime("+ 1 day"))];
        
    ?>
    <?php foreach($date_range as $dr):?>
    <a href="/account/prediction-studio/<?=$dr?>" class="nav-link text-reset nav-item small <?=$date == $dr ? 'tab-selected' : ''?>"><?=date('Y-m-d') == $dr  ? "TODAY": date('M d',strtotime($dr))?></a>
    <?php endforeach?>
</ul>
<section class="row d-none">
    <div class="card-header col-12 mb-2 d-flex justify-content-around small text-uppercase" id="filter-menu">
        <a href="" @click.prevent="showPredictiveTipsModal" class=" small btn-link"><i class="fa fa-lightbulb"></i>Prediction Insights</a>
        <a href="" @click.prevent="" v-b-toggle.myprediction-sidebar class=" d-inline-block d-md-none small btn-link">Your Selections <span class="badge badge-primary">{{mypredictions.length}}</span></a>
    </div>
    <div class="col-lg-9">
        <div v-if="matches.length == 0" class="alert alert-danger rounded-0" role="alert">
            The Site Administrator has not updated the platform for [<?=$date?>] matches. Please check back later or contact the administrator to make an update
        </div>
        <table v-else class="table table-borderless table-sm table-responsive small" v-for='event in events'>
            <thead class=" thead-dark border-0">
                <tr >
                    <th style="width:2rem"></th>
                    <th style="min-width:10rem;width:100%" class="small font-weight-bold">{{event}}</th>
                    <th ></th>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th></th>
                </tr>
            </thead>
            <tbody class='small'>
                <tr :my-predictions="mypredictions" is='match-item-row' v-for="match in matches[event] " :match='match' @on-predict='handleBetChoice($event)'>
                </tr>

            </tbody>
        </table>
        
    </div>
    <div class="col small " v-if='mypredictions.length > 0' >
        <user-predictions :fixed='true' @on-remove='removeBetChoice($event)' :mypredictions="mypredictions"></user-predictions>
    </div>

    <b-modal body-class="p-0" centered scrollable no-close-on-backdrop v-model="showPredictiveTips" size="xl" hide-header hide-footer lazy>
        <button @click.prevent="showPredictiveTips = false" class="text-right btn btn-sm"><i class="fa fa-times-circle"></i></button>
        <iframe :src="prediction_url" style="width:100%;min-height:90vh" frameborder="0"></iframe>
    </b-modal>
    <b-sidebar id='myprediction-sidebar' sidebar-class='card-body'>
        <user-predictions :fixed='false' @on-remove='removeBetChoice($event)' :mypredictions="mypredictions"></user-predictions>
    </b-sidebar>
</section>

<script type="text/x-template" id="user-predictions">
    <form :class="fixed?'position-fixed':''" method='POST' action='/set-prediction' @submit.prevent='setPrediction($event)'>
        <h6 class="text-uppercase small">Your Selections</h6>
        <div style='height:50vh;overflow:auto' class='table-responsive'>
            <table class="table table-sm small table-hover" >
                <tr v-for='i in mypredictions'>
                    <td style='min-width:10rem;width:100%'>
                        <input type="hidden" name="redirect_back" value="<?=current_url()?>">
                        <input type="hidden" name="predictions_to_remove" value='<?=json_encode(array_column($my_predictions,'id'))?>'>

                        <input type="hidden" name="predictions[]" :value="JSON.stringify({match_slug:i.match_slug,prediction:i.prediction})">
                        <div class="d-flex flex-column">
                            <span class='text-nowrap'>{{i.home_team}}</span>
                            <span class='text-nowrap'>{{i.away_team}}</span>
                        </div>
                    </td>
                    <td tyle='width:2rem' class='text-center'>
                        <span class="badge alert-dark text-wrap d-flex align-items-center justify-content-center rounded-0 p-1" style='width:2rem;height:2rem'>{{i.prediction}}</span>
                    </td>
                    <td style='width:1rem'>
                        <a href='' @click.prevent='$emit("on-remove",i.match_slug)'><i v-if='i.match_status == "SCHEDULED"'  class="fas fa-times text-danger"></i></a>
                    </td>
                </tr>
            </table>
        </div>
        <div class="d-flex justify-content-between card-body">
            <span>Total Games:</span>
            <span>{{mypredictions.length}}</span>
        </div>
        <button type='submit' class='btn btn-block btn-sm rounded-0 btn-dark mt-2'>Set Prediction</button>
    </form>
</script>
<script>
    Vue.component("user-predictions", {
        template: "#user-predictions",
        props:{
            mypredictions:{required:true},
            fixed:{default:true,type:Boolean},
        },
        methods: {
            setPrediction(ev){
                this.$bvModal.msgBoxConfirm('Alright Punter!! Can we proceed with these your predictions?', {
                    title: 'Please Confirm',
                    size: 'sm',
                    buttonSize: 'sm',
                    footerClass: 'p-2 border-0',
                    headerClass: 'p-2 border-0',
                    centered: true
                }).then(value => {
                    if(value){
                        ev.target.submit();
                    }
                })
            },
        },
        
    });
</script>

<script type="text/x-template" id="match-item-row">
    <tr :class="can_predict ? '':'alert-dark'">
        <td >{{match.match_time}}</td>
        <td>
            <div class="d-flex flex-column">
                <span class='text-nowrap'>{{match.home_team}}</span>
                <span class='text-nowrap'>{{match.away_team}}</span>
                <div><span class='badge badge-light'>- {{match.match_status}}</span></div>
            </div>
        </td>
        <td>
            <div class="d-flex">
                <match-item-row-button :selected='isSelected(match.slug,i)' v-for="i in ['1','X','2']"  :context="{text:i,value:i}" @on-predict="handleOnPredict($event)" :can-predict="can_predict"></match-item-row-button>
            </div>
        </td>
        <td>
            <div class="d-flex">
                <match-item-row-button :selected='isSelected(match.slug,i)' v-for="i in ['1X','12','2X']" :context="{text:i,value:i}" @on-predict="handleOnPredict($event)" :can-predict="can_predict"></match-item-row-button>
            </div>
        </td>
        <td>
            <div class="d-flex">
                <match-item-row-button :selected='isSelected(match.slug,i.value)' v-for="i in [{text:'O<br>1.5',value:'Over 1.5'},{text:'O<br>2.5',value:'Over 2.5'},{text:'O<br>3.5',value:'Over 3.5'}]" :context="i" @on-predict="handleOnPredict($event)" :can-predict="can_predict"></match-item-row-button>
            </div>
        </td>
        <td>
            <div class="d-flex">
                <match-item-row-button :selected='isSelected(match.slug,i.value)' v-for="i in [{text:'U<br>2.5',value:'Under 2.5'},{text:'U<br>3.5',value:'Under 3.5'},{text:'U<br>4.5',value:'Under 4.5'}]" :context="i" @on-predict="handleOnPredict($event)" :can-predict="can_predict"></match-item-row-button>
            </div>
        </td>
        <td>
            <div class="d-flex">
                <match-item-row-button :selected='isSelected(match.slug,i)' v-for="i in ['BTS','OTS']" :context="{text:i,value:i}" @on-predict="handleOnPredict($event)" :can-predict="can_predict"></match-item-row-button>
            </div>
        </td>
    </tr>
</script>
<script>
    Vue.component("match-item-row-button", {
        template: `<div class="text-center position-relative">
                    <span class="info-box" :class="selected ? 'border border-danger':''"  v-html='context.text' @click="handleClick"></span>
                    <span v-if='!canPredict' class="info-box position-absolute" style='top:0;cursor:not-allowed;opacity:0.2' v-html='context.text'></span>
                </div>`,
        props:['context','canPredict','selected'],
        
        methods:{
            handleClick(){
                if(!this.selected){
                    this.$emit('on-predict',this.context.value)
                }
            }
        }
    });
</script>
<script>
    Vue.component("match-item-row", {
        template: "#match-item-row",
        props:['match','myPredictions'],
        computed:{
            can_predict(){
                if(this.match.match_status == 'SCHEDULED'){
                    if(this.myPredictions.find(e => e.match_slug == this.match.slug)){
                        return false;
                    }
                    return true;
                }
                return false;
            },
            
        },
        methods: {
            handleOnPredict(prediction){
                let data = {
                    match_slug:this.match.slug,
                    prediction,
                    home_team:this.match.home_team,
                    away_team:this.match.away_team,
                    match_status:this.match.match_status,
                }
                this.$emit('on-predict',data)
            },
            isSelected(slug,prediction){
                let choosen = this.myPredictions.find(e => e.match_slug == slug);
                if(choosen && choosen.prediction == prediction){
                    return true
                }
                return false;
            }
        },
        
    });
</script>
<script>
 new Vue({
        el:"section",
        data:{
            matches:[],
            events:null,
            competition:null,
            mypredictions:[],
            showPredictiveTips:false,
            prediction_url :null,
        },
        mounted(){
            this.$el.classList.remove('d-none');
            this.matches =  JSON.parse(JSON.stringify(<?=json_encode($matches)?>)) ?? [];
            this.events = Object.keys(this.matches);
            this.mypredictions = JSON.parse(JSON.stringify(<?=json_encode($my_predictions)?>)) ?? [];
        },
        methods: {
            handleBetChoice(e){
                this.mypredictions.unshift(e);
            },
            removeBetChoice(slug){
                this.mypredictions.splice(this.mypredictions.findIndex(e => e.match_slug == slug),1)
            },
            showPredictiveTipsModal(competition = null){
               let prediction_url = `/get-predictions-tip/<?=$date?>`;
               if(competition){
                    prediction_url+= `?competition=${competition}`;
               }
               this.prediction_url = prediction_url;
                this.showPredictiveTips = true; 
            }
        },

    })
</script>
<?= $this->endSection() ?>