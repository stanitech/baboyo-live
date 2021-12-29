<?= $this->extend('Accounts\Views\layouts\default-with-sidebar') ?>
<?= $this->section("section") ?>

<section class="row app d-none">
    <div class="col-lg-9">
        <div class="row">
            <div class="col-lg-10">
                <h3 class="font-weight-lighter">Platform Users</h3>
                <p>Allow people other than you to sign in with their own accounts. </p>
                <div class="list-group list-group-flush mb-3" >
                    <div class="row ">
                        <div @click="$bvModal.show('add-account')" class="col-12 list-group-item list-group-item-action border-0">
                            <div class="media d-flex align-items-center">
                                <div class="info-box mr-2">
                                    <i class="fa fa-plus"></i>
                                </div>
                                <div class="media-body">
                                    <p class="mb-0">Add someone else to this platform</p>
                                </div>
                            </div>
                        </div>
                        <?php foreach ($users as $key):?> 
                        <div class="col-6 list-group-item border-0 list-group-item-action" v-b-toggle="'<?=$key->slug?>'">
                            <div class="media d-flex align-items-start">
                                <b-avatar src="<?=isset($key->cover_img)? $key->cover_img->file : env("app.image.user-placeholder")?>" class="mr-2"></b-avatar>
                                <div class="media-body">
                                    <h6 class="m-0 font-weight-bolder text-capitalize" style="font-size:0.8rem"><?=$key->name?></h6>
                                    <span class="badge p-0 m-0 text-muted"><?=$key->account_type?></span>
                                    <small class="small d-block text-lowercase badge text-left p-0 font-weight-lighter">Joined: <?=humanize_time($key->created_at)?></small> 
                                    <b-collapse accordion="users" id="<?=$key->slug?>">
                                        <div class="d-flex my-3">
                                            <button @click.prevent='changeAccountType(<?=json_encode($key)?>)' class="btn btn-sm btn-secondary mr-2">Change account type</button>
                                            <button v-b-tooltip title="Remove Account" @click.prevent='removeAccount(<?=json_encode($key)?>)' class="btn btn-sm "><i class="fa fa-trash  text-danger  "></i></button>
                                        </div>
                                    </b-collapse>
                                </div>
                            </div>
                        </div>
                        <?php endforeach?>
                    </div>
                </div>
                <?= $pager->links()?>
            </div>
        </div>
    </div>
    <div class="col small">
        <a href="/settings/manage-newsletter-subscribers" class="text-primary d-block my-5">Manage Newsletter Subscribers <span class="badge badge-primary"><?=$newsletter_subscribers?></span></a>

        <label class="d-block ">Send message to one / all users within a particular user group</label>
        <a href="" @click.prevent="$bvModal.show('compose-message')" class="btn border">Compose <i class="fa fa-envelope ml-2   "></i></a>

        <a href="" @click.prevent='' v-b-toggle.invite-users-sidebar class="text-primary d-block mt-5">Invite friends and contacts via email</a>


    </div>
    <b-modal size="xl" hide-header title="New Message" hide-footer no-close-on-backdrop lazy id="compose-message">
        <compose-message-form></compose-message-form>
    </b-modal>
    <b-modal body-class="p-0 border-0" centered hide-header hide-footer no-close-on-backdrop lazy id="add-account">
        <div class="d-flex justify-content-between align-items-center">
            <span class="badge">Baboyo Account</span>
            <button @click="$bvModal.hide('add-account')" class="btn btn-sm"><i class="fa fa-times"></i></button>
        </div>
        <form action="" method="POST" class="row py-4">
            <div class="col-md-10 mx-auto">
                <h4 class="font-weight-lighter mb-3">Create an account for new User</h4>
                <div class="form-group">
                  <label for="" class="font-weight-bold">Who's going to use this account?</label>
                  <input type="text" name="name" required class="form-control border-dark rounded-0" placeholder="Full name">
                </div>
                <div class="form-group">
                  <input type="email" name="email" required class="form-control border-dark rounded-0" placeholder="Email Address">
                </div>
                <!-- <div class="d-flex justify-content-end mt-3">
                    <button disabled type="submit" class="btn btn-sm btn-dark px-4">Save</button>
                </div> -->
                <b-alert class="small" show variant="warning">This feature has been disabled. Let user create account themselves. Its just a click and it guarantee email integrity</b-alert>
            </div>
            
        </form>
    </b-modal>
    <b-modal v-if="account" body-class="p-0 border-0" centered hide-header hide-footer no-close-on-backdrop lazy id="change-account-type">
        <span class="badge">Change account type</span>
        <form action="" method="POST" class="row py-4">
            <input type="hidden" name="id" :value="account.id">
            <div class="col-md-10 mx-auto">
                <h4 class="font-weight-lighter mb-3">Change Account Type</h4>
                <div class="media my-3">
                    <b-avatar :src="account.cover_img ? account.cover_img.file : '<?= env("app.image.user-placeholder")?>'" class="mr-2"></b-avatar> 
                    <div class="media-body">
                        <h6 class="m-0 font-weight-bolder" style="font-size:0.8rem">{{account.name}}</h6>
                        <span class="badge p-0 m-0 text-muted">{{account.account_type}}</span>
                    </div>
                </div>
                <div class="form-group">
                  <label class="font-weight-bold">Account type</label>
                  <select name="account_type" required class="form-control border-dark rounded-0" v-model="account.account_type">
                        <option>STANDARD</option>
                        <option>EXPERT</option>
                        <option>CONTENT WRITER</option>
                        <option>ADMINISTRATOR</option>
                        <?php if(can_access(["SUPER USER"])):?>
                        <option>SUPER USER</option>
                        <?php endif?>
                      
                  </select>
                </div>
                
                <div class="d-flex justify-content-end mt-5">
                    <button type="submit" class="btn btn-sm btn-light px-4 mr-2">OK</button>
                    <button @click.prevent="hideAccountTypeModal" class="btn btn-sm btn-light px-4">Cancel</button>
                </div>
            </div>
            
        </form>
    </b-modal>
    <b-modal v-if="account" body-class="p-0 border-0" centered hide-header hide-footer no-close-on-backdrop lazy id="remove-account">
        <span class="badge">Delete Account & Data</span>
        <form action="" method="POST" class="row py-4">
            <input type="hidden" name="id" :value="account.id">
            <input type="hidden" name="_method" value="DELETE">
            <div class="col-md-10 mx-auto">
                <h4 class="font-weight-lighter mb-3">Delete Account & Data</h4>
                <div class="media my-3">
                    <b-avatar class="mr-2"></b-avatar>
                    <div class="media-body">
                        <h6 class="m-0 font-weight-bolder" style="font-size:0.8rem">{{account.name}}</h6>
                        <span class="badge p-0 m-0 text-muted">{{account.account_type}}</span>
                    </div>
                </div>
                <p>Deleting this person's account will remove all their data from this platform including predictions, match reviews and posts.</p>
                <div class="d-flex justify-content-end mt-5">
                    <button type="submit" class="btn btn-sm btn-light px-4 mr-2">Delete Account & Data</button>
                    <button @click.prevent="hideRemoveAccount" class="btn btn-sm btn-light px-4">Cancel</button>
                </div>
            </div>
            
        </form>
    </b-modal>
    <b-sidebar no-header title='Bulk Invite' id='invite-users-sidebar' lazy sidebar-class='bg-white'  right shadow width="100%">
        
        <invite-users></invite-users>
    </b-sidebar>
   
</section>
<?=$this->include("Accounts\Views\search-component")?>
<script type="text/x-template" id="compose-message-form">
    <form action="/account/send-message" method="post">
        <h5 class="card-title">New Message</h5>
        <hr>
        <div class="row">
            <b-form-group class="col-md-12" label="Recipient Source">
                <b-form-radio-group name='source' v-model='source'>
                    <b-form-radio value='Individual'>Individual</b-form-radio>
                    <b-form-radio value='Group'>Group</b-form-radio>
                </b-form-radio-group>
            </b-form-group>

            <div class="form-group col-md-12" v-show="source === 'Individual'">
                <custom-search selected-tag-class='btn-dark' inputName="to" btnText="Add Reciepents" multiple :options='<?=json_encode($all_users)?>' ></custom-search> 
            </div>
            <div class="form-group col-md-12" v-show="source === 'Group'">
                <custom-search search-item-icon='fa fa-users' selected-tag-class='btn-info' inputName="to" btnText="Add Group" multiple :options='group_options' ></custom-search>
            </div>
            <div class="form-group col-md-12">
                <input name="subject" required type="text" class="form-control border rounded-0" placeholder="Subject">
            </div>
            <div class="form-group col-md-12">
                <textarea v-model="description" name="message" class="form-control d-none border-dark rounded-0"  rows="10" ></textarea>
                <ckeditor :config="{placeholder:'Begin your writing here', toolbar: [ 'bold', 'italic', 'link','bulletedList','numberedList','|','imageInsert','removeFormat'],image: {toolbar: ['imageTextAlternative','imageStyle:full','imageStyle:side','linkImage']}}" :editor="editor" v-model="description"></ckeditor>
            </div>
            <details class="card-body" open>
                <summary>Dynamic Tags <i class="fa fa-question-circle" v-b-tooltip title="Dynamic tags are shortcodes that are replaced with their equivalent element on runtime"></i></summary>
                <div>
                    <span class="btn btn-light" v-b-tooltip title="Replaces the tag with the full name of the user. Returns an empty string if user is a newsletter subscriber or has no name" >[[name]]</span>
                    <span class="btn btn-light" v-b-tooltip title="Dynamically inject a table containing all the predicted games for the day">[[today predictions]]</span>
                </div>
                    
            </details>
            <div class="col-md-12">
                <a @click.prevent="$bvModal.hide('compose-message')" class="btn btn-light mr-2 text-uppercase px-3 rounded-0 mr-1">Cancel</a>
                <button v-if="source" type="submit" class="btn btn-dark text-uppercase px-3 rounded-0">Send</button>
            </div>
           
        </div>
    </form>
</script>
<script>
    Vue.component("compose-message-form",{
        template:"#compose-message-form",
        data(){
            return {
                editor: ClassicEditor,
                description: null,
                source:null,
                group_options:[
                    {name:"Newsletter Subscribers",id:'newsletter-subscribers'},
                    {name:"Standard Users",id:'STANDARD'},
                    {name:"Content Writers",id:'CONTENT WRITER'},
                    {name:"Expert Users",id:'EXPERT'},
                    {name:"Administrators Users",id:'ADMINISTRATOR'},
                    {name:"Super Users",id:'SUPER USER'},
                ]
            }
        },
    })
</script>
<script type="text/x-template" id="invite-users">
    <b-overlay :show="isLoading" class="card-body">
        <div class="card-header bg-transparent border-0">
        <h4 class='card-title'>Bulk Invite</h4>
        </div>
    <div v-if="enter_multiple_invitees_show_message">
        <b-alert variant='success' dismissible :show="enter_multiple_invitees_number_processed > 0" style="border-left:5px solid">
            <div class="d-flex">
                <i class="fa fa-check h4 text-success mr-2"></i>
                <h6 class="font-weight-bolder">We found {{enter_multiple_invitees_number_processed}} email address{{enter_multiple_invitees_number_processed > 1 ? "es":""}} to invite. We've done our best to guess a name {{enter_multiple_invitees_number_processed > 1 ? "for each one":""}}. See if everything looks right, then press invite members button below. </h6>
            </div>
        </b-alert>
        <b-alert variant='danger' dismissible :show="enter_multiple_invitees_number_processed < 1" style="border-left:5px solid">
            <div class="d-flex">
                <i class="fa fa-times h4 text-danger mr-2"></i>
                <h6 class="font-weight-bolder">We could not find a valid email structure. Please check your data again </h6>
            </div>
        </b-alert>
    </div>
    <form method="POST" action='/account/send-invitation'  class="table-responsive" v-if="!enter_multiple_invitees">
        <table class="table text-uppercase table-striped table-borderless">
            <tr class="">
                <th class="text-center  border-top-0"> Email Address</th>
                <th class="text-center  border-top-0">Full Name (optional)</th>
            </tr>
            <tbody>
                <tr v-for="(i,index) in invitees" :key="i.key">
                    <td colspan="2" >
                        <div class="d-flex align-items-center justify-content-between">
                            <b-form-input :name="`invitees[${i.key}][email]`" type="email" v-model="i.email" class="rounded-0 border w-50" :placeholder="`user${index+1}@example.com`" required></b-form-input>
                            <b-form-input :value="i.fullname" class="rounded-0 border mx-2 w-50 text-capitalize" :name="`invitees[${i.key}][name]`" placeholder="Optional"></b-form-input>
                            <i @click.prevent="removeInviteForm(i.key)" class="fa fa-times h6 text-danger"></i>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
        <div class=" text-uppercase d-flex align-items-center">
            <a href="" class="btn btn-link btn-sm " @click.prevent="addInviteForm"> Add another</a> or 
            <a href="" class="btn btn-link btn-sm " @click.prevent="enter_multiple_invitees = true"> add many at once.</a>
        </div>
        <hr>
        <a href="" @click.prevent="show_invitation_message_form = !show_invitation_message_form" class="btn btn-link text-uppercase btn-sm font-size-10">{{show_invitation_message_form ? "Hide" : "Show"}} invitation message</a>
        <div v-show="show_invitation_message_form">
            <textarea name="template" class='d-none' required v-model="invitation_template"></textarea>
            <ckeditor class="border" :editor="editor" v-model="invitation_template"  :config="{toolbar: ['bold', 'italic', 'link', 'bulletedList']}"></ckeditor>
        </div>
        
        <div class="bg-transparent mt-3 px-0 d-flex justify-content-end border-0">
            <button v-b-toggle.invite-users-sidebar class="btn rounded-0 btn-light   mr-2">Close</button>
            <button type="submit" class="btn btn-dark rounded-0">Invite Members</button> 
        </div>
    </form>
    <div v-else>
        <div class="form-group">
            <label>Enter multiple email addresses</label>
            <b-form-textarea required v-model="enter_multiple_invitees_data" autofocus class="rounded" rows="5"></b-form-textarea>
            <p><strong>Tip:</strong> Copy and paste a list of contacts from your email. Please separate multiple addresses with commas!</p>
        </div>
        <div class="d-flex justify-content-end">
            <button @click.prevent="enter_multiple_invitees = false" class="btn rounded-0 btn-light   mr-2">Cancel</button>
            <button @click.prevent="handleMultipleEmails" :disabled="enter_multiple_invitees_data.length < 5" class="btn btn-dark  rounded-0">Add Invitees</button>
        </div>
    </div>
</b-overlay>
</script>

<script>
    Vue.component("invite-users", {
        template: '#invite-users',
        data() {
            return {
                isLoading: false,
                invitees: [{
                    key: Date.now().toString(18),
                    email: "",
                    fullname: ""
                }],
                invitation_message: '',
                enter_multiple_invitees: false,
                enter_multiple_invitees_data: "",
                enter_multiple_invitees_number_processed: null,
                enter_multiple_invitees_show_message: false,

                show_invitation_message_form: false,
                editor: ClassicEditor,
                invitation_template: `<p>Hi [[name]],<br> We would like to introduce you to Baboyo. A platform for football (soccer) statistics, team information, match predictions, bet tips, expert reviews, bet information and user predictions<br> These and much more are what awaits you when you join the community. Without wasting time, <a href='<?=base_url()?>'>join us now</a>.<p>
                <p>You can also join our mailing list to get the latest prediction tips, news updates and special offers delivered directly to your inbox. [[newletter subscription link]]</p>
                `
            }
        },
        methods: {
            addInviteForm() {
                this.invitees.push({
                    key: Date.now()+Math.random().toString(18),
                    email: "",
                    fullname: ""
                })
            },
            removeInviteForm(index) {
                if (this.invitees.length > 1) {
                    this.invitees.splice(this.invitees.findIndex(e => e.key == index), 1);
                }
            },
            handleMultipleEmails() {
                this.enter_multiple_invitees_show_message = false;
                this.enter_multiple_invitees_number_processed = null;
                let invitees = [];
                this.enter_multiple_invitees_data.split(",").map(e => e.trim()).forEach(item => {
                    if(this.invitees.find(x => x.email != item &&  /[\w]+@[\w]+/i.test(item) )) {
                        let name = item.split("@")[0].trim().replaceAll(".", " ").replaceAll("-", " ").replaceAll("_", " ");
                        invitees.push({
                            key: Date.now()+Math.random().toString(18),
                            email: item,
                            fullname: name
                        })
                    }
                })
                this.invitees = Object.assign(this.invitees,invitees);
                this.enter_multiple_invitees = false;
                this.enter_multiple_invitees_show_message = true;
                this.enter_multiple_invitees_number_processed = invitees.length;

            },

        }
    });
</script>



<script>
    new Vue({
        el:"section",
        data:{
            account:null,
        },
        mounted(){
            this.$el.classList.remove("d-none");
        },
        methods:{
            changeAccountType(account){
                this.account = account;
                setTimeout(() => {
                    this.$bvModal.show('change-account-type')
                });
            },
            hideAccountTypeModal(){
                this.account = null;
                this.$bvModal.hide('change-account-type')
            },
            removeAccount(account){
                this.account = account;
                setTimeout(() => {
                    this.$bvModal.show('remove-account')
                });
            },
            hideRemoveAccount(){
                this.account = null;
                this.$bvModal.hide('remove-account')
            },
        }
    })
</script>



<?= $this->endSection() ?>