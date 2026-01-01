<template>
<div class="card card-primary">
    <div class="modal fade" id="vitalModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header"><h4 class="modal-title" v-html="editMode ? 'Update Vital' : 'Add Vital'"></h4><button type="button" class="close"  @click="closeModal"><span aria-hidden="true">&times;</span></button></div>
                <div class="modal-body"><HimsPatientFormVital :editMode="editMode" :vital="vital" /></div>
            </div>
        </div>
    </div>
    <div class="card-header">
        <h3 class="card-title">
            <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                <li class="nav-item">
                    <a class="nav-link active" id="table" data-toggle="pill" href="#table" role="tab" aria-controls="table" aria-selected="true">Table</a>
                </li>
                <li class="nav-item">
                    <a class="nav-link" id="graph" data-toggle="pill" href="#graph" role="tab" aria-controls="graph" aria-selected="false">Graph</a>
                </li>
            </ul>
        </h3>
        <div class="card-tools"><button type="button" @click="addVital()" class="btn btn-sm btn-primary"><i class="fa fa-plus"></i></button></div>
    </div>
    <div class="card-body table-responsive p-0">
        <div class="tab-pane fade show active" id="table" role="tabpanel" aria-labelledby="table-tab">

        </div>
        <div class="tab-pane fade show active" id="table" role="tabpanel" aria-labelledby="table-tab">

        </div>
        <table class="table table-striped table-hover text-nowrap">
        <thead><tr><th>Date</th><th>Blood Pressure</th><th>Temp</th><th>Pulse</th><th>Height</th><th>Weight</th><th></th></tr></thead>
        <tbody v-if="vitals != null">
            <tr v-for="vital in vitals.data" :key="vital.id" >
                <td>{{vital.date}}</td>
                <td>{{vital.blood_pressure}}</td>
                <td>{{vital.temp}}</td>
                <td>{{vital.pulse}}</td>
                <td>{{vital.height}}</td>
                <td>{{vital.weight}}</td>
                <td>
                    <div class="btn-group">
                        <button class="btn btn-sm btn-primary" @click="editVital(vital)"><i class="fa fa-edit"></i></button>
                    </div>
                </td>
            </tr>
        </tbody>
        <tbody v-else><td colspan="6">No Vital has been taken of this patient</td></tbody>
        </table>
    </div>
    <div class="card-footer">
        <pagination :data="vitals" @pagination-change-page="getInitials">
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
            vitals:{}, 
            vital:{},
        }
    },
    created() {
        Fire.$on('refreshPatientVitals', patient => {
            this.patient = patient;
            this.getInitials();
            this.closeModal();
        });
    },
    methods:{
        addVital(){
            this.$Progress.start();
            this.editMode = false;
            let details = {'vital': {}, 'patient':this.patient};
            Fire.$emit('VitalDataFill', (details));
            $('#vitalModal').modal('show');
            this.$Progress.finish();
        },
        closeModal(){
            $('#vitalModal').modal('hide');
        },
        editVital(vital){
            this.$Progress.start();
            this.editMode = true;
            let details = {'vital': vital, 'patient':this.patient};
            Fire.$emit('VitalDataFill', (details));
            $('#vitalModal').modal('show');
            this.$Progress.finish();
        },
        getInitials(page=1){
            if (this.patient != null){
                axios.get('/api/emr/hims/vitals/'+this.patient.id+'?page='+page).then(response =>{
                    this.$Progress.finish(); this.reloadVital(response);
                })
                .catch(()=>{
                    this.$Progress.fail(); toast.fire({icon: 'error', title: 'Vital not loaded successfully',});
                });
            }
            else{this.vitals = {};}
        },
        reloadVital(response){this.vitals = response.data.vitals;},
    },
    props:{
    },
}
</script>