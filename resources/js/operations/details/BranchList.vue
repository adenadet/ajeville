<template>
<section class="overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="branchFormModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header bg-dark">
                    <h4 class="modal-title">Branch Details</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <OperationFormBranch :branch.sync="branch" :editMode="editMode" :source="source" @refreshBranchForm="refreshPage"/>
                </div>
            </div>
        </div>
    </div>
    <table class="table table-head-fixed text-nowrap">
        <thead>
            <tr>
                <th>S/N</th>
                <th>Name</th>
                <th>Address</th>
                <th>Phone no.</th>
                <th v-if="source == 'emr'">Practice Manager</th>
                <th v-if="source == 'emr'">Chief Consultant</th>
                <th v-if="source == 'emr'">Head of Nurses</th>
                <th></th>
            </tr>
        </thead>
        <tbody v-if="branches.length > 0">
            <tr v-for="(branch, index) in branches" :key="branch.id">
                <td>{{addOne(index)}}</td>
                <td>{{ branch.name }}</td>
                <td v-html="branch.address"></td>
                <td>{{ branch.phone }}</td>
                <td v-if="source == 'emr'">{{ (branch.pm_id != null && branch.practice_manager.user != null)   ? FullName(branch.practice_manager.user) : 'Not Selected' }}</td>
                <td v-if="source == 'emr'">{{ (branch.cinc_id != null && branch.chief_consultant.user != null) ? FullName(branch.chief_consultant.user) : 'Not Selected'}}</td>
                <td v-if="source == 'emr'">{{ (branch.hon_id != null && branch.head_nurse.user != null) ? FullName(branch.head_nurse.user) : 'Not Selected'}}</td>
                <td>
                    <button type="button" class="btn btn-sm btn-tool" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-bars text-dark"></i></button>
                    <div class="dropdown-menu">
                        <router-link class="btn btn-block dropdown-item" :to="'/emr/operations/branches/'+branch.id"><i class="fa fa-eye mr-1 text-primary"></i> View </router-link>
                        <button class="btn btn-block dropdown-item" @click="editBranch(branch)"><i class="fa fa-edit mr-1 text-success"></i> Edit Branch</button>
                        <button class="btn btn-block dropdown-item" @click="deleteBranch(branch.id)"><i class="fa fa-trash mr-1 text-danger"></i> Delete Branch</button>
                    </div>
                </td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr><td colspan="8">No Branches Found</td></tr>
        </tbody>
    </table>
</section>
</template>
<script>
export default {
    data() {
        return {
            branch: {},
            editMode: false,
            loading: false,
        }
    },
    emits: ['refreshBranches'],
    mounted() {},
    methods: {
        closeModal(){
            $('#branchFormModal').modal('show');
        },
        deleteBranch(id){
            //alert("Working")
        },
        editBranch(branch){
            this.loading = true;
            this.branch = branch;
            this.editMode = true;
            $('#branchFormModal').modal('show');
            this.loading = false;
        },
        refreshPage(){
            this.closeModal();
            this.$emit('refreshBranches');
        }
    },
    props: {
        branches: Array,
        source: String,
    }
}
</script>