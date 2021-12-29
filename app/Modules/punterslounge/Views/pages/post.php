<?= $this->extend("layouts/frontend") ?>
<?= $this->section("content") ?>
<style>
.post-body a{
    color:#fb3d0d;
    text-decoration: underline
}
</style>
<div class="breadcrumb-bettix" style="background:url(<?= $post->options->featured_image ? $post->options->featured_image->file : env("app.image.post-placeholder") ?>);background-position:center center;background-size:100%">
    <div class="container">
        <div class="row">
            <div class="col-lg-7">
                <div class="breadcrumb-content">
                    <h2><?= $post->name?></h2>
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

<div class="blog blog-details py-5">
    <div class="container">
        <div class="row">
            <div class="col-lg-9">
                <div class="single-blog">
                    <div class="part-img">
                        <div class="post-date">
                            <span class="date"><?=date("d",strtotime($post->created_at))?></span>
                            <span class="month"><?=date("M",strtotime($post->created_at))?></span>
                        </div>
                    </div>
                    <div class="part-text">
                        <ul class="meta-info border-top-0">
                            <li>
                                <span class="icon"><i class="far fa-user"></i></span> Posted by <a href="#"><?= $post->author?></a>
                            </li>
                            <li>
                                <span class="icon"><i class="far fa-calendar-alt"></i></span> Date : <?=date("d M Y",strtotime($post->created_at))?>
                            </li>
                            <li>
                                <span class="icon"><i class="far fa-clock"></i></span> Time : <?=date("h:iA",strtotime($post->created_at))?>
                            </li>
                        </ul>
                        <div class="my-3">
                            <?=get_ad_code('prediction')?>
                        </div>
                        <div class="mb-3 post-body">
                            <?= $post->description?>
                        </div>
                        <?= $this->include("partials/sharer") ?>
                        <div class="my-3">
                            <?=get_ad_code('prediction')?>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-lg-3">
                <div class="blog-sidebar">
                    <h4 class="title">Recent Post</h4> 
                    <div class="popular-news row">
                        <?php foreach($posts as $post):?>
                            <div class="col-6 col-lg-12">

                                <div class="media align-items-start single-post p-1 shadow-sm rounded-0">
                                    <b-avatar class="mr-2" square size="lg" src="<?= $post->options->featured_image ? $post->options->featured_image->file : env("app.image.post-placeholder") ?>" alt="<?= $post->name ?>"></b-avatar>
                                    <div class="media-body">
                                        <small class="mb-0 small"><a href="/post/<?=$post->slug?>"><?=character_limiter($post->name,75)?></a></small>
                                    </div>
                                </div>
                            </div>
                        <?php endforeach?>
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
    el:".blog-sidebar"
});
</script>

<?= $this->endSection() ?>