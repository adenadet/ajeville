<template>
    <div class="card card-success">
        <div class="card-header"><h3 class="card-title">Birthdays</h3></div>
        <div class="card-body p-0">
            <ul class="users-list clearfix">
                <li v-for="user in birthdays" :key="user.id">
                    <img style="height: 50px;" :src="(user.image) ? '/img/profile/'+user.image : '/img/profile/default.png'" :alt="user ? user.first_name+' '+user.middle_name+' '+user.last_name : 'Default Image' " :title="user ? user.first_name+' '+user.middle_name+' '+user.last_name : 'Celebrant\'s  Image' ">
                    <router-link :to="'/users/staff/'+user.id" class="users-list-name" href="#">{{user.first_name}} {{user.last_name}}</router-link>
                    <span class="users-list-date">{{ExcelDateMonth(user.dob) }}</span>
                </li>
            </ul>
        </div>
        <div class="card-footer text-center">
            <router-link to="/birthdays">See More</router-link>
        </div>
    </div>
</template>
<script>
export default {
    data(){
        return {
            birthdays: [],
        }
    },
    methods:{
        getAllInitials(){
            axios.get('/api/ums/birthdays/latest')
            .then(response =>{
                this.birthdays      = response.data.birthdays;
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({icon: 'error', title: 'Dashboard not loaded successfully',});
            });
        },
        scrollHanle(evt) {
            console.log(evt)
        },
    },
    mounted() {
        this.getAllInitials();
    }
}
</script>
<style >
.scroll-area {
    position: relative;
    margin: auto;
    width: 600px;
    height: 400px;
}
</style>