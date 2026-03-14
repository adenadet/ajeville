<template>
    Put Visit Detail here
</template>
<script>
export default {
    data() {
        return {
            loading: false,
        }
    },
    emits:['refreshLaboratoryRequestForm'],
    mounted() {},
    methods: {
        createRequest(){
            this.loading = true;
            this.requestData.post('/api/emr/laboratory/requests')
            .then(response => {
                this.$swal.fire('Created!', 'Request has been created.', 'success');
                this.$emit('refreshLaboratoryRequestForm')
            })
            .catch(()=>{
                this.$toast.fire({icon: 'error', title: 'Request was not created successfully',});
            })
            .finally(()=>{
                this.loading = false;
            })
        },
        updateRequest(){
            this.loading = true;
            this.requestData.put('/api/emr/laboratory/requests/'+this.requestData.id)
            .then(response => {
                this.$swal.fire('Updated!', 'Request has been updated.', 'success');
                this.$emit('refreshLaboratoryRequestForm')
            })
            .catch(()=>{
                this.$toast.fire({icon: 'error', title: 'Request was not updated successfully',});
            })
            .finally(()=>{
                this.loading = false;
            })
        }
    },
    props: {
        editMode: Boolean,
        request: Object,
    }
}
</script>