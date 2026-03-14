<template>
    <section class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <!--EMRFrontOfficeFormServiceGetter source="patient_service" @ServiceFinderExtract="addItem"/-->
                <section class="card">
                    <div class="card-header">
                        <h3 class="card-title">Service Selection</h3>
                    </div>

                    <form @submit.prevent="sendOut">
                        <div class="card-body">
                            <div class="form-group">
                                <label class="required">Service Type</label>
                                <select class="form-control" v-model="itemForm.service_type_id" @change="onServiceTypeChange">
                                    <option value="">-- Select Service Type --</option>
                                    <option v-for="type in service_types" :key="type.id" :value="type.id">{{ type.name }}</option>
                                </select>
                            </div>
                            <div class="form-group" v-if="showSubCategory">
                                <label>Sub Category</label>
                                <model-list-select class="form-control" :list="availableCategories" v-model="itemForm.category_id" option-value="id" option-text="name" placeholder="Select Sub Category" @input="onCategoryChange"/>
                            </div>
                            <div class="form-group" v-if="source === 'price_list'">
                                <label>Service Name</label>
                                <input type="text" class="form-control" v-model="itemForm.item_name" placeholder="Enter service name"/>
                            </div>
                            <div class="form-group" v-if="source === 'patient_service'">
                                <label>Services</label>
                                <multiselect v-model="itemForm.items" :options="filteredItems" :multiple="true" :taggable="true" :close-on-select="false" :clear-on-select="false" :custom-label="itemName" track-by="id" placeholder="Select services" @tag="addTag"/>
                            </div>
                            <div class="form-group" v-else>
                                <label>Service / Item</label>
                                <model-list-select class="form-control" :list="filteredItems" v-model="itemForm.item_id" option-value="id" :custom-text="itemName" placeholder="Select Service"/>
                            </div>
                        </div>
                        <div class="card-footer text-right">
                            <button type="submit" class="btn btn-primary">Submit</button>
                        </div>
                    </form>
                </section>
            </div>
            <div class="col-md-8">
                <div class="card">
                    <form @submit.prevent="addServices()">
                        <div class="card-header"><h3 class="card-title">Services</h3></div>
                        <div class="card-body table-responsive p-0">
                            <table class="table table-bordered table-striped">
                                <thead>
                                    <tr>
                                        <th style="width: 10px">#</th>
                                        <th>Service</th>
                                        <th>Unit Cost</th>
                                        <th width="15%">Quantity</th>
                                        <th>Total</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in billForm.items" :key="item.id">
                                        <td>{{addOne(index)}}</td>
                                        <td>{{ item.name }}</td>
                                        <td>{{ item.price }}<br/><span class="text-small text-success" style="font-size: x-small;">{{ item.payment_type == 1 ? 'Cash Payment' : (item.payment_type == 2 ? 'Managed Care' : 'Co-Pay') }}</span></td>
                                        <td width="15%"><input class="form-control" type="number" v-model="billForm.items[index].quantity"/></td>
                                        <td>{{ billForm.items[index].price * billForm.items[index].quantity}}</td>
                                        <td><button class="btn btn-xs btn-danger" type="button" @click="removeItem(index)"><i class="fa fa-trash"></i></button></td>
                                    </tr>  
                                </tbody>
                            </table>
                        </div>
                        <div class="card-footer clearfix">
                            <div class="text-right">
                                <button class="btn btn-sm bg-dark" type="submit" :disabled="billForm.items.length == 0">Done</button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    </section>
</template>
<script>
import EMRFrontOfficeFormServiceGetter from '@/emr/front_office/forms/ServiceGetter.vue';
import { ModelListSelect } from 'vue-search-select';

export default {
    components:{
        EMRFrontOfficeFormServiceGetter, ModelListSelect
    },
    computed:{
        availableCategories() {
            return this.selectedServiceType?.categories || [];
        },
        current_branch(){
            var branch = this.$store.getters.currentBranch;
            if (branch == null){this.updateBranch(this.staff_branch);}
            return branch;
        },
        filteredItems() {
            if (!this.selectedServiceType) return [];
            let services = this.selectedServiceType.services || [];
            if (this.itemForm.category_id) {
                services = services.filter(
                    s => s.category_id === this.itemForm.category_id
                );
            }
            return services;
        },
        patient(){
            var visit = this.$store.getters.currentPatient;
            return visit;
        },
        selectedServiceType() {
            return this.service_types.find(
                s => s.id === this.itemForm.service_type_id
            ) || null;
        },
        showSubCategory() {
            return this.availableCategories.length > 0;
        },
        visit(){
            var visit = this.$store.getters.currentVisit;
            return visit;
        },
    },
    data() {
        return {
            active_visits: 0,
            billForm: new Form({
                patient_id: '',
                visit_id: '',
                items: [],
            }),
            itemForm: new Form({
                service_type_id: '',
                category_id: '',
                item_id: '',
                item_name: '',
                items: []
            }),
            source: 'patient_service',
            service_types: [],
            stores: [],
        }
    },
    emits: ['refreshPatientServiceForm'],
    methods: {
        addServices(){
            this.loading = true;
            this.billForm.patient_id = this.patient.id;
            this.billForm.visit_id = this.visit.id;
            this.billForm.post('/api/emr/hims/visit_transactions')
            .then(response =>{
                this.$swal.fire('Success', 'Visit Transactions created', 'success');
                this.$emit('refreshPatientServiceForm');
            })
            .catch(()=>{
                this.$swal.fire('Error', 'Unable to create transaction', 'error')
            })
            .finally(()=>{
                this.loading =false;
            });
        },
        addItem(itemForm) {
            if (!itemForm || !Array.isArray(itemForm.items)) return;

            const branchPriceListItems =
                this.current_branch?.price_list?.price_list_items || [];

            const visitPriceListItems =
                this.visit?.price_list?.price_list_items || [];

            itemForm.items.forEach(service => {
                const serviceId = service.id;

                const existingIndex = this.billForm.items.findIndex(i => i.id === serviceId);
                // If item already exists, increment quantity
                if (existingIndex !== -1) {
                    this.billForm.items[existingIndex].quantity++;
                    return;
                }

                let price = 0;
                let payment_type = 1;

                const cashIndex = branchPriceListItems.findIndex(p => p.item_id === serviceId);

                const insuranceIndex = visitPriceListItems.findIndex(p => p.item_id === serviceId);

                // Insurance logic
                if (insuranceIndex !== -1 && visitPriceListItems[insuranceIndex].covered === 'yes') {
                    price = visitPriceListItems[insuranceIndex].price;
                    payment_type = visitPriceListItems[insuranceIndex].price < visitPriceListItems[insuranceIndex].covered ? 2 : 3;
                }
                else if (cashIndex !== -1) {
                    price = branchPriceListItems[cashIndex].price;
                    payment_type = 1;
                }

                this.billForm.items.push({
                    id: serviceId,
                    item_id: service.item.id,
                    name: service.item.name,
                    quantity: 1,
                    price,
                    payment_type
                });
            });
        },

        removeItem(index) {
            this.billForm.items.splice(index, 1);
        },
        loadInitials() {
            this.loading = true;

            axios.get('/api/emr/hims/services/initials')
                .then(({ data }) => {
                    this.service_types = data.service_types || [];
                })
                .catch(() => {
                    this.$toast.fire({
                        icon: 'error',
                        title: 'Failed to load services'
                    });
                })
                .finally(() => {
                    this.loading = false;
                });
        },
        onServiceTypeChange() {
            this.itemForm.category_id = '';
            this.itemForm.item_id = '';
            this.itemForm.items = [];
        },
        onCategoryChange() {
            this.itemForm.item_id = '';
            this.itemForm.items = [];
        },
        addTag(newTag) {
            this.itemForm.items.push({
                name: newTag,
                is_custom: true
            });
        },
        itemName(service) {
            if (!service) return '';
            if (service.item) {
                return `${service.item.name} [${service.item.unique_id}]`;
            }
            return service?.name || 'Unknown Service';
        },
        sendOut() {
            //this.$emit('ServiceFinderExtract', this.itemForm);
            this.addItem(this.itemForm);
            this.itemForm.reset();
        },
    },
    mounted() {
        this.loadInitials();
    },
    props: {}
}
</script>