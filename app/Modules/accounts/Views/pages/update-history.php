<?= $this->extend('Accounts\Views\layouts\default-with-sidebar') ?>
<?= $this->section("section") ?>
<section class="row">
    <div class="col-lg-5  mb-5">
        <h3 class="font-weight-lighter mb-3">Update History & Stats</h3>
        <table class="table table-borderless table-hover">
            <tbody>
                <?php foreach($history as $key):?>
                <tr >
                    <td >
                        <div class="media">
                            <div class="mr-4">
                                <i class="fa fa-hdd  fa-2x "></i>
                                <?php if($key->status == "SUCCESS"):?>
                                    <i class="fa fa-check-circle text-success"></i>
                                <?php else:?>
                                <i class="fa fa-times-circle text-danger"></i>
                                <?php endif?>
                            </div>
                            <div class="media-body">
                                <h6 class="mb-0 text-uppercase"><?=$key->type?> Update for <?=date("M d, Y",strtotime($key->target_date))?></h6>
                                <p class="text-muted mb-0 small">
                                    <?php if($key->status == "SUCCESS"):?>
                                    <span class="text-success text-underline">Successfully</span>
                                    <?php else:?>
                                    <span class="text-danger text-decoration-underline">Failed</span>
                                    <?php endif?>
                                     updated by <span class="text-info"><?=$key->name?></span> on <?=date("d/m/Y h:i A",strtotime($key->created_at))?></p>
                                     <?php if($key->status == "FAILED"):?>
                                     <a  id="history_<?=$key->id?>" class="small btn-link text-danger">Why did this update fail?</a>
                                     <b-popover target="history_<?=$key->id?>" triggers="hover">
                                         <?=$key->message?>
                                     </b-popover>
                                     <?php endif?>
                            </div>
                        </div>
                    </td>
                </tr>
                <?php endforeach?>
            </tbody>
        </table>
        <?=$pager->links()?>
    </div>
    <div class="col-lg-4"></div>
    <div class="col-lg small">
        <label class="d-block">Free up disk space</label>
        <a href="/settings/clear-history" @click.prevent="clearHistory" class="text-primary my-5">Clear history less than 3 days old</a>
        <div class="my-5">
            <label class="d-block">Ready for another update?</label>
            <a href="/settings/platform-updates" class="text-primary">Back to platform updates</a>
        </div>

    </div>
</section>
<script>
    new Vue({
        el:"section",
        methods:{
            clearHistory(){
                this.$bvModal.msgBoxConfirm('By continuing, you acknowledge clearing the history log less than 3 days old', {
                    size: 'sm',
                    buttonSize: 'sm',
                    okVariant: 'success',
                    headerClass: 'p-2 border-bottom-0',
                    footerClass: 'p-2 border-top-0',
                    centered: true
                })
                .then(value => {
                    if(value){
                        location = '<?=base_url('settings/clear-history')?>'
                    }
                })
                .catch(err => {})
            }
        }
    })
</script>
<?= $this->endSection() ?>