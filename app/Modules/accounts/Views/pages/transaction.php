<?= $this->extend('Accounts\Views\layouts\default-with-sidebar') ?>
<?= $this->section("section") ?>
<?php if($transaction):?>
<section class="row">
    <div class="col-lg-4">
        <ul class="list-group list-group-flush">
            <li class="list-group-item d-flex justify-content-between align-items-center pb-3">
                 <div>
                    <span class="text-muted">Amount</span>
                    <h5 class="h5 font-weight-light"><?="{$transaction->data->currency} ".number_format($transaction->data->amount/100)?></h5>
                 </div>
                 <?php if($transaction->status):?>
                 <span class="badge badge-success badge-pill text-white p-2">Success</span>
                 <?php else:?>
                <span class="badge badge-danger badge-pill text-white p-2">Failed</span>
                <?php endif?>
            
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span class="font-weight-light">Reference</span>
                <span><?=$transaction->data->reference?></span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span class="font-weight-light">Channel</span>
                <span class="text-capitalize"><?=$transaction->data->channel?></span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span class="font-weight-light">Fees</span>
                <span><?="{$transaction->data->currency} ".number_format($transaction->data->fees/100)?></span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span class="font-weight-light">Paid At</span>
                <span><?=date("M d,Y h:i A e",strtotime($transaction->data->paid_at))?></span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span class="font-weight-light">Message</span>
                <span class="text-capitalize"><?=$transaction->data->gateway_response?></span>
            </li>
            <li class="list-group-item d-flex justify-content-between align-items-center">
                <span class="font-weight-light">Email</span>
                <span><?=$transaction->data->customer->email?></span>
            </li>
        </ul>
    </div>
    <div class="col-lg-5 offset-lg-1">
        <h5 class="lead">Analytics</h5>
        <hr>
        <div class="row">
            <div class="col-md-6">
                <h6 class="h6">Card Type</h6>
                <p class="text-capitalize"><?=$transaction->data->authorization->card_type?></p>
            </div>
            <div class="col-md-6">
                <h6 class="h6">Card Number</h6>
                <p class="text-capitalize"><?=$transaction->data->authorization->bin?>****<?=$transaction->data->authorization->last4?></p>
            </div>
            <div class="col-md-6">
                <h6 class="h6">Authorization</h6>
                <p><?=$transaction->data->authorization->authorization_code?></p>
            </div>
            <div class="col-md-6">
                <h6 class="h6">Bank and Country</h6>
                <p class="text-capitalize"><?=$transaction->data->authorization->bank?> (<?=$transaction->data->authorization->country_code?>)</p>
            </div>
            <div class="col-md-6">
                <h6 class="h6">IP Address</h6>
                <p ><a class="text-primary" href="//db-ip.com/<?=$transaction->data->ip_address?>"><?=$transaction->data->ip_address?></a></p>
            </div>
        </div>
        <hr class="my-5">
        <div class="row">
            <div class="col-6 ">
                <div class="border rounded-circle border-primary text-center d-flex flex-column align-items-center justify-content-center" style="height:200px;width:200px;position:relative">
                    <h1 class="display-4 mb-0"><?=$transaction->data->log->time_spent?></h1>
                    <p>seconds</p>
                    <span style="position:absolute;bottom:-13px" class="bg-white px-5 text-primary text-uppercase h6">Spent On<br> Page</span>
                </div>
            </div>
            <div class="col-6">
                <div class="media">
                    <i class="fa <?=$transaction->data->log->mobile ? 'fa-mobile':'fa-desktop'?>  mr-2 "></i>
                    <div class="media-body">
                        <h6 class="h6 mb-0 text-primary">DEVICE TYPE</h6>
                        <p><?=$transaction->data->log->mobile ? 'Mobile':'Desktop'?></p>
                    </div>
                </div>
                <div class="media ">
                    <i class="fa fa-reply mr-2 "></i>
                    <div class="media-body">
                        <h6 class="h6 mb-0 text-primary">ATTEMPT</h6>
                        <p><?=counted($transaction->data->log->attempts,"attempt")?></p>
                    </div> 
                </div>
                <div class="media">
                    <i class="fa fa-exclamation-circle mr-2 "></i>
                    <div class="media-body">
                        <h6 class="h6 mb-0 text-danger">ERRORS</h6>
                        <p><?=counted($transaction->data->log->errors,"error")?></p>
                    </div> 
                </div>
            </div>
        </div>
    </div>

</section>
<?php else:?>
<h1>No result found</h1>
<?php endif?>

<?= $this->endSection() ?>