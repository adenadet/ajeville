<template>
<div class="col-12">
    <div class="modal fade" id="policyModal">
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
    <div class="card">
        <div class="card-header bg-navy">
            <h3 class="card-title">List of All Policies</h3>
            <div class="card-tools">
                <div class="input-group input-group-sm" style="width: 300px;">
                    <input type="text" name="table_search" class="form-control float-right" placeholder="Search" v-model="search">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-primary" @click="getAllInitials()"><i class="fas fa-search"></i></button>
                        <button type="button" class="btn btn-sm btn-success" @click="createPolicy"><i class="fa fa-plus mr-1"></i>Add New</button>
                        <button title="View As Cards" type="button" class="btn btn-default" @click="changeStyle('grid')" v-if="style != 'grid'"><i class="fas fa-table"></i></button>
                        <button title="View As Table" type="button" class="btn btn-default" @click="changeStyle('table')"  v-if="style != 'table'"><i class="fas fa-list"></i></button>         
                    </div>  
                </div>
            </div>
        </div>    
        <PoliciesDetailList :policies.sync="policies.data" source="admin" :style.sync="style" @refreshPolicies="getAllInitials(current_page)"/>
        <div class="card-footer">
            <pagination v-model="current_page" @paginate="getAllInitials" :per-page="policies.per_page != null ? policies.per_page : 52" :records="policies.total != null ? policies.total : 550"></pagination>
        </div>
    </div>    
</div>
</template>
<script>
export default {
    data(){
        return {
            categories: {},
            current_page: 1,
            departments: [],
            editMode: false,
            form: new Form({}),
            policy: {},
            policies: {},
            search: '',
            style: 'table',
        }
    },
    emits:['refreshPolicies'],
    methods:{
        changeStyle(style){
            this.style = style;
        },
        createPolicy(){
            this.editMode = false;
            this.policy = {};
            $('#policyModal').modal('show');
        },
        getAllInitials(page=1){
            this.loading = true;
            axios.get('/api/policies?page='+page+'&search='+this.search).then(response =>{
                this.loading = false;
                this.policies = response.data.policies;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'Policies not loaded successfully',});
            });
        },
    },
    mounted() {
        this.getAllInitials();
    }   
}
</script>