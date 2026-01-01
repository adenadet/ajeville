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
                        <FinanceFormDeposit :editMode="editMode" :visit="visit" :patient="visit.patient"/>
                    </div>
                </div>
            </div>
        </div>
        <div class="modal fade" id="queueModal">
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
        </div>
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
                    <div class="card-body p-0">
                        <div class="tab-content" id="tabContent">
                            <table class="table table-striped table-bordered">
                                
                                <thead class="th-dark">
                                    <tr>
                                        <th>Date</th>
                                        <th>Visit ID</th>
                                        <th>Patient</th>
                                        <th>Type</th>
                                        <th>Status</th>
                                        <th>Booked</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody v-if="visits != null && visits.data != null">
                                    <tr v-for="visit in visits.data" :key="visit.id">
                                        <td>{{ visit.start_date }}</td>
                                        <td><router-link :to="'/hims/visits/'+visit.unique_id">{{ visit.unique_id }}</router-link></td>
                                        <td>{{ visit.patient | patientName }}</td>
                                        <td>{{ visit.visit_type.name }}</td>
                                        <td>{{ visit.status == 0 ? 'Planned' : (visit.status == 1 ? 'Queued' : (visit.status == 2 ? 'Ongoing' : 'Completed'))}}</td>
                                        <td>{{ visit.created_at | excelDate }}</td>
                                        <td>
                                            <span class="nav-link" data-toggle="dropdown" href="#">
                                                <i class="fa fa-ellipsis-v"></i>
                                            </span>
                                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                                <button v-if="visit.status == 0" class="btn btn-block dropdown-item" @click="startVisit(visit.id)"><i class="fas fa-cash-register mr-2"></i> Start Visit</button>
                                                <button class="btn btn-block dropdown-item" @click="addToQueue(visit)"><i class="fas fa-list mr-2"></i> Add To Queue</button>
                                                <router-link :to="'/hims/visits/'+visit.unique_id" class="btn btn-block dropdown-item"><i class="fas fa-eye mr-2"></i> View Visit</router-link>
                                                <router-link :to="'/hims/visits/new_bill/'+visit.unique_id" class="btn btn-block dropdown-item"><i class="fas fa-money-check mr-2"></i> Add To Bill</router-link>
                                                <button class="btn btn-block dropdown-item" @click="receiveDeposit(visit)"><i class="fas fa-cash-register mr-2"></i> Receive Deposit</button>
                                                <router-link :to="'/hims/visits/bills/'+visit.unique_id" class="btn btn-block dropdown-item"><i class="fas fa-print mr-2" ></i> Print Bill</router-link>
                                                <button v-if="visit.status == 1" class="btn btn-block dropdown-item" type="button" @click="endVisit(visit)"><i class="fas fa-times mr-2 text-danger"></i>End Visit</button>
                                            </div>
                                        </td>
                                    </tr>
                                </tbody>
                                <tbody v-else>
                                    <tr><td colspan="7">No Visit has been created yet. <router-link to="/hims/visits/create" class="btn btn-primary btn-xs">Create Visit</router-link></td></tr>
                                </tbody>
                            </table>
                        </div>
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
            consultations: {},
            editMode: false,
            form: new Form({}),
            investigations: {},
            loading: false,
            patient: {},
            patients: [],
            pharmacies: {},
            services: {},
            visits: {},
            visit_types: [],
            visit: {},
        }
    },
    mounted() {
        this.getAllInitials();
        Fire.$on('AssessmentTypeDataFill', request => {
            if (request != null) {
                this.VisitForm.name = request.name;
                this.VisitForm.description = request.description;    
                this.VisitForm.id = request.id;
                this.VisitForm.assessments = [];
                for (let i = 0; i < request.assessments.length; i++) {
                    this.VisitForm.assessments.push(request.assessments[i].id);
                }  
            }
            else { this.VisitForm.reset(); }
        });
        Fire.$on('Reload', () => {
            this.getAllInitials();
        });
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
            this.$Progress.start();
            this.VisitForm.put('/api/emr/visits')
            .then(response => {
                this.$Progress.finish();
                Fire.$emit('refreshResponse', response);
                Swal.fire({
                    icon: 'success',
                    title: 'A Visit has been created',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
                this.$Progress.fail();
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
            this.$Progress.start();
            this.loading = true;
            axios.get('/api/emr/hims/visits').then(response =>{
                this.refresh(response);
                this.$Progress.finish();
                this.loading = false;
            })
            .catch(()=>{
                this.$Progress.fail();
                this.loading = false;
                toast.fire({
                    icon: 'error',
                    title: 'Visits were not loaded successfully',
                })
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