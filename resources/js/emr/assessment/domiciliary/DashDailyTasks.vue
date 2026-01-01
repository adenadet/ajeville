<template>
<section>
    <div class="card card-primary">
        <div class="card-header">
            <h3 class="card-title"><i class="ion ion-clipboard mr-1"></i>Today's Shifts</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover">
                <thead>
                    <tr>
                        <th>Date</th>
                        <th>Shift Type</th>
                        <th>Patient</th>
                        <th>Status</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody v-if="daily_batches != null">
                    <tr v-for="batch in daily_batches.data" :key="batch.id">
                        <td>{{batch.date}}</td>
                        <td>{{(batch.shift_type != null ? batch.shift_type.name : 'Old Shift Type')}} | {{(batch.staff_type != null ? batch.staff_type.name : 'Old staff Type')}}</td>
                        <td>{{(batch.patient != null ? batch.patient.first_name+' '+batch.patient.middle_name+' '+batch.patient.last_name : 'Old Patient')}}
                            <br /><span v-html="batch.patient != null ? batch.patient.uk_address: 'No address yet'" ></span>
                        </td>
                        <td v-html="batch.status == null ? 'Unassigned' : batch.status == 0 ? 'Not Arrived': (batch.status == 1 ? 'Arrived':(batch.status == 2 ? 'Departed': 'Confirmed'))"></td>
                        <td>
                            <div class="btn-group">
                                <router-link :to="'/domiciliary/daily_tasks/'+batch.id" class="btn btn-sm btn-primary" title="View Details"><i class="fa fa-eye"></i></router-link>
                            </div>
                        </td>
                    </tr>
                </tbody>
                <tbody v-else>
                    <tr><td colspan="5">No Shift has been assigned to you</td></tr>
                </tbody>
            </table>                
        </div>
        <div class="card-footer clearfix"><button type="button" class="btn btn-primary float-right"><i class="fas fa-eye"></i> See All</button></div>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return {
            daily_batches: {},     
        }
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
    mounted() {
        this.getInitials();
    },
    props:{},
}
</script>