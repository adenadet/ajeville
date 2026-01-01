<template>
    <section class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">
                            Insurance Desk
                        </h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-hover table-striped text-nowrap">
                            <thead>
                                <tr>
                                    <th>Date</th>
                                    <th>Patient</th>
                                    <th>Category</th>
                                    <th>Item</th>
                                    <th>Status</th>
                                    <th></th>
                                </tr>
                            </thead>
                            <tbody>
                                <tr v-for="request in requests.data" :key="request.id"  :class="request.special != null ? 'bg-danger' : ''" @click="updateRequest(request)">
                                    <td>{{ request.date | excelDate }}</td>
                                    <td>{{ request.patient | patientName }}</td>
                                    <td>{{ request.item.category != null ? request.item.category.name : 'No Category Yet' }}</td>
                                    <td>{{ request.item.name }}</td>
                                    <td>{{ request.status }}</td>
                                    <td>
                                        <span class="nav-link" data-toggle="dropdown" href="#">
                                            <i class="fa fa-ellipsis-v"></i>
                                        </span>
                                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                                            <router-link :to="'/laboratory/requests/'+request.id" class="btn btn-block dropdown-item"><i class="fas fa-eye mr-2 text-primary"></i> View Request</router-link>
                                            <button v-if="request.status == 0" class="btn btn-block dropdown-item"><i class="fas fa-cash-register mr-2"></i> Receive Deposit</button>
                                        </div>
                                    </td>  
                                </tr>
                            </tbody>
                        </table>
                    </div>
                    <div class="card-footer">
                        <pagination :data="users" @pagination-change-page="getUser">
                            <span slot="prev-nav">&lt; Previous </span>
                            <span slot="next-nav">Next &gt;</span>
                        </pagination>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<script>
export default {
    data() {
        return {
            request: {},
            requests: {},
            editMode: true,
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        getInitials(page=1) {
            axios.get('/api/emr/laboratory/requests/insurance?page='+page)
            .then(response => {
                this.refreshQueue(response)
            })
            .catch(() => {
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Your appointments did not loaded successfully',
                })
            });
        },
        refreshQueue(response) {
            this.requests = response.data.requests;
            this.request = response.data.requests.data[0];
        },
    },
    props: {}
}
</script>