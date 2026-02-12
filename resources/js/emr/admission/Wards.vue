<template>
<section class="">
    <div class="container-fluid">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Wards</h3>
                        <div class="card-tools">
                            <div class="input-group" style="width: 300px;">
                                <input type="text" name="table_search" class="form-control float-right" placeholder="Search">
                                <div class="input-group-append">
                                    <select class="form-control ml-1 mr-1" v-model="type">
                                        <option value="all">All</option>
                                        <option value="active">Active</option>
                                        <option value="inactive">Inactive</option>
                                    </select>
                                    <button type="button" class="btn btn-default mr-1" @click="getAllInitials"><i class="fas fa-search"></i></button>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body table-responsive p-0" style="height: 500px">
                        <EMRAdmissionDetailWardList :wards="wards.data" @refreshWardList="getAllInitials" />
                    </div>
                    <div class="card-footer">
                        <pagination v-model="current_page" @paginate="getAllInitials" :per-page="wards.per_page != null ? wards.per_page : 52" :records="wards.total != null ? wards.total : 550" ></pagination>
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
            loading: false,
            query: '',
            type: 1,
            ward: {},
            wards: {total: 0, data: []},
        }
    },
    methods: {
        addWard(){
            this.editMode = false;
            this.loading = true;
            this.ward = {};
            $('#wardFormModal').modal('show');
            this.loading = false;
        },
        closeModals(){
            $('#wardFormModal').modal('hide');
        },
        deactivateWard(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, cancel it!'
            })
            .then((result) => {
                if (result.isConfirmed) {
                    axios.delete('/api/emr/admissions/wards/'+id)
                    .then(() => {
                        this.$swal.fire({ icon: 'success', title: 'The Ward has been cancelled', showConfirmButton: false, timer: 1500 });
                        this.getAllInitials();
                    })
                    .catch(() => {
                        this.$swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!' });
                    })
                }
            })
        },
        getAllInitials(){
            this.loading = true;
            this.closeModals();
            axios.get('/api/emr/admissions/wards?type='+this.type+'&query='+this.query)
            .then(res => {
                this.wards = res.data.wards;
            })
            .finally(() => {
                this.loading = false
            })
        },
        updateWard(ward){
            this.editMode = true;
            this.loading = true;
            this.ward = ward;
            $('#wardFormModal').modal('show');
            this.loading = false;
        },
    },
    mounted() {
        this.getAllInitials()
    },
}
</script>