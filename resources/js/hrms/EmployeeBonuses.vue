<template>
<section class="p-0">
    <div class="modal fade" id="employeeBonusFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-success">
                    <h4 class="modal-title">New Employee Bonus</h4>
                    <button type="button" @click="closeModals" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <HrmsFormEmployeeBonus :bonus="bonus" :editMode="editMode" @refreshBonusForm="getAllInitials()"/>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header bg-success">
            <h3 class="card-title">Employee Bonuses</h3>
            <div class="card-tools">
                <div class="input-group" style="width: 550px;">
                    <input type="text" name="table_search" class="form-control float-right" placeholder="Search"  v-model="query">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                        <select class="form-control ml-1" v-model="status">
                            <option value="">All</option>
                            <option value="active">Active</option>
                            <option value="pending">Pending</option>      
                        </select>
                        <input type="month" name="month" class="form-control ml-1" placeholder="Select Month"  v-model="month">
                        <button type="submit" class="btn btn-primary ml-1" @click="addBonus"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body table-responsive p-0 overlay-wrapper" style="height: 500px;">
            <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
            <HrmsDetailEmployeeBonusList :bonuses.sync="bonuses.data" @refreshBonusList="getAllInitials" />
        </div>
        <div class="card-footer">
            <pagination v-model="current_page" @paginate="getAllInitials" :per-page="bonuses.per_page != null ? bonuses.per_page : 52" :records="bonuses.total != null ? bonuses.total : 550" ></pagination>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            bonuses: {data:[]},
            bonus: {},
            editMode: false,
            employee: {user: {}},
            current_page: 1,
            loading: false,
            month: '',
            query: '',
            status: '',
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        addBonus(){
            this.loading = true;
            this.bonus = {};
            $('#employeeBonusFormModal').modal('show');
            this.loading = false;
        },
        closeModals(){
            $('#employeeBonusFormModal').modal('hide');
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/hrms/employee_bonuses?type=admin&query='+this.query+'&status='+this.status)
            .then(response =>{
                this.refreshPage(response);
            })
            .catch(()=>{
                this.$toast.fire({icon: 'error', title: 'Employee Bonuses did not load successfully',});
            });
            this.loading = false;
        }, 
        refreshPage(response){
            this.bonuses = response.data.bonuses;
        }
    },
}
</script>