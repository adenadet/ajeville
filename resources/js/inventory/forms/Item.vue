<!--template>
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
                    <label>Current Cost Price</label>
                    <input type="text" class="form-control" id="last_landing_cost" name="last_landing_cost" v-model="ItemData.last_landing_cost" />
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
            {{ item.service }}
            {{ item.service.referenceable }}
            <div class="row mb-3">
                <div class="col-md-12">
                    <label>Service Type {{ item.service.service_type_id }}</label>
                    <select class="form-control" v-model="ItemData.service.service_type_id">
                        <option value=''>--Select Service Type--</option>
                        <option v-for="item_type in types" :value="item_type.id">{{ item_type.name }}</option>
                    </select>
                </div>
            </div>
            <component v-if="serviceComponent" :is="serviceComponent" v-model="ItemData.service.referenceable"/>
        </div>
        <div class="row" v-if="!isService">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Category</label>
                    <select class="form-control" id="category_id" name="category_id" v-model="ItemData.category_id">
                        <option value=''>--Select Category--</option>
                        <option v-for="category in categories" :value="category.id">{{ category.name }}</option>
                    </select>
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
                    <QuillEditor v-model:content="ItemData.description" content-type="html" theme="snow"/>
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
                4: 'ConsultationServiceForm',
                6: 'LaboratoryServiceForm',
                7: 'RadiologyServiceForm',
                3: 'AdmissionServiceForm',
                8: 'PhysiotherapyServiceForm',
                9: 'ProcedureServiceForm',
                14: 'DialysisServiceForm',
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
                    referenceable: {}
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
        resetItem() {
            this.ItemData.reset();
            this.ItemData.service = {service_type_id: '', reference: {}};
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
        item: {
            immediate: true,
            deep: true,
            handler(val) {
                if (!val) {
                    this.resetItem(); return ;
                }
                else{
                    this.ItemData.reset();
                    this.ItemData.fill(val);
                    console.log(this.ItemData.service);
                    if (!this.item.service) {this.ItemData.service = {service_type_id: '', referenceable: {}}}
                    else{
                        this.ItemData.service = this.item.service;
                    }
                }
            }
        },
        'ItemData.type_id'(val) {
            if (!this.isService) {
                this.ItemData.service = { service_type_id: '', referenceable: {} }
            }
            else{
                this.ItemData.service = this.item.service;
            }
        },
    }
}
</script-->
<template>
<section>
    <form>
        <alert-error :form="ItemData" />
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" v-model="ItemData.name"/>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Type</label>
                    <select class="form-control" v-model="ItemData.type_id">
                        <option value="">--Select Item Type--</option>
                        <option value="1">Drug</option>
                        <option value="2">Services</option>
                        <option value="3">Other Consumables</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Current Cost Price</label>
                    <input type="text" class="form-control" v-model="ItemData.last_landing_cost"/>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" v-model="ItemData.status">
                        <option value="">--Select Status--</option>
                        <option value="active">Active</option>
                        <option value="inactive">Inactive</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row" v-if="Number(ItemData.type_id) === 1">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Drug</label>
                    <Multiselect v-model="ItemData.drug_id" :options="drugs" label="name" track-by="name" :searchable="true" :close-on-select="false" :show-labels="false" placeholder="Pick a drug"/>
                </div>
            </div>
        </div>
        <div v-if="isService">
            <div class="row mb-3">
                <div class="col-md-12">
                    <label>Service Type {{ ItemData.service.service_type_id }}</label>
                    <select class="form-control" v-model="ItemData.service.service_type_id">
                        <option value="">--Select Service Type--</option>
                        <option v-for="type in types" :key="type.id" :value="type.id">{{ type.name }}</option>
                    </select>
                </div>
            </div>
            <component v-if="serviceComponent"  :is="serviceComponent" :key="ItemData.service.service_type_id" v-model="ItemData.service.referenceable"/>
        </div>
        <div class="row" v-if="!isService">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Category</label>
                    <select class="form-control" v-model="ItemData.category_id">
                        <option value="">--Select Category--</option>
                        <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Barcode</label>
                    <input type="text" class="form-control" v-model="ItemData.barcode"/>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Brand</label>
                    <select class="form-control" v-model="ItemData.brand_id">
                        <option value="">Select Brand Name</option>
                        <option v-for="brand in brands" :key="brand.id" :value="brand.id">{{ brand.name }}</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Image</label>
                    <input type="file" class="form-control" @change="uploadImage"/>
                </div>
            </div>
        </div>
        <div class="row mt-3">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Description</label>
                    <QuillEditor v-model:content="ItemData.description" content-type="html" theme="snow"/>
                </div>
            </div>
        </div>
        <input type="hidden" v-model="ItemData.id" />
        <button class="btn btn-success" @click.prevent="submit">Submit</button>
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

    props: {
        editMode: Boolean,
        item: Object,
    },

    emits: ['itemReload'],

    data() {
        return {
            brands: [],
            categories: [],
            drugs: [],
            ItemData: new Form({
                id: '',
                name: '',
                type_id: '',
                status: '',
                barcode: '',
                brand_id: '',
                category_id: '',
                description: '',
                last_landing_cost: '',
                drug_id: '',
                service: {
                    service_type_id: '',
                    referenceable: {}
                }
            }),
            loading: false,
            types: [],
        }
    },

    computed: {
        isService() {
            return Number(this.ItemData.type_id) === 2
        },

        serviceComponent() {
            const map = {
                4: 'ConsultationServiceForm',
                6: 'LaboratoryServiceForm',
                7: 'RadiologyServiceForm',
                8: 'PhysiotherapyServiceForm',
                14: 'DialysisServiceForm',
            }
            return map[this.ItemData.service.service_type_id] || null
        }
    },

    mounted() {
        this.getInitials()
    },

    methods: {
        normalizeService(service) {
            if (!service) {
                return { service_type_id: '', referenceable: {} }
            }

            return {
                service_type_id: Number(service.service_type_id),
                referenceable: service.referenceable ? { ...service.referenceable } : {}
            }
        },

        submit() {
            this.editMode ? this.updateItem() : this.createItem()
        },

        createItem() {
            this.loading = true;
            this.ItemData.post('/api/inventory/items')
            .then(res => {
                this.$emit('itemReload', res)
            })
            .catch(()=>{

            })
            .finally(()=>{
                this.loading = false;
            });
        },

        updateItem() {
            this.ItemData.put(`/api/inventory/items/${this.ItemData.id}`)
                .then(res => {
                    this.$emit('itemReload', res)
                })
        },

        getInitials() {
            axios.get('/api/inventory/items/initials')
            .then(res => {
                this.brands = res.data.brands
                this.categories = res.data.categories
                this.drugs = res.data.drugs
                this.types = res.data.types
            })
        }
    },

    watch: {
        item: {
            immediate: true,
            deep: true,
            handler(val) {
                this.ItemData.reset()

                if (!val) {
                    this.ItemData.service = {
                        service_type_id: '',
                        referenceable: {}
                    }
                    return
                }

                const cloned = JSON.parse(JSON.stringify(val))
                this.ItemData.fill(cloned)
                this.ItemData.service = this.normalizeService(cloned.service)
            }
        },

        'ItemData.type_id'(val) {
            if (Number(val) !== 2) {
                this.ItemData.service.service_type_id = ''
                this.ItemData.service.referenceable = {}
            }
        }
    }
}
</script>
