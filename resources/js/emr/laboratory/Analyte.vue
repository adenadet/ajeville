<template>
<section class="overlay-wrapper p-0">
    <div class="row">
        <div class="col-md-4">
            <div class="card card-primary">
                <div class="card-header">
                    <h3 class="card-title">Analyte Details</h3>
                </div>
                <div class="card-body">
                    <strong><i class="fas fa-vials mr-1"></i> Name</strong>
                    <p class="text-muted" v-html="analyte.name"></p>
                    <hr>
                    <strong><i class="fas fa-map-marker-alt mr-1"></i> Default Unit</strong>
                    <p class="text-muted" v-html="analyte.default_unit"></p>
                    <hr>
                    <strong><i class="fas fa-map-marker-alt mr-1"></i> Input Type</strong>
                    <p class="text-muted" v-html="analyte.input_type"></p>
                    <hr v-if="analyte.input_type == 'select'">
                    <strong v-if="analyte.input_type == 'select'"><i class="fas fa-list mr-1"></i> Options</strong>

                    <p class="text-muted" v-if="analyte.input_type == 'select'">
                        <span class="tag tag-danger" v-for="o in analyte.options" :key="o">{{ o }}</span>
                    </p>

                    <hr>
                    <strong><i class="far fa-file-alt mr-1"></i> Description</strong>
                    <p class="text-muted" v-html="analyte.description"></p>
                </div>
            </div>
        </div>
        <div class="col-md-8">
            <EMRLaboratoryDetailReferenceRangeList :reference_ranges.sync="reference_ranges" :analyte.sync="analyte" @refreshReferenceRangesList="getAllInitials()"/>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            analyte: {},
            loading: false,
            reference_ranges: [],
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        closeModal(){
            $('#paymentModal').modal('hide');
        },
        getAllInitials(page=1) {
            this.loading = true;
            axios.get('/api/emr/laboratory/analytes/'+this.$route.params.id)
            .then(response => {
                this.refreshAnalyte(response);
                this.loading = false;
            })
            .catch(() => {
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Visit Form was not loaded successfully',
                })
            });
        },
        refreshAnalyte(response){
            this.closeModal();
            this.analyte = response.data.analyte;
            this.reference_ranges = response.data.reference_ranges;
        }
    },
}
</script>