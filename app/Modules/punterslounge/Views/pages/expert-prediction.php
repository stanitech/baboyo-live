<?= $this->extend("layouts/frontend") ?>
<?= $this->section("content") ?>
<script src="https://js.paystack.co/v1/inline.js"></script> 
<style>
    .tab-selected{
        border-bottom: 3px solid orangered !important;
        font-weight:bolder;
    }
 
    .modal-backdrop{
        opacity:.98;
    }
</style>
<?php 
    $events = array_keys($matches??[]);
?>
<div class="betting pt-0">
    <div id="filter-menu">
        <div class="breadcrumb-bettix blog-page">
            <div class="container">
                <div class="row">
                    <div class="col-lg-8">
                        <div class="breadcrumb-content py-5">
                            <h2><?=$expert->name?>'s Prediction</h2>
                            <ul>
                                <li>
                                    <a href="/"> Home</a>
                                </li>
                                <li><a class="" href="/experts">Experts</a></li>
                                <li><a class="active" href="#">Prediction <?=date("d/m/Y",strtotime($date))?></a></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>
        </div>

    </div>
    <div class="container my-2 app">

        <ul class="nav nav-tabs justify-content-end border-0 small">
            <?php
                $date_range = [date("Y-m-d",strtotime("- 4 day")),date("Y-m-d",strtotime("- 3 day")),date("Y-m-d",strtotime("- 2 day")),date("Y-m-d",strtotime("- 1 day")),date("Y-m-d"),date("Y-m-d",strtotime("+ 1 day"))];
                $i = 1;
            ?>
            <?php foreach($date_range as $dr):?>
            <a href="/expert/prediction/<?=$expert->slug?>/<?=$dr?>" class="nav-link text-reset nav-item px-2 px-md-3 <?=$date == $dr ? 'tab-selected' : ''?> <?=$i < 3 ? 'd-none d-md-inline-block':''?>"><?=date('Y-m-d') == $dr  ? "TODAY": date('M d',strtotime($dr))?></a>
            <?php $i++?>
            <?php endforeach?>
        </ul>
        <div class="row my-2">
            <div class="col-md-6 col-12 order-0 order-md-1">
                <div class="input-group border rounded">
                    <input class="form-control border-0" type="text" name="" placeholder="Search...">
                    <div class="input-group-append">
                        <span class="input-group-text border-0 bg-transparent"><i class="la la-search"></i></span>
                    </div>
                </div>
            </div>
            <div class="form-group  col-md-6 col-12 order-md-2 mb-0">
                <select class="form-control" v-model="competition">
                    <option :value="null" selected disabled>Scroll To Competition</option>
                    <?php if(isset($events)):?>
                    <?php foreach ($events as $key):?>
                    <option value="<?=url_title($key)?>"><?=$key?></option>
                    <?php endforeach?>
                    <?php endif?>
                </select>
            </div>
            <?php if(! $can_access):?>
            <b-modal no-fade static no-close-on-backdrop content-class="bg-transparent"  body-class="p-0 bg-transparent" size="xl" visible hide-header hide-footer>
                <div class="contact">
                    <div class="col-md-7 mx-auto">
                        <p class="lead text-center text-white">This expert has monetize his/her prediction tips. This means you wont be able to see today / future predictions. You'll have to subscribe to see predictions. Though you can see <a class="text-warning" href="/expert/prediction/<?=$expert->slug?>/<?=date("Y-m-d",strtotime("- 1 day"))?>">previous</a> tips</p>
                    </div>
                    <div class="row justify-content-center">
                        <?php foreach ($packages as $key):?>
                        <div class="col-md-4">
                            <div class="contact-information" style="transform:unset">
                                <div class="text-center text-white">
                                    <h3><?=$key->name?></h3>
                                    <h6 class="display-4"><?=humanize_currency($key->amount)?> <sup><small style="font-size:1rem">/ <?=counted($key->duration,'days')?> </small></sup></h6>
                                    <p><?=$key->description?></p>
                                </div>
                                <ul class="info-list">
                                    <li>
                                        <span class="icon">Refundable Policy</span>
                                        <span class="text text-dark font-weight-bold"><?=$key->refundable?></span>
                                    </li>
                                    <li>
                                        <span class="icon">Notification Alerts</span>
                                        <span class="text text-dark font-weight-bold"><?=$key->notification?></span>
                                    </li>
                                </ul>
                                
                                <form action="/account/subscribe" method="POST" class="mt-5" @submit.prevent="initPaystack($event,<?=$key->amount?>)">
                                    <input type="hidden" name="package" value="<?=$key->id?>">
                                    <input type="hidden" name="redirect_to" value="<?=current_url()?>">
                                    <input type="hidden" name="reference" :value="payment_reference">
                                    <?php if(is_logged_in()):?>
                                    <button type="submit" class="btn btn-block btn-dark mt-5">Subscribe</button>
                                    <?php else:?>
                                    <span class="text-center"><i class="fa fa-exclamation-triangle"></i> Login Required</span>
                                    <?php endif?>
                                </form>
                                
                            </div>
                        </div>
                        <?php endforeach?>
                    </div>
                    <div class="text-center mt-3">
                        <a class=" btn-link text-white" href="/experts"><u>Maybe Later</u></a>
                    </div>
                </div>
            </b-modal>
            <?php endif?> 
        </div>
    </div>

    <?= view_cell('\App\Libraries\ReusableComponents::predictionTable', ['matches'=>$matches,'events'=>$events,'slug'=>$expert->slug]) ?>
</div>
<script>
    new Vue({
        el:".app",
        data:{
            current_date:"<?=$date?>",
            competition:null,
            payment_reference:null,
        },
        watch:{
            competition(value){
                this.scrollElementToBottom("#"+value);
            }
        },
        mounted(){
            this.$el.classList.remove("d-none");
        },
        methods:{
            scrollElementToBottom(el) {
                let element = document.querySelector(el)
                let styles = ["flash","infinite","animated"];
				element.scrollIntoView({ behavior: "smooth", block: "center", inline: "nearest" });
                setTimeout(() => {
                    element.classList.remove(...styles)
                }, 3000);
                element.classList.add(...styles)
            },
            initPaystack(ev,amount){
                var handler = PaystackPop.setup({
                    key: '<?=env("app.apis.paystack-publickey")?>',
                    email: '<?=isset($user)?$user->email:''?>',
                    amount: amount * 100, 
                    currency: 'NGN',
                    callback:  (response) => {
                        this.payment_reference = response.reference;
                        console.log(this.payment_reference);
                        setTimeout(() => {
                            ev.target.submit();
                        });

                    }
                })
                handler.openIframe() 
            }
        }
    })
</script>
<?= $this->endSection() ?>