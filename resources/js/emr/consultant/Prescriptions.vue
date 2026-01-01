<template>
<div class="card card-primary">
    <div class="modal fade" id="prescriptionModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header"><h4 class="modal-title" v-html="editMode ? 'Update Prescription' : 'Add Prescription'"></h4><button type="button" class="close"  @click="closeModal"><span aria-hidden="true">&times;</span></button></div>
                <div class="modal-body"><HimsPatientFormPrescription :editMode="editMode" :prescription="prescription" /></div>
            </div>
        </div>
    </div>
    <div class="card-header">
        <h3 class="card-title">List of Prescriptions</h3>
        <div class="card-tools"><button type="button" @click="addPrescription()" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i></button></div>
    </div>
    <div class="card-body table-responsive p-0">
        <table class="table table-striped table-hover text-nowrap">
        <thead><tr><th>Prescribed By</th><th>Drugs</th><th>Start Date</th><th>End Date</th><th></th></tr></thead>
        <tbody v-if="prescriptions != null">
            <tr v-for="prescription in prescriptions.data" :key="prescription.id" >
            <td>{{prescription.doctor_name}}</td>
            <td>{{prescription.drugs.length}}</td>
            <td>{{ prescription.start_date }}</td>
            <td>{{prescription.end_date}}</td>
            <td>
                <div class="btn-group">
                    <button class="btn btn-sm btn-primary" @click="editPrescription(prescription)"><i class="fa fa-edit"></i></button>
                </div>
            </td>
            </tr>
        </tbody>
        <tbody v-else><td colspan="6">No Prescription has been added to this patient</td></tbody>
        </table>
    </div>
    <div class="card-footer">
        <pagination :data="prescriptions" @pagination-change-page="getInitials">
            <span slot="prev-nav">&lt; Previous </span>
            <span slot="next-nav">Next &gt;</span>
        </pagination>
    </div>
</div>
</template>
<script>
export default {
    data(){
        return  {
            editMode: true, 
            patient: {},
            prescriptions:{}, 
            prescription:{},
        }
    },
    created() {
        Fire.$on('Reload', response =>{this.refreshProfile(response);});
        Fire.$on('refreshPatientPrescriptions', patient => {
            this.patient = patient;
            this.getInitials();
            this.closeModal();
        });
    },
    methods:{
        addPrescription(){
            this.$Progress.start();
            this.editMode = false;
            let details = {'prescription': {}, 'patient':this.patient};
            Fire.$emit('PrescriptionDataFill', (details));
            $('#prescriptionModal').modal('show');
            this.$Progress.finish();
        },
        closeModal(){
            $('#prescriptionModal').modal('hide');
        },
        editPrescription(prescription){
            this.$Progress.start();
            this.editMode = true;
            let details = {'prescription': prescription, 'patient':this.patient};
            Fire.$emit('PrescriptionDataFill', (details));
            $('#prescriptionModal').modal('show');
            this.$Progress.finish();
        },
        getInitials(page=1){
            if (this.patient != null){
                axios.get('/api/emr/hims/prescriptions/'+this.patient.id+'?page='+page).then(response =>{
                    this.$Progress.finish();
                    this.reloadPrescription(response);
                })
                .catch(()=>{
                    this.$Progress.fail();
                    toast.fire({icon: 'error', title: 'Prescription not loaded successfully',});
                });
            }
            else{
                this.prescriptions = {};
            }
        },
        reloadPrescription(response){
            this.prescriptions = response.data.prescriptions;
        },
    },
    props:{
        //patient: Object,
    },
}
</script>