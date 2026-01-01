<template>
    <section>
        <table class="table table-head-fixed text-nowrap table-stripped table-hover" :id="actionable == 'yes' ? 'example1' : ''">
            <thead>
                <tr>
                    <th>ID</th>
                    <th>Date</th>
                    <th>Patient</th>
                    <th>Category</th>
                    <th>Item</th>
                    <th>Status</th>
                    <th v-if="source == 'laboratory'"></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(request, index) in requests" :key="index">
                    <td>{{ index | addOne }}</td>
                    <td>{{ request.date }}</td>
                    <td v-if="request.patient != null">{{ request.patient | patientName }}</td>
                    <td v-else>{{request.patient_id}}</td>
                    <td>{{ (request.item != null && request.item.category != null) ? request.item.category.name : 'No Category Yet' }}</td>
                    <td>{{ request.item != null ? request.item.name : '' }}</td>
                    <td>{{ request.status == 0 ? 'Unpaid' : 'Cleared' }}</td>
                    <td v-if="source == 'laboratory'">
                        <span class="nav-link" data-toggle="dropdown" href="#">
                            <i class="fa fa-ellipsis-v"></i>
                        </span>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                            <router-link :to="'/laboratory/requests/'+request.id" class="btn btn-block dropdown-item"><i class="fas fa-eye mr-2 text-primary"></i> View Request</router-link>
                            <button v-if="request.status == 0 && (request.transaction == null || request.transaction.paid_by == 1)" class="btn btn-block dropdown-item" @click="pay_from_wallet(request.transaction.id)"><i class="fas fa-cash-register mr-2"></i> Pay from Wallet</button>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </section>
</template>
<script>
export default {
    data() {
        return {
            editMode: true,
        }
    },
    mounted() {
        
    },
    methods: {
        pay_from_wallet(id){
            Swal.fire({
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
                    Swal.fire('Deleted!', 'Category has been deleted.', 'success');
                    Fire.$emit('CatRefresh', response);   
                    })
                    .catch(()=>{
                    Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        }
    },
    props: {
        actionable: String,
        requests: Array,
        source: String,
    }
}
</script>