<template>
    <section class="overlay-wrapper">
        <div class="overlay dark"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <div class="direct-chat-contacts">
            <ul class="contacts-list">
                <li v-for="room in rooms" :key="room.id">
                    <a :href="'/chat/rooms/'+room.id">
                        <img class="contacts-list-img" :src="'dist/img/user1-128x128.jpg'">
                        <div class="contacts-list-info">
                            <span class="contacts-list-name">Count Dracula <small class="contacts-list-date float-right">2/28/2015</small></span>
                            <span class="contacts-list-msg">How have you been? I was...</span>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <img class="contacts-list-img" :src="'dist/img/user7-128x128.jpg'">
                        <div class="contacts-list-info">
                            <span class="contacts-list-name">Sarah Doe<small class="contacts-list-date float-right">2/23/2015</small></span>
                            <span class="contacts-list-msg">I will be waiting for...</span>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <img class="contacts-list-img" :src="'dist/img/user3-128x128.jpg'">

                        <div class="contacts-list-info">
                            <span class="contacts-list-name">
                            Nadia Jolie
                            <small class="contacts-list-date float-right">2/20/2015</small>
                            </span>
                            <span class="contacts-list-msg">I'll call you back at...</span>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <img class="contacts-list-img" :src="'dist/img/user5-128x128.jpg'">

                        <div class="contacts-list-info">
                            <span class="contacts-list-name">
                            Nora S. Vans
                            <small class="contacts-list-date float-right">2/10/2015</small>
                            </span>
                            <span class="contacts-list-msg">Where is your new...</span>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <img class="contacts-list-img" :src="'dist/img/user6-128x128.jpg'">

                        <div class="contacts-list-info">
                            <span class="contacts-list-name">
                            John K.
                            <small class="contacts-list-date float-right">1/27/2015</small>
                            </span>
                            <span class="contacts-list-msg">Can I take a look at...</span>
                        </div>
                    </a>
                </li>
                <li>
                    <a href="#">
                        <img class="contacts-list-img" :src="'dist/img/user8-128x128.jpg'">

                        <div class="contacts-list-info">
                            <span class="contacts-list-name">
                            Kenneth M.
                            <small class="contacts-list-date float-right">1/4/2015</small>
                            </span>
                            <span class="contacts-list-msg">Never mind I found...</span>
                        </div>
                    </a>
                </li>
            </ul>
        </div>
    </section>
</template>
<script>
import moment from 'moment'
export default {
    data(){
        return {
            contacts: [],
            editMode: false,
            messages: [],
            //rooms: [],
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
                this.contacts       = response.data.contacts;
                this.chats          = response.data.chats;
                this.messages       = response.data.messages;
                this.message_rooms  = response.data.chats;
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
    },
    props:{
        rooms: Array,
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