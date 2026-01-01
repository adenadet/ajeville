<template>
    <div class="card">
        <div class="card-header bg-dark"><h3 class="card-title">Birthdays</h3></div>
        <div class="card-body overlay-wrapper p-0">
            <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
            <ul class="users-list clearfix">
                <li v-for="user in birthdays" :key="user.id">
                    <router-link :to="'/contacts/'+user.id">
                        <img style="height: 50px;" :src="(user.image) ? '/img/profile/'+user.image : '/img/profile/default.png'" :alt="user ? user.first_name+' '+user.middle_name+' '+user.last_name : 'Default Image' " :title="user ? user.first_name+' '+user.middle_name+' '+user.last_name : 'Celebrant\'s  Image' ">
                        <span class="users-list-name" href="#">{{user.first_name}} {{user.last_name}}</span>
                        <span class="users-list-date">{{ExcelDateMonth(user.dob)}}</span>
                    </router-link>
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
            loading: false,
        }
    },
    methods:{
        getAllInitials(){
            this.loading = true;
            axios.get('/api/ums/birthdays')
            .then(response =>{
                this.birthdays      = response.data.birthdays;
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
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