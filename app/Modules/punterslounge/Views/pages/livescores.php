<?= $this->extend("layouts/frontend") ?>
<?= $this->section("content") ?>
<?php 
    $events = array_keys($matches??[]);
?>
<div class="betting pt-4 min-75vh" style="background:url(/assets/img/banner-1.jpg);background-position:center center;background-size:100%;background-attachment: fixed;background-repeat: no-repeat;background-blend-mode: overlay;background-color:#ffffffdb;background-origin: border-box">
    <div class=" py-3 mb-3 d-none" id="filter-menu" >
        <div  class="container">
            <div class="row">
                <div class="col-md-6 col-12 order-0 order-md-1">
                    <b-form-datepicker today-button @input="loadMatch" v-model="current_date" locale="en"></b-form-datepicker>
                </div>
                <div class="form-group  col-md-6 col-12 order-md-2 mb-0">
                    <b-form-input debounce="100000" autocomplete="off" v-model.lazy.trim="competition" type="search" class="form-control" list="competitions" placeholder="Scroll To Competition" ></b-form-input>
                    <datalist id="competitions">
                        <?php if(isset($events)):?>
                        <?php foreach ($events as $key):?>
                        <option value="<?=url_title($key)?>"></option>
                        <?php endforeach?>
                        <?php endif?>
                    </datalist>
                </div>
            </div>
        </div>
        <b-modal  body-class="p-0" centered scrollable no-close-on-backdrop v-model="showStats" size="lg" hide-header hide-footer lazy>
            <button @click.prevent="showStats = false" class="text-right btn btn-sm"><i class="fa fa-times-circle"></i></button>
            <iframe src="/predictions-statistics" style="width:100%;min-height:80vh" frameborder="0"></iframe>
        </b-modal>
        <subscription-form/>
    </div>
    <?= view_cell('\App\Libraries\ReusableComponents::predictionTable', ['matches'=>$matches,'events'=>$events]) ?>  
</div>
<?=get_ad_code('footer')?>

<script type="text/x-template" id="subscription-form">
    <b-modal  modal-class="" lazy content-class="bg-transparent border-0" body-class="p-0" size="xl"  v-model='show_form' centered hide-header hide-footer no-close-on-backdrop>
        <div class="newsletter" >
            <div
             class="row no-gutters justify-content-center">
                <div class="col-lg-5">
                    <div class="part-img">
                        <img src="/assets/img/newsletter-img.png" alt="">
                    </div>
                </div>
                <div class="col-lg-7">
                    <b-overlay :show="isLoading"> 
                        <div class="part-text">
                            <h4>Baboyo Newsletter</h4>
                            <p>Join our mailing list to get the latest prediction tips, news updates and special offers delivered directly to your inbox</p>
                            <div class="part-form" style="background:none">
                                <form @submit.prevent="handleSubmit($event)">
                                    <input type="email" name="email" required placeholder="Your email address">
                                    <button type="submit" class="btn-dark text-white rounded-0 bg-dark btn-lg ">Subscribe</button>
                                </form>
                            </div>
                            <span class="text-white pt-5">Remind me <select class="text-white bg-transparent border-0" v-model="reminder_duration">
                                <option class="text-dark" :value="null">-- when? --</option>
                                <option class="text-dark" :value="1">Tommorrow</option>
                                <option class="text-dark" :value="7">In a week time</option>
                                <option class="text-dark" :value="30">Next Month</option>
                            </select></span>
                            <!-- <button @click.prevent="remindMeTommorrow" class='btn btn-link text-white'>Remind me tommorrow</button> -->
                        </div>
                    </b-overlay>
                </div>
            </div>
        </div>
    </b-modal>
</script>

<script>
    Vue.component('subscription-form',{
        template:"#subscription-form",
        data(){
            return {
                show_form:false,
                isLoading:false,
                reminder_duration:null
            }
        },
        mounted(){
            let show_subscription =  Cookies.get('show_subscription');
            if(show_subscription == undefined){
                setTimeout(() => {
                    this.show_form = true; 
                }, 5000);
            }
        },
        watch:{
            reminder_duration(value){
                if(value !== null){
                    Cookies.set('show_subscription',"baboyo",{ expires : value })
                    this.show_form=false;
                }
            }
        },

        methods:{
            handleSubmit(e){
                this.isLoading = true;
                let formdata = new FormData(e.target);
                fetch("/subscribe-to-newsletter",{
                    method:"POST",
                    body:formdata
                }).then(res => res.json())
                .then(result => {
                    if(result && result.status){
                        this.$bvModal.msgBoxOk("Thank you for subscribing",{
                            title:"Feedback",
                            centered:true,
                            headerClass:"p-2 border-0",
                            footerClass:"p-2 border-0",
                            size:'sm',
                            noCloseOnBackdrop:true,
                        })
                        .then(value => {
                            Cookies.set('show_subscription',"baboyo",{ expires : 365 });
                            this.show_form = false;
                        })
                    }
                    this.isLoading = false;
                }).catch(err => {})
            }
        }
    })
</script>

<script>
    new Vue({
        el:"#filter-menu",
        data(){
            const now = new Date()  
            return {
                competition:null,
                current_date: '<?=$date?>',
                showStats:false,
            }
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
            loadMatch(){
                location = "<?=base_url("livescore")?>"+`/${this.current_date}`
            },
            scrollElementToBottom(el) {
                let element = document.querySelector(el)
                let styles = ["flash","infinite","animated"];
				element.scrollIntoView({ behavior: "smooth", block: "center", inline: "nearest" });
                setTimeout(() => {
                    element.classList.remove(...styles)
                }, 3000);
                element.classList.add(...styles)
            }
        }
    })
</script>
<?= $this->endSection() ?>