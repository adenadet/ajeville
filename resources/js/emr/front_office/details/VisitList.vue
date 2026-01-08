<template>
    <div class="card-body table-responsive p-0" style="height: 300px;">
        <table class="table table-head-fixed table-striped  text-nowrap">
            <thead>
                <tr>
                    <th>Date</th>
                    <th>Visit ID</th>
                    <th>Patient </th>
                    <th>Plan</th>
                    <th>Status</th>
                    <th>&nbsp;</th>
                </tr>
            </thead>
            <tbody v-if="visits.length > 0">
                <tr v-for="visit in visits" :key="visit.id">
                    <td>{{ExcelDate(visit.date)}}</td>
                    <td>{{ visit.unique_id}}</td>
                    <td>{{ patientName(visit.patient) }}</td>
                    <td>{{ visit.plan_id  }}</td>
                    <td><span class="badge" :class="statusClass(visit.status)">{{ visit.status == 0 ? 'Booked' : (visit.status == 1 ? 'Open' : (visit.status == 5 ? 'Ongoing' : (visit.status == 100 ? 'Completed' : (visit.status == 400 ? 'Cancelled' : 'Undisclosed')))) }}</span></td>
                    <td class="text-end">
                        <span class="nav-link" data-toggle="dropdown" href="#">
                            <i class="fa fa-ellipsis-v"></i>
                        </span>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            <router-link :to="'/emr/front_office/visits/'+visit.unique_id" class="btn btn-block dropdown-item"><i class="fas fa-eye mr-1 text-primary"></i> View Visit</router-link>
                            <button class="btn btn-block dropdown-item" v-if="visit.status <= 1"><i class="fas fa-calendar-alt mr-1 text-warning"></i> Reschedule</button>
                            <button class="btn btn-block dropdown-item" @click="endVisit(visit)"><i class="fas fa-trash mr-1 text-danger"></i> Delete</button>
                        </div>
                    </td>
                </tr>
            </tbody>
            <tbody v-else>
                <tr>
                    <td colspan="7">No Visit matches your criteria</td>
                </tr>
            </tbody>
        </table>
    </div>
</template>
<script>
export default {
    data() {
        return {
            editMode: false,
            loading: true,
        }
    },
    mounted() {
        //this.getAllInitials();
    },
    methods: {
        closeModal(){
            $('#authCodeModal').modal('hide');
            $('#requestCodeModal').modal('hide');
            $('#planModal').modal('hide');
            $('#providerModal').modal('hide');
        },
        refresh(response){
            this.transactions = response.data.transactions;
            this.transaction = response.data.transactions.data[0];
            this.loading = false;
        },
        statusClass(status) {
            return {
                'bg-warning': status === 0,
                'bg-primary': status === 1,
                'bg-info': status === 5,
                'bg-dark': status === 10,
                'bg-success': status === 100,
                'bg-danger': status === 400,
            }
        },
    },
    props:{
        view: String,
        visits: Array,
    },
}
</script>