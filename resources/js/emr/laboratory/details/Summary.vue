<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="approvalFormModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h4 class="modal-title">Approve Result</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModals()"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <EMRLaboratoryFormResultAction :values.sync="values" :result_id.sync="result_id" :version_id="version_id" @resultActionReload="refreshPage"/> 
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="collectionFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h4 class="modal-title">Collect Specimen</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModals()"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <EMRLaboratoryFormCollect :editMode.sync="editMode" :request.sync="request" @itemReload="refreshPage"/> 
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="depositFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title">Deposit</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <EMRFinanceFormDeposit @refreshBottleForm="refreshPage"/>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="resultFormModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h4 class="modal-title">Enter Result</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <EMRLaboratoryFormResult @refreshResultForm="refreshPage" :request_id.sync="$route.params.id"/>
                </div>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="position-relative">
                <div class="ribbon-wrapper ribbon-xl">
                    <div class="ribbon bg-dark" v-if="request?.status == 0">Pending</div>
                    <div class="ribbon bg-info" v-else-if="request?.status == 1">Accepted</div>
                    <div class="ribbon bg-purple" v-else-if="request?.status == 2">Sample Collected</div>
                    <div class="ribbon bg-success" v-else-if="request?.status == 20">Completed</div>
                    <div class="ribbon bg-danger" v-else-if="request?.status == 100">Cancelled</div>
                    <div class="ribbon bg-primary" v-else>Ongoing</div>
                </div>
                <div class="card">
                    <div class="card-header bg-navy">
                        Laboratory Request Summary 
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-bordered table-hover table-striped">
                            <tbody>
                                <tr>
                                    <td><strong>Patient</strong></td>
                                    <td>{{ patientName(request?.patient) }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Balance</strong></td>
                                    <td>{{ currency(request?.patient?.balance || 0)}}</td>
                                </tr>
                                <tr>
                                    <td><strong>Item</strong></td>
                                    <td>{{ request?.lab_service?.emr_service?.item?.name}}</td>
                                </tr>
                                <tr>
                                    <td><strong>Transation</strong></td>
                                    <td>
                                        {{ currency(request?.transaction?.item_total) }}
                                        <span class="badge badge-danger float-right" v-if="request?.transaction?.status == 1">Unpaid</span>
                                        <span class="badge badge-primary float-right" v-else-if="request?.transaction?.status == 10">Paid</span>
                                        <span class="badge badge-success float-right" v-else-if="request?.transaction?.status == 100">Completed</span>
                                        <span class="badge badge-info float-right" v-else-if="request?.transaction?.status == 50">Deferred</span>
                                        <span class="badge badge-danger float-right" v-else-if="request?.transaction?.status == 400">Cancelled</span>
                                        <span class="badge badge-danger float-right" v-else-if="request?.transaction?.status == 1000">Transferred</span>
                                    </td>
                                </tr>
                                <tr>
                                    <td><strong>Status</strong></td>
                                    <td>
                                        <span class="badge badge-dark" v-if="request?.status == 0">Booked</span>
                                        <span class="badge badge-info" v-else-if="request?.status == 1">Started</span>
                                        <span class="badge bg-purple" v-else-if="request?.status == 2">Sample Collected</span>
                                        <span class="badge badge-info" v-else-if="request?.status == 4">Ongoing</span>
                                        <span class="badge badge-success" v-else-if="request?.status >= 20 && request?.status <= 40">Completed</span>
                                        <span class="badge badge-success" v-else>Cancelled</span>
                                    </td>
                                </tr>
                                <tr v-if="request.status >= 2">
                                    <td><strong>Unique ID</strong></td>
                                    <td>{{ request?.unique_id}}</td>
                                </tr>
                                <tr>
                                    <td><strong>Category</strong></td>
                                    <td>{{ request.item?.emr_service?.referenceable?.category?.name || 'No Category Yet' }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Requested By</strong></td>
                                    <td>{{ FullName(request?.creator)  }}</td>
                                </tr>
                                <tr>
                                    <td><strong>Requested At</strong></td>
                                    <td>{{ ExcelDate(request?.created_at) }}</td>
                                </tr>
                                <tr v-if="request.status >= 2">
                                    <td><strong>Collected By</strong></td>
                                    <td>{{ FullName(request?.acceptor) }}</td>
                                </tr>
                                <tr v-if="request.status >= 2">
                                    <td><strong>Collected At</strong></td>
                                    <td>{{ ExcelDate(request?.collected_at)}}</td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer p-0" v-show="show_status">
                        <div class="float-right btn-group">
                            <button v-if="request?.status == 0 && ((request?.transaction?.coverage?.patient_payable || request?.transaction?.item_total || 0) + (request?.patient?.balance || 0) <= 0)" class="btn btn-primary float-left m-2 mr-0" @click="startRequest()"><i class="fa fa-check mr-1"></i>Start Request</button>
                            <button v-else-if="request.transaction?.status == 1" class="btn btn-default m-2 mr-0" @click="receivePayment"><i class="fa fa-cash-register text-success mr-1"></i>Receive Payment</button>

                            <!-- When status is 1, allow start -->
                            <button v-if="request?.status == 1" class="btn btn-primary float-left m-2 mr-0" type="button" @click="startRequest()">Accept Request</button>
                            <button v-if="request.special != null && request.status <= 2" class="btn btn-outline-danger float-right m-2 mr-0" @click="startRequest()" ><i class="fa fa-vial text-primary mr-1"></i>Accept Emergency Request</button>

                            <!-- When status is started, then -->
                            <button v-if="request.status == 3" class="btn btn-outline-primary float-right m-2 mr-0" @click="collectSpecimen()" ><i class="fa fa-vial text-primary mr-1"></i>Collect Sample</button>

                            <button v-else-if="request.status >= 3 && request.specimens?.length >= 1 && request.status < 20" class="btn btn-default float-right m-2 mr-0"  @click="collectSpecimen()"><i class="fa fa-vial text-success mr-1"></i>Re-Collect Sample</button>
                            <button v-if="request.status == 4" class="btn btn-outline-primary float-right m-2 mr-0"  @click="startInvestigation()"><i class="fa fa-check text-primary mr-1"></i>Start Investigation</button>
                            <button v-if="request.status == 5 && request.result?.latest_version?.status == 0" class="btn btn-default float-right m-2 mr-0" @click="enterResult()"><i class="fa fa-file-pdf text-success mr-1"></i>Enter Result</button>
                            <button v-else-if="request.status == 5 && request.result?.status == 25" class="btn btn-default float-right m-2 mr-0" @click="enterResult()"><i class="fa fa-file-pdf text-success mr-1"></i>Enter Secondary Result</button>
                            <button v-else-if="request.status == 5 && request.result?.latest_version?.status == 10" class="btn btn-default float-right m-2 mr-0" @click="approveResult()"><i class="fa fa-check text-success mr-1"></i>Approve Result</button>
                            <button v-else-if="request.status == 5 && request.latest_version?.status == 25" class="btn btn-default float-right m-2 mr-0"><i class="fa fa-file-pdf text-success mr-1"></i>Seek Secondary Result</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
import EMRLaboratoryFormCollect from '@/emr/laboratory/forms/Collect.vue';
import EMRLaboratoryFormResult from '@/emr/laboratory/forms/Result.vue';
import EMRLaboratoryFormResultAction from '@/emr/laboratory/forms/ResultAction.vue';
import EMRFinanceFormDeposit from '@/emr/finance/forms/Deposit.vue';
export default {
    components:{
        EMRLaboratoryFormCollect, EMRLaboratoryFormResult, EMRLaboratoryFormResultAction, EMRFinanceFormDeposit
    },
    data() {
        return {
            editMode: false,
            form: new Form({}),
            loading: false,
            patient: {},
            result_id: '',
            version_id: '',
            values: [],
        }
    },
    emits:['refreshLaboratoryPage'],
    mounted() {
    },
    methods: {
        approveResult(){
            this.loading = true;
            if(this.request.result?.latest_version?.status == 10){
                this.result_id = this.request.result?.id;
                this.values = this.request.result?.latest_version?.id;
                this.values = this.request.result?.latest_version?.values;
                $('#approvalFormModal').modal('show');    
            }
            else{
                this.$swal('Error', 'Invalid Result can not be approved', 'error');
            }
            this.loading = false
        },
        closeModals(){
            $('#approvalFormModal').modal('hide');
            $('#collectionFormModal').modal('hide');
            $('#depositFormModal').modal('hide');
            $('#resultFormModal').modal('hide');
        },
        collectSpecimen(){
            this.loading = true;
            $('#collectionFormModal').modal('show');
            this.loading = false;
        },
        enterResult(){
            this.loading = true;
            $('#resultFormModal').modal('show');
            this.loading = false;
        },
        receivePayment(){
            this.loading = true;
            $('#depositFormModal').modal('show');
            this.loading = false;
        },
        refreshPage(){
            this.closeModals();
            this.$emit('refreshLaboratoryPage');
        },
        startInvestigation(){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, start request!'
            })
            .then((result) => {
                if(result.value){
                    this.form.get('/api/emr/laboratory/requests/'+this.request.id+'/start_report')
                    .then(response=>{
                        this.$emit('refreshLaboratoryPage')
                        this.$swal.fire('Started!', 'Request has been started.', 'success');
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        startRequest(){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, start request!'
            })
            .then((result) => {
                if(result.value){
                    this.form.get('/api/emr/laboratory/requests/'+this.request.id+'/start')
                    .then(response=>{
                        this.$emit('refreshLaboratoryPage')
                        this.$swal.fire('Started!', 'Request has been started.', 'success');
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        }
    },
    props: {
        request: Object,
        print_label: Boolean,
        show_status: Boolean,
    }
}
</script>