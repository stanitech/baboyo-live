<?= $this->extend('Accounts\Views\layouts\default-with-sidebar') ?>
<?= $this->section("section") ?>
<?=$this->include("Media\Views\partials\media-library-components")?>
<style>
.ck-content a{
    color:#fb3d0d;
    text-decoration: underline
}
</style>
<?php if(isset($action) && $action == 'create-new'):?>
<section class="d-none">
    <media-gallery type="image" @use-image="useImage($event)" @modal_id = "modal_id=$event" ></media-gallery>
    <form action="" method="POST" class="row" >
        <div class="col-lg-8 mb-5">
            <h3 class="font-weight-lighter mb-3">Create Posts</h3>
            <div class="row">
                <div class="form-group col-md-11">
                    <input name="name" required type="text" class="form-control border-dark rounded-0" placeholder="Enter a title">
                </div>
                <div class="form-group col-md-11">
                    <textarea v-model="description" name="description" required class="form-control d-none border-dark rounded-0" placeholder="Description" rows="10" ></textarea>
                    <ckeditor :config="{placeholder:'Begin your writing here', toolbar: [ 'bold', 'italic', 'link','bulletedList','numberedList','|','imageInsert','removeFormat'],image: {toolbar: ['imageTextAlternative','imageStyle:full','imageStyle:side','linkImage']}}" :editor="editor" v-model="description"></ckeditor>
                </div>
                <div class="col-md-12">
                    <a href="/settings/manage-posts" class="btn btn-light mr-2 text-uppercase px-3 rounded-0 mr-1">Cancel</a>
                    <button class="btn btn-dark text-uppercase px-3 rounded-0">Save</button>
                </div>
            </div>
        </div>
        <div class="col small">
            <?php if(can_access(["ADMINISTRATOR","SUPER USER"])):?>
            <b-form-group class='mb-3'>
                <input type="hidden" name="is_announcement" :value="is_announcement ? 'YES':'NO'">
                <b-checkbox switch v-model='is_announcement'>Make Post An Announcement</b-checkbox>
            </b-form-group>
            <?php endif?>
            <b-form-group class='mb-3'>
                <select name='status' class="form-control form-control-sm" required >
                    <option :value='null'>-- POST STATUS --</option>
                    <option>PUBLISHED</option>
                    <option>UNPUBLISHED</option>
                </select>
            </b-form-group>

            <div v-if="cover_img">
                <img class="btn p-0" @click.prevent="showGallery"  :src="cover_img.file"  style="width:100%;height:10rem;object-fit:cover;"/>
                <input type="hidden" name="cover_img" :value="cover_img.id">
                <a href="" @click.prevent="showGallery" class="text-primary">Change Image</a>
            </div>
            <div v-else>
                <label class="d-block">Images complements your post</label>
                <a href="" @click.prevent="showGallery" class="text-primary btn-link">Add Cover Image</a>
            </div>
        </div>
    </form>
</section>
<script>
    new Vue({
        el:"section",
        data:{
            is_announcement: false,
            cover_img:null,
            modal_id:null,
            editor: ClassicEditor,
            description: null,
        },
        mounted(){
            this.$el.classList.remove("d-none");
        },
        methods:{
            showGallery(){
                this.$bvModal.show(this.modal_id)
            },
            useImage(event){
                this.cover_img = event;
            }
        }
    })
</script>

<?php elseif(isset($action) && $action == 'edit'):?>
<section class="d-none">
    <media-gallery type="image" @use-image="useImage($event)" @modal_id = "modal_id=$event" ></media-gallery>
    <h3 class="font-weight-lighter">Update Posts</h3>
    <div class="row" >
        <div class="col-lg-4 offset-lg-8 order-1" >
            <label class="d-block mt-5">Not interested in this post?</label>
            <form action="" method="POST"  @submit.prevent="handleDelete($event)">
                <input type="hidden" name="id" value="<?=$post->id?>">
                <input type="hidden" name="_method" value="DELETE">
                <button class="btn btn-sm small p-0 text-danger btn-link"><i class="fas fa-trash-alt mr-1"></i> Remove this post</button>
            </form>
        </div>
        <form method="POST" class="col-lg-12 mb-5 order-0">
            <div class="row">
                <div class="col-lg-8 mb-3">
                    <input type="hidden" name="id" value="<?=$post->id?>">
                    <div class="form-group">
                        <input name="name" value="<?=$post->name?>" required type="text" class="form-control border-dark rounded-0" placeholder="Enter a title">
                    </div>
                    <div class="form-group">
                        <textarea v-model="description" name="description" required class="form-control d-none border-dark rounded-0" placeholder="Description" rows="10" ></textarea>
                        <ckeditor :config="{placeholder:'Begin your writing here', toolbar: [ 'bold', 'italic', 'link','bulletedList','numberedList','|','imageInsert','removeFormat'],image: {toolbar: ['imageTextAlternative','imageStyle:full','imageStyle:side','linkImage']}}" :editor="editor" v-model="description"></ckeditor>
                    </div>
                    <div class="">
                        <a href="/settings/manage-posts" class="btn btn-light mr-2 text-uppercase px-3 rounded-0 mr-1">Cancel</a>
                        <button class="btn btn-dark text-uppercase px-3 rounded-0">Save</button>
                    </div>
                </div>
                <div class="col-lg-4 small my-3 my-lg-0">
                    <?php if(can_access(["ADMINISTRATOR","SUPER USER"])):?>
                    <b-form-group class='mb-3'>
                        <input type="hidden" name="is_announcement" :value="is_announcement ? 'YES':'NO'">
                        <b-checkbox switch v-model='is_announcement'>Make Post An Announcement</b-checkbox>
                    </b-form-group>
                    <?php endif?>
                    <b-form-group class='mb-3'>
                        <select name='status' class="form-control form-control-sm" required>
                            <option :value='null'>-- POST STATUS --</option>
                            <option <?=$post->status == 'PUBLISHED' ?'selected':''?>>PUBLISHED</option>
                            <option <?=$post->status == 'UNPUBLISHED' ?'selected':''?>>UNPUBLISHED</option>
                        </select>
                    </b-form-group>
                    <div v-if="cover_img">
                        <img class="btn p-0" @click.prevent="showGallery"  :src="cover_img.file"  style="width:100%;height:10rem;object-fit:cover;"/>
                        <input type="hidden" name="cover_img" :value="cover_img.id">
                        <a href="" @click.prevent="showGallery" class="text-primary">Change Image</a>
                    </div>
                    <div v-else>
                        <label class="d-block">Images complements your post</label>
                        <a href="" @click.prevent="showGallery" class="text-primary btn-link">Add Cover Image</a>
                    </div>
                </div>

            </div>
        </form>
    </div>
</div>
</section>
<script>
    new Vue({
        el:"section",
        data:{
            is_announcement: '<?=$post->is_announcement?>' == 'YES' ? true:false,
            cover_img:JSON.parse('<?=$post->cover_img ? json_encode($post->cover_img) : 'false' ?>'),
            modal_id:null,
            editor: ClassicEditor,
            description: <?=json_encode($post->description)?>,
        },
        mounted(){
            this.$el.classList.remove("d-none");
        },
        methods:{
            showGallery(){
                this.$bvModal.show(this.modal_id)
            },
            useImage(event){
                this.cover_img = event;
            },
            handleDelete(e) {
                this.$bvModal.msgBoxConfirm('Please confirm that you want to delete', {
                    title: 'Please Confirm',
                    size: 'sm',
                    headerClass: 'p-2 border-bottom-0',
                    footerClass: 'p-2 border-top-0',
                    centered: true,
                    static: true,
                    buttonSize: 'sm'
                }).then(value => {
                    if (value) {
                        e.target.submit();
                    }
                })
            },
        }
    })
</script>
<?php else:?>

<section class="row">
    <div class="col-lg-12 mb-3">
        <div class="d-flex justify-content-between align-items-center">
            <h3 class="font-weight-lighter">All Posts</h3>
            <a href="?action=create-new" class="btn-sm text-uppercase btn border-dark">Add New</a>
        </div>
    </div>

    <?php if($posts && count($posts) > 0):?>
    <?php foreach($posts as $post):?>
    <div class="col-xl-3 col-md-4 col-6">
        <div class="mb-2">  
            <img src="<?= $post->cover_img ? $post->cover_img->file : env("app.image.post-placeholder") ?>" alt="<?= $post->name ?>" class="w-100 card-img-top" style="height:9rem;object-fit:cover">
            <div class="media py-2" style="height:7rem;">
                <div class="media-body">
                    <h6 class="mb-0 small font-weight-bold"><a class="text-reset" href="/news/<?=$post->slug?>"><?=ellipsize($post->name,60)?></a></h6>
                    <p class="text-muted mb-0 small"><small><?=$post->author_name?></small> <small class="ml-2"><i class="fa fa-calendar mr-1"></i> <?= humanize_time($post->created_at) ?></small>
                    <small class="ml-2"><a href="?action=edit&post=<?=$post->slug?>" class="text-muted"><i class="fa fa-edit "></i>Edit</a> </small>
                    </p>

                </div>
            </div>
        </div>
    </div>
    <?php endforeach?>
    <div class="col-12">
        <?=$pager->links()?>
    </div>
    <?php else:?>
        <div class="col-12">
            <h2 class="">No result returned</h2>
        </div>
    <?php endif?>

</section>

<?php endif?>


<?= $this->endSection() ?>