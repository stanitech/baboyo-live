<?= $this->extend('Accounts\Views\layouts\default-with-sidebar') ?>
<?= $this->section("section") ?>
<style>
    .tab-selected{
        border-bottom: 3px solid orangered !important;
        font-weight:bolder;
    }
</style>
<section class="row d-none">
    <div class="col-lg-5  mb-5">
        <h3 class="font-weight-lighter">Platform Updates</h3>
        <ul class="nav nav-tabs border-0 small">
            <?php
                $date_range = [date("Y-m-d",strtotime("- 4 day")),date("Y-m-d",strtotime("- 3 day")),date("Y-m-d",strtotime("- 2 day")),date("Y-m-d",strtotime("- 1 day")),date("Y-m-d"),date("Y-m-d",strtotime("+ 1 day")),date("Y-m-d",strtotime("+ 2 day"))];
                $i = 1;
            ?>
            <?php foreach($date_range as $dr):?>
            <a href="/settings/platform-updates/<?=$dr?>" class="px-2 nav-link text-reset nav-item <?=$date == $dr ? 'tab-selected' : ''?> <?=$i < 3 ? 'd-none d-md-inline-block':''?>"><?=date('Y-m-d') == $dr  ? "TODAY": date('M d',strtotime($dr))?></a>
            <?php $i++?>
            <?php endforeach?>
        </ul>
        <div class="media my-5 pl-0">
            <b-avatar variant="transparent" size="5rem" class="mr-3" square src="/assets/img/stadium.png"></b-avatar>
            <div class="media-body">
                <h6>Livescore Matches for <a target="_blank" href="/livescore/<?=$date?>" class="btn-link font-weight-bolder p-0 text-reset" ><?=date('jS M Y',strtotime($date))?></a> </h6>
                <div>
                    <small class="badge px-0">Update on: <span class="text-muted ml-2 text-uppercase"><?=$recent_livescore_update ? date('d/m/Y h:i a',strtotime($recent_livescore_update->updated_at))." | {$recent_livescore_update->name}": "Never"?></span></small>
                </div>
                <form action="/livescore" method="POST" >
                    <input type="hidden" name="date" value="<?=$date?>">
                    <input type="hidden" name="redirect" value="<?=current_url()?>">

                    <button <?= ($recent_livescore_update && (time() - strtotime($recent_livescore_update->updated_at) < 5 * MINUTE) ? 'disabled' : null) ?> type="submit" class="btn btn-link small p-0 btn-sm"><i class="fa fa-refresh"></i> Update Now</button>
                </form>
            </div>
        </div>
        <div class="media my-5">
            <b-avatar variant="transparent" size="5rem" class="mr-3" square src="/assets/img/tips.png"></b-avatar>
            <div class="media-body">
                <h6>Prediction Tips for <a @click.prevent="showPredictiveTips = true" target="_blank" href="/get-predictions-tip/<?=$date?>" class="btn-link font-weight-bolder p-0 text-reset" ><?=date('jS M Y',strtotime($date))?></a> matches </h6>
                <div>
                    <small class="badge px-0">Update on: <span class="text-muted ml-2 text-uppercase"><?=$recent_prediction_tips_update ? date('d/m/Y h:i a',strtotime($recent_prediction_tips_update->updated_at))." | {$recent_prediction_tips_update->name}": "Never"?></span></small>
                </div>
                <form action="/get-predictions-tip" method="POST" >
                    <input type="hidden" name="date" value="<?=$date?>">
                    <input type="hidden" name="redirect" value="<?=current_url()?>">

                    <button <?= ($recent_prediction_tips_update && (time() - strtotime($recent_prediction_tips_update->updated_at) < 5 * MINUTE) ? 'disabled' : null) ?> type="submit" class="btn btn-link small px-0 btn-sm"><i class="fa fa-refresh"></i> Update Now</button>
                </form>
            </div>
        </div>
        <ul class="list-group list-group-flush my-5">
            <a href="/settings/update-history" class="list-group-item list-group-item-action pl-0">
                <div class="media d-flex align-items-start pl-0">
                    <div class="info-box mr-3">
                        <i class="fa fa-clone"></i>
                    </div>
                    <div class="media-body">
                        <p class="mb-0">View update history</p>
                        <small>See more detailed history on all updates</small>
                    </div>
                </div>
            </a>
        </ul>

        <form action="/settings/options/sync_updates" method="POST" ref="sync_dates"> 
            <input type="hidden" name="redirect" value="<?=current_url()?>">
            <input type="hidden" name="sync_updates" :value="sync_updates">
            <b-checkbox @change="$refs.sync_dates.submit()" v-model='sync_updates' switch>Synchronously updates platform<i class="ml-2 fa fa-question-circle" v-b-tooltip title="This means an update on livescore triggers an update on prediction and vice versa. This uses more computing power and might slow down the platform while updating"></i></b-checkbox>
        </form>

    </div>
    <div class="col-lg-4"></div>
    <div class=" col-lg-3 small my-3">
        <label class="d-block">Packages subscriptions and status</label>
        <a href="/account/subscribers" class="text-primary ">View all </a>
        <label class="d-block mt-5">Inspect and diagnose transactions on the platform</label>
        <a href="/account/transactions" class="text-primary ">View log </a>
        <label class="d-block mt-5">Easily control ads & banners all across the platform</label>
        <a href="/settings/ad-management" class="text-primary ">Set Up </a>
    </div>
    <b-modal body-class="p-0" centered scrollable v-model="showPredictiveTips" size="xl" hide-header hide-footer lazy>
        <button @click.prevent="showPredictiveTips = false" class="text-right btn btn-sm"><i class="fa fa-times-circle"></i></button>
        <iframe :src="`/get-predictions-tip/${current_date}`" style="width:100%;min-height:90vh" frameborder="0"></iframe>
    </b-modal>
</section>
<script>
    new Vue({
        el:"section",
        data:{
            account:null,
            current_date:"<?=$date?>",
            showPredictiveTips:false,
            sync_updates: JSON.parse(<?=json_encode($options->sync_updates)?>)
        },
        mounted(){
            this.$el.classList.remove("d-none");
        },
        methods:{
            
        }
    })
</script>
<?= $this->endSection() ?>