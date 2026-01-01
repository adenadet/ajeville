<template>
<section class="overlay-wrapper p-0">
    
    <div class="card-body box-profile" v-if="bonus != null && bonus.employee != null">
        <div class="text-center">
            <img class="profile-user-img img-fluid img-circle" :src="'/img/profile/'+bonus.employee.user.image" :alt="FullName(bonus.employee.user)">
        </div>

        <h3 class="profile-username text-center">{{ FullName(bonus.employee.user) }}</h3>

        <p class="text-muted text-center">{{ bonus.employee.username }}</p>

        <ul class="list-group list-group-unbordered mb-3">
            <li class="list-group-item">
                <b>Name</b> <a class="float-right">{{ bonus.name }}</a>
            </li>
            <li class="list-group-item">
                <b>Amount</b> <a class="float-right">{{ currency(bonus.amount) }}</a>
            </li>
            <li class="list-group-item">
                <b>Month</b> <a class="float-right">{{ ExcelMonthYear(bonus.month) }}</a>
            </li>
        </ul>
    </div>
</section>
</template>
<script>
export default {
    computed:{
        today(){
            return new Date().toJSON().slice(0, 10);
        }
    },
    data() {
        return {
            loading: false,
        }
    },
    emits:['refreshBonusList'],
    mounted() {},
    methods: {
    },
    props: {
        bonus: Object,
    },
    watch:{
        bonus(){}
    },
}
</script>