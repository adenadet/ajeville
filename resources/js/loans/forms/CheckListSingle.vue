<template>
    <section>
        <form @submit.prevent="assignLoan">
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label >Requirement </label>
                    <div class="form-control">{{check_list != null && check_list.requirement != null ? check_list.requirement.name : 'Awaiting '  }}</div>
                    <input type="hidden" name="loan_id" id="loan_id" v-model="checkListData.account_id" />
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" name="status" id="status" v-model="checkListData.status" required>
                        <option value="">--Select Status--</option>
                        <option value="0"> Not Done </option>
                        <option value="1"> Done </option>
                    </select>  
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Comment</label>
                    <wysiwyg name="comment" id="comment" v-model="checkListData.comment" required /> 
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-success btn-sm" >Confirm</button>
    </form>
    </section>
</template>
<script>
export default {
    data(){
        return {
            form: new Form({}),
            check_list: {},
            checkListData: new Form({
                loan_id: '',
                requirement_id: '',
                status: 0,
                comment: '',
            }),
        }
    },
    methods:{
        assignLoan(){
            this.$Progress.start();
            this.checkListData.put('/api/loans/checklists/'+this.check_list.id)
            .then(response =>{
                this.$Progress.finish();
                Fire.$emit('checklistReload', response);
                this.RescheduleData.reset();
                Swal.fire({icon: 'success', title: 'The Checklist has been saved', showConfirmButton: false, timer: 1500});})
            .catch({})
        },
        reloadStaffs(response){
            this.users = response.data.users;
        }
    },
    mounted() {
        Fire.$on('checklistDataFill', checklist=> {
            this.check_list = checklist;
            this.checkListData.fill(checklist);
        });
    },
    props:{
        checklist: Object,
    }
}
</script>