<template>
    <section class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <EMRFrontOfficeFormServiceGetter source="patient_service" @ServiceFinderExtract="addItem"/>
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
                                    <td>{{ billForm.items[index].price *   billForm.items[index].quantity}}</td>
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
import { ModelListSelect } from 'vue-search-select';

export default {
    computed:{
        current_branch(){
            var branch = this.$store.getters.currentBranch;
            if (branch == null){this.updateBranch(this.staff_branch);}
            return branch;
        },
        patient(){
            var visit = this.$store.getters.currentPatient;
            return visit;
        },
        visit(){
            var visit = this.$store.getters.currentVisit;
            return visit;
        }
    },
    components: {
        ModelListSelect
    },
    data() {
        return {
            active_visits: 0,
            billForm: new Form({
                patient_id: '',
                visit_id: '',
                items: [],
            }),
            stores: [],
        }
    },
    methods: {
        addServices(){
            this.loading = true;
            this.billForm.patient_id = this.patient.id;
            this.billForm.visit_id = this.visit.id;
            this.billForm.post('/api/emr/hims/visit_transactions')
            .then(response =>{
                this.$swal.fire('Success', 'Visit Transactions created', 'success');
            })
            .catch(()=>{
                this.$swal.fire('Error', 'Unable to check in patient', 'error')
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
        }
    },
    mounted() {},
    props: {}
}
</script>