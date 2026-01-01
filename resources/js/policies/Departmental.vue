<template>
    <div class="row clearfix">
        <div class="col-md-12">
            <div class="card">
                <div class="card-header bg-navy">
                    <h3 class="card-title">My Departmental Policies</h3>
                    <div class="card-tools">
                        <div class="input-group input-group-sm" style="width: 300px;">
                            <input type="text" name="table_search" class="form-control float-right" placeholder="Search" v-model="search">
                            <div class="input-group-append">
                                <button type="button" class="btn btn-primary" @click="getAllInitials()"><i class="fas fa-search"></i></button>
                                <button type="button" class="btn btn-sm btn-success" @click="createPolicy" v-if="source == 'admin'"><i class="fa fa-plus mr-1"></i>Add New</button>
                                <button title="View As Cards" type="button" class="btn btn-default" @click="changeStyle('grid')" v-if="style != 'grid'"><i class="fas fa-table"></i></button>
                                <button title="View As Table" type="button" class="btn btn-default" @click="changeStyle('table')"  v-if="style != 'table'"><i class="fas fa-list"></i></button>         
                            </div>  
                        </div>
                    </div>
                </div>            
                <PoliciesDetailList :policies.sync="policies.data" source="departmental" :style.sync="style" @refreshPolicies="getAllInitials(current_page)"/>
                <div class="card-footer">
                    <pagination v-model="current_page" @paginate="getAllInitials" :per-page="policies.per_page != null ? policies.per_page : 52" :records="policies.total != null ? policies.total : 550" ></pagination>
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    data(){
        return {
            current_page: 1,
            editMode: false,
            loading: false,
            policy:{},
            policies:{
                data: [],
            },
            style: 'grid',
            search: '',
        }
    },
    methods:{
        changeStyle(style){
            this.style = style;
        },
        getAllInitials(page=1){
            this.loading = true;
            axios.get('/api/policies/all/departmental?page='+page+'&query='+this.search)
            .then(response =>{
                this.policies = response.data.policies;
                this.loading = false;
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Policies were not loaded successfully',
                })
            });
        },
    },
    mounted() {
        this.getAllInitials();
    },
}
</script>