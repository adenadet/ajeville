<template>
<section class="content-header p-0 pl-2">
<div class="row justify-content-center">
    <div class="col-md-12">
        <TicketPersonalSummary />
        <div class="card">
            <div class="card-header bg-navy">
                <ul class="nav nav-pills">
                <li class="nav-item"><a class="nav-link active text-white" href="#created" data-toggle="tab">Created Tickets</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="#assigned" data-toggle="tab">Assigned Tickets</a></li>
                <li class="nav-item"><a class="nav-link text-white" href="#newTicket" data-toggle="tab">Create New Tickets</a></li>
                </ul>
            </div>
            <div class="card-body">
                <div class="tab-content">
                    <div class="tab-pane active" id="created">
                        <div class="card">
                            <TicketDetailList :tickets="by_tickets" source="created" />
                            <div class="card-footer">
                                <pagination v-model="current_page" @paginate="getByTickets" :per-page="by_tickets.per_page != null ? by_tickets.per_page : 52" :records="by_tickets.total != null ? by_tickets.total : 550" >
                                </pagination>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="assigned">
                        <div class="card">
                            <TicketDetailList :tickets="my_tickets" source="assigned" />
                            <div class="card-footer">
                                <pagination v-model="my_current_page" @paginate="getByTickets" :per-page="my_tickets.per_page != null ? my_tickets.per_page : 52" :records="my_tickets.total != null ? my_tickets.total : 550" ></pagination>
                            </div>
                        </div>
                    </div>
                    <div class="tab-pane" id="newTicket">
                        <TicketFormNew :priorities="priorities" :categories="categories" :departments="departments" :editMode="editMode" />
                    </div>
                </div>
            </div>
        </div>
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
        this.getAllInitials();
    },
}
</script>