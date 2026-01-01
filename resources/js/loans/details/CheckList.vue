<template>
<section>
    <div class="modal fade" id="checklistModal" tabindex="-1" aria-labelledby="repaymentModalLabel" aria-hidden="true">
        <div class="modal-dialog">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="repaymentModalLabel">Update Checklist</h5>
                    <button type="button" class="btn btn-default" data-bs-dismiss="modal" aria-label="Close" @click="closeModal()"><i class="fa fa-times text-success"></i></button>
                </div>
                <div class="modal-body">
                    <LoanFormCheckListSingle />
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header bg-dark">
            <h3 class="card-title">Loan Officer Checklist</h3>
        </div>
        <div class="card-body">
            <table class="table table-bordered table-hover table-stripped" v-if="checklists != null && checklists.length != 0">
                <thead>
                    <tr>
                        <th style="width: 10px">#</th>
                        <th>Requirement</th>
                        <th>Progress</th>
                        <th>By</th>
                        <th>&nbsp;</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(checklist, index) in checklists" :key="checklist.id">
                        <td>{{ index | addOne }}</td>
                        <td>{{ checklist.requirement ? checklist.requirement.name : 'Old Requirement' }}</td>
                        <td>{{ checklist.status == true ? 'Done' : 'Not Done' }}</td>
                        <td>{{ checklist.account_officer | FullName }}</td>
                        <td>
                            <button type="button" class="btn btn-light" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fas fa-ellipsis-v"></i></button>
                            <div class="dropdown-menu">
                                <button class="btn btn-block dropdown-item" @click="editRequirement(checklist)"><i class="fa fa-edit mr-1"></i> Modify</button>
                            </div>
                        </td>
                    </tr>    
                </tbody>
            </table>
            <LoanFormCheckList :editMode="editMode" v-else-if="source == 'account'"/>
            <div class="row" v-else>
                <div class="col-md-12 text-center">
                    <h3>Checklist Not Yet Completed</h3>
                </div>
            </div>
        </div>
        
    </div>
</section>
</template>
<script>
export default {
    data(){
        return {
            account: {},
            checklists: {},
            editMode: false,
            form: new Form({}),
        }
    },
    methods:{
        closeModal(){
            $('#checklistModal').modal('hide');
        },
        editRequirement(checklist){
            Fire.$emit('checklistDataFill', checklist);
            $('#checklistModal').modal('show');
        },
        getAllInitials(){
            this.$Progress.start();
            axios.get('/api/loans/checklists/'+this.$route.params.id).then(response =>{
                this.reloadPage(response);
                this.$Progress.finish();
                toast.fire({
                    icon: 'success',
                    title: 'Account Check List was loaded successfully',
                });
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Account Check List was not loaded successfully',
                })
            });
        },
        reloadPage(response){
            this.account = response.data.account;
            this.checklists = response.data.checklists;
        }
    },
    mounted() {
        this.getAllInitials();
        Fire.$on('checklistReload', checklist=> {
            this.closeModal();
            this.getAllInitials();
        });
    },
    props:{
        source: String
    }
}
</script>