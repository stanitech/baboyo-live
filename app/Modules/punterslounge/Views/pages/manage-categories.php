<?= $this->extend('Accounts\Views\layouts\default-with-sidebar') ?>
<?= $this->section("section") ?>
<?=$this->include("Media\Views\partials\media-library-components")?>

<?php if($action == 'new' || $action == 'edit'):?>
<section class="section mb-5" style="min-height:100vh">
    <b-overlay :show="isLoading">
        <template #overlay> Please wait...<br>
            <progress></progress>
        </template>
        <div class="d-flex justify-content-between">
            <h3 class="font-weight-lighter"> <?= isset($post) ? 'Edit': 'New'?> Category</h3>
        </div>
        <form class="my-4" method="POST" action='/settings/manage-post-category' @submit='isLoading=true'>
            <?php if(isset($post)):?>
            <input type="hidden" name='id' value="<?=$post->id?>">
            <?php endif?>
            <div class="d-flex justify-content-end mb-3">
                <button name="action" value='save' type='submit' class="btn btn-sm btn-outline-dark mr-2"><i class="fa fa-save mr-1"></i> <span class="d-none d-md-inline">Save</span></button>
                <button name="action" value='save-and-close' type='submit' class="btn btn-sm btn-light mr-1"><i class="fa fa-check mr-1"></i> <span class="d-none d-md-inline">Save & Close</span></button>
                <button name="action" value='save-and-new' type='submit' class="btn btn-sm btn-light mr-1"><i class="fa fa-plus mr-1"></i> <span class="d-none d-md-inline">Save & New</span></button>
                <a href='/settings/manage-post-category' class="btn btn-sm btn-light "><i class="fa fa-times-circle mr-1"></i> <span class="d-none d-md-inline">Cancel</span></a>
            </div>
            <div class="row">
                <div class="col-md-6">
                    <b-form-group label='Title *' >
                        <b-form-input name="name" maxlength="30" required value='<?=$post->name??''?>'></b-form-input>
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
                    <b-form-group label='Description'>
                        <b-form-textarea v-model="description" class="d-none" name="description"></b-form-textarea>
                        <ckeditor :config="{ placeholder:'Begin your writing here', toolbar: {shouldNotGroupWhenFull: false,items: ['heading', '|','fontfamily', 'fontsize', '|', 'bold', 'italic', 'link','bulletedList','numberedList','|','imageInsert','removeFormat'],image: {toolbar: ['imageTextAlternative','imageStyle:full','imageStyle:side','linkImage']}} }" :editor="editor" v-model="description"></ckeditor>
                    </b-form-group>
                </div>
                <div class="col-md-3">
                    <b-tabs >
                        <b-tab title='Basic'>
                            <b-form-group class='mt-3' label='Status'>
                                <select class="form-control" name='status'>
                                    <?php foreach (['Published','Unpublished'] as $key):?>
                                    <option <?=isset($post) && $post->status == strtoupper($key) ? 'selected':''?> value="<?=strtoupper($key)?>"><?=$key?></option>
                                    <?php endforeach?>
                                </select>
                            </b-form-group>
                            <div class="row mt-3">
                                <div class="col-md-6">
                                    <b-form-group  label="Featured">
                                        <b-form-radio-group size="sm" button-variant="outline-dark" 
                                            v-model="featured"
                                            :options="['YES','NO']"
                                            name="featured"
                                            buttons
                                        ></b-form-radio-group>
                                    </b-form-group>
                                </div>
                            </div>
                            <b-form-group label='Created Date' >
                                <b-form-input name='created_at' type='datetime-local' value='<?=isset($post)? date('Y-m-d\TH:i',strtotime($post->created_at)):''?>'></b-form-input>
                            </b-form-group>
                        </b-tab>
                        <b-tab title='Options' disabled>
                            <b-form-group class='mt-3' label="Enable Background Options">
                                <b-form-radio-group  button-variant="outline-primary" 
                                    v-model="options.background_type"
                                    :options="['None','Image','Video']"
                                    name="options[background_type]"
                                    buttons
                                ></b-form-radio-group>
                            </b-form-group>
                            <div v-if='options.background_type == "Image" '>
                                <open-gallery-button btn-text="Choose Your Image" thumbnail :item="options.cover_img" input-name="options[cover_img]"></open-gallery-button>
                            </div>
                            <div v-if='options.background_type == "Video" '>
                                <b-form-group label='Youtube/Vimeo URL'>
                                    <b-form-input v-model='options.video_url' name="options[video_url]" type='url'></b-form-input>
                                </b-form-group> 
                            </div>
                        </b-tab>
                    </b-tabs>
                    

                </div>
            </div>
                
        </form>
       
    </b-overlay>
    <b-sidebar>

    </b-sidebar>
</section>
<script>
    new Vue({
        el: ".section",
        data:{
            isLoading:false,
            description:'<?=$post->description??''?>',
            featured:'<?=$post->featured??'YES'?>',
            options: JSON.parse('<?=isset($post) ? json_encode($post->options): json_encode(['background_type'=>'None','cover_img'=>'']) ?>'),
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
                <h3 class="font-weight-lighter">All Categories</h3>
                <div class="d-flex my-3 justify-content-lg-end align-items-center">
                    <a class="btn  btn-dark btn-sm mr-1" href="/settings/manage-post-category/new"><i class="fa fa-plus-circle mr-1"></i> New</a>
                    <form v-show='selected.length > 0' method="POST" action="/settings/manage-posts-category-batch-operations">
                        <div class="btn-group btn-group-sm">
                            <input type="hidden" name="id[]" v-for='i in selected' :value="i">
                            <button type='submit' name='status' value='PUBLISHED' class="btn border btn-light btn-xs mr-1"><i class="fa fa-check text-success mr-1"></i> Publish</button>
                            <button type='submit' name='status' value='UNPUBLISHED' class="btn border btn-light btn-xs mr-1"><i class="fa fa-times text-danger mr-1"></i> Unpublish</button>
                            <button type='submit' name='featured' value='YES' class="btn border btn-light btn-xs mr-1"><i class="fa fa-star  text-warning mr-1"></i> Feature</button>
                            <button type='submit' name='featured' value='NO' class="btn border btn-light btn-xs mr-1"><i class="fa fa-star mr-1"></i> Unfeature</button>
                            <button type='submit' name='trash' value='YES' class="btn border btn-light btn-xs mr-1"><i class="fa fa-trash mr-1"></i> Trash</button>
                        </div>
                    </form>
                </div> 
                <form class="row mb-3">
                    <div class="col-lg-3">
                        <div class="input-group border rounded">
                            <div class="input-group-prepend bg-white border-0">
                                <span class="input-group-text bg-white border-0"><i class="fa fa-search"></i></span>
                            </div>
                            <input name="search" class="form-control border-0" type="search" placeholder="Find in Categories" value="<?=service('request')->getGet('search')?>">
                        </div>
                    </div>
                </form>

                <div class="table-responsive">
                    <table class="table table-light table-sm small">
                        <tbody>
                            <tr>
                                <td style='width:1rem'><input type='checkbox'></td>
                                <td style='width:7rem'>Status</td>
                                <td style='min-width:20rem'>Title</td>
                                <td style='width:1rem'><i class='fa fa-check text-success' v-b-tooltip title='Published items'></i></td>
                                <td style='width:1rem'><i class='fa fa-times-circle text-danger' v-b-tooltip title='Unpublished items'></i></td>
                                <td style='width:1rem'><i class='fa fa-star text-warning' v-b-tooltip title='Featured items'></i></td>
                            </tr>
                            <?php if(count($posts) > 0  ):?>
                            <?php foreach ($posts as $key):?>
                            <tr>
                                <td>
                                    <input <?=$key->name == 'Uncategorized'?'disabled':''?> type='checkbox' value='<?=$key->id?>' v-model='selected'>
                                </td>
                                <td>
                                    <form method="POST">
                                        <input type="hidden" name="id" value='<?=$key->id?>'>
                                        <input type="hidden" name="slug" value='<?=$key->slug?>'>
                                        <input type="hidden" name="action" value='save-and-close'>
                                        <div class="btn-group btn-group-sm">
                                            <?php if($key->status == 'PUBLISHED'):?>
                                            <button type='submit' name='status' value='UNPUBLISHED' v-b-tooltip title='Unpublish item' class='btn btn-xs border  text-success'><i class='fa fa-check'></i></button>
                                            <?php else:?>
                                            <button type='submit' name='status' value='PUBLISHED' v-b-tooltip title='Publish item' class='btn btn-xs border text-danger'><i class='fa fa-times-circle'></i></button>
                                            <?php endif?>
                                            <?php if($key->featured == 'YES'):?>
                                            <button type='submit' name='featured' value='NO' v-b-tooltip title='Unfeatured' class='btn btn-xs border'><i class='fa fa-star text-warning'></i></button>
                                            <?php else:?>
                                            <button type='submit' name='featured' value='YES' v-b-tooltip title='Featured' class='btn btn-xs border'><i class='fa fa-star'></i></button>
                                            <?php endif?>
                                        </div>
                                    </form>
                                </td>
                                <td v-b-tooltip title='Edit'><a  href="/settings/manage-post-category/edit/<?=$key->slug?>"><?=$key->name?></a> <small>[ alias: <?=$key->slug?> ]</small></td>
                                <td><span class="badge badge-light rounded-circle"><?=$key->published_articles?></span></td>
                                <td><span class="badge badge-light rounded-circle"><?=$key->unpublished_articles?></span></td>
                                <td><span class="badge badge-light rounded-circle"><?=$key->featured_articles?></span></td>
                            </tr>
                            <?php endforeach?>
                            <?php else:?>
                                <tr>
                                    <td colspan='6'>No categories found.</td>
                                </tr>
                            <?php endif?>
                        </tbody>
                    </table>
                </div>
                <div class="d-flex justify-content-center my-2">
                    <?= $pager->links() ?>
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