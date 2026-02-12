<template>
<section class="container-fluid">
    <div class="row">
        <div class="col-md-4">
            <VisitDetailSummary :visit="visit"/>
        </div>
        <div class="col-md-8">
            <VisitDetailTransactions source="finance" :visit="visit" :transactions="transactions"/> 
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
            visit: {},
            transactions: [],
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        getAllInitials(page=1) {
            this.$Progress.start();
            axios.get('/api/emr/hims/visits/'+this.$route.params.id+'?page='+page).then(response => {
                this.refresh(response);
                this.$Progress.finish();
            })
            .catch(() => {
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Visit Form was not loaded successfully',
                })
            });
        },
        removeItem(index){
            alert(index);
            this.InvestigationForm.investigations.splice(index, 1);
        },
        sortStaff(){},
        refresh(response) {
            this.transactions = response.data.transactions;
            this.visit = response.data.visit;
        },
    },
    props: {
        patient: Object,
        editMode: Boolean,
    }
}
</script>