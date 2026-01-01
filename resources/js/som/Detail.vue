<template>
<section class="p-0">
    <div class="modal fade" id="monthModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Month Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <SOMFormMonth :editMode="editMode" :month.sync="staff_month"/>
                </div>
            </div>
        </div>
    </div>
    
    <div class="card card-widget p-0 m-0 widget-user-2"  v-if="staff_month.id != null ">
        <div class="card-body">
            <div class="row p-0">
                <div class="col-6 p-0 m-0">
                    <div class="card m-0">
                        <div class="card-header bg-dark"><h3 class="card-title">Month: {{ ExcelMonthYear(staff_month.month) }}</h3></div>
                        <div class="card-body">
                            <p>Just a full details of the SOM</p>
                        </div>
                    </div>
                </div>
                <div class="col-6">
                    <div class="card m-0">
                        <div class="card-header bg-dark"><h3 class="card-title">Month: {{ ExcelMonthYear(staff_month.month) }}</h3></div>
                        <div class="card-body">
                            <i class="fas fa-user mr-1"></i> Nomination Opened By:
                            <p><strong>{{ FullName(staff_month.nomination_open)}}<br /></strong>
                            <span class="text-muted">{{ ExcelDate(staff_month.nomination_start) }}</span></p>
                            <hr>

                            <i class="fas fa-user mr-1"></i> Nomination Closed By:
                            <p><strong>{{ FullName(staff_month.nomination_close) }}<br /></strong>
                            <span class="text-muted">{{ ExcelDate(staff_month.nomination_end)}}</span></p>
                            <hr>

                            <i class="fas fa-user mr-1"></i> Voting Opened By:
                            <p><strong>{{ FullName(staff_month.voting_open)}}<br /></strong>
                            <span class="text-muted">{{ ExcelDate(staff_month.voting_start)}}</span></p>
                            <hr>
                            
                            <i class="fas fa-user mr-1"></i> Voting Closed By:
                            <p><strong>{{ FullName(staff_month.voting_close)}}<br /></strong>
                            <span class="text-muted">{{ ExcelDate(staff_month.voting_end)}}</span></p>
                            <hr>
                            
                            <i class="fas fa-map-marker-alt mr-1"></i> Status:
                            <p> <strong>{{ staff_month.status }}</strong></p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-footer">
            <button class="btn btn-sm btn-primary" @click="updateMonth(staff_month)"><i class="fa fa-edit mr-1"></i>Edit</button>
        </div>
    </div>
    <div class="card" v-else>
        <div class="card-body">
            No Month has been selected, kindly select a month
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
        }
    },
    emits:['fireEditMonth'],
    methods:{
        reloadPage(response){
            this.month = response.data.month;
        },
        updateMonth(month){
            this.$emit('fireEditMonth', month);
        },
    },
    mounted() {},
    props:{
        staff_month: Object,
    },
    watch:{
        staff_month(){
            
        }
    }
}
</script>