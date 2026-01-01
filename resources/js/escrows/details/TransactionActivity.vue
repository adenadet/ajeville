<template>
    <div class="row">
        <div class="col-md-12">
            <div class="timeline">
                <div v-for="activity in activities" :key="activity.id">
                    <i class="fas fa-tag bg-blue"></i>
                    <div class="timeline-item">
                        <span class="time"><i class="fas fa-clock"></i> {{ dateFrom(activity.created_at) }}</span>
                        <h3 class="timeline-header"><a href="#">{{FullName(activity.user)}}</a> {{activity.subject}}</h3>
                        <div class="timeline-body" v-html="activity.details"></div>
                    </div>
                </div>
            </div>    
        </div>
    </div>
</template>
<script>
export default {
    data(){
        return  {
            current_page: 1,
            editMode: false,
            loading: false,
            transactions: { data: []},
            transaction: {},
            date: '2025-03-12 11:30:23',
        }
    },
    mounted() {
        //this.getAllInitials();
    },
    methods:{
        closeModals(){
            $('#transactionModal').modal('hide');
        },
        editTransaction(){
            this.loading = true;
            this.editMode = false;
            this.transaction = {};
            $('#transactionModal').modal('show');
            this.loading = false;  
        },
    },
    props:{
        activities: Array,
    },
    watch:{
        activities(){
            this.loading = true;
            this.loading = false;
        }
    }
}
</script>

