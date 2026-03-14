<template>
    <section class="container-fluid overlay-wrapper">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h3 class="card-title">Laboratory Queue</h3>
                        <div class="card-tools">
                            <div class="input-group" style="width: 650px;">
                                <input type="text" name="query" v-model="query" class="form-control float-right" placeholder="Search">
                                <div class="input-group-append">
                                    <button type="submit" class="btn btn-default mr-1"><i class="fas fa-search"></i></button>
                                    <select class="form-control mr-1" name="status" id="status" v-model="status">
                                        <option value="unpaid">Unpaid</option>
                                        <option value="uncollected">Uncollected</option>
                                        <option value="awaiting">Awaiting Results</option>
                                        <option value="reffered_out">Reffered Out</option>
                                        <option value="reffered_in">Reffered In</option>
                                        <option value="completed">Completed</option>
                                    </select>
                                    <input type="date" class="form-control mr-1" v-model="start_date" /> -
                                    <input type="date" class="form-control" v-model="end_date" />
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
export default {
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
            status: "uncollected",
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        getInitials() {
            axios.get('/api/emr/laboratory/requests?page='+this.current_page+'&status=referred_in')
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