<template>
<section class="col-md-12">
    <div class="modal fade" id="hrItemModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">HR Items Detail</h4><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <HrmsFormAssessmentHrItem :editMode="editMode" :hr_item.sync="hr_item" @reloadHrItem="getAllInitials"/>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header bg-navy">
            <h3 class="card-title">HR Assessment Items</h3>
            <div class="card-tools">
                <div class="input-group" style="width: 350px;">
                    <input type="text" name="table_search" class="form-control float-right" placeholder="Search" v-model="query">
                    <div class="input-group-append">
                        <button type="button" class="btn btn-default" @click="getAllInitials"><i class="fas fa-search"></i></button>
                        <select name="table_search" class="form-control float-right ml-1" v-model="type" @change="getAllInitials">
                            <option value="">All</option>
                            <option value="active">Active</option>
                            <option value="inactive">Inactive</option>
                        </select>
                        <button type="button" class="btn btn-primary ml-1" @click="addHrItem"><i class="fas fa-plus"></i></button>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body table-responsive p-0 overlay-wrapper"style="height: 500px;">
            <HrmsDetailAssessmentHrItemList :hr_items="hr_items.data" @relaodHrItemList="getAllInitials"/>
        </div>
        <div class="card-footer">
            <pagination v-model="current_page" @paginate="getAllInitials" :per-page="hr_items.per_page != null ? hr_items.per_page : 52" :records="hr_items.total != null ? hr_items.total : 550" ></pagination>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return {
            current_page: 1,
            editMode: false,
            form: new Form({}),
            hr_item: {},
            hr_items: {data: [], total: 0},
            loading: false,
            query: '',
            type: '',
        }
    },
    emits:['relaodHrItemList'],
    methods:{
        addHrItem(){
            this.editMode = false;
            this.loading = true;
            this.hr_item = {};
            $('#hrItemModal').modal('show');
            this.loading = false;
        },
        closeModals(){
            $('#hrItemModal').modal('hide');
        },
        getAllInitials(){
            this.loading = true
            axios.get('/api/hrms/assessment_hr_items?query='+this.query+'&type='+this.type+'&page='+this.current_page).then(response =>{
                this.refreshPage(response);
                this.loading = false;
                this.$toast.fire({icon: 'success', title: 'Hr Assessment Items loaded successfully',
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'Hr Assessment Items not loaded successfully',})
            });
        },
        refreshPage(response){
            this.hr_items = response.data.hr_items;
            this.closeModals();
        },
        viewHrItem(hr_item){
            this.hr_item = hr_item;
            $('#hrItemModal').modal('show');
        },
    },
    mounted(){ 
        this.getAllInitials();
    },
}
</script>