<template>
    <section class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="invoice p-3 mb-3">
                    <div class="row">
                        <div class="col-12">
                            <h4>
                                <i class="fas fa-globe"></i> Mattville Hospital 
                                <small class="float-right">Date: {{ prescription.updated_at | excelDate }}</small>
                            </h4>
                        </div>
                    </div>
                    <div class="row invoice-info">
                        <div class="col-sm-4 invoice-col">
                            Patient:
                            <address>
                                <strong>Admin, Inc.</strong><br>
                                795 Folsom Ave, Suite 600<br>
                                San Francisco, CA 94107<br>
                                Phone: (804) 123-5432<br>
                                Email: info@almasaeedstudio.com
                            </address>
                        </div>
                        <div class="col-sm-4 invoice-col">
                            Prescribed By:
                            <address>
                                <strong>John Doe</strong><br>
                                795 Folsom Ave, Suite 600<br>
                                San Francisco, CA 94107<br>
                                Phone: (555) 539-1037<br>
                                Email: john.doe@example.com
                            </address>
                        </div>
                        <div class="col-sm-4 invoice-col">
                            <b>Prescription #{{ prescription.id | precedingZero(8) }}</b><br>
                            <br>
                            <b>Order ID:</b> 4F3S8J<br>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-12 table-responsive">
                            <table class="table">
                                <thead class="bg-dark">
                                    <tr>
                                        <th width="10px">#</th>
                                        <th width="15%">Drug</th>
                                        <th width="15%">Specific Brand</th>
                                        <th width="15%">Dose</th>
                                        <th width="10%">Route</th>
                                        <th width="10%">Frequency</th>
                                        <th width="10%">Form</th>
                                        <th width="10%">Quantity</th>
                                        <th width="10%">Detail</th>
                                        <th width="30px"></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(drug, index) in drugs" class="p-0">
                                        <td colspan="10" class="p-0">
                                            <table width="100%" class="table p-0 m-0">
                                                <tr>
                                                    <td width="10px">{{ index | addOne }}</td>
                                                    <td width="15%">{{ drug.drug_name }}</td>
                                                    <td width="15%" v-if="drug.specific_drug != null">{{ drug.specific_drug.name}}</td>
                                                    <td width="15%" v-else="drug.specific_drug != null"><select class="form-control" :id="'specific_drug_'+index" :name="'specific_drug_'+index" v-model="prescriptionConfirmationData.drugs[index].specific_drug_id" @change="getPrice(index)">
                                                            <option value="">--Select Specific Drug</option>
                                                            <option v-for="specific_drug in drug.drug.specific_drugs" :value="specific_drug.id">{{ specific_drug.name }}</option>
                                                        </select>
                                                    </td>
                                                    <td width="15%">{{ drug.dose }}</td>
                                                    <td width="10%">{{ drug.route != null ? drug.route.name : 'None Chosen'}}</td>
                                                    <td width="10%">{{ drug.frequency }}</td>
                                                    <td width="10%">{{ drug.form }}</td>
                                                    <td width="10%">{{ drug.quantity * drug.duration }}</td>
                                                    <td width="10%">{{ drug.detail }}</td>
                                                    <td width="30px">
                                                        <span class="nav-link p-0" data-toggle="dropdown" href="#"><i class="fa fa-ellipsis-v"></i></span>
                                                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                                            <button class="btn btn-block dropdown-item" @click="process(index)"><i class="fa fa-file mr-1"></i>Process</button>
                                                        </div>
                                                    </td>
                                                </tr>
                                                <tr>
                                                    <td colspan="2">
                                                        <label>Quantity</label>
                                                        <input type="number" class="form-control" :id="'quantity_'+index" v-model="prescriptionConfirmationData.drugs[index].sold_quantity" min="1" required :name="'quantity_'+index"/>
                                                    </td>
                                                    <td>
                                                        <label>Unit Cost</label>
                                                        <div class="form-control" v-html="prescriptionConfirmationData.drugs[index].unit_cost"></div>
                                                    </td>
                                                    <td>
                                                        <label>Total Cost</label>
                                                        <div class="form-control">{{ prescriptionConfirmationData.drugs[index].unit_cost *  prescriptionConfirmationData.drugs[index].sold_quantity}}</div>
                                                    </td>
                                                    <td>
                                                        <label>Patient Bill</label>
                                                        <div class="form-control">{{ (prescriptionConfirmationData.drugs[index].unit_cost - prescriptionConfirmationData.drugs[index].coverage) *  prescriptionConfirmationData.drugs[index].sold_quantity}}</div>
                                                    </td>
                                                    <td>
                                                        <label>Mode</label>
                                                        <select class="form-control" v-model="prescriptionConfirmationData.drugs[index].payment_mode" @change="getPrice(index, prescriptionConfirmationData.drugs[index].payment_mode)">
                                                            <option v-for="(price_list, index) in price_lists" :value="index" >{{price_list.type_id == 0 ? 'Cash' : price_list.plan.name }}</option>
                                                        </select>
                                                    </td>
                                                </tr>
                                            </table>
                                        </td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                    <div class="row no-print">
                        <div class="col-12">
                            <button type="button" class="btn bg-dark float-right" @click="generateInvoice" ><i class="far fa-credit-card"></i> Generate Invoice</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<script>
export default {
    data() {
        return {
            drugs: [],
            editMode: true,
            issuing_stores: [],
            patient: {},
            prescriptionConfirmationData: new Form({
                patient_id: '',
                visit_id: '',
                drugs: [],
            }),
            prescription: {},
            prescriptions: [],
            visit: {},
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        getInitials(page = 1) {
            axios.get('/api/emr/pharmacy/prescriptions/' + this.$route.params.id)
            .then(response => {
                this.refreshDashboard(response)
            })
            .catch(() => {
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Your appointments did not loaded successfully',
                })
            });
        },
        generateInvoice(){
            this.prescriptionConfirmationData.post('/api/emr/pharmacy/prescriptions/generate_invoice/'+ this.$route.params.id)
            .then(()=>{})
        },
        getPrice(i, k = 0){
            let array = this.price_lists[k]['price_list_items'];
            let chosen_item = this.prescriptionConfirmationData.drugs[i].specific_drug_id;
            var available_price = array.find(item => item.item_id === chosen_item) 
            this.prescriptionConfirmationData.drugs[i].unit_cost = available_price.price;
            this.prescriptionConfirmationData.drugs[i].coverage = ((available_price.covered == 'yes') && (available_price.coverage != null)) ? available_price.coverage : 0 ;
            this.prescriptionConfirmationData.drugs[i].payment_mode = k;
        },
        refreshDashboard(response) {
            this.drugs = response.data.drugs;
            this.prescription = response.data.prescription;
            this.patient = response.data.patient;
            this.visit = response.data.visit;
            this.issuing_stores = response.data.issuing_stores;
            this.prescriptionConfirmationData.drugs = response.data.drugs;
            this.price_lists = response.data.price_lists;
        },
    },
    props: {}
}
</script>