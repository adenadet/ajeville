<template>
<section class="container-fluid">
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Services</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 150px;">
                            <input type="text" name="table_search" class="form-control float-right" placeholder="Search" v-model="query">
                            <div class="input-group-append"><button type="button" class="btn btn-default" @click="getInitials"><i class="fas fa-search"></i></button></div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0 overlay-wrapper" style="height: 500px;">
                    <OperationDetailServiceList :services.sync="services.data" @refreshServices="getInitials"/>
                </div>
                <div class="card-footer">
                    <div class="col-12">
                        <pagination v-model="current_page" @paginate="getInitials" :per-page="services.per_page != null ? services.per_page : 52" :records="services.total != null ? services.total : 550" ></pagination>
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
            editMode: false,
            item: {},
            items: {data: [], total: 0, per_page: 20},
            loading: false,
            query: '',
            service: {},
            services: {data: [], total: 0, per_page: 20},
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        closeModals(){
            $('#priceListModal').modal('hide');
        },
        getInitials(){
            this.loading = true;
            axios.get('/api/operations/services?query='+this.query+'&page='+this.current_page)
            .then(response => {
                this.refreshPage(response);
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Services did not load successfully',
                })
            });
            this.loading = false;
        },
        refreshPage(response) {
            this.items = response.data.items;
            this.services = response.data.services;
        }
    },
}
</script>