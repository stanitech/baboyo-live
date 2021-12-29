<?= $this->extend('Accounts\Views\layouts\default-with-sidebar') ?>
<?= $this->section("section") ?>

<section class="row app d-none">
    <div class="col-lg-9">
        <div class="row">
            <div class="col-lg-10">
                <h3 class="font-weight-lighter">Newsletter Subscribers</h3>
                <p>Customers who subscribed to regular updates on predictions and latest news to their inbox </p>
                <hr>
                <form method="POST" class="list-group list-group-flush mb-3" @submit.prevent="handleDelete($event)">
                    <input type="hidden" name="_method" value='DELETE'>
                    <div v-if="selected_emails.length > 0">
                        <button type='submit' class='btn btn-sm btn-outline-danger mb-2'><i class="fas fa-trash-alt"></i> Trash Selected <span class="badge badge-pills">{{selected_emails.length}}</span></button>
                    </div>
                    
                    <div class="row ">
                        <?php if(count($users) > 0):?>
                        <?php foreach ($users as $key):?> 
                        <div class="col-md-6 list-group-item border-0 list-group-item-action">
                            <b-form-checkbox-group name='id[]' v-model="selected_emails">
                                <b-form-checkbox  value="<?=$key->id?>"><?=$key->email?><br><small class='text-muted mt-n5 small'>- <?=humanize_time($key->created_at)?></small></b-form-checkbox>
                            </b-form-checkbox-group>
                        </div>
                        <?php endforeach?>
                        <?php else:?>
                            <h6 class='h6 col-12'>There are no newsletter subscribers on the platform</h6>
                        <?php endif?>
                    </div>
                </form>
                <?= $pager->links()?>
            </div>
        </div>
    </div>
    <div class="col">
        <span class="my-5"><a href="/settings/manage-accounts" class="text-primary ">Manage Platform members</a></span>
    </div>
   
</section>

<script>
    new Vue({
        el:"section",
        data:{
            selected_emails:[]
        },
        mounted(){
            this.$el.classList.remove("d-none");
        },
        methods:{
            handleDelete(e){
                this.$bvModal.msgBoxConfirm(`Are you sure you want to remove ${this.selected_emails > 1 ? 'these emails':'this email'} from the newsletter subscription maillin list? This action is irreversible`,{
                    title:"Confirmation",
                    headerClass:'border-0 p-2',
                    footerClass:'border-0 p-2'
                }).then(value => {
                    if(value){
                        e.target.submit();
                    }
                })
            }
        }
    })
</script>
<?= $this->endSection() ?>