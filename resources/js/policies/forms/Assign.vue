<template>
    <div class="overlay-wrapper">
        <div class="overlay dark" v-if="loading">
            <i class="fas fa-3x fa-sync-alt fa-spin"></i>
            <div class="text-bold pt-2">Loading...</div>
        </div>
        <form role="form" @submit.prevent="assignDepartment">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Policy {{ policy }}</label>
                        <div class="form-control" v-html="policy.name"></div>
                        <input type="hidden" name="policy_id" id="policy_id" v-model="assignData.policy_id" />
                    </div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group"><label>Departments</label></div>
                </div>
            </div>
            <div class="row">
                <div class="col-sm-3" v-for="department in departments" :key="department.id" v-if="policy.departments != null">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="departments[]" id="departments[]" v-model="assignData.departments" :value="department.id" :checked="assignData.departments.includes(department.id)">
                        <label class="form-check-label">{{department.name}}</label>
                    </div>
                </div>
            </div> 
            <div class="row">
                <input type="submit" name="submit" class="submit btn btn-success" value="Submit" />
            </div>
        </form>
    </div>
</template>
<script>
export default {
    data(){
        return  {
            assignData: new Form({'policy_id': '', 'policy_name': '', 'departments': [],}),
            departments: [],
            loading: false,
        }
    },
    mounted(){  
        this.getInitials();    
    },
    methods:{
        assignDepartment(){
            this.loading = true;
            this.assignData.post('/api/policies/assign')
            .then(response =>{
                this.loading = false
                this.$emit('reload', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Policy "'+ response.data.policy.name+'" has been updated',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                this.$swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
                this.loading = false;
            });
        },
        reloadData(policy){
            this.assignData.policy_id   = policy.id;
            this.assignData.policy_name = policy.name;
            this.assignData.departments = [];
            for (let i = 0; i < policy.depts.length; i++) {this.assignData.departments.push(policy.depts[i].department.id);}    
        },
        getInitials(){
            this.loading = true;
            axios.get('/api/policies/initials')
            .then(response => {
                this.departments = response.data.departments; 
                this.loading = false;       
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Policy form did not loaded successfully',})
                this.loading = false;
            });
        },
        
    },
    props:{
        policy: Object,
    },
    watch:{
        policy(){
            this.assignData.clear();
            this.assignData.policy_id = this.policy.id;
        }
    }
}
</script>
