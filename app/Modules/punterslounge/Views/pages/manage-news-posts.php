<?= $this->extend('Accounts\Views\layouts\default-with-sidebar') ?>
<?= $this->section("section") ?>
<?=$this->include("Media\Views\partials\media-library-components")?>
<style>
.ck-content a{
    color:#fb3d0d;
    text-decoration: underline
}
</style>
<?php if($action == 'new' || $action == 'edit'):?>
<section class="section mb-5" style="min-height:100vh">
    <b-overlay :show="isLoading">
        <template #overlay> Please wait...<br>
            <progress></progress>
        </template>
        <div class="d-flex justify-content-between">
            <h3 class="font-weight-lighter"><?= isset($post) ? 'Edit': 'New'?> Post</h3>
        </div>
        <form class="my-4" method="POST" action='/settings/manage-posts' @submit='isLoading=true'>
            <?php if(isset($post)):?>
            <input type="hidden" name='id' value="<?=$post->id?>">
            <?php endif?>
            <div class="d-flex justify-content-end mb-3">
                <button name="action" value='save' type='submit' class="btn btn-sm btn-outline-dark mr-2"><i class="fa fa-save mr-1"></i> <span class="d-none d-md-inline">Save</span></button>
                <button name="action" value='save-and-close' type='submit' class="btn btn-sm btn-light mr-1"><i class="fa fa-check mr-1"></i> <span class="d-none d-md-inline">Save & Close</span></button>
                <button name="action" value='save-and-new' type='submit' class="btn btn-sm btn-light mr-1"><i class="fa fa-plus mr-1"></i> <span class="d-none d-md-inline">Save & New</span></button>
                <a href='/settings/manage-posts' class="btn btn-sm btn-light "><i class="fa fa-times-circle mr-1"></i> <span class="d-none d-md-inline">Cancel</span></a>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <b-form-group label='Title *' >
                        <b-form-input name="name" maxlength="100" required value='<?=quotes_to_entities( $post->name??'')?>'></b-form-input>
                    </b-form-group>
                </div>
                <div class="col-md-3">
                    <b-form-group label='Slug'>
                        <b-form-input name="slug" value='<?=$post->slug??''?>' placeholder='Auto-generate from title'> </b-form-input>
                    </b-form-group>
                </div>
            </div>

            <div class="row mt-3">
                
                <div class="col-md-9">
                    <b-tabs nav-class='mb-2'>
                        <b-tab title='Content'>
                            <b-form-group label='Description'>
                                <b-form-textarea v-model="description" class="d-none" name="description"></b-form-textarea>
                                <ckeditor :config="{ placeholder:'Begin your writing here', toolbar: {shouldNotGroupWhenFull: false,items: ['heading', '|','fontfamily', 'fontsize', '|', 'bold', 'italic', 'link','bulletedList','numberedList','|','imageInsert','removeFormat'],image: {toolbar: ['imageTextAlternative','imageStyle:full','imageStyle:side','linkImage']}} }" :editor="editor" v-model="description"></ckeditor>
                            </b-form-group>
                        </b-tab>
                        <b-tab title='Options'>
                            <div class="row">
                                <label class="col-md-2">Featured Image</label>
                                <div class="col-md-6">
                                    <open-gallery-button  btn-text="Choose Your Image" thumbnail :item="options.featured_image" input-name="options[featured_image]"></open-gallery-button>
                                </div>
                            </div>
                            <div class="row mt-5">
                                <label class="col-md-2">Post Format</label>
                                <div class="col-md-auto">
                                    <b-form-radio-group  button-variant="outline-dark" 
                                        v-model="options.post_format"
                                        :options="['Standard']"
                                        name="options[post_format]"
                                        buttons
                                    ></b-form-radio-group>
                                </div>
                            </div>
                            <div class="row mt-5" v-if="options.post_format == 'Video'">
                                <label class="col-md-2">Video URL</label>
                                <b-form-input name="options[video_url]" v-model='options.video_url' class="col-md-4" type='url' required></b-form-input>
                            </div>
                            <div class="row mt-5" v-if="options.post_format == 'Audio'">
                                <label class="col-md-2">Audio Embed Code</label>
                                <b-form-textarea name="options[audio_embed_code]" v-model=options.audio_embed_code class="col-md-4"  required></b-form-textarea>
                            </div>
                            <div class="row mt-5" v-if="options.post_format == 'Gallery'">
                                <label class="col-md-2">Upload Gallery Images</label>
                                <div class="col-md-6">
                                    <image-grid input-name='options[gallery_images]' :item="options.gallery_images"></image-grid>
                                </div>
                            </div>

                        </b-tab>
                    </b-tabs>
                </div>
                <div class="col-md-3">

                    <b-form-group  label='Status'>
                        <select class="form-control" name='status'>
                            <?php foreach (['Published','Unpublished'] as $key):?>
                            <option <?=isset($post) && $post->status == strtoupper($key) ? 'selected':''?> value="<?=strtoupper($key)?>"><?=$key?></option>
                            <?php endforeach?>
                        </select>
                    </b-form-group>
                    <b-form-group  label='Category *'>
                        <select class="form-control" name='cat_id' required>
                            <option :value="null">-- Choose one --</option>
                            <?php foreach ($categories as $key):?>
                            <option <?=isset($post) && $post->cat_id == $key->id ? 'selected':''?> value="<?=$key->id?>"><?=$key->name?></option>
                            <?php endforeach?>
                        </select>
                        <a href='/settings/manage-post-category' target='_blank' class='small text-underline'>Manage Categories <i class='fa fa-angle-right'></i></a>
                    </b-form-group>
                    <b-form-group  label="Featured">
                        <b-form-radio-group size="sm" button-variant="outline-dark" 
                            v-model="featured"
                            :options="['YES','NO']"
                            name="featured"
                            buttons
                        ></b-form-radio-group>
                    </b-form-group>
                    
                    <b-form-group label='Created Date' >
                        <b-form-input name='created_at' type='datetime-local' value='<?=isset($post)? date('Y-m-d\TH:i',strtotime($post->created_at)):''?>'></b-form-input>
                    </b-form-group>
                </div>
            </div>
                
        </form>
       
    </b-overlay>
</section>
<script>
    new Vue({
        el: ".section",
        data:{
            isLoading:false,
            description:JSON.parse(JSON.stringify(<?=json_encode($post->description??'')?>)),
            featured:'<?=$post->featured??'YES'?>',
            options: JSON.parse(JSON.stringify(<?=isset($post) ? json_encode($post->options): json_encode(['post_format'=>'Standard','featured_image'=>'','gallery_images'=>[],'audio_embed_code'=>'']) ?>)),
            editor: ClassicEditor,
        }
    })
</script>

<?php else:?>
<section class="section mb-5" style="min-height:100vh">
    <div class="row">
        <div class="col-lg-10">
            <b-overlay :show="isLoading">
                <template #overlay> Please wait...<br>
                    <progress></progress>
                </template>
                <h3 class="font-weight-lighter">All Posts</h3>
                <div class="d-flex my-3 justify-content-lg-end align-items-center">
                    <a class="btn  btn-dark btn-sm mr-1" href="/settings/manage-posts/new"><i class="fa fa-plus-circle mr-1"></i> New</a>
                    <form v-show='selected.length > 0' method="POST" action="/settings/manage-posts-batch-operations">
                        <input type="hidden" name="id[]" v-for='i in selected' :value="i">
                        <div class="btn-group btn-group-sm">
                            <button type='submit' name='status' value='PUBLISHED' class="btn border btn-light mr-1"><i class="fa fa-check text-success mr-1"></i> Publish</button>
                            <button type='submit' name='status' value='UNPUBLISHED' class="btn border btn-light mr-1"><i class="fa fa-times text-danger mr-1"></i> Unpublish</button>
                            <button type='submit' name='featured' value='YES' class="btn border btn-light  mr-1"><i class="fa fa-star  text-warning mr-1"></i> Feature</button>
                            <button type='submit' name='featured' value='NO' class="btn border btn-light  mr-1"><i class="fa fa-star  mr-1"></i> Unfeature</button>
                            <button type='submit' name='trash' value='YES' class="btn border btn-light  mr-1"><i class="fa fa-trash mr-1"></i> Trash</button>
                        </div>
                    </form>
                </div> 
                <form class="row mb-3">
                    <div class="col-lg-4">
                        <div class="input-group border rounded">
                            <div class="input-group-prepend bg-white border-0">
                                <span class="input-group-text bg-white border-0"><i class="fa fa-search"></i></span>
                            </div>
                            <input name="search" class="form-control border-0" type="search" placeholder="Find in Posts" value="<?=service('request')->getGet('search')?>">
                        </div>
                    </div>
                </form>
                <div class="table-responsive">
                    <table class="table table-light table-sm small">
                        <thead> 
                            <tr>
                                <td style='width:1rem'><input type='checkbox'></td>
                                <td style='width:5rem'>Status</td>
                                <td style='min-width:15rem'>Title</td>
                                <td style='width:7rem' class='d-none d-lg-inline-block'>Author</td>
                                <td style='width:7rem' >Date Created</td>
                                <td style='width:2rem'>Hits</td>
                                <td style='width:2rem' class='d-none d-lg-inline-block'>ID</td>
                            </tr>
                        </thead>
                        <tbody>
                            <?php if(count($posts) > 0  ):?>
                            <?php foreach ($posts as $key):?>
                            <tr>
                                <td><input type='checkbox' value='<?=$key->id?>' v-model='selected'></td>
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="id" value='<?=$key->id?>'>
                                        <input type="hidden" name="slug" value='<?=$key->slug?>'>
                                        <input type="hidden" name="action" value='save-and-close'>
                                        <div class="btn-group btn-group-sm">
                                            <?php if($key->status == 'PUBLISHED'):?>
                                            <button type='submit' name='status' value='UNPUBLISHED' v-b-tooltip title='Unpublish item' class='btn border  text-success'><i class='fa fa-check'></i></button>
                                            <?php else:?>
                                            <button type='submit' name='status' value='PUBLISHED' v-b-tooltip title='Publish item' class='btn border text-danger'><i class='fa fa-times-circle'></i></button>
                                            <?php endif?>
                                            <?php if($key->featured == 'YES'):?>
                                            <button type='submit' name='featured' value='NO' v-b-tooltip title='Unfeatured' class='btn border'><i class='fa fa-star text-warning'></i></button>
                                            <?php else:?>
                                            <button type='submit' name='featured' value='YES' v-b-tooltip title='Featured' class='btn border'><i class='fa fa-star'></i></button>
                                            <?php endif?>
                                        </button>
                                    </form>
                                </td>
                                <td ><a v-b-tooltip class='font-weight-bolder' title='Edit' href="/settings/manage-posts/edit/<?=$key->slug?>"><?=$key->name?></a> <small class='d-none d-lg-block text-muted'>[ alias: <?=$key->slug?> ]</small>
                                <span class='text-muted'>Category: <a href="" class='small '><?=$key->category_name?></a></span>
                                </td>
                                <td class='d-none d-lg-inline-block'><a href="" class='small'><?=$key->author?></a></td>
                                <td><?=date('d/m/Y',strtotime($key->created_at))?></td>
                                <td><span class="badge alert-info rounded-circle"><?=$key->hits?></span></td>
                                <td class='d-none d-lg-inline-block'><span class="badge p-0"><?=$key->id?></span></td>
                            </tr>
                            <?php endforeach?>
                            <?php else:?>
                                <tr>
                                    <td colspan='6'>No Article found.</td>
                                </tr>
                            <?php endif?>
                        </tbody>
                    </table>
                    <div class="d-flex justify-content-center my-2">
                        <?= $pager->links() ?>
                    </div>
                </div>
               
            </b-overlay>
        </div>
    </div>
</section>
<script>
    new Vue({
        el: ".section",
        data:{
            isLoading:false,
            selected:[],
        }
    })
</script>
<?php endif?>


<?= $this->endSection() ?>