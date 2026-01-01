<template>
<section class="overlay-wrapper">
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title">Price Lists</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 150px;">
                            <input type="text" name="table_search" class="form-control float-right" placeholder="Search">
                            <div class="input-group-append"><button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button></div>
                        </div>
                    </div>
                </div>
                <OperationDetailPricelistList :price_lists.sync="price_lists.data" source="operations" />
                <div class="card-footer">
                    <div class="col-12">
                        <pagination v-model="current_page" @paginate="getInitials" :per-page="price_lists.per_page != null ? price_lists.per_page : 52" :records="price_lists.total != null ? price_lists.total : 550" ></pagination>
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
            categories: [],
            current_page: 1,
            invoices: {},
            jsonData: [],
            loading: false,
            patients: [],
            price_lists: {},
            price_list:{},
            pending_invoices: {},
            searchData: new Form({
                branch_id: '',
                service_type_id: '',
                category_id: ''
            }),
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        getInitials() {
            axios.get('/api/operations/price_lists').then(response => {
                this.refreshPage(response);
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'The price list did not load successfully',});
            });
        },
        refreshPage(response) {
            this.price_lists = response.data.price_lists;
        },
        updatePlan(){},
    },
    props: {}
}
</script>