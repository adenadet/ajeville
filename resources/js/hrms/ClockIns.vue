<template>
<section class="overlay-wrapper p-0">
    <div class="overlay" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="row">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-primary">
                    <h3 class="card-title">Clock Ins</h3>
                    <div class="card-tools">
                        <div class="input-group" style="width: 420px;">
                            <input type="date" name="start_date" class="form-control" placeholder="Start Date" v-model="start_date">
                            <div class="input-group-append">
                                <input type="date" name="end_date" class="form-control ml-1" placeholder="End Date" v-model="end_date">
                                <button type="button" class="btn btn-default ml-1" @click="getAllInitials"><i class="fas fa-search"></i></button>
                                <button type="button" class="btn btn-success ml-1" title="Clock In" @click="clockIn"><i class="fas fa-bell"></i></button>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0" style="height: 500px;">
                    <HrmsDetailClockInList :clock_ins="clock_ins.data" source="employee" />
                </div>
                <div class="card-footer">
                    <pagination v-model="current_page" @paginate="getAllInitials" :per-page="clock_ins.per_page != null ? clock_ins.per_page : 52" :records="clock_ins.total != null ? clock_ins.total : 550" ></pagination>
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
            clock_ins: {data: []},
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