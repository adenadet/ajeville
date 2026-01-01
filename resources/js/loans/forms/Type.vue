<template>
<section class="row">
    <div class="col-md-12">
        <form class="form" method="post" @submit.prevent="editMode ? updateLoanType() : createLoanType() ">
            <div class="row">
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Loan Type Name *</label>
                        <input type="text" required class="form-control" id="name" name="name" placeholder="Name *" v-model="LoanTypeData.name">
                    </div>
                </div>
                <div class="col-sm-12">
                    <div class="form-group">
                        <label>Status *</label>
                        <select class="form-control" id="status" name="status" placeholder="middle Name" v-model="LoanTypeData.status" required>
                            <option value="">--Select Status--</option>
                            <option value=1>Active</option>
                            <option value=2>Inactive</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Interest Rate*</label>
                        <input type="text" class="form-control" id="percentage" name="percentage" placeholder="Interest Rate in Percentage" v-model="LoanTypeData.percentage" required/>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Category *</label>
                        <select class="form-control" id="category_id" name="category_id" placeholder="middle Name" v-model="LoanTypeData.category_id" required>
                            <option value="">--Select Category--</option>
                            <option value=1>Personal</option>
                            <option value=2>Business</option>
                            <option value=3>Others</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-4">
                    <div class="form-group">
                        <label>Approval Matrix *</label>
                        <select class="form-control" id="matrix_id" name="matrix_id" v-model="LoanTypeData.matrix_id" required>
                            <option value="">--Select Approval Matrix--</option>
                            <option v-for="matrix in matrices" :value="matrix.id">{{ matrix.name }}</option>
                        </select>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Minimum Duration (in Weeks)</label>
                        <input type="number" class="form-control" id="min_duration" name="min_duration" placeholder="MinimumDuration in Weeks " v-model="LoanTypeData.min_duration"/>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Maximum Duration (in weeks)</label>
                        <input type="text" class="form-control" id="max_duration" name="max_duration" placeholder="Max Duration in Weeks" v-model="LoanTypeData.max_duration">
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>Start Date</label>
                        <input type="date" class="form-control" id="start_date" name="start_date" placeholder="Start Date, you can leave empty " v-model="LoanTypeData.start_date"/>
                    </div>
                </div>
                <div class="col-sm-6">
                    <div class="form-group">
                        <label>End Date</label>
                        <input type="date" class="form-control" id="end_date" name="end_date" placeholder="End Date" v-model="LoanTypeData.end_date">
                    </div>
                </div>
            </div>
            <div class="row">
                <label class="col-sm-12">Loan Type Requirements</label>
                <div class="col-sm-3" v-for="requirement in requirements" :key="requirement.id">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" name="requirements[]" id="requirements[]" v-model="LoanTypeData.requirements" :value="requirement.id" :checked="LoanTypeData.requirements.includes(requirement.id)">
                        <label class="form-check-label">{{requirement.name}}</label>
                    </div>
                </div> 
            </div>
            <input type="submit" name="submit" class="submit btn btn-success" value="Submit" />
        </form>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return {
            matrices: {},
            requirements: {},
            LoanTypeData: new Form({
                id: '',
                name: '',
                matrix_id: '',
                percentage: '',
                category_id: '',
                min_duration: '',
                max_duration: '',
                start_date: '',
                end_date: '',
                status: '',
                requirements: [],
            }),
        }
    },
    methods:{
        createLoanType(){
            this.$Progress.start();
            this.LoanTypeData.post('/api/loans/types')
            .then(response =>{
                Swal.fire({icon: 'success', title: 'The Loan Type has been updated', showConfirmButton: false, timer: 1500});
                Fire.$emit('TypesRefresh', response);
                this.LoanTypeData.reset();
                this.$Progress.finish();
                })
            .catch(()=>{
                Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
                this.$Progress.fail();
            });
        },
        getAllInitials(){           
            axios.get('/api/loans/types/initials').then(response =>{
                this.matrices = response.data.matrices;
                this.requirements = response.data.requirements;
            })
            .catch(()=>{
                toast.fire({icon: 'error', title: 'Roles not loaded successfully',});
            });
        },
        updateLoanType(){
            this.$Progress.start();
            this.LoanTypeData.put('/api/loans/types/'+this.LoanTypeData.id)
            .then(response =>{
                Swal.fire({icon: 'success', title: 'The Loan Type has been updated', showConfirmButton: false, timer: 1500});
                Fire.$emit('TypesRefresh', response);
                this.LoanTypeData.reset();
                this.$Progress.finish();
                })
            .catch(()=>{
                Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
                this.$Progress.fail();
            });         
        },
        
    },
    mounted() {
        this.getAllInitials();
        Fire.$on('LoanTypeDataFill', loan_type=>{
            this.LoanTypeData.fill(loan_type);
            this.LoanTypeData.requirements = [];
            for (let i = 0; i < loan_type.requirements.length; i++) {
                this.LoanTypeData.requirements.push(loan_type.requirements[i].id);
            }
        });
    },
    props:{
        'editMode': Boolean,
    },
}
</script>