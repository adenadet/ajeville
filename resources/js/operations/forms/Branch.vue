<template>
<section class="overlay-wrapper p-0">
    <div class="container-fluid">
    <form @submit.prevent="editMode ? updateBranch() : createBranch()">
        <alert-error :form="branchData"></alert-error>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Branch Name*</label>
                    <input type="text" class="form-control" id="name" name="name" v-model="branchData.name" required />
                    <has-error :form="branchData" field="name"></has-error>
                </div>
            </div>
        </div>
        <div class="row" v-if="source == 'emr'">
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Chief Consultant*</label>
                    <!--model-list-select class="form-control" :list="users" v-model="branchData.cinc_id" option-value="id" :custom-text="codeAndNameAndDesc" placeholder="Select Applicant" /-->
                    <select class="form-control" id="cinc_id" name="cinc_id" v-model="branchData.cinc_id" required >
                        <option v-for="employee in employees" :value="employee.id">{{ FullName(employee.user) }}</option>
                    </select>
                    <has-error :form="branchData" field="cinc_id"></has-error>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Head of Nurses*</label>
                    <!--model-list-select class="form-control" :list="users" v-model="branchData.hon_id" option-value="id" :custom-text="codeAndNameAndDesc" placeholder="Select Applicant" /-->
                    <select class="form-control" id="hon_id" name="hon_id" v-model="branchData.hon_id" required>
                        <option v-for="employee in employees" :value="employee.id">{{ FullName(employee.user) }}</option>
                    </select>
                    <has-error :form="branchData" field="hon_id"></has-error>
                </div>
            </div>
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Practice Manager*</label>
                    <!--model-list-select class="form-control" :list="users" v-model="branchData.pm_id" option-value="id" :custom-text="codeAndNameAndDesc" placeholder="Select Applicant" /-->
                    <select class="form-control" id="pm_id" name="pm_id" v-model="branchData.pm_id" required>
                        <option v-for="employee in employees" :value="employee.id">{{ FullName(employee.user) }}</option>
                    </select>
                    <has-error :form="branchData" field="pm_id"></has-error>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Default Price List*</label>
                    <select class="form-control" id="price_list_id" name="price_list_id" v-model="branchData.price_list_id" required>
                        <option value="">--Select Price List--</option>
                        <option v-for="price_list in price_lists" :value="price_list.id">{{ price_list.name }}</option>
                    </select>
                    <has-error :form="branchData" field="price_list_id"></has-error>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Phone number*</label>
                    <input type="number" class="form-control" id="phone" name="phone" v-model="branchData.phone" required>
                    <has-error :form="branchData" field="phone"></has-error>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" id="status" name="status" v-model="branchData.status" required>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                    <has-error :form="branchData" field="status"></has-error>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Address*</label>
                    <QuillEditor class="form-control" id="address" name="address" v-model:content="branchData.address" required content-type="html"/>
                    <has-error :form="branchData" field="address"></has-error>
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Descrption*</label>
                    <QuillEditor class="form-control" id="description" name="description" v-model:content="branchData.description" required content-type="html"/>
                    <has-error :form="branchData" field="description"></has-error>
                </div>
            </div>
        </div>
        <button class="btn btn-success btn-sm mt-3" :disabled="loading"><i class="fas fa-check"></i> Save Branch</button>
    </form>
</div>
</section>
</template>
<script>
export default {
    data() {
        return {
            branchData: new Form({
                id: '',
                name: '',
                address: '',
                cinc_id: '',
                description: '',
                hon_id: '',
                phone: '',
                pm_id: '',
                price_list_id: '',
                status: '',
            }),
            employees: [],
            loading: false,
            price_lists: [],
            users: [],
        }
    },
    mounted() {
        this.getAllInitials();
    },
    methods: {
        codeAndNameAndDesc(item){
            return `${item.last_name}, ${item.first_name} ${item.middle_name != null ? item.middle_name : '' }`;
        },
        createBranch(){
            this.loading = true;
            this.branchData.post('/api/operations/branches')
            .then(response => {
                this.$emit('refreshBranchList');
                //$('#branchFormModal').modal('hide');
                this.$swal.fire({icon: 'success', title: 'Branch Created Successfully', showConfirmButton: false,timer: 1500});
            })
            .catch(error => {
                this.loading = false;
                if (error.response.status === 422) {this.branchData.errors = error.response.data.errors;}
            })
            .finally(() => {
                this.loading = false;
            });
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/operations/branches/initials')
            .then(response => {
                this.price_lists = response.data.price_lists;
                this.employees = response.data.employees;
            })
            .finally(() => {
                this.loading = false;
            });
        },
        updateBranch(){
            this.loading = true;
            this.branchData.put('/api/operations/branches/'+this.branchData.id)
            .then(response =>{
                this.$emit('refreshBranchList', response);
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Branch details has been modified',
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
            })
            .finally(() => {
                this.loading = false;
            });
        },
    },
    props: {
        branch: {
            type: Object,
            required: true,
        },
        editMode: {
            type: Boolean,
            required: true,
        },
        source: {
            type: String,
            required: true,
        },
    },
    watch:{
        branch(){
            this.branchData.fill(this.branch);
        },
    }
}
</script>