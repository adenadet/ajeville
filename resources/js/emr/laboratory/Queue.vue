<template>
    <section class="container-fluid overlay-wrapper">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h3 class="card-title">Laboratory Queue</h3>
                        <div class="card-tools">
                            <div class="input-group" style="width: 750px;">
                                <input type="text" name="query" v-model="query" class="form-control float-right" placeholder="Search">
                                <div class="input-group-append">
                                    <button type="button" class="btn btn-default mr-1" @click="getInitials"><i class="fas fa-search"></i></button>
                                    <select class="form-control mr-1" name="type" id="type" v-model="status" @change="getInitials">
                                        <option value="">--Select Status--</option>
                                        <option value="0">Booked</option>
                                        <option value="1">Accepted</option>
                                        <option value="2">Started</option>
                                        <option value="4">Sample Collected</option>
                                        <option value="5">Ongoing</option>
                                        <option value="15">Referred Out</option>
                                        <option value="20">Confirmed</option>
                                        <option value="30">Completed</option>
                                        <option value="100">Cancelled</option>
                                    </select>
                                    <input type="date" class="form-control mr-1" v-model="start_date" />
                                    <input type="date" class="form-control mr-1" v-model="end_date" />
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0" style="height: 600px;">
                        <EMRLaboratoryDetailRequestList actionable="yes" :requests="requests.data" source="laboratory" />
                    </div>
                    <div class="card-footer">
                        <div class="col-12">
                            <pagination v-model="current_page" @paginate="getInitials" :per-page="requests.per_page != null ? requests.per_page : 52" :records="requests.total != null ? requests.total : 550" ></pagination>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<script>
import EMRLaboratoryDetailRequestList from '@/emr/laboratory/details/RequestList.vue'
export default {
    components:{EMRLaboratoryDetailRequestList},
    data() {
        return {
            current_page: 1,
            editMode: true,
            end_date: '',
            loading: false,
            query: '',
            request: {},
            requests: {data: [],total: 0,},
            start_date: '',
            status: '',
            type: '',
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        getInitials() {
            axios.get('/api/emr/laboratory/requests?end_date='+this.end_date+'&page='+this.current_page+'&query='+this.query+'&start_date='+this.start_date+'&status='+this.status)
            .then(response => {
                this.refreshQueue(response)
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Your appointments did not loaded successfully',})
            });
        },
        refreshQueue(response) {
            this.requests = response.data.requests;
        },
        updateRequest(request){
            this.request = request;
        }
    },
    props: {}
}
</script>