<template>
<section class="content-header pt-0">
    <div class="row">
        <div class="col-12">
            <div class="card overlay-wrapper">
                <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                <div class="card-header bg-navy">
                    <h3 class="card-title">My Team Leave Requests</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 150px;">
                            <select name="table_search" class="form-control float-right" @change="getAllInitials(1)" v-model="list_type">
                                <option value="all">All</option>
                                <option value="0">Unapproved</option>
                                <option value="1">Approved</option>
                                <option value="3">Ongoing</option>
                                <option value="2">Completed</option>
                            </select>
                            <div class="input-group-append">
                                <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <HrmsDetailLeaveRequestList source="team" :requests.sync="requests" @refreshRequests="getAllInitials"/>
                </div>
                <div class="card-footer"><pagination v-model="current_page" @paginate="getAllInitials" :per-page="requests.per_page != null ? requests.per_page : 52" :records="requests.total != null ? requests.total : 550" ></pagination></div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            current_page: 1,
            editMode: true,
            loading: false,
            request: {},
            requests: {},
            list_type: 'all',
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        getAllInitials(page=1){
            this.loading = true;
            axios.get('/api/hrms/leaves?type=team&page='+page+'&list_type='+this.list_type)
            .then(response => {
                this.requests = response.data.requests;
                this.loading = false;
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'Your leave requests did not loaded successfully',})
            });
        },
    },
    props: {}
}
</script>