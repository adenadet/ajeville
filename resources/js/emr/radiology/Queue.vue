<template>
<section class="overlay-wrapper p-0">
    <div class="card">
        <div class="card-header bg-purple">
            <h3 class="card-title">Radiology Queue</h3>
            <div class="card-tools">
                <div class="input-group" style="width: 550px;">
                    <input type="text" name="table_search" v-model="query" class="form-control float-right" placeholder="Search">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                        <select class="form-control ml-1" v-model="type">
                            <option value="1">Paid</option>
                            <option value="0">Unpaid</option>
                        </select>
                        <select class="form-control ml-1" v-model="status">
                            <option value="0">--Select Processing Status--</option>
                            <option value="0">Pending</option>
                            <option value="1">Started</option>
                            <option value="2">Collected</option>
                            <option value="5">Referred Out</option>
                            <option value="10">Reported</option>
                            <option value="13">Awaiting Secondary Report</option>
                            <option value="15">Secondary Report</option>
                            <option value="20">Confirmed</option>
                            <option value="100">Cancelled</option>
                        </select>
                        <button class="btn btn-primary ml-3" type="button" @click="addRequest"><i class="fa fa-plus"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body table-responsive p-0" style="height: 500px;">
            <EMRRadiologyDetailRequestList :requests="requests.data" view="main" />
        </div>
        <div class="card-footer">
            <pagination v-model="current_page" @paginate="getInitials" :per-page="requests.per_page != null ? requests.per_page : 52" :records="requests.total != null ? requests.total : 550" ></pagination>
        </div>
    </div>
</section>
</template>
<script>
import EMRRadiologyDetailRequestList from '@/emr/radiology/details/RequestList.vue'
export default {
    components:{EMRRadiologyDetailRequestList},    
    data() {
        return {
            current_page: 1,
            editMode: true,
            query: '',
            requests: {data: [], total: 0},
            status: '',
            type: '',
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        addRequest(){
            this.editMode = false;
            this.request = {};
            $('#requestFormModal').modal('show');
        },
        getInitials(page=1) {
            axios.get('/api/emr/radiology/requests?page='+this.current_page+'&query='+this.query+'&status='+this.status+'&type='+this.type)
            .then(response => {
                this.refreshList(response)
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Your appointments did not loaded successfully',
                })
            });
        },
        refreshList(response) {
            this.requests = response.data.requests;
        }
    },
    props: {}
}
</script>