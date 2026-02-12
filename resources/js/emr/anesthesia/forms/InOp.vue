<template>
<section class="overlay-wrapper">
    <div class="overlay dark"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form @submit.prevent="editMode ? updateInOP() :createInOp()">

    </form>
</section>
</template>
<script>

export default {
    name: 'PreOpAssessment',

    data() {
        return {
            drugs: [],
            forms: [],
            inOpData: new Form({
                case_id: '',
                start_time: '',
                end_time: '',
                airway_device: '',
                ventilation_mode: '',
                remarks: '',
                status: '',
                vital_signs: [],
                drug_admins: [],
            }),
            loading: false,
            routes: [],
        }
    },

    computed: {
        store() {return useAnesthesiaCaseStore()},
        isEditable() {return this.store.case?.status === 1}
    },
    emits:{},
    mounted() {
        if (this.store.preOp) {
            Object.assign(this.form, this.store.preOp)
        }
    },
    methods: {
        async createInOp(){
            this.loading = true;
            this.inOpData.case_id = this.case.id;
            await this.inOpData.post('/api/emr/anesthesia/in_ops')
            .then(()=>{})
            .catch(()=>{
                this.$toast.fire({
                    icon: 'error',
                    title: 'Anesthesia Cass did not loaded successfully',
                })
            })
            .finally(()=>{
                this.loading = false;
            })
        },
        updateInOp(){
            this.loading = true;
            this.preOpData.put()
            .then(()=>{

            })
            .catch(()=>{})
            .fially(()=>{
                this.loading = false;
            })
        },
        async save() {
            await axios.post(
                `/api/emr/anesthesia/cases/${this.store.case.id}/pre-op`,
                this.form
            )
        },

        async clear() {
            await axios.post(
                `/api/emr/anesthesia/cases/${this.store.case.id}/pre-op/clear`
            )
        }
    },
    props:{
        case: Object,
        editMode: Boolean,
        preOp: Object,
    },
    watch:{
        preOp(){
            if (this.inOp == null){
                this.inOpData.reset();
            }
            else{
                this.inOpData.fill(this.inOp);
            }
        },
    }
}
</script>