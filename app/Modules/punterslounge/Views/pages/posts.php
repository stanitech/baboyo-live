<?= $this->extend("layouts/frontend") ?>
<?= $this->section("content") ?>

<div class="breadcrumb-bettix blog-page" style="background:url(<?= env("app.image.post-placeholder") ?>);background-position:center center;background-size:100%">
    <div class="container">
        <div class="row">
            <div class="col-lg-7">
                <div class="breadcrumb-content py-4">
                    <h2>Football News</h2>
                    <ul>
                        <li>
                            <a href="/"> Home</a>
                        </li>
                        <li><a class="active" href="/news">News</a></li>
                    </ul>
                </div>
            </div>
        </div>
    </div>
</div>
<div class='container my-2'>
    <?=get_ad_code('header')?>
</div>

<div class="py-5 min-75vh">
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

        <div class="row">
            <?php if($posts && count($posts) > 0):?>
            <?php foreach($posts as $post):?>
            <div class=" col-lg-3 col-md-4 col-6">
                <div class="mb-2">  
                    <a href="/post/<?=$post->slug?>">
                        <img src="<?= $post->options->featured_image ? $post->options->featured_image->file : env("app.image.post-placeholder") ?>" alt="<?= $post->name ?>" class="w-100 card-img-top" loading="lazy" style="height:9rem;object-fit:cover">
                    </a>
                    <div class="media py-2" style="height:6rem;">
                        <div class="media-body">
                            <h6 class="mb-0 small font-weight-bold"><a class="text-reset" href="/post/<?=$post->slug?>"><?=character_limiter($post->name,75)?></a></h6>
                            <p class="text-muted small"><span><?=$post->author?></span> <small class="ml-2"><i class="fa fa-calendar mr-1"></i> <?= humanize_time($post->created_at) ?></small></p>
                        </div>
                    </div>
                </div>
            </div>
            <?php if(mt_rand(0,100) % 7 == 0):?>
            <div class=" col-lg-3 col-md-4 col-6">
                <?=get_ad_code('prediction');?>
            </div>
            <?php endif?>

            <?php endforeach?>
            <div class="col-12">
                <?=$pager->links()?>
            </div>
            <?php else:?>
                <div class="col-12 mt-4 text-center">
                    <p class="display-4">No result returned</p>
                </div>
            <?php endif?>
        </div>
    </div>
</div>
<div class='container my-2'>
    <?=get_ad_code('footer')?>
</div>

<?= $this->endSection() ?>