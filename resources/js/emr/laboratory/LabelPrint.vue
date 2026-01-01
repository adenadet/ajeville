<template>
    <section class="container-fluid">
        <div class="row">
        <div class="col-md-12">
            <div class="card">        
                <div class="card-body table-responsive p-0">                
                    <table class="table table-bordered table-hover table-stripped">
                        <tbody>
                            <tr>
                                <td colspan="3">{{ request.patient | patientName}}</td>
                            </tr>
                            <tr>
                                <td>Item</td>
                                <td colspan="2">{{ request.item.name}}</td>
                            </tr>
                            <tr>
                                <td>Unique ID</td>
                                <td colspan="2">{{ request.unique_id}}</td>
                            </tr>
                            <tr>
                                <td colspan="2">
                                    MTV Logo
                                </td>
                                <td>
                                    <barcode :value="request.unique_id">Show</barcode>
                                </td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    </section>
</template>
<script>
import VueBarcode from 'vue-barcode';
export default {
    components: {
        'barcode': VueBarcode
    },
    data() {
        return {
            editMode: true,
            request: {},
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        getInitials(page=1) {
            axios.get('/api/emr/laboratory/requests/'+this.$route.params.id)
            .then(response => {
                this.refreshDashboard(response)
            })
            .catch(() => {
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Your requests did not loaded successfully',
                })
            });
        },

        refreshDashboard(response) {
            this.request = response.data.request;
        }
    },
    props: {}
}
</script>