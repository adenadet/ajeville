<template>
<section class="overlay-wrapper p-0">
    <div class="modal fade" id="priceListModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Price List Form</h4>
                    <button type="button" class="close" data-dismiss="modal" @click="closeModals" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <FinanceFormPricelist :price_list.sync="price_list" :editMode.sync="editMode" @refreshPriceListForm="getInitials" />
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header">
            <h4 class="card-title">Price Lists</h4>
            <div class="card-tools">
                <button class="btn btn-sm btn-success" @click="addPriceList" :disabled="loading">
                    <i class="fas fa-plus mr-1"></i> Add Price List
                </button>
            </div>
        </div>
        <div class="card-body p-0 table-responsive">
            <FinanceDetailPricelistList :price_lists="price_lists.data" @refreshPriceLists="getInitials" />
        </div>
        <div class="card-footer">
            <pagination v-model="current_page" @paginate="getInitials" :per-page="price_lists.per_page != null ? price_lists.per_page : 52" :records="price_lists.total != null ? price_lists.total : 550" ></pagination>
        </div>
    </div>
</section>
</template>
<script>
import FinanceFormPricelist from '@/finance/forms/PriceList.vue';
import FinanceDetailPricelistList from '@/finance/details/PricelistList.vue';
export default {
    components:{FinanceDetailPricelistList, FinanceFormPricelist},
    data() {
        return {
            current_page: 1,
            editMode: false,
            loading: false,
            price_list: {},
            price_lists: {data: [], total: 0, per_page: 20},
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        addPriceList(){
            this.loading = true;
            this.editMode = false;
            this.price_list = {};
            $('#priceListModal').modal('show');
            this.loading = false;
        },
        closeModals(){
            $('#priceListModal').modal('hide');
        },
        getInitials(page=1) {
            this.loading = true;
            axios.get('/api/finance/price_lists?page='+page)
            .then(response => {
                this.refreshPage(response);
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Your appointments did not loaded successfully',
                })
            });
            this.loading = false;
        },
        refreshPage(response) {
            this.price_lists = response.data.price_lists;
        }
    },
}
</script>