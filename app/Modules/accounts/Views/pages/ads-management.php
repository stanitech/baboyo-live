<?= $this->extend('Accounts\Views\layouts\default-with-sidebar') ?>
<?= $this->section("section") ?>
<section class="row">
    <div class="col-lg-10  mb-5">
        <h3 class="font-weight-lighter">Ads Management</h3>
        <form action="/settings/options/ad_management" class='mt-3' method="POST">
            <input type="hidden" name="redirect" value="<?=current_url()?>">
            <div class="card border-0 mb-3">
                <h6 class='border-bottom pb-2'>Header Fullwidth Ad Code</h6>
                <div class="row">
                    <div class="col-md-8">
                        <textarea name='ads[header]' class='rounded-0 form-control' rows='4' style='resize:none;font-size:10px;font-family:monospace'><?=get_ad_code('header',false)?></textarea>
                    </div>
                    <div class="col-md-4 text-muted">
                        <span class='small'>Enter your ad code (Eg. Google Adsense) for the header area. This would appear immediately after the navbar.</span>
                    </div>
                </div>
            </div>
            <div class="card border-0 mb-3">
                <h6 class='border-bottom pb-2'>Footer Fullwidth Ad Code</h6>
                <div class="row">
                    <div class="col-md-8">
                        <textarea name='ads[footer]' class='rounded-0 form-control' rows='4' style='resize:none;font-size:10px;font-family:monospace'><?=get_ad_code('footer',false)?></textarea>
                    </div>
                    <div class="col-md-4 text-muted">
                        <small>Enter your ad code (Eg. Google Adsense) for the footer area. This would appear before the footer bar.</small>
                    </div>
                </div>
            </div>
            <div class="card border-0 mb-3">
                <h6 class='border-bottom pb-2'>Recursive Ad Code</h6>
                <div class="row">
                    <div class="col-md-8">
                        <textarea  name='ads[prediction]' class='rounded-0 form-control' rows='4' style='resize:none;font-size:10px;font-family:monospace'><?=get_ad_code('prediction',false)?></textarea>
                    </div>
                    <div class="col-md-4 text-muted">
                        <small>Enter your ad code (Eg. Google Adsense) for the recursive area. This ads are useful in areas like Prediction or Post. Where or the number of times they appear is all at random</small>
                    </div>
                </div>
            </div>
            <button type='submit' class='btn btn-dark rounded-0 '>Save All Changes</button>
        </form>
    </div>
</section>
<script>
    new Vue({
        el:"section",
    })
</script>
<?= $this->endSection() ?>