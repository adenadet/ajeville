<template>
<section class="container-fluid overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title">Anesthesia Cases</h3>
                    <div class="card-tools">
                        <div class="input-group" style="width: 650px;">
                            <input type="text" name="table_search" class="form-control float-right" placeholder="Search" v-model="query">
                            <div class="input-group-append">
                                <input type="date" class="form-control ml-1" v-model="start_date" placeholder="Start Date"/>
                                <input type="date" class="form-control ml-1" v-model="end_date" placeholder="End Date"/>
                                <select class="form-control ml-1" v-model="status">
                                    <option value="">--Select Status--</option>
                                    <option value="1">Requested</option>
                                    <option value="5">Assessed</option>
                                    <option value="20">Cleared</option>
                                    <option value="40">In Progress</option>
                                    <option value="100">Completed</option>
                                    <option value="500">Signed Off</option>
                                </select>
                                <button type="button" class="ml-1 btn btn-default" @click="getAllInitials"><i class="fas fa-search"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0" style="height:600px;">
                    <EMRAnesthesistDetailCaseList :cases="cases.data" />
                </div>
                <div class="card-footer">
                    <pagination v-model="current_page" @paginate="getAllInitials" :per-page="cases.per_page != null ? cases.per_page : 52" :records="cases.total != null ? cases.total : 550" ></pagination>
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
            cases: {data:[], total: 0,},
            current_page: 1,
            end_date: '',
            loading: false,
            query: '',
            start_date: '',
            status: '',
        }
    },
    methods: {
        async getAllInitials() {
            this.loading = true;
            axios.get('/api/emr/anesthesia/cases?end_date='+this.end_date+'&page='+this.current_page+'&query='+this.query+'&start_date='+this.start_date+'&status='+this.status)
            .then(response => {
                this.cases = response.data.cases;
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Anesthesia Cass did not loaded successfully',
                })
            })
            .finally(()=>{
                this.loading = false;
            });
        }
    },
    mounted() {
        this.getAllInitials()
    },
}
</script>
