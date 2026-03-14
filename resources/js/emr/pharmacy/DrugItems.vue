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
                                <select class="form-control" v-model="type" @change="getAllInitials">
                                    <option value="all">All</option>
                                    <option value="active">Active</option>
                                    <option value="inactive">Inactive</option>
                                </select>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0" style="height:600px;">
                    <EMRPharmacyDetailDrugItemList :drug_forms.sync="drug_items.data" @refreshDrugItemList="getAllInitials()" />
                </div>
                <div class="card-footer">
                    <pagination v-model="current_page" @paginate="getAllInitials" :per-page="drug_items.per_page != null ? drug_items.per_page : 52" :records="drug_items.total != null ? drug_items.total : 550" >
                    </pagination>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
import EMRPharmacyDetailDrugItemList from '@/emr/pharmacy/details/DrugItemList.vue';
export default {
    components:{
        EMRPharmacyDetailDrugItemList,
    },
    data() {
        return {
            current_page: 1,
            drug_items: {data: [], total: 0,},
            loading: false,
            query: '',
            type: 'all',
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        getAllInitials() {
            this.loading = true;
            axios.get('/api/emr/pharmacy/drug_items?page='+this.current_page+'&query='+this.query+'&type='+this.type)
            .then(response => {
                this.refreshDashboard(response)
            })
            .catch(() => {
                this.$swal.fire({
                    icon: 'error',
                    title: 'Drug Item did not loaded successfully',
                })
            })
            .finally(()=>{
                this.loading = false;
            });
        },
        refreshDashboard(response) {
            this.drug_items = response.data.drug_items;
        }
    },
}
</script>