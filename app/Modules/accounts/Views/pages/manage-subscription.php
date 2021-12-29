<?= $this->extend('Accounts\Views\layouts\default-with-sidebar') ?>
<?= $this->section("section") ?>
<section class="row app d-none">
    <div class="col-lg-9 mb-5">
        <h3 class="font-weight-lighter">Packages Information</h3>
        <div class="table-responsive">
            <table class="table">
                <thead>
                    <tr>
                        <th><b-checkbox inline></b-checkbox> Package</th>
                        <th>Subscribers</th>
                        <th>Amount</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if(count($packages) > 0):?>
                    <?php foreach ($packages as $key):?>
                        <tr>
                            <td>
                                <div class="media">
                                    <b-checkbox inline></b-checkbox>
                                    <div class="media-body">
                                        <h6 class="h6" v-b-tooltip title="<?=$key->description?>"><?=$key->name?></h6>
                                        <span class="badge badge-light font-weight-light">
                                        <?php if($key->notification == 'NO'):?>
                                        <i v-b-tooltip title="Notification is disabled for this package" class="fa fa-bell-slash" ></i>
                                        <?php else:?>
                                        <i v-b-tooltip title="Notification is enable for this package" class="fa fa-bell" ></i>
                                        <?php endif?>
                                        </span>
                                        
                                        <span class="badge badge-light font-weight-light"><i class="fa fa-reply mr-2"></i> <?=$key->refundable?></span>
                                        <span class="badge badge-light font-weight-light"><?=counted($key->duration,"day")?></span>
                                    </div>
                                </div>
                            </td>
                            <td>
                                <p class="btn btn-light btn-sm ">Active Subscribers <span class="badge badge-pill badge-warning"><?=$key->active_subscribers?></span></p>
                                <p class="btn btn-light btn-sm ">All Time Subscribers <span class="badge badge-pill badge-dark"><?=$key->all_time_subscribers?></span></p>
      
                            </td>
                            <td>
                                <h6 class="font-weight-light mb-0"><?=humanize_currency($key->amount)?></h6>
                                <small>[ Total Revenue: <?=humanize_currency($key->revenue)?> ]</small>
                            </td>
                        </tr>
                    <?php endforeach?>
                    <?php else: ?>
                    <tr>
                        <td colspan="3">
                            <h4 >No Packages</h4>
                            <a @click.prevent="$bvModal.show('create-package')" class="btn btn-link">Create New</a>
                        </td>
                    </tr>
                    <?php endif?>
                </tbody>
            </table>
        </div>
    </div>
    <div class="col small">
        <label class="d-block">With packages, you can monetize your prediction and control who views your tips</label>
        <a href="" @click.prevent="$bvModal.show('create-package')" class="text-primary">Add Package</a>
    </div>
    <b-modal size="xl" body-class="p-0" header-class="border-0" centered hide-header hide-footer no-close-on-backdrop lazy id="create-package" title="Create Package">
        <create-package-form></create-package-form>
    </b-modal>

</section>
<script type="text/x-template" id="create-package-form">
<div class="contact my-0 py-0">

<div class="row no-gutters">
    <div class="col-xl-8 col-lg-8 col-md-7">
        <div class="contact-form">
            <form action="" method="POST">
                <input type="hidden" name="redirect_to" value="<?=current_url()?>">
                <div class="row">
                    <div class="col-6">
                        <label>Name</label>
                        <input maxlength="20" v-model="package.name" name="name" required type="text" placeholder="Basic">
                    </div>
                    <div class="col-md-6">
                        <label>Amount</label>
                        <input v-model="package.amount" name="amount" required type="number">
                    </div>
                    <div class="col-12 mb-2">
                        <label>Description</label>
                        <textarea maxlength="100" v-model="package.description" name="description"></textarea>
                    </div>
                    <div class="col-md-6">
                        <label>Duration [Days]</label>
                        <input type="number" v-model="package.duration" name="duration" placeholder="In Days" min="1">
                    </div>
                    <div class="col-md-6">
                        <label>Refundable Policy <i class="fa fa-question-circle" v-b-tooltip title="Choose this option with caution. In a case of lose, your subscriber may request a refund of their subscription money based on the agreed percent."></i></label>
                        <select v-model="package.refundable" name="refundable">
                            <option>No Refund</option>
                            <option>5%</option>
                            <option>25%</option>
                            <option>50%</option>
                            <option>75%</option>
                            <option>100%</option>
                        </select>
                    </div>
                    <div class="col-md-12">
                        <input type="hidden" name="notification"  :value="package.notification? 'YES' :'NO'">
                        <b-checkbox v-model="package.notification" switch>Activate Email & SMS notification when a soccer tip is released <i class="fa fa-question-circle" v-b-tooltip title="Tips are sent to your subscriber's email and/or phone number once they are placed"></i> <span class='badge badge-warning'>Coming Soon</span></b-checkbox>
                    </div>
                </div>
                <div>
                    <button type="submit" class="submit-btn">Submit</button>
                    <button @click.prevent="$bvModal.hide('create-package')" class="submit-btn bg-white text-dark">Close</button>
                </div>
                
            </form>
        </div>
    </div>
    <div class="col-xl-4 col-lg-4 col-md-5">
        <div class="contact-information">
            <div class="text-center text-white">
                <h3>{{package.name}}</h3>
                <h6 class="display-4">₦ {{package.amount}} <sup><small style="font-size:1rem">/ {{package.duration}} Days</small></sup></h6>
                <p>{{package.description}}</p>
            </div>
            <ul class="info-list">
                <li>
                    <span class="icon">Refundable Policy</span>
                    <span class="text text-dark font-weight-bold">{{package.refundable}}</span>
                </li>
                <li>
                    <span class="icon">Notification Alerts</span>
                    <span class="text text-dark font-weight-bold">{{package.notification ? "Yes":"NO"}}</span>
                </li>
            </ul>
        </div>
    </div>
</div>
</div>
</script>
<script>
    Vue.component("create-package-form",{
        template:"#create-package-form",
        data(){
            return{
                package:{
                    name:"Starter",
                    amount:0,
                    description:"",
                    duration:1,
                    refundable:"No Refund",
                    notification:false,
                }
            }
        }
    })
</script>
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
            
        }
    })
</script>
<?= $this->endSection() ?>