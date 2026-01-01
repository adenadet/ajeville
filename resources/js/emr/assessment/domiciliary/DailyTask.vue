<template>
<section>
    <div class="col-3">
        <HimsPatientCard :patient="patient" />
    </div>
    <div class="col-9">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Task</h3>
                <div class="card-tools">
                    <button class="btn btn-sm btn-primary" @click="addTask()"><i class="fa fa-plus"></i></button>
                    <div class="input-group input-group-sm" style="width: 150px;">
                        <input type="text" name="table_search" class="form-control float-right" placeholder="Search" />
                        <div class="input-group-append"><button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button></div>
                    </div>
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
            patient: {},
            tasks: {},

        }
    },
    mounted() {
        console.log('Component mounted.')
    },
    created() {
        //this.getInitials();
    },
    methods:{
        getInitials(){
            axios.get('/api/emr/batch_assigns/'+this.$route.params.id).then(response =>{
                this.$Progress.finish();
                toast.fire({
                    icon: 'success',
                    title: 'Profile loaded successfully',
                });
                Fire.$emit('BioDataFill', this.user);
                Fire.$emit('NextOfKinFill', this.nok);
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Profile not loaded successfully',
                })
            });
        },
    },
    props:{
    }
}
</script>