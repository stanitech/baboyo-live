<?= $this->extend("layouts/frontend") ?>
<?= $this->section("content") ?>
<?php
    $labels = array_reverse(array_map(function($date){return date('M d',strtotime($date));},array_keys($stats)));
    $data = array_reverse(array_map(function($i){return $i == '--' ? 0 : str_replace('%','',$i); },array_column($stats,'win_rate')));
?>

<?=$this->include("partials/preloader")?>
<?=$this->include("partials/chart")?>
<div class="container">
<?=get_ad_code('header');?>
</div>
<section class='container my-5'>
    <h3 class="my-2 lead">Predictions Statistics from <strong><?=$labels[0]?> - <?=$labels[count($data)-1]?></strong> <?=$slug? '('.humanize($slug,'-').')' :''?></h3>
    <bar-chart :data="chartData" class='mb-3'></bar-chart>
    <h6 class="text-uppercase small">At a glance</h6>
    <div class="row text-muted text-uppercase'">
        <div class="col-md-4">
            <div class="card card-body text-center d-flex align-items-center justify-content-center flex-column" style='height:7rem'>
                <span class="text mb-0">Average Win</span>
                <h1 class='display-4 text-dark my-0'><?=round(array_sum($data)/count($data))?>%</h1> 
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-body text-center d-flex align-items-center justify-content-center flex-column" style='height:7rem'>
                <span class="text mb-0">Best Day</span>
                <h1 class='display-4 text-dark my-0'><?=max($data)?>%</h1> 
            </div>
        </div>
        <div class="col-md-4">
            <div class="card card-body text-center d-flex align-items-center justify-content-center flex-column" style='height:7rem'>
                <span class="text mb-0">Lowest Day</span>
                <h1 class='display-4 text-dark my-0'><?=min($data)?>%</h1> 
            </div>
        </div>
    </div>
</section>
<div class="container">

    <?=get_ad_code('footer');?>
</div>
<script>
    new Vue({
        el:"section",
        data:{
            chartData:{
                labels: JSON.parse(JSON.stringify(<?=json_encode($labels)?>)),
                datasets: [
                    {
                        label: 'PREDICTION STATISTICS',
                        backgroundColor: 'orangered',
                        data: JSON.parse(JSON.stringify(<?=json_encode($data)?>))
                    }
                ]
            }
        },
    })
</script>
<?= $this->endSection() ?>