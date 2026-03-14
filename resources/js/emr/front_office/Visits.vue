<template>
<section class="container-fluid">
    <div class="overlay-wrapper">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>     
        <div class="modal fade" id="paymentModal">
            <div class="modal-dialog">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title" v-show="editMode">Edit Payment</h4>
                        <h4 class="modal-title" v-show="!editMode">New Payment</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal()"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <EMRFinanceFormDeposit :editMode="editMode" :visit="visit" :patient="visit.patient"/>
                    </div>
                </div>
            </div>
        </div>
        <!--div class="modal fade" id="queueModal">
            <div class="modal-dialog modal-xl">
                <div class="modal-content">
                    <div class="modal-header">
                        <h4 class="modal-title">Add To Queue</h4>
                        <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModal()"><span aria-hidden="true">&times;</span></button>
                    </div>
                    <div class="modal-body">
                        <VisitFormQueue :visit="visit" :visit_types="visit_types"/>
                    </div>
                </div>
            </div>
        </div-->
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-dark" >
                        <h3 class="card-title">Active Visits</h3>
                        <div class="card-tools">
                            <div class="input-group input-group-sm" style="width: 300px;">
                                <input type="text" name="table_search" class="form-control float-right" placeholder="Search" />
                                <div class="input-group-append">
                                <button type="submit" class="btn btn-default">
                                    <i class="fas fa-search"></i>
                                </button>
                                <router-link to="/hims/visits/create" class="btn btn-sm btn-primary" >Create New</router-link>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body p-0" style="height: 500px;">
                        <EMRFrontOfficeDetailVisitList :visits="visits.data" view="visit" />
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
import EMRFinanceFormDeposit from '@/emr/finance/forms/Deposit.vue';
import EMRFrontOfficeDetailVisitList from '@/emr/front_office/details/VisitList.vue';
export default {
    components:{
        EMRFinanceFormDeposit, EMRFrontOfficeDetailVisitList
    },
    data() {
        return {
            consultations: {},
            editMode: false,
            form: new Form({}),
            investigations: {},
            loading: false,
            patient: {},
            patients: [],
            pharmacies: {},
            query: '',
            services: {},
            status: '',
            visits: {data: [], total: 0},
            visit_types: [],
            visit: {},
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        addToQueue(visit){
            this.$store.dispatch('setVisitCookie', visit);
            Fire.$emit('queueVisitDataFill', visit);
            $('#queueModal').modal('show');
        },
        addVisit(){
            this.editMode = false;
            
            Fire.$emit('visitDataFill', {});
            $('#visitModal').modal('show');
        },
        closeModal(){
            $('#paymentModal').modal('hide');
            $('#queueModal').modal('hide');
            $('#visitModal').modal('hide');
        },
        createVisit() {
            this.VisitForm.put('/api/emr/visits')
            .then(response => {
                //Fire.$emit('refreshResponse', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'A Visit has been created',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(() => {
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
            })
            .finally(()=>{
                this.loading = false;
            });
        },
        endVisit(visit){
            if (visit.transactions_sum_item_total > 0){
                Swal.fire({
                    icon: 'error',
                    title: 'Can not end visit',
                    text: 'Patient owes '+ visit.transactions_sum_item_total,
                    footer: 'Please try again later!'
                });
            }
            else{
                Swal.fire({
                    title: 'Are you sure?',
                    text: "You won't be able to revert this!",
                    icon: 'warning',
                    showCancelButton: true,
                    confirmButtonColor: '#3085d6',
                    cancelButtonColor: '#d33',
                    confirmButtonText: 'Yes, end visit!'
                    })
                .then((result) => {
                    //Send End Visit request
                    if(result.value){
                        this.$Progress.start();
                        this.form.put('/api/emr/hims/visits/end/'+visit.id)
                        .then(response=>{
                            Swal.fire('Deleted!', 'Visit has been ended.', 'success');
                            this.refresh(response);
                            this.$Progress.finish(); 
                        })
                        .catch(()=>{
                            Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                        });
                    }
                });
            }
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/emr/hims/visits').then(response =>{
                this.refresh(response);
            })
            .catch(()=>{
                this.$toast.fire({
                    icon: 'error',
                    title: 'Visits were not loaded successfully',
                })
            })
            .finally(()=>{
                this.loading = false;
            });
        },
        refresh(response){
            this.visits = response.data.visits;
            this.visit_types = response.data.visit_types;
        },
        receiveDeposit(visit){
            this.editMode = false;
            this.visit = visit;
            let deposit = {
                patient_id: visit.patient_id,
            };
            Fire.$emit('DepositDataFill', deposit);
            $('#paymentModal').modal('show');
        },
        startVisit(id){
            axios.get('/api/emr/hims/visits/'+id+'/start').then(response =>{
                this.refresh(response);
                this.$Progress.finish();
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Visits were not loaded successfully',
                })
            });
        }
    },
}
</script>