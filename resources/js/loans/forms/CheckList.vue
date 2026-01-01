<template>
<section>
    <form @submit.prevent="editMode ? updateCheckList() : createCheckList()">
        <div class="row" v-for="(requirement, index) in loanCheckData.requirements" :key="requirement.id">
            <div class="col-sm-4">
                <div class="form-group">
                    <label >{{loanCheckData.requirements[index].name}}</label>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" v-model="loanCheckData.requirements[index].status">
                        <option value="0"> Not Done </option>
                        <option value="1"> Completed </option>
                    </select>  
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Comments</label>
                    <input class="form-control" type="text" v-model="loanCheckData.requirements[index].comment" /> 
                </div>
            </div>
        </div>
        <button type="submit" class="btn btn-success btn-sm" >{{ editMode ? 'Update' : 'Confirm' }}</button>
    </form>
</section>
</template>
<script>
export default{
	created(){
		//this.getInitials();
	},
    data () {
        return {
            account: {},
            loanCheckData: new Form({
                loan_id: '',
                requirements: [],
            }),

            requirement: {
                id: 0,
                name: '',
                requirement_id: 0,
                status: 0,
                comment: ''
                
            },
        };
    },
    methods: {
        getInitials(){
			axios.get('/api/loans/accounts/staffs/'+this.$route.params.id).then(response =>{
                this.refreshPage(response);
                this.$Progress.finish();
                toast.fire({
                    icon: 'success',
                    title: 'Users loaded successfully',
                });
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Users not loaded successfully',
                })
            });
		},
		refreshPage(response){
            this.account = response.data.account;
			this.requirements = response.data.requirements;
			if (response.data.loanCheck != null){ this.loanCheckData.fill(response.data.loanCheck);}
			else{
                for ( let i = 0; i < this.requirements.length; i++){
                    this.loanCheckData.requirements.push(
                        {
                            name: this.requirements[i].requirement.name,
                            requirement_id: this.requirements[i].requirement_id,
                            status: 0,
                            comment: '',
                        }
                    );
                }
            }
		},
		updateProfilePic(){

		},
        createCheckList(){
            this.$Progress.start();
            this.loanCheckData.loan_id = this.account.id;
            this.loanCheckData.post('/api/loans/checklists')
            .then(response =>{
                this.$Progress.finish();
                Fire.$emit('refreshRepayment', response);
                this.RescheduleData.reset();
                Swal.fire({icon: 'success', title: 'The Checklist has been saved', showConfirmButton: false, timer: 1500});
            })
            .close();
        }
    },
    props:{
        editMode: Boolean,
    }
}
</script>
