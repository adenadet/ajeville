<template>
    <section class="content">
        <div class="container-fluid">            
            <div class="row">
                <h2 class="mb-4">Welcome, {{ user.name }}</h2>
                <section class="col-lg-8 connectedSortable">
                    <div class="row">
                        <div v-for="module in user.modules" :key="module.name" class="col-md-4 mb-4">
                            <div class="card shadow-sm h-100">
                                <div class="card-body text-center">
                                    <i :class="module.icon" style="font-size: 2rem;"></i>
                                    <h5 class="card-title mt-3">{{ module.name }}</h5>
                                    <p class="card-text">{{ module.description }}</p>
                                    <a :href="module.link" class="btn btn-primary btn-sm">Go to {{ module.name }}</a>
                                </div>
                            </div>
                        </div>
                    </div>
                </section>
                <section class="col-lg-4 connectedSortable">
                    <UserBirthday :birthdays="birthdays"/> 
                    <!--StaffMonthLatest :items="staff_months"/-->
                </section>
            </div>
        </div>
    </section>
</template>
<script>
import moment from 'moment';
import UserBirthday from '@/users/Birthday.vue';
export default {
    components: {
        UserBirthday,
    },
    data(){
        return {
            birthdays: [],
            contacts: [],
            editMode: false,
            messages: [],
            message_rooms: [],
            month: '',
            modules: [],
            new_staffs: [],
            notices: {},
            staff_months: [],
            tickets: {},   
            settings: {
                suppressScrollY: false,
                suppressScrollX: false,
                wheelPropagation: false
            },
            user: {},
        }
    },
    methods:{
        getAllInitials(){
            axios.get('/api/dashboard')
            .then(response =>{
                this.birthdays      = response.data.birthdays;
                this.contacts       = response.data.contacts;
                this.chats          = response.data.chats;
                this.messages       = response.data.messages;
                this.message_rooms  = response.data.chats;
                this.notices        = response.data.notices;
                this.new_staffs     = response.data.new_staffs;
                this.tickets        = response.data.tickets;
                this.staff_months   = response.data.staff_months;
                this.user           = response.data.user;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'Dashboard not loaded successfully',});
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