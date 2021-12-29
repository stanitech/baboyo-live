<?= $this->extend("layouts/frontend") ?>
<?= $this->section("content") ?>
<div class="container-fluid my-3" style="min-height:80vh">
    <div class="row no-gutters">
        <div class="col-5 col-md-4 col-lg-3 d-none d-md-block">
        </div>
        <div class="col">
            <div class="sidebar d-none">
                <b-sidebar width="250px" bg-variant="transparent" no-header shadow="sm" sidebar-class="d-none d-md-block" visible >
                    <div class="small" style="margin-top:10rem">
                        <div v-for="i in menu" v-if="i.access" class="list-group list-group-flush text-uppercase small">
                            <a class="list-group-item list-group-item-action list-group-item-light" :href="i.link" :class="location.includes(i.link) ? 'active':''">
                            <i class="fa mr-1" :class="i.icon"></i>{{i.text}}</a>
                        </div>
                    </div>
                </b-sidebar>
                <b-sidebar bg-variant="white" no-header shadow="sm" backdrop id="mobile-sidebar" >
                    <div class="logo text-center my-3 text-center">
                        <a href="/">
                            <b-img width='200' src="<?=env('app.logo')?>" alt="logo"></b-img>
                        </a>
                    </div>
                    
                    <div v-for="i in menu" v-if="i.access" class="list-group list-group-flush small text-uppercase">
                        <a class="list-group-item list-group-item-action list-group-item-light small" :href="i.link" :class="location.includes(i.link) ? 'active':''">
                        <i class="fa mr-1" :class="i.icon"></i>{{i.text}}</a>
                    </div>
                   
                </b-sidebar>
                <button class="btn btn-light d-block d-md-none" v-b-toggle.mobile-sidebar type="button"><span class="fa fa-bars"></span></button>
            </div>
            <?=$this->renderSection("section")?>
            <div class='footer bg-transparent'>
                <?=get_ad_code('footer')?>
            </div>
        </div>
    </div>
</div>
<script>
    var sidebar = new Vue({
        el:".sidebar",
        data:{
            location:window.location.href,
            menu:[
                {icon:"fa-user-cog",link:"/account/your-info",text:"Your Info",access:true},
                {icon:"fa-trophy",link:"/account/prediction-studio",text:"Prediction Studio",access: <?=json_encode(can_access(["EXPERT","SUPER USER"]) ? true: false )?>},
                {icon:"fa-users-cog",link:"/account/manage-subscription",text:"Manage Subscription",access: <?=json_encode(can_access(["EXPERT","SUPER USER"]) ? true: false )?>},
                {icon:"fa-users",link:"/settings/manage-accounts",text:"Manage Accounts",access: <?=json_encode(can_access(["ADMINISTRATOR","SUPER USER"]) ? true: false)?>},
                {icon:"fa-cog",link:"/settings/platform-updates",text:" Advanced Control",access: <?=json_encode(can_access(["SUPER USER"]) ? true: false)?>},
                {icon:"fa-newspaper",link:"/settings/manage-posts",text:"Manage News & Post",access: <?=json_encode(can_access(["SUPER USER","CONTENT WRITER"]) ? true: false )?>},
            ],
        },
        mounted(){
            this.$el.classList.remove("d-none");
        },
    })
</script>
<?=$this->endSection()?>