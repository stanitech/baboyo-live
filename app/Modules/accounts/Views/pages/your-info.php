<?= $this->extend('Accounts\Views\layouts\default-with-sidebar') ?>
<?= $this->section("section") ?>
<?=$this->include("Media\Views\partials\media-library-components")?>
<section class="row app d-none">
    <media-gallery type="image" @use-image="useImage($event)" @modal_id = "modal_id=$event" ></media-gallery>
    <div class="col-lg-3 mb-5 text-center">
        <h3 class="font-weight-lighter text-left">Your Info</h3>
        <b-avatar @click="showGallery" variant="light" button class="my-5" variant="dark" size="12rem" :src="user_image"></b-avatar>
        <h5 class="font-weight-bolder lead"><?=$user->name?></h5>
        <p class="text-muted mb-1 small"><?=$user->email?></p>
        <h6 class="text-muted mb-0 small"><?=$user->account_type?></h6>
    </div>
    <div class="col-lg-6 mb-5">
        <h3 class="font-weight-lighter mb-3">My Subscriptions</h3>
        <table class="table small">
            <thead >
                <tr>
                    <th>Package</th>
                    <th>Pricing</th>
                    <th>Next Due Date</th>
                    <th>Status</th>
                </tr>
            </thead>
            <tbody>
                <?php if(count($subscriptions) > 0):?>
                <?php foreach ($subscriptions as $key):?>
                <tr>
                    <td >
                        <h6 class="mb-0 h6"><?=$key->package_name?></h6>
                        <a class="btn-link small" href="#"><?=$key->expert_name?></a>
                    </td>
                    <td>
                        <span class=""><?=humanize_currency($key->amount)?></span><br>
                        <small><?=counted($key->duration,"day")?></small>
                    </td>
                    <td>
                        <?=date("d/m/Y h:i",strtotime($key->expires_at))?><br>
                        <?=humanize_time($key->expires_at)?>
                    </td>
                    <td>
                        <?php if(strtotime($key->expires_at) < time()):?>
                        <a href="/account/transaction/<?=$key->transaction_reference?>" class="btn btn-light btn-sm rounded-0">Terminated</a>
                        <?php else:?>
                        <a href="/account/transaction/<?=$key->transaction_reference?>" class="btn btn-success btn-sm rounded-0">Active</a>
                        <?php endif?>
                    </td>
                </tr>
                <?php endforeach?>
                <?php else: ?>
                <tr>
                    <td colspan="4">
                        <h6 class>You have never subscribed to any package</h6>
                    </td>
                </tr>
                <?php endif?>
            </tbody>
        </table>
    </div>
    <div class="col small">
        <label class="d-block">Not your correct details?</label>
        <a href="" @click.prevent="$bvModal.show('edit-account')" class="text-primary">Edit Profile</a>
        <label class="d-block mt-5">Let people who visit your profile know more about you?</label>
        <a href="" @click.prevent="$bvModal.show('edit-description')" class="text-primary">Add / modify your bio</a>
        <label class="d-block mt-5">Don't like your avatar?</label>
        <a href="" @click.prevent="showGallery" class="text-primary">Change Image</a>
    </div>
    <b-modal body-class="p-0" centered hide-footer no-close-on-backdrop lazy id="edit-account" title="Update your account details">
        <div class="contact py-0">
            <div class="contact-form p-2">
                <form action="/settings/manage-accounts" method="POST" >
                    <input type="hidden" name="id" value="<?=$user->id?>">
                    <input type="hidden" name="redirect_to" value="<?=current_url()?>">
                    
                    <input type="text" name="name" value="<?=$user->name?>" required  placeholder="Full name">
                    <input type="email" value="<?=$user->email?>" name="email" required  placeholder="Email Address">

                    <div class="d-flex justify-content-end mt-3">
                        <button @click.prevent="$bvModal.hide('edit-account')" class="btn btn-light text-uppercase rounded-0 px-4 mr-2">Close</button>
                        <button type="submit" class="btn btn-dark text-uppercase rounded-0 px-4">Next</button>
                    </div>
                    
                </form>
            </div>
        </div>
    </b-modal>
    <b-modal body-class="p-0" centered hide-footer no-close-on-backdrop lazy id="edit-description" title="Edit Description">
        <div class="contact py-0">
            <div class="contact-form p-2">
                <form action="/settings/manage-accounts" method="POST" class="">
                    <input type="hidden" name="id" value="<?=$user->id?>">
                    <input type="hidden" name="redirect_to" value="<?=current_url()?>">
                    <textarea name="description" rows="5" required  placeholder="Update your description"><?=$user->description?></textarea>
                    <div class="d-flex justify-content-end mt-3">
                        <button @click="$bvModal.hide('edit-description')" class="btn btn-light text-uppercase rounded-0 px-4 mr-2">Close</button>
                        <button type="submit" class="btn btn-dark text-uppercase rounded-0 px-4">Next</button>
                    </div>
                    
                </form>
            </div>
        </div>
    </b-modal>
</section>
<script>
    new Vue({
        el:"section",
        data:{
            user_image:'<?=isset($user->cover_img)? $user->cover_img->file : env("app.image.user-placeholder")?>',
            media: event,
            modal_id:null,
        },
        mounted(){
            this.$el.classList.remove("d-none");
        },
        methods:{
            showGallery(){
                this.$bvModal.show(this.modal_id)
            },
            useImage(event){
                let formdata = new FormData();
                formdata.append("cover_img",event.id)
                formdata.append("id",<?=$user->id?>)
                fetch("/settings/manage-accounts/api",{
                    method:"POST",body:formdata
                }).then(response => response.json())
                .then(result => {
                    if(result.status){
                        this.user_image = event.file;
                    }
                });
            }
        }
    })
</script>
<?= $this->endSection() ?>