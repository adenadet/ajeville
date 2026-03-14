<template>
<section class="overlay-wrapper position-relative p-0">
    <div class="ribbon-wrapper ribbon-lg">
        <div class="ribbon bg-dark" v-if="version.status == 0">Draft</div>
        <div class="ribbon bg-primary" v-else-if="version.status == 10">Unconfirmed</div>
        <div class="ribbon bg-primary" v-else-if="version.status == 20">Verified</div>
        <div class="ribbon bg-primary" v-else-if="version.status == 100">Released</div>
    </div>
    <div class="row">
        <div class="col-12">
            <div class="invoice p-3 mb-3">
                <div class="row">
                    <div class="col-12">
                        <h4>
                            <i class="fas fa-globe"></i> Mattville Hospitals.
                        </h4>
                    </div>
                </div>
                <div class="row invoice-info">
                    <div class="col-sm-5 invoice-col">
                        Patient Details:
                        <address>
                            <strong>{{ patientName(request.patient) }}</strong><br>
                            Date of Birth: {{ request?.patient?.user?.dob  }}<br>
                            Sex: {{ firstUp(request?.patient?.user?.sex) }}<br>
                            Phone: {{ request?.patient?.user?.phone }}<br>
                            Email: {{ request?.patient?.user?.email }}
                        </address>
                    </div>
                    <div class="col-sm-2 invoice-col">
                        &nbsp;
                    </div>
                    <div class="col-sm-5 invoice-col">
                        <b>Request #007612</b><br>
                        <br>
                        <b>Request Date:</b> {{ ExcelDate(request.created_at) }}<br>
                        <span v-if="request.accepted_at"><b>Collect Date:</b> {{ ExcelDate(request.accepted_at) }}</span>
                        <b>Received Date:</b>{{ ExcelDate(version?.values[0].specimen.collected_at) }}<br />
                        <b>Reported Date:</b>{{ ExcelDate(request.result.entered_at)}}<br />
                        <b>Released Date:</b>{{ ExcelDate(request.result.released_at)}}<br />
                    </div>
                </div>
                <div class="row">
                    <div class="col-12 table-responsive">
                        <table class="table table-striped">
                            <thead>
                                <tr>
                                    <th>S/N</th>
                                    <th>Analyte</th>
                                    <th>Value</th>
                                    <th>Unit</th>
                                    <th>Reference Range</th>
                                    <th>Flag</th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="(result_value, index) in version.values" :key="result_value.id">
                                    <td>{{ addOne(index) }}</td>
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
        version: {type:Object, default:()=>{}},
        source: String,
        request: Object,
    }
}
</script>