<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="bonusModal">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h4 class="modal-title">Employee Bonus Detail</h4>
                    <button type="button" @click="closeModals" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <HrmsDetailEmployeeBonus :bonus="bonus" :editMode="editMode" @refreshBonusForm="refreshPage"/>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="bonusFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h4 class="modal-title">New Employee Bonus</h4>
                    <button type="button" @click="closeModals" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <HrmsFormEmployeeBonus :bonus="bonus" :editMode="editMode" @refreshBonusForm="refreshPage"/>
                </div>
            </div>
        </div>
    </div>
    <table class="table table-head-fixed text-nowrap">
        <thead>
            <tr>
                <th>S/N</th>
                <th>Employee</th>
                <th>Bonus Name</th>
                <th>Month</th>
                <th>Amount</th>
                <th>Description</th>
                <th></th>
            </tr>
        </thead>
        <tbody v-if="true">
            <tr v-for="(bonus, index) in bonuses" :key="bonus.id">
                <td>{{ addOne(index) }}</td>
                <td>{{ bonus.employee != null ? FullName(bonus.employee.user) : 'No Employee' }}</td>
                <td>{{ bonus.name }}</td>
                <td>{{ ExcelMonthYear(bonus.month) }}</td>
                <td>{{ currency(bonus.amount) }}</td>
                <td :title="bonus.description" v-html="readMore(bonus.description, 60, '...')"></td>
                <td>
                    <button class="nav-link btn btn-sm btn-default" data-toggle="dropdown" type="button">
                        <i class="fa text-small fa-ellipsis-v"></i>
                    </button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <button class="dropdown-item btn btn-block btn-sm" @click="viewEmployeeBonus(bonus)"><i class="fa fa-eye mr-1 text-primary"></i> View Employee Bonus</button>
                        <button class="dropdown-item btn btn-block btn-sm" @click="updateEmployeeBonus(bonus)"><i class="fa fa-edit mr-1 text-warning"></i> View Employee Bonus</button>
                        <button class="dropdown-item btn btn-block btn-sm" @click="deactivateEmployeeBonus(bonus.id)"><i class="fa fa-trash mr-1 text-danger"></i> Cancel Employee Bonus</button>
                    </div>
                </td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr>
                <td colspan="5">No Bonuses meet your criteria</td>
            </tr>
        </tbody>
    </table>
</section>
</template>
<script>
export default {
    computed:{
        today(){
            return new Date().toJSON().slice(0, 10);
        }
    },
    data() {
        return {
            bonus: {},
            editMode: false,
            loading: false,
        }
    },
    emits:['refreshBonusList'],
    mounted() {},
    methods: {
        addBonus(){
            this.loading = true;
            this.bonus = {};
            $('#bonusFormModal').modal('show');
            this.loading = false;
        },
        closeModals(){
            $('#bonusModal').modal('hide');
            $('#bonusFormModal').modal('hide');
        },
        deactivateEmployeeBonus(id){
            this.$swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, delete it!'
            })
            .then((result) => {
                //Send Delete request
                if(result.value){
                    this.loading = true;
                    this.form.delete('/api/hrms/employee_bonuses/'+id)
                    .then(response=>{
                        this.$swal.fire('Deleted!', response.data.message, 'success');
                        this.refreshPage(response);
                        this.loading = false;   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        refreshPage(){
            this.loading = true;
            this.closeModals();
            this.$emit('refreshBonusList');
            this.loading = false;
        },
        updateEmployeeBonus(bonus){
            this.loading = true;
            this.editMode = true;
            this.bonus = bonus;
            $('#bonusFormModal').modal('show');
            this.loading = false;
        },
        viewEmployeeBonus(bonus){
            this.loading = true;
            this.bonus = bonus;
            $('#bonusModal').modal('show');
            this.loading = false;
        },
    },
    props: {
        bonuses: Array,
        employee: Object,
        source: String,
    },
    watch:{
        bonuses(){}
    },
}
</script>