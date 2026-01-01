<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form @submit.prevent="editMode ? updateAsset() : createAsset()" class="form-horizontal row">
        <div class="col-sm-12">
            <div class="form-group">
                <label>Name</label>
                <input required class="form-control" id="name" name="name" placeholder="Asset Name *" v-model="assetData.name" :class="{'is-invalid' : assetData.errors.has('name') }" >
                <has-error :form="assetData" field="name"></has-error> 
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                <label>Type</label>
                <select required class="form-control" id="type_id" name="type_id" placeholder="Branch Name *" v-model="assetData.type_id" :class="{'is-invalid' : assetData.errors.has('type_id') }" >
                    <option value="">Select Asset Type</option>
                    <option v-for="type in asset_types" :key="type.id" :value="type.id">{{ type.name }}</option>
                </select>
                    <has-error :form="assetData" field="date"></has-error> 
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label>Serial Number</label>
                <input class="form-control" id="serial_number" name="serial_number" placeholder="Asset Name *" v-model="assetData.serial_number" :class="{'is-invalid' : assetData.errors.has('serial_number') }" >
                <has-error :form="assetData" field="serial_number"></has-error> 
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label>Purchase Value</label>
                <input type="number" step="0.01" required class="form-control" id="purchase_value" name="purchase_value" placeholder="Account Name *" v-model="assetData.purchase_value" :class="{'is-invalid' : assetData.errors.has('purchase_value') }" >
                <has-error :form="assetData" field="purchase_value"></has-error> 
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label>Acquisition Date</label>
                <input type="date" required  placeholder="Date of Acquisition *" class="form-control" id="acquisition_date" name="acquisition_date" v-model="assetData.acquisition_date"   :class="{'is-invalid' : assetData.errors.has('acquisition_date') }"/>
                <has-error :form="assetData" field="acquisition_date"></has-error> 
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label>Depreciation Rate <small>in %</small></label>
                <input type="number" required  placeholder="Rate of Depreciation*" class="form-control" id="depreciation_rate" name="depreciation_rate" v-model="assetData.depreciation_rate"   :class="{'is-invalid' : assetData.errors.has('depreciation_rate') }"/>
                <has-error :form="assetData" field="depreciation_rate"></has-error> 
            </div>
        </div>
        <div class="col-sm-4">
            <div class="form-group">
                <label>Status</label>
                <select required class="form-control" id="status" name="status" v-model="assetData.status"   :class="{'is-invalid' : assetData.errors.has('status') }">
                    <option value="">--Select Status--</option>
                    <option value=1>Active</option>
                    <option value=0>Inactive</option>
                </select>
                <has-error :form="assetData" field="status"></has-error> 
            </div>
        </div>
        <div class="col-md-12">
            <div class="form-group">
                <label>Description</label>
                <QuillEditor class="form-control" id="description" name="description" theme="snow" content-type="html" v-model:content="assetData.description" />
            </div>        
        </div>
        <div class="col-md-12">
            <button @click.prevent="editMode ? updateAsset() : createAsset()" type="submit" name="submit" class="submit btn btn-primary float-right">Submit</button>
        </div>
    </form>
</section>
</template>
<script>
export default {
    data(){
        return  {
            asset_types: [],
            assetData: new Form({
                id: '',
                uuid: '',
                name: '',
                purchase_value: '',
                acquisition_date: '',
                depreciation_rate: '',
                description: '',
                type_id: '',
                serial_number: '',
                status: '',
            }),
            loading: true,
        }
    },
    emits: ['refreshAssetForm'],
    mounted() {
        this.getInitials();
    },
    methods:{
        createAsset(){
            this.loading = true;
            this.assetData.post('/api/equipments/assets')
            .then( () =>{
                this.$emit('refreshAssetForm');
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Branch Account detail has been captured',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });
            this.loading = false;    
        },
        getInitials(){
            this.loading = true;
            axios.get('/api/equipments/assets/initials')
            .then(response =>{
                this.asset_types = response.data.asset_types;
            })
            .catch(()=>{
                this.$toast.fire({icon: 'error', title: 'Branch Account Form not loaded successfully',})
            });
            this.loading = false;
        },
        updateAsset(){
            this.loading = true;
            this.assetData.put('/api/equipments/assets/'+this.assetData.id)
            .then(() => {
                this.$emit('refreshAssetForm');
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Asset detail has been updated',
                    showConfirmButton: false,
                    timer: 1500
                    });
                })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });
            this.loading = false;            
        },
    },
    props:{
        asset: Object,
        editMode: Boolean,
        source: String,
    },
    watch:{
        asset(){
            this.loading = true;
            this.assetData.reset();
            this.assetData.fill(this.asset);
            this.loading = false;
        }
    }
}
</script>