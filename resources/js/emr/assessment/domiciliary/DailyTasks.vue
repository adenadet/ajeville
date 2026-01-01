<template>
<section>
    <div class="col-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Task</h3>
                <div class="card-tools">
                    <button class="btn btn-sm btn-primary" @click="addTask()"><i class="fa fa-plus"></i></button>
                </div>
            </div>
            <div class="card-body table-responsive p-0">
                <table class="table table-bordered table-hover">
                    <thead>
                        <tr>
                            <th>Date</th>
                            <th>Shift Type | Staff Type</th>
                            <th>Patient </th>
                            <th>Arrival</th>
                            <th>Departure</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(batch, index) in daily_batches.data" :key="batch.id">
                        
                            <td>{{batch.raw_date}}</td>
                            <td>{{(batch.shift_type != null ? batch.shift_type.name : 'Old Shift Type')}} | {{(batch.staff_type != null ? batch.staff_type.name : 'Old staff Type')}}</td>
                            <td>{{(batch.patient != null ? batch.patient.first_name+' '+batch.patient.middle_name+' '+batch.patient.last_name : 'Old Patient')}}</td>
                            <td>{{batch.start_date}}</td>
                            <td>{{batch.end_date}}</td>
                            <td v-html="batch.status == null ? 'Unassigned' : batch.status == 0 ? 'Not Arrived': (batch.status == 1 ? 'Arrived':(batch.status == 2 ? 'Departed': 'Confirmed'))"></td>
                            <td>
                                <div class="btn-group">
                                    <button class="btn btn-sm btn-success" @click.prevent="assignBatch(batch)" title="Assign Batch to Staff"><i class="fa fa-user"></i></button>
                                    <button class="btn btn-sm btn-primary" @click.prevent="editBatch(batch)" title="Edit Shift"><i class="fa fa-edit"></i></button>
                                    <button class="btn btn-sm btn-danger" @click.prevent="deleteBatch(batch.id)" title="Assign Shift to Staff"><i class="fa fa-trash"></i></button>
                                </div>
                            </td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return  {
            daily_batches : {},
        }
    },
    mounted() {
        const date =new Date().toJSON().slice(0, 10);
        this.today = date;
        Fire.$on('ApplicantDataFill', user =>{
            this.ApplicantData.fill(user);
        });
        Fire.$on('AfterCreation', ()=>{
            //axios.get("api/profile").then(({ data }) => (this.ApplicantData.fill(data)));
        });
    },
    methods:{
        getInitials(){
            this.$Progress.start();
            axios.get('/api/emr/domiciliary/batch_assigns/assigned').then(response =>{
                this.refresh(response);
                this.$Progress.finish();
                toast.fire({
                    icon: 'success',
                    title: 'Daily Tasks were loaded successfully',
                });
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Daily Tasks were not loaded successfully',
                })
            });
        },
        refresh(response){
            this.daily_batches = response.data.daily_batches;
        },
    },
    props:{
        applicant: Object,
        editMode: Boolean,   
        nations: Array, 
        patient: Object,
        patients: Array,
    }
}
</script>