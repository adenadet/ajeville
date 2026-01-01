<template>
<section class="content">
    <div class="container-fluid overlay-wrapper"> 
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>           
        <div class="row">
            <section class="col-lg-8 connectedSortable">
                <div class="row">
                    <div class="col-md-6">
                        <LoanDetailCustomerKYC />
                    </div>
                    <div class="col-md-6">
                        <TicketDashboard :tickets="tickets" />
                    </div>
                    <div class="col-md-12">
                        <LoanAccounts />
                    </div>
                </div>
            </section>
            <section class="col-lg-4 connectedSortable">
                <div class="card">
                    <div class="card-header border-0 bg-dark">
                        <h3 class="card-title">Recent Activities</h3>
                        <!--div class="card-tools">
                        <a href="#" class="btn btn-tool btn-sm">
                            <i class="fas fa-download"></i>
                        </a>
                        <a href="#" class="btn btn-tool btn-sm">
                            <i class="fas fa-bars"></i>
                        </a>
                        </div-->
                    </div>
                    <div class="card-body table-responsive p-0">
                        <table class="table table-striped">
                            <thead class="bg-green"><tr><th></th><th>Activity</th><th>Date</th></tr></thead>
                            <tbody>
                                <tr v-for="activity in activities" :key="activity.id">
                                    <td><img class="img-circle img-size-32 mr-2" :src="(activity.user.image) ? '/img/profile/'+activity.user.image : '/img/profile/default.png'" :alt="activity.user ? activity.user.first_name+' '+activity.user.middle_name+' '+activity.user.last_name : 'Default Image' " :title="activity.user ? activity.user.first_name+' '+activity.user.middle_name+' '+activity.user.last_name : 'User\'s  Image' "><br />{{FullName(activity.user) }}</td>
                                    <td>{{activity.subject}}</td>
                                    <td><span class="fs-14">{{ ExcelDate(activity.created_at)  }}</span></td>
                                </tr>
                            </tbody>
                        </table>
                    </div>
                </div>
            </section>
        </div>
    </div>
</section>
</template>
<script>
import moment from 'moment'
export default {
    data(){
        return {
            activities: [],
            birthdays: [],
            contacts: [],
            editMode: false,
            loading: false,
            loans: {},
            messages: [],
            message_rooms: [],
            month: '',
            new_staffs: [],
            notices: {},
            staff_months: [],
            tickets: {},   
            settings: {
                suppressScrollY: false,
                suppressScrollX: false,
                wheelPropagation: false
            },
        }
    },
    methods:{
        getAllInitials(){
            this.loading = true;
            axios.get('/api/loans/dashboard')
            .then(response =>{
                this.birthdays      = response.data.birthdays;
                this.contacts       = response.data.contacts;
                this.chats          = response.data.chats;
                this.loading        = false;
                this.loans          = response.data.loans;
                this.messages       = response.data.messages;
                this.message_rooms  = response.data.chats;
                this.notices        = response.data.notices;
                this.new_staffs     = response.data.new_staffs;
                this.tickets        = response.data.tickets;
                this.staff_months   = response.data.staff_months;
                this.activities     = response.data.activities;

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