<template>
    <section class="container-fluid overlay-wrapper">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <div class="row">
            <div class="col-md-9">
                <div class="card">
                    <div class="card-header bg-dark">
                        <h4 class="card-title mb-sm-0">&nbsp;</h4>
                        <div class="card-tools">
                            <button @click="updateDrugs()" class="btn btn-xs">Save</button>
                        </div>
                    </div>
                    <div class="card-body p-0">
                        <div class="row">
                            <div class="col-md-12">
                                <vue-excel-editor v-model="DrugData.drugs">
                                    <vue-excel-column field="name"             label="Drug Name"       type="string" />
                                    <vue-excel-column field="ham"              label="High Alert Med"  type="select" :options="['Yes','No']" />
                                    <vue-excel-column field="description"      label="Description"     type="string" />
                                    <vue-excel-column field="status"           label="Status"          type="select" :options="['Inactive','Active']" />
                                </vue-excel-editor>
                            </div>
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
            editMode: false,
            loading: false,
            DrugData: new Form({
                drugs: [],
            })
        }
    },
    mounted() {
        this.getAllInitials();
        Fire.$on('ServiceFinderExtract', data => {
            this.loading = true;
            axios.put('/api/pharmacy/drugs')
            .then(response => {
                this.refresh(response);
                this.loading = false;
            })
        })
    },
    methods: {
        getAllInitials() {
            this.loading = true;
            this.$Progress.start();
            axios.get('/api/emr/pharmacy/drugs')
            .then(response => {
                this.refresh(response);
                this.loading = false;
                this.$Progress.finish();
            })
            .catch(() => {
                this.$Progress.fail();
                this.loading = false;
                toast.fire({icon: 'error', title: 'Price List were not loaded successfully',});
            });
        },
        refresh(response) {
            this.DrugData.drugs = response.data.drugs;
        },
        updateDrugs() {
            this.$Progress.start();
            this.loading = true;
            this.DrugData.post('/api/emr/pharmacy/drugs/all')
            .then(response => {
                this.refresh(response);
                this.loading = false;
                Swal.fire({
                    icon: 'success',
                    title: 'The Drugs have been updated',
                    showConfirmButton: false,
                    timer: 1500
                });
                this.$Progress.finish();
             })
            .close(() => {
                this.$Progress.fail();
                this.loading = false;
                toast.fire({
                    icon: 'error',
                    title: 'Price List was not updated successfully',
                })
            });
        },

    },
}
</script>