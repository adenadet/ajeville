<template>
<section>
    Put all the content of a prescription here
</section>
</template>
<script>
import ComponentPrescriptionItemForm from '@/emr/consultant/components/PrescriptionItemForm.vue';

export default {
    components: { ComponentPrescriptionItemForm },
    data() {
        return {
            current_page: 1,
            drugForm: new Form({
                id: '',
                name: '',
                description:'',
                ham: '',
                status: 1,
                interactions: [],
            }), 
            query: '',
            type: 'active',
        }
    },
    emits:['refreshDrugForm'],
    mounted() {},
    methods: {
        createDrug(){
            this.loading = true;
            this.drugForm.post('/api/emr/pharmacy/drugs')
            .then( () =>{
                this.$emit('refreshDrugForm');
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Drug detail has been captured',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            })
            .finally(()=>{
                this.loading = false;    
            });
        },
        updateDrug(){
            this.loading = true;
            this.drugForm.put('/api/emr/pharmacy/drugs/'+this.drugForm.id)
            .then( () =>{
                this.$emit('refreshDrugForm');
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Drug detail has been updated',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            })
            .finally(()=>{
                this.loading = false;    
            });
        },
    },
    props: {
        drug: Object,
    },
    watch:{
        drug(){
            this.drugForm.fill(this.drug);
        }
    }
}
</script>