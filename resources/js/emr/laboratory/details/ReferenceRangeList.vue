<template>
<section>
    <div class="modal fade" id="referenceRangeFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title">Reference Range Detail</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close" @click="closeModals()"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <EMRLaboratoryFormReferenceRange :analyte.sync="analyte" :editMode.sync="editMode" :reference_range.sync="reference_range" @refreshReferenceRangeForm="resetPage()"/> 
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">Reference Ranges</h3>
            <div class="card-tools">
                <button type="button" class="btn btn-sm btn-primary" @click="addReferenceRange"><i class="fas fa-plus"></i></button>
            </div>
        </div>
        <div class="card-body table-responsive p-0 overlay-wrapper">
            <table class="table table-head-fixed text-nowrap">
                <thead>
                    <tr>
                        <th>Analyte</th>
                        <th>Gender</th>
                        <th>Age Range </th>
                        <th>Critical High</th>
                        <th>High</th>
                        <th>Normal</th>
                        <th>Low</th>
                        <th>Critical Low</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody v-for="rr in reference_ranges" :key="rr.id">
                    <tr>
                        <td v-if="source != 'analyte_detail'">{{ rr.analyte != null ? rr.analyte.name : 'Not Applicable' }}</td>
                        <td>{{ rr.gender }}</td>
                        <td>{{ rr.age_min+" - "+rr.age_max }}</td>
                        <td> > {{ rr.high_value }} {{ rr.analyte.default_unit }}</td>
                        <td>{{ rr.normal_value }} - {{ rr.high_value }} {{ rr.analyte.default_unit }}</td>
                        <td>{{ rr.low_value }} - {{ rr.normal_value }} {{ rr.analyte.default_unit }}</td>
                        <td>{{ rr.critical_low }} - {{ rr.normal_value }} {{ rr.analyte.default_unit }}</td>
                        <td> < {{ rr.critical_low }}  {{ rr.analyte.default_unit }}</td>
                        <td>
                            <span class="nav-link" data-toggle="dropdown" href="#">
                                <i class="fa fa-ellipsis-v"></i>
                            </span>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                <button @click="editReferenceRange(rr)" class="btn btn-block dropdown-item"><i class="fas fa-edit mr-1 text-primary"></i> Edit Range</button>
                                <button class="btn btn-block dropdown-item" @click="deactivateReferenceRange(rr.id)"><i class="fas fa-power-off mr-1"></i> Deactivate Range</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            editMode: true,
            form: new Form({}),
            reference_range: {},
        }
    },
    emits:['refreshReferenceRangesList'],
    mounted() {
        
    },
    methods: {
        addReferenceRange(){
            this.loading = true;
            this.editMode = false;
            this.reference_range = {};
            $('#referenceRangeFormModal').modal('show'); 
            this.loading = false;
        },
        closeModals(){
            $('#referenceRangeModal').modal('hide'); 
        },
        deactivateReferenceRange(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
                })
            .then((result) => {
                if(result.value){
                    this.form.delete('/api/emr/laboratory/reference_ranges/'+id)
                    .then(response=>{
                        this.resetPage();
                        this.$swal.fire('Deleted!', 'Reference Range has been deactivated/reactivated.', 'success');
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        editReferenceRange(range){
            this.loading = true;
            this.editMode = true;
            this.reference_range = range;
            $('#referenceRangeFormModal').modal('show'); 
            this.loading = false;
        },
        resetPage(){
            $('#referenceRangeFormModal').modal('hide');
            this.$emit('refreshReferenceRangesList');
        },
    },
    props: {
        analyte: Object,
        item: Object,
        reference_ranges: Array,
        source: String,
    }
}
</script>