<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="assignModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Assign Policies to Department</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <PoliciesFormAssign :policy.sync="policy" @reloadPolicies="getPolicies"/>
                </div>
            </div>
        </div>
    </div>
    <div class="modal fade" id="policyUpdateModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">{{ 'Edit Policy: '+ policy.name}}</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <PoliciesFormNew :editMode.sync="editMode" :policy.sync="policy" @reloadPolicies="getPolicies"/>
                </div>
            </div>
        </div>
    </div>
    <div class="card-body table-responsive p-0" style="height: 500px;">
        <div class="row p-3" v-if="style=='grid'">
            <div class="col-lg-4 col-md-4 col-sm-6 d-flex align-items-stretch" v-for="policy in policies" :key="policy.id">
                <div class="card bg-light">
                    <div class="card-header text-muted border-bottom-0">&nbsp;</div>
                    <div class="card-body pt-0">
                        <div class="row">
                            <div class="col-7"><h2 class="lead"><b>{{policy.name}}</b></h2></div>
                            <div class="col-5 text-center"><h1><i class="fa fa-file-pdf"></i></h1></div>
                            <div class="col-12">
                                <ul class="ml-4 mb-0 fa-ul text-muted">
                                    <li class="small"><span class="fa-li"><i class="fas fa-tag"></i></span> Category: {{policy.category_id != null && policy.category != null ? policy.category.name: policy.category_id}}</li>
                                </ul>
                            </div>
                        </div>
                    </div>
                    <div class="card-footer">
                        <div class="text-right" v-if="source == 'departmental'">
                            <a :href="policy != null ? '/policies/view/'+policy.id : '/policies/'"><button class="btn btn-sm btn-primary" title="Read"><i class="fa fa-eye"></i></button></a>
                        </div>
                        <div class="text-right float-right" v-else>
                            <button class="nav-link btn btn-sm btn-default" data-toggle="dropdown" type="button">
                                <i class="fa fa-ellipsis-v text-dark"></i>
                            </button>
                            <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" v-if="source == 'admin'">
                                <a :href="'/policies/view/'+policy.id" class="dropdown-item btn btn-block btn-sm" title="Read Policy"><i class="fa fa-eye mr-1 text-success"></i> View Policy</a>
                                <button class="dropdown-item btn btn-block btn-sm" title="Edit Policy" @click="editPolicy(policy)"><i class="fa fa-edit text-primary"></i> Edit Policy</button>
                                <button class="dropdown-item btn btn-block btn-sm" title="Assign Policy" @click="assignPolicy(policy)"><i class="fa fa-inbox text-warning"></i> Assign Policy to Dept.</button>
                                <button class="dropdown-item btn btn-block btn-sm"  title="Delete Policy" @click="deletePolicy(policy.id)"><i class="fa fa-trash text-danger"></i>Delete Policy</button>
                            </div>    
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <table class="table table-hover table-stripped table-head-fixed text-nowrap m-b-0 col-md-12" v-if="style == 'table'">
            <thead>
                <tr>
                    <th>Name</th>
                    <th>File</th>
                    <th>Category</th>
                    <th>Created By</th>
                    <th>No of Depts</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="policy in policies" :key="policy.id">
                    <td><h6 class="mb-0" :title="policy.name">{{readMore(policy.name, 50, '...')}}</h6></td>
                    <td v-if="policy.file === null"><i class="fa fa-times"></i></td>
                    <td v-else><i class="fa fa-check"></i></td>
                    <td>{{typeof policy.category != 'undefined' && policy.category != null ? policy.category.name: 'General Category'}}</td>
                    <td>{{typeof policy.creator != 'undefined' && policy.creator != null ? policy.creator.first_name+' '+policy.creator.last_name: 0 }}</td>
                    <td>{{typeof policy.depts != 'undefined' && policy.depts != null ? policy.depts.length: 0 }}</td>
                    <td>
                        <button class="nav-link btn btn-sm btn-default" data-toggle="dropdown" type="button">
                            <i class="fa fa-ellipsis-v"></i>
                        </button>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" v-if="source == 'admin'">
                            <a :href="'/policies/view/'+policy.id" class="dropdown-item btn btn-block btn-sm" title="Read Policy"><i class="fa fa-eye mr-1 text-success"></i> View Policy</a>
                            <button class="dropdown-item btn btn-block btn-sm" title="Edit Policy" @click="editPolicy(policy)"><i class="fa fa-edit text-primary"></i> Edit Policy</button>
                            <button class="dropdown-item btn btn-block btn-sm" title="Assign Policy" @click="assignPolicy(policy)"><i class="fa fa-inbox text-warning"></i> Assign Policy to Dept.</button>
                            <button class="dropdown-item btn btn-block btn-sm"  title="Delete Policy" @click="deletePolicy(policy.id)"><i class="fa fa-trash text-danger"></i>Delete Policy</button>
                        </div>
                        <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right" v-else>
                            <a :href="'/policies/view/'+policy.id" class="dropdown-item btn btn-block btn-sm" title="Read Policy"><i class="fa fa-eye mr-1 text-success"></i> View Policy</a>
                        </div>
                    </td>
                </tr>
            </tbody>
        </table>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return {
            categories: {},
            departments: [],
            editMode: false,
            form: new Form({}),
            loading: false,
            policy: {},
            search: '',
        }
    },
    methods:{
        assignPolicy(policy){
            if (policy.category_id == 0){
                this.$swal.fire({
                    title: 'This is a General Policy, it can not be assigned to a Department',
                    icon: 'error',
                })
            }
            else{
                this.policy = policy;
                $('#assignModal').modal('show');
            }
        },
        closeModals(){
            $('#assignModal').modal('hide');
            $('#policyModal').modal('hide');
        },
        createPolicy(){
            this.editMode = false;
            this.policy = {};
            $('#policyModal').modal('show');
        },
        deletePolicy(id){
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
                if(result.value){
                    this.form.delete('/api/policies/'+id)
                    .then(response=>{
                        this.$swal.fire('Deleted!', 'Policies has been deleted.', 'success');
                        this.$emit('CatRefresh', response);   
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!',});
                    });
                }
            }); 
        },
        editPolicy(policy){
            this.editMode = true;
            this.policy = policy;
            console.log(policy)
            $('#policyUpdateModal').modal('show');
        },
        getPolicies(){
            $('#policyModal').modal('hide');
            $('#policyModal').modal('hide');
            this.$emit('getInitials')
        },
        reset(response){
            this.categories = response.data.categories;
            this.departments = response.data.departments;
            this.policies = response.data.policies;
            this.closeModals();
        }
    },
    mounted() {},
    props:{
        policies: Array,
        source: String,
        style: String,
    },
    watch:{
        policies(){

        },
    }
}
</script>