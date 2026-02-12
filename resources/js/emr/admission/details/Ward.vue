<template>
<section>
    <div class="card card-primary card-outline">
        <div class="card-body box-profile">
            <h3 class="profile-username text-center">{{ ward.name }}</h3>
            <p class="text-muted text-center">{{ ward.branch != null ? ward.branch.name : 'No Branch Assigned' }}</p>

            <ul class="list-group list-group-unbordered mb-3">
                <li class="list-group-item"><b>Rooms</b> <a class="float-right">1,322</a></li>
                <li class="list-group-item"><b>Beds</b> <a class="float-right">543</a></li>
                <li class="list-group-item"><b>Occupied</b> <a class="float-right">13,287</a></li>
            </ul>
        </div>
    </div>
</section>
</template>
<script>
export default { 
    data() {
        return {
            loading:false,
        }
    },
    mounted() {
        
    },
    methods: {
        
    },
    props: {
        ward: Object
    },
    watch: {}

}
</script>
