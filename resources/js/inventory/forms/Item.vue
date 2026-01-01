<template>
<section>
    <form>
        <alert-error :form="ItemData"></alert-error> 
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" id="name" name="name" v-model="ItemData.name" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Type</label>
                    <select class="form-control" id="type_id" name="type_id" v-model="ItemData.type_id" required @change="changeType">
                        <option value=''>--Select Item Type--</option>
                        <option value="1">Drug</option>
                        <option value="2">Services</option>
                        <option value="3">Other Consumables</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Category</label>
                    <select class="form-control" id="category_id" name="category_id" v-model="ItemData.category_id">
                        <option value=''>--Select Category--</option>
                        <option v-for="category in categories" :value="category.id">{{ category.name }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" id="status" name="status" v-model="ItemData.status">
                        <option value=''>--Select Status--</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row" v-if="ItemData.type_id == 1">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Drug</label>
                    <Multiselect v-model="ItemData.drug_id" :options="drugs" track-by="name" label="name" :searchable="true" :close-on-select="false" :show-labels="false" placeholder="Pick a drug"></multiselect>
                </div>
            </div>
        </div>
        <div v-if="isService">
            <div class="row mb-3">
                <div class="col-md-12">
                    <label>Service Type</label>
                    <select class="form-control" v-model="ItemData.service.service_type_id">
                        <option value=''>--Select Service Type--</option>
                        <option v-for="item_type in types" :value="item_type.id">{{ item_type.name }}</option>
                    </select>
                </div>
            </div>
            <!-- Dynamic service config -->
            <component v-if="serviceComponent" :is="serviceComponent" v-model="ItemData.service.reference"/>
        </div>
        <div class="row" v-if="!isService">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Current Cost Price</label>
                    <input type="text" class="form-control" id="last_landing_cost" name="last_landing_cost" v-model="ItemData.last_landing_cost" />
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Barcode</label>
                    <input type="text" class="form-control" id="barcode" name="barcode" v-model="ItemData.barcode" />
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Brand</label>
                    <select class="form-control" id="brand_id" name="brand_id" v-model="ItemData.brand_id">
                        <option value="">Select Brand Name</option>
                        <option v-for="brand in brands" :value="brand.id">{{brand.name}}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Image</label>
                    <input type="file" class="form-control" id="image" name="image" @change="uploadImage" />
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="form-group">
                    <label class="form-label">Description</label>
                    <QuillEditor v-model:content="serviceData.item.description" content-type="html" theme="snow"/>
                </div>
            </div>
        </div>
        <input type="hidden" name="id" id="id" v-model="ItemData.id" />
        <button @click.prevent="editMode ? updateItem() : createItem()" type="submit" name="submit" class="submit btn btn-success">Submit</button>
    </form>
</section>
</template>
<script>
import ConsultationServiceForm from '@/operations/components/ConsultationService.vue'
import LaboratoryServiceForm from '@/operations/components/LaboratoryService.vue'
import RadiologyServiceForm from '@/operations/components/RadiologyService.vue'
import PhysiotherapyServiceForm from '@/operations/components/PhysiotherapyService.vue'
import DialysisServiceForm from '@/operations/components/DialysisService.vue'

export default {
    components: {
        ConsultationServiceForm,
        LaboratoryServiceForm,
        RadiologyServiceForm,
        PhysiotherapyServiceForm,
        DialysisServiceForm,
    },
    computed: {
        isService() {
            return Number(this.ItemData.type_id) === 2 // SERVICE type_id
        },

        serviceComponent() {
            const map = {
                1: 'ConsultationServiceForm',
                2: 'LaboratoryServiceForm',
                3: 'RadiologyServiceForm',
                4: 'PhysiotherapyServiceForm',
                5: 'DialysisServiceForm',
            }
            return map[this.ItemData.service.service_type_id] || null
        }
    },
    data(){
        return  {
            brands: [],
            categories: [],
            drugs: [],
            loading: true,
            ItemData: new Form({
                barcode: '',
                brand_id: '',
                classification_id: '',
                category_id: '',
                description: '',
                id: '', 
                image: '',
                items:[],
                last_landing_cost: '',
                name: '', 
                specific_id: '',
                category_id: '', 
                quantity: 0,
                service: {
                    service_type_id: '',
                    reference: {}
                },
                service_id: '', 
                status: '',
                type_id: '',     
                unique_id: '',
            }),
            types: [],
        }
    },
    emits: ['itemReload'],
    mounted() {
        this.getInitials();
    },
    methods:{
        addPackageItem(item){
            this.ItemData.items.push(item)
        },
        changeType(){
            alert(this.ItemData.type_id)
        },
        createItem(){
            this.loading = true;
            this.ItemData.post('/api/inventory/items')
            .then(response =>{
                this.$emit('itemReload', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Item has been created',
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
            axios.get('/api/inventory/items/initials')
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
            this.brands = response.data.brands;
            this.categories =  response.data.categories;
            this.classifications = response.data.classifications;
            this.drugs = response.data.drugs;
            this.types = response.data.types;
        },
        updateItem(){
            this.loading = true;
            this.ItemData.put('/api/inventory/items/'+this.ItemData.id)
            .then(response =>{
                this.loading = false;
                this.$emit('itemReload', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Item has been updated',
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
                this.loading = false;
            });              
        },
    },
    props:{
        editMode: Boolean,
        item: Object,
    },
    watch:{
        item(){
            if (this.item != null ){
                this.ItemData.fill(this.item);
                if (this.ItemData.service == null) {
                    this.ItemData.service = {
                        service_type_id: '',
                        reference: {}
                    }
                }
            }
            else{
                this.ItemData.reset(); 
                this.ItemData.service = {
                    service_type_id: '',
                    reference: {}
                };                
            }
        },
        'ItemData.type_id'(val) {
            if (!this.isService) {
                this.ItemData.service = { service_type_id: '', reference: {} }
            }
        },

        'ItemData.service.service_type_id'() {
            this.ItemData.service.reference = {}
        }
    }
}
</script>