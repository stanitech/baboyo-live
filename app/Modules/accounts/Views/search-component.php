<style>
    .h-200{
        height:200px;
        width:100%;
        overflow-y: auto;
    }
</style>
<script type="text/x-template" id="custom-search">
    <div>
        <div v-if="selected_ids.length > 0">
            <input v-if="multiple" type="hidden" v-for="i in selected_ids" :name="`${inputName}[]`" :value="i">
            <input v-else type="hidden" :name="inputName" :value="selected_ids[0]">
        </div>

        <b-form-tags v-model="value" no-outer-focus class="mb-2" :limit="multiple ? null :1 ">
            <template v-slot="{ tags, disabled, addTag, removeTag }">
                <ul v-if="tags.length > 0" class="list-inline d-inline-block mb-2">
                    <li v-for="tag in tags" :key="tag" class="list-inline-item">
                        <b-form-tag class="mb-2 btn " :class="selectedTagClass" @remove="removeTag(tag)" :title="tag" :disabled="disabled" variant="transparent">{{tag}}</b-form-tag>
                    </li>
                </ul>

                <b-dropdown size="sm" variant="transparent" no-caret boundary="viewport" menu-class="h-200" style="max-height:200px;overflow:auto">
                    <template #button-content><i class="fa fa-plus-circle mr-2"></i>{{btnText}}</template>
                    <b-dropdown-form @submit.stop.prevent="() => {}">
                        <b-form-group >
                            <b-form-input class="border-bottom" v-model="search" type="search" size="sm" autocomplete="off" placeholder="Enter Keyword"
                            ></b-form-input>
                        </b-form-group>
                    </b-dropdown-form>
                    <b-dropdown-item-button v-for="option in availableOptions" :key="option.id" @click="onOptionClick({ option, addTag })">
                        <template #default>
                            <search-item :icon-class='searchItemIcon' :item="option"></search-item>
                        </template>
                    </b-dropdown-item-button>
                    <b-dropdown-text v-if="availableOptions.length === 0">There nothing to select</b-dropdown-text>
                </b-dropdown>
            </template>
        </b-form-tags>
    </div>
</script>
<script>
    Vue.component("custom-search", {
        template: "#custom-search",
        props:{
            options:{type:Array},
            btnText:{type:String,default:"Choose"},
            multiple:{type:Boolean},
            inputName:{type:String,default:"selected_entry"},
            item:{type:Array | null,default:()=>[]},
            selectedTagClass:{type:String,default:''},
            searchItemIcon:{default:'fa fa-user-alt',type:String}
        },
        data() {
            return {
                search: '',
                value: [],
                selected_ids:[],
            }
        },
        watch:{
            value(value){
                let ss = [];
                value.forEach(element => {
                    let b = this.options.find(e => e.name == element);
                    if(b && b.id){
                        ss.push(b.id)
                    }
                });
                this.selected_ids = ss;
                this.$emit("input",this.selected_ids)
            }      
        },
        computed: {
            criteria() {
                return this.search.trim().toLowerCase()
            },
            availableOptions() {
                const criteria = this.criteria
                // Filter out already selected options
                const options = this.options.filter(opt => this.value.indexOf(opt.name) === -1)
                if (criteria) {
                // Show only options that match criteria
                return options.filter(opt => opt.name.toLowerCase().indexOf(criteria) > -1);
                }
                // Show all options available
                return options
            },
            searchDesc() {
                if (this.criteria && this.availableOptions.length === 0) {
                return 'There are no tags matching your search criteria'
                }
                return ''
            }
        },
        mounted(){
            if(this.item && Array.isArray(this.item)){
               this.value =  this.item.map(e => e.name)
            }
        },
        methods: {
            onOptionClick({ option, addTag }) {
                addTag(option.name)
                this.search = ''
            }
        }
    });
</script>

<script>
    Vue.component("search-item", {
        template: `<div class="media border-bottom">
                        <i class=" mr-1" :class='iconClass'> </i>
                        <div class='media-body' >
                            <h6 class='text-wrap text-uppercase font-weight-normal h6'>{{item.name}}</h6>
                        </div>
                    </div>`,

        props: {
            item:{
                type:Object,
            },
            iconClass:{
                default:'fa fa-user-alt',
                type:String
            }
        },
    })
</script>
