<template>
    <section class="overlay-wrapper p-0">
            <table class="table table-head-fixed table-striped text-nowrap">
                <thead>
                    <tr>
                        <th>ID</th>
                        <th>Branch</th>
                        <th>Modules</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(branch_module, index) in branch_modules" :key="branch_module.id">
                        <td>{{ addOne(index) }}</td>
                        <td>{{ branch_module.branch != null ? branch_module.branch.name : 'No Branch Selected' }}</td>
                        <td>{{ branch_module.module != null ? branch_module.module.name : 'No Module Selected' }}</td>
                        <td>
                            <button type="button" class="btn btn-sm btn-tool" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false"><i class="fa fa-ellipsis text-dark"></i></button>
                            <div class="dropdown-menu">
                                <router-link class="btn btn-block dropdown-item" :to="'/emr/operations/branch_modules/'+branch_module.id"><i class="fa fa-eye mr-1 text-primary"></i> View </router-link>
                                <button class="btn btn-block dropdown-item" @click="editBranch(branch)"><i class="fa fa-edit mr-1 text-success"></i> Edit Branch</button>
                                <button class="btn btn-block dropdown-item" @click="deleteBranch(branch.id)"><i class="fa fa-trash mr-1 text-danger"></i> Delete Branch</button>
                            </div>
                        </td>
                    </tr>
                </tbody>
            </table>
    </section>
</template>
<script>
export default {
    data() {
        return {
            branch_module: {},
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
        branch_modules: Array,
        source: String,
    }
}
</script>