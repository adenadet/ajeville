<template>
<section class="row">
    <div class="col-12 card p-0">
        <div class="card-header bg-navy"><h3 class="card-title">Staff of the Month Administrator</h3>
            <div class="card-tools">
                <button class="btn btn-xs btn-primary" @click="addMonth()"><i class="fa fa-calendar-check"></i> Add new Month</button>
            </div>
        </div>
        <div class="card-body">
            <section class="row">
                <div class="col-2">
                    <section>
                        List of Months 
                        <button class="btn btn-block btn-default" @click="showMonth(month.month)" v-for="month in staff_months.data" :key="month.id">{{ExcelMonthYear(month.month)}}</button>
                    </section>
                </div>
                <div class="col-10">
                    <section class="row">
                        <div class="col-12 col-md-12 col-sm-6">
                            <div class="card card-primary card-tabs">
                                <div class="card-header p-0 pt-1">
                                    <ul class="nav nav-tabs" id="custom-tabs-five-tab" role="tablist">
                                        <li class="nav-item"><a class="nav-link active" id="details-tab" data-toggle="pill" href="#details" role="tab" aria-controls="detail" aria-selected="true">Details</a></li>
                                        <li class="nav-item"><a class="nav-link" id="nomination-tab" data-toggle="pill" href="#nomination" role="tab" aria-controls="nomination" aria-selected="true">Nominations</a></li>
                                        <li class="nav-item"><a class="nav-link" id="voting-tab" data-toggle="pill" href="#voting" role="tab" aria-controls="voting" aria-selected="false">Voting</a></li>
                                        <li class="nav-item"><a class="nav-link" id="winners-tab" data-toggle="pill" href="#winners" role="tab" aria-controls="winners" aria-selected="true">Winners</a></li>
                                    </ul>
                                </div>
                                <div class="card-body p-0">
                                    <div class="tab-content p-0" id="custom-tabs-five-tabContent">
                                        <div class="tab-pane fade show active p-0" id="details" role="tabpanel" aria-labelledby="details-tab"><SOMDetail :staff_month.sync="staff_month"/></div>
                                        <div class="tab-pane fade show" id="nomination" role="tabpanel" aria-labelledby="nomination-tab"><SOMDetailNominations :staff_month.sync="staff_month"/></div>
                                        <div class="tab-pane fade" id="voting" role="tabpanel" aria-labelledby="voting-tab"><SOMDetailVotes :staff_month.sync="staff_month"/></div>
                                        <div class="tab-pane fade show" id="winners" role="tabpanel" aria-labelledby="winners-tab"><SOMDetailWinners :month="staff_month"/></div>
                                    </div>
                                </div>
                            </div> 
                        </div>  
                    </section>
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
            dept_users: [],
            editMode: false,
            month: {},
            months: {},
            nominateData: new Form({id: '', user_id: 0, month: '', description: '',}),
            staff_months: {}, 
            staff_month: {},  
        }
    },
    methods:{
        addMonth(){
            this.editMode = false;
            this.month = {};
            $('#monthModal').modal('show');
        },
        closeModal(){
            $('#monthModal').modal('hide');
        },
        editMonth(month){
            this.editMode = true;
            this.month = month;
            $('#monthModal').modal('show');
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/som/months')
            .then(response =>{
                this.reloadPage(response);
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                toast.fire({icon: 'error', title: 'Dashboard not loaded successfully',});
            });
        },
        reloadPage(response){
            this.staff_months = response.data.staff_months;
            this.staff_month = response.data.staff_month;
        },
        showMonth(month){
            var futureMonth = moment(month).format('YYYY-MM');     
            axios.get('/api/som/months/'+futureMonth)
            .then((response ) => {
                this.staff_month = response.data.month;
            })
            .catch(()=>{});
        }
    },
    mounted() {
        this.getAllInitials();
    }
}
</script>