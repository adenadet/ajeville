<template>
<section>
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-dark">
                <h3 class="card-title">Requests</h3>
                <div class="card-tools">
                    <div class="input-group" style="width: 450px;">
                        <input type="text" name="table_search" v-model="query" class="form-control" placeholder="Search">
                        <div class="input-group-append">
                            <select class="form-control ml-1" v-model="type" @change="getAllInitials">
                                <option value="admitted">Admitted</option>
                                <option value="all">All</option>
                                <option value="bed_assigned">Bed Assigned</option>
                                <option value="billed">Billed</option>
                                <option value="confirmed">Confirmed</option>
                                <option value="deleted">Deleted</option>
                                <option value="discharged">Discharged</option>
                                <option value="pending">Pending</option>
                                <option value="prechecked">Prechecked</option>
                            </select>
                            <select class="form-control ml-1" v-model="ward_id" @change="getAllInitials">
                                <option value="">All</option>
                                <option v-for="ward in wards" :value="ward.id">{{ ward.name }}</option>
                            </select>
                            <button type="button" class="btn btn-primary ml-1" @click="getAllInitials"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive p-0" style="height: 600px">
                <EMRAdmissionDetailRequestList :requests="requests.data" @refreshRequestList="getAllInitials" />
            </div>
            <div class="card-footer">
                <pagination v-model="current_page" @paginate="getAllInitials" :per-page="requests.per_page != null ? requests.per_page : 52" :records="requests.total != null ? requests.total : 550" ></pagination>
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
            editMode: false,
            loading: false,
            query: '',
            request: {},
            requests: {data: [], total: 0},
            type: 'pending',
            wards: [],
            ward_id: '',
        }
    },
    methods: {
        getAllInitials(){
            this.loading = true
            axios.get('/api/emr/admissions/requests?type='+this.type+'&query='+this.query+'&ward_id='+this.ward_id+'&page='+this.current_page)
            .then(res => {
                this.requests = res.data.requests;
                this.wards = res.data.wards;
            })
            .finally(() => {
                this.loading = false
            })
        },
    },
    mounted() {
        this.getAllInitials()
    },
}
</script>