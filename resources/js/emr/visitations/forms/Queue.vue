<template>
    <section class="container-fluid overlay-wrapper">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <div class="row">
            <div class="col-md-4">
                <VisitDetailSummary />
            </div>
            <div class="col-md-8">
                <div class="row">
                    <div class="col-sm-12">
                        <div class="form-group">
                            <label>Select Queue</label>
                            <select class="form-control" name="queue_type" id="queue_type" v-model="queue_type">
                                <option value="">--Select Visit Type--</option>
                                <option v-for="visit_type in visit_types" :key="visit_type.id" :value="visit_type.id">{{ visit_type.name }}</option>
                            </select>
                        </div>
                    </div>
                    <VisitFormAdmission v-if="queue_type == 3"/>
                    <VisitFormConsultation  v-if="queue_type == 4"/>
                    <VisitFormEmergency  v-if="queue_type == 11"/>
                    <VisitFormLaboratory  v-if="queue_type == 6"/>
                    <VisitFormPharmacy  v-if="queue_type == 2"/>
                    <VisitFormRadiology  v-if="queue_type == 7"/>
                </div>   
            </div>
        </div>
    </section>
</template>
<script>
import { ModelListSelect } from 'vue-search-select';

export default {
    components: {
        ModelListSelect
    },
    data() {
        return {
            queue_type: '',
            loading: false,
        }
    },
    mounted() {
        //this.getAllInitials();
    },
    methods: {

    },
    props: {
        editMode: Boolean,
        visit_types: Array,
        visit: Object,
    }
}
</script>