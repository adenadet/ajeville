<template>
<section class="row">
    <div class="col-md-12">
        <div class="card">
            <div class="card-header bg-dark">
                <h3 class="card-title">Batches</h3>
                <div class="card-tools">
                    <div class="input-group input-group" style="width: 450px;">
                        <input type="text" name="table_search" class="form-control float-right" placeholder="Search" v-model="query">
                        <div class="input-group-append">
                            <button type="button" class="btn btn-primary mr-1" @click="searchBatch"><i class="fas fa-search"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-body table-responsive p-0" style="height:500px;">
                <ProcurementDetailBatchList :batches="batches.data" source="approvals" @refreshBatchList="getInitials"/>
            </div>
            <div class="card-footer">
                <div class="col-12">
                    <pagination v-model="current_page" @paginate="getInitials" :per-page="batches.per_page != null ? batches.per_page : 52" :records="batches.total != null ? batches.total : 550" ></pagination>
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
            form: new Form({}),
            batches: { data: []},
            query: '',
            source: 'all',
            status: 1,
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        closeModals() {
            $('#storeModal').modal('hide');
        },
        deleteBatch(id) {
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            })
            .then((result) => {
                if (result.value) {
                    this.form.delete('/api/procurement/batches/' + id)
                        .then(response => {
                            this.$emit('storeReload', response);
                            this.$swal.fire('Deleted!', 'Category has been deleted.', 'success');
                        })
                        .catch(() => {
                            this.$swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>' });
                        });
                }
            });
        },
        getInitials(page = 1) {
            this.loading = true //.start();
            axios.get('/api/procurement/batches?page='+page+'&status=unapproved')
                .then(response => {
                    this.refreshPage(response);
                    this.loading = false; //.finish();
                    this.$toast.fire({
                        icon: 'success',
                        title: 'Batches loaded successfully',
                    });
                })
                .catch(() => {
                    this.loading = false; //.fail();
                    this.$toast.fire({
                        icon: 'error',
                        title: 'Batches not loaded successfully',
                    })
                });
        },
        refreshPage(response) {
            this.batches = response.data.batches;
            this.closeModals();
        },
        searchBatch(){
            this.loading = true;

            
            this.loading = false;
        }
    },
}
</script>