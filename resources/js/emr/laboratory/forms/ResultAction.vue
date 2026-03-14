<template>
<section class="overlay-wrapper p-0">
    <div class="row">
        <div class="col-md-6">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-primary">
                            <h3 class="card-title">Values</h3>
                        </div>
                        <div class="card-body table-responsive p-0" style="height: 300px;">
                            <table class="table table-head-fixed text-nowrap">
                                <thead>
                                    <tr>
                                        <th>Analyte</th>
                                        <th>Value</th>
                                        <th>Reference Range</th>
                                        <th>Flag</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="result_value in values" :key="result_value.id">
                                        <td>{{ result_value.analyte_name }}</td>
                                        <td>{{ result_value.value }}</td>
                                        <td>{{ result_value.unit }}</td>
                                        <td>{{ result_value.reference_range?.low ? result_value.reference_range.low+" - "+result_value.reference_range.normal : result_value.reference}}</td>
                                        <td><span class="badge" :class="flagClass(firstUp(result_value.flag))">{{ firstUp(result_value.flag) }}</span></td>
                                    </tr>
                                </tbody>
                            </table>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="col-md-6">
            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="card-header bg-primary">
                            <h3 class="card-title">Values</h3>
                        </div>
                        <div class="card-body" style="min-height: 300px;">
                            {{ result_id }} {{ version_id}}
                            <div class="col-md-12">
                                <div class="form-group">
                                    <label>Decision</label>
                                    <select class="form-control" id="decision" name="decision" v-model="approvalData.decision">
                                        <option value="">--Select Decision--</option>
                                        <option value="confirm">Confirm</option>
                                        <option value="reject">Request Secondary Opinion</option>
                                    </select>
                                </div>
                                <div class="form-group" v-if="approvalData.decision == 'reject'">
                                    <label>Reason</label>
                                    <input class="form-control" id="reason" name="reason" v-model="approvalData.reason">
                                </div>
                                <div class="form-group">
                                    <label>Remarks</label>
                                    <QuillEditor class="form-control" content-type="html" name="remarks" id="remarks" v-model:content="approvalData.remarks" placeholder="Description"></QuillEditor>
                                </div>
                            </div>
                            <div class="col-md-6">
                                <div class="form-group"><button class="btn btn-primary" @click="approveRequest">Submit</button></div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
import EMRLaboratoryDetailResult from '@/emr/laboratory/details/Result.vue'
export default {
    components:{EMRLaboratoryDetailResult},
    data() {
        return {
            approvalData: new Form({
                decision: '',
                specimen_id: '',
                remarks: '',
                reason: '',
            }),
            editMode: true,
            loading: false,
        }
    },
    emits:['resultActionReload'],
    mounted() {},
    methods: {
        approveRequest(){
            this.loading = true;
            this.approvalData.result_id = this.result_id;
            this.approvalData.version_id = this.version_id;
            this.approvalData.put('/api/emr/laboratory/results/'+this.result_id+'/verify')
            .then(response =>{
                this.loading = false;
                this.$emit('resultActionReload', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Sample has been actioned',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                this.$swal.fire({
                    icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'
                });
                this.loading = false;
            });  
            this.loading = false;
        },
        closeModals(){
            $('#requestFormModal').modal('hide');
            this.$emit('refreshLaboratoryRequestList');
        },
        flagClass(flag){
            return {
                'badge-warning': flag === 'High' || flag === 'Low',
                'badge-danger': flag === 'Critical High' || flag === 'Critical Low',
                'badge-success': flag === 'Normal'
            }
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
                        this.$swal.fire('Deleted!', 'Category has been deleted.', 'success');
                        this.$emit('CatRefresh', response);   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        refreshPage(){
            this.closeModals();
        },
    },
    props: {
        values: Array,
        version: {type:Object, default:()=>{}},
        source: String,
        request: Object,
        result_id: {type: [Number, String]},
        version_id: {type: [Number, String]}
    }
}
</script>