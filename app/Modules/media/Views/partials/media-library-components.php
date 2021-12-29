<script>
    var playing = [];

    function numberToSize(bytes, decimals = 2) {
        if (bytes === 0) return '0 Bytes';

        const k = 1024;
        const dm = decimals < 0 ? 0 : decimals;
        const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB', 'PB', 'EB', 'ZB', 'YB'];

        const i = Math.floor(Math.log(bytes) / Math.log(k));

        return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
    }

</script>

<script type="text/x-template" id="audio-thumbnail">
    <div>
        <i v-if="iconOnly" @click="togglePlay"  :class="is_playing ? 'icon-pause '+iconClass :'icon-play '+iconClass "></i>
        <b-avatar v-else variant="light" style="height:5rem" size="100%" square v-b-tooltip :title="item?.name" @click.prevent="$emit('onclick',item)"  class="mb-3 border-primary">
            <i @click="togglePlay"  :class="is_playing ? 'icon-pause '+iconClass :'icon-play '+iconClass "></i>
        </b-avatar>
        <div class="form-group form-float" v-if="showDuration">
            <div class="form-line">
                <input type="text" class="form-control form-control-sm" name="duration" v-model="duration" placeholder="Audio Duration"> 
            </div>
        </div>
    </div>
</script>
<script>
    Vue.component("audio-thumbnail",{
        "template":"#audio-thumbnail",
        props:{
            item:{
                type:Object | null,
                required:true,
            },
            iconOnly:{
                type:Boolean
            },
            iconClass:{
                type:String,
                default:"icon h1 text-primary"
            },
            showDuration:{
                type:Boolean
            }
        },
        data(){
            return {
                is_playing: false,
                file:null,
                duration:null,
            }
        },
        mounted(){
            this.file =  new Audio(this.item.file_id ? this.item.file_id.file :  this.item.file)  
            this.file.oncanplay = () => this.duration = this.getDuration();
            this.file.onplay = () => this.is_playing = true
            this.file.onpause = () => this.is_playing = false
            this.file.onended = () => this.is_playing = false 
        },
        destroyed(){
            this.file.pause();
            this.file = null;
        },
        methods:{
            togglePlay(){
                if(this.is_playing){
                    this.file.pause()
                    //playing.splice(playing.findIndex(e => e == this.file),1)
                }else{
                    playing.forEach(e => {
                        e.pause();
                        if(e !== this.file){
                            e.currentTime = 0
                        }
                    })
                    this.file.play().then(()=>{
                        playing.push(this.file);
                        let cover_img = this.item.cover_img ?  this.item.cover_img.file : '<?=env('app.image.logo')?>';
                        let content = `<div class="media text-white">
                                        <div class="mr-2" style="width:2.5rem;height:2.5rem;background-size:cover;background-position:center center;background-image:url(${cover_img})"></div>
                                        <div class="media-body">
                                            <small class="text-muted">Now Playing:</small>
                                            <h6>${this.item.name}</h6>
                                        </div>
                                    </div>`
                        this.showNotification("warning",content)
                    });
                   
                }
            
            },
            getDuration(){
                let duration = Math.round(this.file.duration);
                
                let minutes = Math.floor(duration / 60);
                var seconds = duration - minutes * 60;
    
                return this.strPadLeft(minutes,'0',2)+ ':' +this.strPadLeft(seconds,'0',2)
            },
            strPadLeft(string,pad,length) {
                return (new Array(length+1).join(pad)+string).slice(-length);
            },
        }
    })
</script>

<script type="text/x-template" id="media-thumbnail">
<div style="position:relative;cursor:pointer" @click.prevent="$emit('onclick',item)">
    <div v-if="item.type.includes('audio')" class=" w-100 h-100 d-flex justify-content-center align-items-center text-white text-center" style="position:absolute;top:0;left:0;z-index:1">
        <small class="font-weight-bold"><i class="icon-file-audio-o mr-1"></i> {{item.name}}</small>
    </div>
    <b-avatar :size="size" :alt="item?.name" square button v-b-tooltip :title="item?.name"   class="mb-3" :src="src"></b-avatar>
</div>
    
</script>
<script>
    Vue.component("media-thumbnail",{
        "template":"#media-thumbnail",
        props:{
            item:{
                type:Object | null,
                required:true,
            },
            size:{
                type:String,
                default:'10rem'
            }
        },
        computed:{
            src(){
                if(this.item){
                    if(this.item.type.includes("image")){
                        return this.item.file;
                    }else if(this.item.type.includes("audio")){
                        return "<?=env("app.audio.placeholder")?>"
                    }
                }else{
                    return "<?=env("app.audio.placeholder")?>"
                }
            } 
        }
    })
</script>


<script>
    Vue.component("image-uploadify", {
        template: `<form class="my-4" method="POST" enctype="multipart/form-data">
        <b-overlay :show="isLoading">
            <template #overlay>Uploading file. Please wait...<br>
            <progress></progress>
            </template>
        <input ref="uploadify" name="files[]" type="file" @change='handleUpload()' accept=".xlsx,.xls,image/*,.doc,audio/*,.docx,video/*,.ppt,.pptx,.txt,.pdf" multiple/>
        </b-overlay>
        </form>`,
        data(){
            return {
                isLoading:false,
            }
        },
        
        mounted() {
            $( this.$refs.uploadify).imageuploadify();
        },
        methods: {
            handleUpload(){
                this.isLoading = true;
                fetch("/administrator/media/api",{method:"POST",body:new FormData(this.$el)})
                .then(res =>  res.json())
                .then (result => {this.$emit("on-upload",result);this.isLoading = false});
            }
        }
    })
</script>

<script type="text/x-template" id="media-gallery">
    <b-modal  static scrollable :id="id" size="xl" title="Insert Media" no-close-on-backdrop hide-footer>
        <b-overlay :show="isLoading">
            <div class="row">
                <div class="col-lg-2">
                    <p class="text-muted">Actions</p>
                    <nav class="nav flex-lg-column flex-row mb-3">
                        <li class="nav-item ">
                            <a :class="primaryTabIndex == 0 ? 'font-weight-bolder':''" @click.prevent="primaryTabIndex = 0" class="text-dark" href="#">Insert Media</a>
                        </li>
                        <li  class="nav-item d-none d-md-block"><div role="separator" class="dropdown-divider"></div></li>
                        <li class="nav-item ml-5 ml-lg-0">
                            <a :class="primaryTabIndex == 1 ? 'font-weight-bolder':''" @click.prevent="primaryTabIndex = 1" class="text-dark" href="#">Insert from URL</a>
                        </li>
                    </nav>
                </div>
                <div class="col-lg">
                    <b-tabs nav-class="d-none" pills v-model="primaryTabIndex">
                        <b-tab>
                            <b-tabs lazy pills v-model="tabIndex" nav-class="mb-2">
                                <b-tab title="Upload Files">
                                    <image-uploadify @on-upload="reload($event)"></image-uploadify>
                                </b-tab>
                                <b-tab title="Media Library">
                                    <div class="row ">
                                        <div class="col-4 col-lg-3 col-xl-2 text-center mb-3" v-for="m in media">
                                            <media-thumbnail @onclick="handleThumbnailClick($event)" :item="m"></media-thumbnail>
                                        </div>
                                    </div>
                                    <div class="col-12 mb-5">
                                        <button @click.prevent="current_page++ " class="btn btn-dark btn-block">Load More</button>
                                    </div>
                                    <b-modal no-fade scrollable v-if="detail" lazy :id="id+'_details'" size="lg" hide-header hide-footer no-close-on-backdrop>
                                        <div class="card border-0">
                                            <div class="d-flex justify-content-end">
                                                <button class="btn btn-light btn-sm mr-2" @click.prevent="detail = null;$bvModal.hide(id+'_details')">Close</button>
                                                <button class="btn btn-primary btn-sm" @click.prevent="$emit('use-image',detail);$bvModal.hide(id);$bvModal.hide(id+'_details')">Use File</button>
                                            </div>
                                            <div class=" card-body">
                                                <div class="row">
                                                    <div class="col-md-6"> 
                                                        <media-thumbnail :size="'100%'" :item="detail" v-if="detail.type.includes('image')"></media-thumbnail>
                                                        <audio-thumbnail v-else-if="detail.type.includes('audio')" :item="detail"></audio-thumbnail> 
                                                    </div>
                                                    <div class="col-md-6 ">
                                                        <ul class="list-unstyled">
                                                            <li class="mb-2 lead"><a :href="detail.file" target="_blank">{{detail.name}}</a></li>
                                                            <li class="mb-2"><strong>Uploaded On</strong>: {{detail.created_at}}</li>
                                                            </li>
                                                            <li class="mb-2"><strong>File type</strong>: {{detail.type}}</li>
                                                            <li class="mb-2"><strong>File size</strong>: {{size }}</li>
                                                            <li class="mb-2 "><strong>File URL</strong>: {{detail.file}}</li>

                                                        </ul>
                                                        <form class="mt-3" @submit.prevent="handleDelete($event)">
                                                            <input type="hidden" name="id" :value="detail.id">
                                                            <input type="hidden" name="_method" value="DELETE" />
                                                            <button type="submit" class="btn text-danger btn-sm p-0">Delete Permanently</button>
                                                        </form>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                        
                                    </b-modal>
                                </b-tab>
                            </b-tabs> 
                        </b-tab>
                        <b-tab>
                            <h3>Insert from URL</h3>
                            <form class="form-material" method="POST" @submit.prevent="handleUpdate($event)">
                                <input type="hidden" name="source" value="EXTERNAL">
                                <input type="hidden" name="_method" value="PUT">
                                <input type="hidden" name="type" :value="file.type">
                                <div class="form-group form-float">
                                    <label class="form-label" >File URL</label>
                                    <div class="form-line">
                                        <input class="form-control" type="url" name="file" placeholder="https://" v-model="file.url">
                                    </div>
                                </div>
                                <div class="form-group form-float">
                                    <label class="form-label" >File Name</label>
                                    <div class="form-line">
                                        <input class="form-control" type="text" name="name" v-model="file.name">
                                    </div>
                                </div>
                                <div class="form-group d-flex justify-content-end">
                                    <button @click.prevent="$bvModal.hide(id)" class="btn btn-light mr-2">Close</button>
                                    <button class="btn btn-primary">Save</button>
                                </div>
                            </form>
                        </b-tab>
                    </b-tabs>
                </div>
            </div>
        </b-overlay>
    </b-modal>
</script>

<script>
 var my_media_gallery =  Vue.component("media-gallery",{
        template:"#media-gallery",
        props:{
            type:{
                type:String,
                default:""
            }
        },
        data(){
            return {
                pagination:null,
                current_page:1,
                isLoading:false,
                media:[],
                detail:null,
                tabIndex:1,
                clicks:0,
                timer: null,
                primaryTabIndex:0,
                file:{
                    url:"",
                    name:'',
                    type:'',
                }
            }
        },
        watch:{
            'file.url'(value){
                if(value.endsWith(".mp3") || value.endsWith(".aac") || value.endsWith(".wma")){
                    this.file.type = "audio/mpeg"
                }
                else if(value.endsWith(".jpg") || value.endsWith(".png") || value.endsWith(".webp") || value.endsWith(".jpeg") || value.endsWith(".gif") || value.endsWith(".tiff") || value.endsWith(".svg")){
                    this.file.type = "image/jpeg"
                }else{
                    this.file.type = ""
                }
            },
            current_page(value){
                this.getMedia(value)
            }
        },
        computed:{
            id(){
                return '__media-gallery-modal__'+Date.now().toString(36);
            },
            size(){
                if(!this.detail.size){
                    return 0;
                }
                return numberToSize(this.detail.size)
            }
        },
        mounted(){
            this.$emit("modal_id",this.id); 
            this.getMedia();
        },
        methods:{
            getMedia(page = 1,reload=false){
                this.isLoading = true;
                this.current_page = page;
                fetch(`/administrator/media/get-media-ajax/${this.current_page}?type=`+this.type)
                .then(res =>  res.json())
                .then(result => {
                    if(reload){
                        this.media = result.files;
                    }else{

                    this.media = this.media.concat(result.files);
                    }
                
                    this.isLoading = false
                });
            },

            handleUpdate(e){
                fetch("/administrator/media/api",{method:"POST",body:new FormData(e.target)})
                .then(res =>  res.json())
                .then(result => {
                    this.getMedia(1,true);
                    this.primaryTabIndex = 0;
                    this.tabIndex = 1;
                });
            },
            handleDelete(e){
                this.$bvModal.msgBoxConfirm('Please confirm that you want to delete', {
                    title: 'Please Confirm',
                    size: 'sm',
                    headerClass: 'p-2 border-bottom-0',
                    footerClass: 'p-2 border-top-0',
                    centered: true,
                    static: true,
                    buttonSize:'sm'
                }).then(value => {

                    if (value) {
                        this.detail = null;
                        this.isLoading = true
                        fetch("/administrator/media/api",{method:"POST",body:new FormData(e.target)})
                        .then(res =>  res.json())
                        .then(result =>{
                            this.getMedia(1,true)
                        });
                    }
                })
            },
            reload(e){
                if(e && e.status){
                    this.isLoading = true;
                    this.getMedia(1,true);
                    this.tabIndex = 1;
                    this.isLoading = false;
                }
            },
            handleThumbnailClick(event){
                playing.forEach(e => {
                    e.pause();
                    e.currentTime = 0

                })
                playing = [];
                this.clicks++;
                if(this.clicks === 1){
                    this.timer = setTimeout(() => {
                        playing.forEach(e => e.pause())
                        this.detail = event;
                        this.clicks = 0;
                        this.$bvModal.show(this.id+'_details')
                    }, 300);
                }else{
                    clearTimeout(this.timer);
                    this.clicks = 0;
                    this.$emit('use-image',event);
                    this.$bvModal.hide(this.id);

                }
            }

        }  
    })
    function handleClick(page){
        my_media_gallery.options.methods.getMedia(page);
    }
</script>

<script type="text/x-template" id="open-gallery-button">
    <div class="d-flex align-items-center flex-column">
        <media-gallery :type="fileType" @use-image="useImage($event)" @modal_id = "modal_id=$event" ></media-gallery>
        <template  v-if="thumbnail" >
            <template v-if="media">
                <media-thumbnail @onclick="showGallery" size="100%" v-if="media.type.includes('image')" :item="media"></media-thumbnail>
                <audio-thumbnail size="100%" show-duration v-else-if="media.type.includes('audio')" :item="media"></audio-thumbnail>
                <input type="hidden" :name="inputName" :value="media.id"/>
            </template>
            <b-avatar v-else size="50%" variant="white" square button class="p-0" @click="showGallery" :src="fileType == 'image' ? '<?=env("app.image.placeholder")?>':'<?=env("app.audio.placeholder")?>'"></b-avatar>
        </template>
        <div class="d-flex justify-content-between align-items-center">
            <small>{{btnText}}</small>
            <button  type="button" class='btn btn-primary btn-sm mr-1 d-none' @click.prevent="showGallery">{{btnText}}</button>
            <button v-if="media" type="button" class='btn badge ' @click.prevent="media = null"><i class="icon-times-circle mr-1"></i> Clear</button>
        </div>
    </div>
</script>

<script>
    Vue.component("open-gallery-button", {
        template: '#open-gallery-button',
        props:{
            thumbnail:{
                type:Boolean,
            },
            fileType:{
                type:String,
                default:"image",
            },
            item:{
                type:Object | null
            },
            inputName:{
                type:String,
                default:"cover_img"
            },
            btnText:{
                type:String,
                default:"Choose Image"
            }
        },
        data(){
            return {
                modal_id:null,
                show_gallery:false,
                media: null,
            }
        },
        beforeMount(){
            setTimeout(() => {
                this.media = this.item;
            });
        },
        methods:{
            showGallery(){
                this.$bvModal.show(this.modal_id)
            },
            useImage(event){
                this.media = event;
                this.$emit('file',event)
            }
        }
    })
</script>

<script type="text/x-template" id="image-grid">
    <div>
        <media-gallery type="image" @use-image="useImage($event)" @modal_id = "modal_id=$event" ></media-gallery>
        <div class="d-flex align-items-center flex-wrap w-100">
            <div class="m-2" style="position:relative" v-for="image in media">
                <i @click.prevent="removeImage(image.id)" class="icon-times s-18 text-danger" style="position:absolute;top:0;z-index:10"></i>
                <b-avatar  :src="image.file" button square size="6rem"></b-avatar>
                <input type="hidden" required :name="`${inputName}[]`" :value="image.id">
            </div>
            <b-avatar @click.prevent="showGallery"  variant="transparent" button square size="6rem" src="/assets/img/icon/add-img.png">
            </b-avatar>
        </div>
    </div>
</script>
<script>
    Vue.component("image-grid", {
        template: '#image-grid',
        props:{
            item:{
                type:Object | null,
                default:()=>[],
            },
            inputName:{
                type:String,
                default:"images"
            },
        },
        data(){
            return {
                modal_id:null,
                show_gallery:false,
                media: [],
            }
        },
        mounted(){
            setTimeout(() => {
                this.media = this.item;
            });
        },
        methods:{
            showGallery(){
                this.$bvModal.show(this.modal_id)
            },
            useImage(event){
                this.media.push(event);
            },
            removeImage(id){
                this.media.splice(this.media.findIndex(e => e.id == id),1)
            }
        }
    })
</script>
