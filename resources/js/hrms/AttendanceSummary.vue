<template>
<section class="overlay-wrapper p-0">
    <div class="overlay" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header border-0">
                    <div class="d-flex justify-content-between">
                        <h3 class="card-title">Online Store Visitors</h3>
                        <a href="javascript:void(0);">View Report</a>
                    </div>
                </div>
                <div class="card-body">
                    <HrmsDetailAttendanceSummary />
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            attendance_summaries: {data: []},
            current_page: 1,
            end_date: '',
            form: new Form({ 'source': 'web'}),
            loading: false,
            start_date: '',
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        clockIn(){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, clock me in!'
            })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.loading = true;
                    this.form.post('/api/hrms/clock_ins')
                    .then(response=>{
                        this.$swal.fire('Clocked In!', response.data.message, 'success');
                        this.getAllInitials();
                        this.loading = false;   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });  
        },
        getAllInitials(){
            this.loading = true;
            if (this.start_date == '' || this.end_date == ''){
                var date = new Date();
                this.end_date = date.toISOString().split('T')[0];
                date.setDate(date.getDate() - 30);
                this.start_date = date.toISOString().split('T')[0];
            }
            
            axios.get('/api/hrms/clock_ins?type=mine&start='+this.start_date+'&end='+this.end_date).then(response =>{
                this.reset(response);
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'Clock Ins did not load successfully',});
            });
        }, 
        reset(response){
            this.clock_ins = response.data.clock_ins;
        }
    },
}
</script>