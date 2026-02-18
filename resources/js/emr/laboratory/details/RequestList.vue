<template>
    <section class="overlay-wrapper p-0">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <div class="modal fade" id="requestFormModal">
            <div class="modal-dialog modal-lg">
                <div class="modal-content">
                    <div class="modal-header bg-dark">
                        <h4 class="modal-title">Request Details</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="text-white">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <EMRLaboratoryFormRequest :request.sync="request" :editMode="editMode" @refreshLaboratoryRequestForm="refreshPage"/>
                    </div>
                </div>
            </div>
        </div>
        <table class="table table-head-fixed text-nowrap table-stripped table-hover" :id="actionable == 'yes' ? 'example1' : ''">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Patient</th>
                    <th>Category</th>
                    <th>Item</th>
                    <th>Status</th>
                    <th>
                        <!--button class="btn btn-xs btn-primary" @click="addRequest"><i class="fa fa-plus"></i></button-->
                    </th>
                </tr>
            </thead>
            <tbody v-if="requests.length > 0">
                <tr v-for="(request, index) in requests" :key="index">
                    <td>{{ addOne(index) }}</td>
                    <td>{{ request.date }}</td>
                    <td v-if="request.patient != null">{{ patientName(request.patient) }}</td>
                    <td v-else>{{request.patient_id}}</td>
                    <td>{{ (request.item != null && request.item.category != null) ? request.item.category.name : 'No Category Yet' }}</td>
                    <td>{{ request.item != null ? request.item.name : '' }}</td>
                    <td>{{ request.status == 0 ? 'Unpaid' : 'Cleared' }}</td>
                    <td v-if="source == 'laboratory'">
                        <span class="nav-link" data-toggle="dropdown" href="#">
                            <i class="fa fa-ellipsis-v"></i>
                        </span>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            <router-link :to="'/laboratory/requests/'+request.id" class="btn btn-block dropdown-item"><i class="fas fa-eye mr-2 text-primary"></i> View Request</router-link>
                            <button v-if="request.status == 0 && (request.transaction == null || request.transaction.paid_by == 1)" class="btn btn-block dropdown-item" @click="pay_from_wallet(request.transaction.id)"><i class="fas fa-cash-register mr-2"></i> Pay from Wallet</button>
                        </div>
                    </td>
                </tr>
            </tbody>
            <tbody v-else>
                <tr>
                    <td colspan="8">No Request meets your requests</td>
                </tr>
            </tbody>
        </table>
    </section>
</template>
<script>
import EMRLaboratoryFormRequest from '@/emr/laboratory/forms/Request.vue'
export default {
    components:{EMRLaboratoryFormRequest},
    data() {
        return {
            editMode: true,
            loading: false,
            request: {},
        }
    },
    emits:['refreshLaboratoryRequestList'],
    mounted() {},
    methods: {
        addRequest(){
            this.loading = true;
            this.request = {};
            $('#requestFormModal').modal('show');
            this.loading = false;
        },
        closeModals(){
            $('#requestFormModal').modal('hide');
            this.$emit('refreshLaboratoryRequestList');
        },
        pay_from_wallet(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "Debit patient's wallet for transaction!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
                })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.form.delete('/api/lms/courses/'+id)
                    .then(response=>{
                    Swal.fire('Deleted!', 'Category has been deleted.', 'success');
                    Fire.$emit('CatRefresh', response);   
                    })
                    .catch(()=>{
                    Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        refreshPage(){
            this.closeModals();
        },
    },
    props: {
        actionable: String,
        requests: Array,
        source: String,
    }
}
</script>