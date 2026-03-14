<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="specimenModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h4 class="modal-title">Specimen Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <EMRLaboratoryDetailSpecimen :specimen.sync="specimen"/>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="specimenActionFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-primary">
                    <h4 class="modal-title">Specimen Action</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <EMRLaboratoryFormSpecimenAction :specimen.sync="specimen" :editMode="editMode" @specimenActionReload="refreshPage"/>
                </div>
            </div>
        </div>
    </div>
    <table class="table table-head-fixed text-nowrap">
        <thead>
            <tr>
                <th>S/N</th>
                <th>Received Date</th>
                <th>Patient</th>
                <th>Specimen</th>
                <th>Status</th>
                <th></th>
            </tr>
        </thead>
        <tbody v-if="specimens.length > 0">
            <tr v-for="(specimen, index) in specimens">
                <td>{{ addOne(index) }}</td>
                <td>{{ ExcelDate(specimen.created_at) }}</td>
                <td>{{ patientName(specimen.request?.patient) }}</td>
                <td>{{ specimen.specimen_type?.name }}</td>
                <td>
                    <span v-if="specimen.status == 0"class="badge badge-info">Pending</span>
                    <span v-else-if="specimen.status == 10" class="badge badge-dark">Collected</span>
                    <span v-else-if="specimen.status == 20" class="badge badge-success">Received</span>
                    <span v-else-if="specimen.status == 30" class="badge badge-danger">Rejected</span>
                </td>
                <td>
                    <span class="nav-link" data-toggle="dropdown" href="#"><i class="fa fa-ellipsis-v"></i></span>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <button type="button" @click="viewSpecimen(specimen)" class="btn btn-block dropdown-item"><i class="fas fa-eye mr-2 text-primary"></i> View Request</button>
                        <button type="button" v-if="specimen.status == 10" class="btn btn-block dropdown-item" @click="actionSpecimen(specimen)"><i class="fas fa-stamp mr-2"></i> Confirm Specimen</button>
                    </div>
                </td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr><td colspan="6">No Specimen meets your requirement</td></tr>
        </tbody>
    </table>
</section>
</template>
<script>
import EMRLaboratoryDetailSpecimen from '@/emr/laboratory/details/Specimen.vue';
import EMRLaboratoryFormSpecimenAction from '@/emr/laboratory/forms/SpecimenAction.vue';
export default {
    components:{
        EMRLaboratoryDetailSpecimen, EMRLaboratoryFormSpecimenAction
    },
    data() {
        return {
            editMode: true,
            loading: false,
            specimen: {},
        }
    },
    emits:['refreshSpecimenList'],
    mounted() {},
    methods: {
        actionSpecimen(specimen){
            this.loading = true;
            this.specimen = specimen;
            $('#specimenActionFormModal').modal('show');
            this.loading = false;
        },
        closeModals(){
            $('#specimenActionFormModal').modal('hide');
        },
        refreshPage(){
            this.closeModals();
            this.$emit('refreshSpecimenList');
        },
        viewSpecimen(specimen){
            this.loading = true;
            this.specimen = specimen;
            $('#specimenModal').modal('show');
            this.loading = false;
        }
    },
    props: {
        specimens: Array,
        source: String,
    }
}
</script>