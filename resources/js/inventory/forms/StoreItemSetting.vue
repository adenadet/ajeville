<template>
<section class="overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form>
        <alert-error :form="storeItemSettingData"></alert-error> 
        <div class="row" v-if="editMode">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Store Name</label>
                    <div class="form-control" v-html="store_item_setting.store.name"></div>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Item Name</label>
                    <div class="form-control" v-html="store_item_setting.item.name"></div>
                </div>
            </div>
        </div>
        <div class="row" v-else>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Store Name</label>
                    <select class="form-control" id="store_id" name="name" v-model="storeItemSettingData.store_id">
                        <option value="">--Select Store--</option>
                        <option v-for="store in stores" :value="store.id">{{ store.name }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Item Name</label>
                    <select class="form-control" id="name" name="name" v-model="storeItemSettingData.item_id">
                        <option value="">--Select Item--</option>
                        <option v-for="item in items" :value="item.id">{{ item.name }}</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Reorder Level</label>
                    <input type="number" class="form-control" id="reorder_level" name="reorder_level" v-model="storeItemSettingData.reorder_level" :class="{'is-invalid' : storeItemSettingData.errors.has('reorder_level') }"/>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Maximum Level</label>
                    <input type="number" class="form-control" id="maximum_level" name="maximum_level" v-model="storeItemSettingData.maximum_level" :class="{'is-invalid' : storeItemSettingData.errors.has('maximum_level') }"/>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Expiry Notification (in days)</label>
                    <input type="number" class="form-control" id="expiry_notification" name="expiry_notification" v-model="storeItemSettingData.expiry_notification" :class="{'is-invalid' : storeItemSettingData.errors.has('expiry_notification') }"/>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Description</label>
                    <QuillEditor content-type="html" theme="snow" class="form-control" id="description" name="description" v-model:content="storeItemSettingData.description" :class="{'is-invalid' : storeItemSettingData.errors.has('description') }"></QuillEditor>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12"><button type="button" class="btn btn-sm btn-primary" @click="editMode ? updateStoreItemSetting() : createStoreItemSetting()">{{ editMode ? 'Update Settings' : 'Save Settings' }}</button></div>
        </div>
    </form>
</section>
</template>
<script>
export default {
    data(){
        return  {
            items: [],
            loading: false,
            storeItemSettingData: new Form({
                description: '',
                expiry_notification: '',
                maximum_level: '',
                id: '',
                item_id: '',
                reorder_level: '',
                status: '', 
                store_id: '', 
            }),
            stores: [],
        }
    },
    emits: ['storeReload'],
    mounted() {
        this.getInitials();
    },
    methods:{
        createStoreItemSetting(){
            this.loading = true;
            this.storeItemSettingData.post('/api/inventory/store_items')
            .then(response =>{
                this.$emit('storeReload', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Store Item Setting has been created',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                this.$swal.fire({
                    icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'
                });
            });  
            this.loading = false;
        },
        getInitials(){
            this.loading = true;
            axios.get('/api/inventory/store_items/initials')
            .then(response =>{
                this.refreshPage(response);
                this.loading = false;
                this.$toast.fire({
                    icon: 'success',
                    title: 'Users loaded successfully',
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Users not loaded successfully',
                })
            });
        },
        refreshPage(response){
            this.branches = response.data.branches;
            this.departments = response.data.departments;
        },
        updateStoreItemSetting(){
            this.loading = true;
            this.storeItemSettingData.put('/api/inventory/store_items/'+this.storeItemSettingData.id)
            .then(response =>{
                this.$emit('storeReload', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Store Item Setting has been updated',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
            });
            this.loading = false;              
        },
    },
    props:{
        editMode: Boolean,
        store_item_setting: Object,
    },
    watch:{
        store_item_setting(){
            this.storeItemSettingData.fill(this.store_item_setting);
        }
    }
}
</script>