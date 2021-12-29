<?= $this->extend('Accounts\Views\layouts\default-with-sidebar') ?>
<?= $this->section("section") ?>
<section class="row app d-none">
    <div class="col-lg-7 mb-5">
        <h3 class="font-weight-lighter">Packages Subscribers</h3>
        <div class="table-responsive"> 
            <table class="table small">
                <thead >
                    <tr>
                        <th>Package</th>
                        <th>Subscriber</th>
                        <th>Pricing</th>
                        <th>Next Due Date</th>
                        <th>Status</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($subscriptions) > 0):?>
                    <?php foreach ($subscriptions as $key):?>
                    <tr>
                        <td >
                            <h6 class="mb-0 h6"><?=$key->package_name?></h6>
                            <a class="btn-link" href="#"><?=$key->expert_name?></a>
                        </td>
                        <td>
                            <div class="media">
                                <b-avatar size="sm" src="<?=$key->subscriber->cover_img ? $key->subscriber->cover_img->file : env("app.image.user-placeholder")?>" class="mr-2"></b-avatar> 
                                <div class="media-body">
                                    <h6 class="m-0 font-weight-bolder" style="font-size:0.8rem"><?=$key->subscriber->name?></h6>
                                    <span class="badge p-0 m-0 text-muted"><?=$key->subscriber->account_type?></span>
                                </div>
                            </div>
                        </td>
                        <td>
                            <span class=""><?=humanize_currency($key->amount)?></span><br>
                            <small><?=counted($key->duration,"day")?></small>
                        </td>
                        <td>
                            <?=date("d/m/Y h:i",strtotime($key->expires_at))?><br>
                            <?=humanize_time($key->expires_at)?>
                        </td>
                        <td>
                            <?php if(strtotime($key->expires_at) < time()):?>
                            <a href="/account/transaction/<?=$key->transaction_reference?>" class="btn btn-light btn-sm rounded-0">Terminated</a>
                            <?php else:?>
                            <a href="/account/transaction/<?=$key->transaction_reference?>" class="btn btn-success btn-sm rounded-0">Active</a>
                            <?php endif?>
                        </td>
                    </tr>
                    <?php endforeach?>
                    <?php else: ?>
                    <tr>
                        <td colspan="5">
                            <h6 class>Nobody on this platform has ever subscribed to any package</h6>
                        </td>
                    </tr>
                    <?php endif?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col small">
    </div>

</section>
<script>
    new Vue({
        el:"section",
        data:{
            user_image:'<?=isset($user->cover_img)? $user->cover_img->file : env("app.image.user-placeholder")?>',
            media: event,
            modal_id:null,
        },
        mounted(){
            this.$el.classList.remove("d-none");
        },
        methods:{
            
        }
    })
</script>
<?= $this->endSection() ?>