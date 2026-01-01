<template>
<section class="card">
    <div class="card-header">
        <h3 class="card-title">Loan Confirmation Timeline</h3>
    </div>
    <div class="card-body">
        <div class="timeline timeline-inverse">
            <div v-for="action in actions.data">
                <i class="fa fa-sticky-note"></i>
                <div class="timeline-item">
                    <span class="time"><i class="far fa-clock"></i> {{ action.created_at | excelDate }}</span>
                    <h3 class="timeline-header"><a href="#">{{ action.creator | FullName }}</a> sent {{ action.summary }}</h3>
                    <div class="timeline-body" v-if="action.description != null" v-html="action.description"></div>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return {
            account: {},
            actions: {},
            editMode: false,
            form: new Form({}),
        }
    },
    methods:{
        getAllInitials(){
            this.$Progress.start();
            axios.get('/api/loans/confirms/'+this.$route.params.id).then(response =>{
                this.reloadPage(response);
                this.$Progress.finish();
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Accounts was not loaded successfully',
                })
            });
        },
        reloadPage(response){
            this.account = response.data.account;
            this.actions = response.data.actions;
        }
    },
    mounted() {
        this.getAllInitials();
    },
}
</script>
