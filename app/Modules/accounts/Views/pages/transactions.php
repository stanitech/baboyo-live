<?= $this->extend('Accounts\Views\layouts\default-with-sidebar') ?>
<?= $this->section("section") ?>
<section class="row app d-none">
    <div class="col-lg-6 mb-5">
        <h3 class="font-weight-lighter">Packages Information</h3>
        <div class="table-responsive">
            <table class="table small">
                <thead>
                    <tr>
                        <th><b-checkbox inline></b-checkbox> User</th>
                        <th>Amount</th>
                        <th>Reference</th>
                        <th>Status</th>
                        <th>Created At</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($transactions) > 0):?>
                    <?php foreach ($transactions as $key):?>
                        <tr>
                            <td>
                                <div class="media">
                                    <b-checkbox inline></b-checkbox>
                                    <div class="media-body">
                                        <h6 class="h6 mb-0"><?=$key->name?></h6>
                                        <p class="text-muted mb-0"><?=$key->email?></p>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <span class=""><?=humanize_currency($key->amount)?></span>
                            </td>
                            <td><a class="btn-link" href="/account/transaction/<?=$key->reference?>"><?=$key->reference?></a></td>
                            <td>
                                <?php if($key->status == "FAILED"):?>
                                <span class="btn btn-danger btn-sm rounded-0">Failed</span>
                                <?php else:?>
                                <span class="btn btn-success btn-sm rounded-0">Success</span>
                                <?php endif?>
                            </td>
                            <td><?=date("M d, Y h:i A",strtotime($key->created_at))?></td>
                            
                        </tr>
                    <?php endforeach?>
                    <?php else: ?>
                    <tr>
                        <td colspan="3">
                            <h4>No Transaction has occured on this platform</h4>
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
        mounted(){
            this.$el.classList.remove("d-none");
        },
    })
</script>
<?= $this->endSection() ?>