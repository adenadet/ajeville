<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    For each Value, show the critical values as well as auto highlight flags
</section>
</template>
<script>
export default {
    data() {
        return {
            bottle_types: [],
            categories: [],
            specimens: [],
            
            serviceForm: new Form({
                name: '',
                colour: '',
                size: '',
                id: '',
            }),
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        createResult(){
            this.loading = true;
            this.serviceForm.post('/api/emr/laboratory/results')
            .then(response => {
                
            })
            .catch(()=>{})
            .finally(()=>{
                this.loading = false;
            });
        },
        getInitials(page=1) {
            axios.get('/api/emr/laboratory/results/initials')
            .then(response => {
                this.refreshQueue(response)
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Your appointments did not loaded successfully',
                })
            });
        },
        refreshQueue(response) {
            this.requests = response.data.requests;
            this.request = response.data.requests.data[0]
        },
        updateResult(){
            this.request = request;
        }
    },
    props: {
        editMode: Boolean,
    }
}
</script>