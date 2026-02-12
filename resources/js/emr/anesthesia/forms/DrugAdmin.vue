<template>
<section class="overlay-wrapper">
    <div class="overlay dark"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form @submit.prevent="editMode ? updateDrugAdmin() :createDrugAdmin()">

    </form>
</section>
</template>
<script>
export default {
    data() {
        return {
            drugs: [],
            forms: [],
            drugAdminData: new Form({
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
    emits:[],
    mounted() {
        
    },
    methods: {
        async createDrugAdmin(){
            this.loading = true;
            this.drugAdminData.case_id = this.case.id;
            await this.drugAdminData.post('/api/emr/anesthesia/drug_admin')
            .then(()=>{})
            .catch(()=>{
                this.$toast.fire({
                    icon: 'error',
                    title: 'Anesthesia Drug  did not loaded successfully',
                })
            })
            .finally(()=>{
                this.loading = false;
            })
        },
        updateDrugAdmin(){
            this.loading = true;
            this.preOpData.put('/api/emr/anesthesia/drug_admin/'+this.drugAdminData.id)
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
        drug_admin: Object,
    },
    watch:{
        drug_admin(){
            if (this.drug_admin == null){
                this.drugAdminData.reset();
            }
            else{
                this.drugAdminData.fill(this.drug_admin);
            }
        },
    }
}
</script>