<template>
<section>
    <div class="card-header bg-navy">
        <h3 class="card-title" v-if="source == 'assigned'">My Assigned Tickets</h3>
        <h3 class="card-title" v-if="source == 'created'">My Created Tickets</h3>
    </div>
    <div class="card-body p-0">
        <div class="table-responsive p-0">
            <table class="table m-b-0">
                <thead class="thead">
                    <tr><th>S/N</th><th>Subject</th><th>Created By</th><th>Priority</th><th>Category</th><th>Assigned To</th><th>Status</th><th></th></tr>
                </thead>
                <tbody>
                    <tr v-for="(ticket, index) in tickets.data" :key="ticket.id" :class="ticket.status_id == 1 ? 'bg-warning disabled': (ticket.status_id == 2 ? 'bg-yellow disabled' : (ticket.status_id == 3 ? 'bg-purple disabled': 'bg-success disabled'))">
                        <td>{{index | addOne}}</td>
                        <td :title="ticket.subject">{{ readMore(ticket.subject, 40, '...')}}</td>
                        <td>{{ticket.creator.first_name}} {{ticket.creator.last_name}}</td>
                        <td>{{ticket.priority !== null ? ticket.priority.name : 'No Priority Chosen'}}</td>
                        <td>{{ticket.category !== null ? ticket.category.name : 'No Priority Chosen'}}</td>
                        <td>{{ticket.agent != null ? ticket.agent.first_name+' '+ticket.agent.last_name : 'Not Yet Assigned'}}</td>
                        <td>{{ticket.status != null ? ticket.status.name : 'No Status Assigned'}}</td>
                        <td>
                            <button class="nav-link btn btn-sm btn-default" data-toggle="dropdown" type="button">
                                <i class="fa fa-ellipsis-v"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" v-if="source == 'created'">
                                <router-link :to="'/ticketing/'+ticket.id" class="btn-success dropdown-item btn btn-block btn-sm"><i class="fa fa-eye"></i> View Ticket</router-link>
                                <button class="btn-sm btn-danger dropdown-item btn btn-block" @click="closeTicket(ticket.id)"><i class="fa fa-trash"></i> Close Ticket</button>
                            </div>         
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</section>
</template>
<script>
import Form from 'vform';
import Swal from 'sweetalert2/dist/sweetalert2.js';
import 'sweetalert2/src/sweetalert2.scss';
const toast = Swal.mixin({
    toast: true,
    position: "top-end",
    showConfirmButton: false,
    timer: 3000,
    timerProgressBar: true,
    didOpen: (toast) => {
        toast.onmouseenter = Swal.stopTimer;
        toast.onmouseleave = Swal.resumeTimer;
    }
});
export default {
    data(){
        return {
            by_tickets: {},
            categories: [], 
            current_page : 1,
            departments: [],editMode: false,
            form: new Form({}),
            loading: false,
            my_tickets: {},
            my_current_page: 1,
            priorities: [],
        }
    },
    methods:{
        closeTicket(id){
            Swal.fire({
                title: 'Are you sure?',
                text: "This ticket would only be closed, you can reopen by updating it!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
                })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.form.delete('/api/tickets/ticket/'+id)
                    .then(response=>{
                        this.ticketReload(response);
                        this.$Progress.finish();
                        Swal.fire('Deleted!', 'Ticket has been closed.', 'success');  
                    })
                    .catch(()=>{
                        Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            }); 
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/tickets/ticket/personal').then(response =>{
                this.by_tickets = response.data.by_tickets;
                this.my_tickets = response.data.my_tickets;
                this.priorities = response.data.priorities;    
                this.categories = response.data.categories;    
                this.priorities = response.data.priorities;    
                this.loading = false;
                toast.fire({
                    icon: 'success',
                    title: 'Tickets loaded successfully',
                });
            })
            .catch(()=>{
                this.loading = false;
                toast.fire({
                    icon: 'error',
                    title: 'Tickets were not loaded successfully',
                })
            });
        },
        getByTickets(page=1){
            axios.get('/api/tickets/ticket/personal?page='+page)
            .then(response=>{
                this.by_tickets = response.data.by_tickets;
            });
        },
        getMyTickets(page=1){
            axios.get('/api/tickets/ticket/personal?page='+page)
            .then(response=>{
                this.my_tickets = response.data.my_tickets;   
            });
        },
    },
    mounted() {
        //this.getAllInitials();
    },
    props:{
        source: String,
        tickets: Object,
    }
}
</script>