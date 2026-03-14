<template>
<section>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-info">
                    <h3 class="card-title">Drugs</h3>
                    <div class="card-tools">
                        <div class="input-group" style="width: 450px;">
                            <input type="text" name="table_search" class="form-control float-right" placeholder="Search">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-default mr-1"><i class="fas fa-search"></i></button>
                                <select class="form-control" v-model="type">
                                    <option value="all">All</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0" style="height:600px;">
                    <EMRPharmacyDetailDrugsList :drugs.sync="drugs.data" @refreshDrugList="getAllInitials()" />
                </div>
                <div class="card-footer">
                    <pagination v-model="current_page" @paginate="getAllInitials" :per-page="drugs.per_page != null ? drugs.per_page : 52" :records="drugs.total != null ? drugs.total : 550" >
                    </pagination>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
import EMRPharmacyDetailDrugsList from '@/emr/pharmacy/details/DrugsList.vue';
//import EMRPharmacyDetailDrugsList from '@/emr/pharmacy/details/DrugsList.vue';
export default {
    components:{
        EMRPharmacyDetailDrugsList,
    },
    data() {
        return {
            current_page: 1,
            drugs: {data: [], total: 0,},
            query: '',
            type: 'active',
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        getAllInitials() {
            this.loading = true;
            axios.get('/api/emr/pharmacy/drugs?page='+this.current_page+'&query='+this.query+'&type='+this.type)
            .then(response => {
                this.refreshDashboard(response)
            })
            .catch(() => {
                this.$swal.fire({
                    icon: 'error',
                    title: 'Your appointments did not loaded successfully',
                })
            })
            .finally(()=>{
                this.loading = false;
            });
        },
        refreshDashboard(response) {
            this.drugs = response.data.drugs;
        }
    },
    props: {}
}
</script>