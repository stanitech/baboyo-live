<?= $this->extend("layouts/frontend") ?>
<?= $this->section("content") ?>
<?=$this->include("partials/chart")?>

<div class="breadcrumb-bettix blog-page" style="background:url(<?= env("app.image.experts-banner") ?>);background-position:center center;background-size:100%">
    <div class="container">
        <div class="row">
            <div class="col-lg-7">
                <div class="breadcrumb-content py-4">
                    <h2>Experts Predictions</h2>
                    <ul>
                        <li>
                            <a href="/"> Home</a>
                        </li>
                        <li><a class="active" href="/experts">Experts</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<div class='container my-2'>
    <?=get_ad_code('header')?>
</div>
<div class="betting py-4">
    <div class="container">
        <div class="filter-menu">
            <div class="row">
                <form class="col-md-3 col-12 mb-3">
                    <div class="input-group border rounded">
                        <input class="form-control border-0" type="search" name="search" placeholder="Search..." value="<?=isset($search) ? $search : null?>">
                        <div class="input-group-append">
                            <button type="submit" class="input-group-text border-0 bg-white" ><i class="fa fa-search"></i></button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
        <div class="betting-table">
            <div class="row">
                <div class="col-lg-12">
                    <div class="tab-content bet-tab-content" id="myTabContent">
                        <div class="tab-pane fade show active" id="football">
                            <div class="sport-content-title">
                                <h3 class="d-flex justify-content-between">Experts</h3>
                            </div>
                            <?php if($experts && count($experts) > 0):?>

                            <?php foreach ($experts as $key) : ?>
                                <div class="card my-2 shadow">
                                    <div class="card-header py-1 bg-transparent"><h4 class='text-muted text-uppercase'><?=$key->name?></h4></div>
                                    <div class="card-body">
                                        <div class="row">
                                            <div class="col-md-4 align-items-center d-flex flex-column border-right">
                                                <b-avatar size="10rem"  square src="<?=isset($key->cover_img)? $key->cover_img->file : env("app.image.user-placeholder")?>"></b-avatar>
                                                <div class='small my-4'>
                                                    <a href="#" class='btn btn-dark text-uppercase btn-sm'>Profile</a> 
                                                    <a href="/expert/prediction/<?=$key->slug?>/<?=date("Y-m-d")?>" class='btn btn-dark text-uppercase btn-sm'>Predictions</a> 
                                                </div>
                                            </div>
                                            <div class="col-md-4 border-right">
                                                <h6 class="text-muted mb-0">Prediction Stats <small>(Last 7 Days)</small></h6>
                                                <div class='text-center'>
                                                    <h1 class='display-4 text-dark my-0'><?=$key->stats->win_rate?>%</h1> 
                                                </div>
                                                <div class='p-2 text-uppercase text'>
                                                    <table class='table table-borderless table-sm small'>
                                                        <tr>
                                                            <td class='text-left'>Selected</td>
                                                            <td class='text-right'><?=$key->stats->selected?></td>
                                                        </tr>
                                                        <tr>
                                                            <td class='text-left'>Won</td>
                                                            <td class='text-right'><?=$key->stats->won?></td>
                                                        </tr>
                                                        <tr>
                                                            <td class='text-left'>Lost</td>
                                                            <td class='text-right'><?=$key->stats->lost?></td>
                                                        </tr>
                                                    </table>
                                                </div>
                                            </div>
                                            <div class="col-md-4 align-items-center d-flex flex-column">
                                                <div style='width:18rem'>
                                                    <pie-chart :data="{labels: ['Won','Lost','Awaiting'],datasets: [{label: 'Last 7 Days Prediction Stats ',data: [<?=$key->stats->won?>, <?=$key->stats->lost?>, <?=$key->stats->unplayed?>],backgroundColor: ['rgb(117, 238, 115)','rgb(250, 122, 122)'],hoverOffset: 4}]}"></pie-chart>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            <?php endforeach?>
                               
                            <?php else:?>
                                <h3 class="text-uppercase text-center mt-3">No Expert found</h3>
                            <?php endif?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</div>
<div class='container my-2'>
    <?=get_ad_code('footer')?>
</div>
<script>
    new Vue({
        el:".betting",
    })
</script>
<?= $this->endSection() ?>