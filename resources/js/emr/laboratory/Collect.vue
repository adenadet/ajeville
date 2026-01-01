<template>
    <section class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <HimsVisitSummary :visit="visit"/>
            </div>
            <div class="col-md-4">
                <LaboratoryDetailSummary :request="request" :show_status="false" :print_label="true"/>
            </div>
            <div class="col-md-4">
                <div class="card">
                    <div class="card-header">
                        <h3 class="card-title">Confirm Sample Collection</h3>
                    </div>
                    <form>
                    <div class="card-body">
                        <div class="form-group">
                            <label for="exampleInputEmail1">Bottle Information</label>
                            <div class="form-control">Put Bottle Type Here</div>
                        </div>
                        <div class="form-group">
                            <label for="exampleInputPassword1">Remark</label>
                            <wysiwyg v-model="collectionForm.remark" rows="5" required/>
                        </div>
                    </div>
                    <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                    </div>
            </form>
        </div>
            </div>
        </div>
    </section>
</template>
<script>
export default {
    data() {
        return {
            editMode: true,
            collectionForm: new Form({
                request_id: '',
                remark: '',
            }),
            requests: 0,
            request: {},
            visit: {},
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        getInitials(page=1) {
            axios.get('/api/emr/laboratory/requests/collect/'+this.$route.params.id)
            .then(response => {
                this.refreshDashboard(response)
            })
            .catch(() => {
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Your appointments did not loaded successfully',
                })
            });
        },

        refreshDashboard(response) {
            this.request = response.data.request;
            this.visit = response.data.visit;
        }
    },
    props: {}
}
</script>