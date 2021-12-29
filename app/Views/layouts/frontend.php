<?= $this->extend("layouts/core") ?>
<?= $this->section("layout") ?>
         
    <?=$this->include("partials/header")?>
    
    <?= $this->renderSection("content") ?>
<?=$this->include("partials/footer")?>

<script src="/assets/js/clock.min.js"></script>

<?= $this->endSection() ?>