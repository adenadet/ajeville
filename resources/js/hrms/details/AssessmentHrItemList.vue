<template>
<section class="overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="modal fade" id="hrItemFormModal">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header bg-navy">
                    <h4 class="modal-title">HR Items Detail</h4><button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true" class="text-white">&times;</span></button>
                </div>
                <div class="modal-body">
                    <HrmsFormAssessmentHrItem :editMode.sync="editMode" :hr_item.sync="hr_item" @reloadHrItem="getAllInitials"/>
                </div>
            </div>
        </div>
    </div>
    <table class="table table-head-fixed text-nowrap table-striped table-hover">
        <thead>
            <tr>
                <th>Title</th>
                <th>Max Score</th>
                <th>Status</th>
                <th>Description</th>
                <th>&nbsp;</th>
            </tr>
        </thead>
        <tbody v-if="hr_items.length > 0">
            <tr v-for="hr_item in hr_items">
                <td>{{ hr_item.title }}</td>
                <td>{{ hr_item.max_score }}</td>
                <td>
                    <span v-if="hr_item.status == 1" class="badge badge-success">Active</span>
                    <span v-else-if="hr_item.status == 0" class="badge badge-danger">Inactive</span>
                </td>
                <td v-html="readMore(hr_item.description, 50, '...')" :title="hr_item.description"></td>
                <td>
                    <button class="nav-link btn btn-tool" data-toggle="dropdown" type="button"><i class="fa fa-ellipsis-v text-dark"></i></button>
                    <div class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                        <button class="dropdown-item btn btn-block btn-sm"><i class="fa fa-eye mr-1 text-primary"></i> View HR item</button>
                        <button class="dropdown-item btn btn-block btn-sm" @click="updateHrItem(hr_item)"><i class="fa fa-edit mr-1 text-warning"></i> Update HR Item</button>
                        <button v-if="hr_item.status == 1" class="dropdown-item btn btn-block btn-sm" @click="deactivateHRItem(hr_item.id)"><i class="fa fa-recycle mr-1 text-danger"></i> Deactivate Item</button>
                        <button v-if="hr_item.status == 0" class="dropdown-item btn btn-block btn-sm" @click="deactivateHRItem(hr_item.id)"><i class="fa fa-recycle mr-1 text-success"></i> Reactivate Item</button>
                    </div>
                </td>
            </tr>
        </tbody>
        <tbody v-else>
            <tr>
                <td colspan="5">No Items meets your requirements</td>
            </tr>
        </tbody>
    </table>
</section>
</template>
<script>
export default {
    data(){
        return {
            hr_item: {},
            editMode: false,
            form: new Form({}),
            loading: false,
        }
    },
    emits:['relaodHrItemList'],
    methods:{
        closeModals(){
            $('#hrItemFormModal').modal('hide');
        },
        deleteHrItem(id){
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
                //Send Delete request
                if(result.value){
                    this.loading = true;
                    this.form.delete('/api/hrms/assessment_hr_items/'+id)
                    .then(response=>{
                        this. $swal.fire('Deleted!', response.data.message, 'success');
                        this.getAllInitials();
                        this.loading = false;   
                    })
                    .catch(()=>{
                        Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });  
        },
        getAllInitials(){
            this.loading = true;
            this.$emit('relaodHrItemList');
            this.closeModals();
            this.loading = false;
        },
        updateHrItem(hr_item){
            this.loading = true;
            this.editMode = true;
            this.hr_item = hr_item;
            $('#hrItemFormModal').modal('show');
            this.loading = false;
        },
        searchHrItem(){
            axios.get('/api/hrms/hr_items/search/'+this.query)
            .then((response ) => {this.hr_items = response.data.hr_items;})
            .catch(()=>{});
        },
        viewHrItem(hr_item){
            this.hr_item = hr_item;
            $('#hr_itemModal').modal('show');
        },
    },
    mounted(){ 
        //this.getAllInitials();
    },
    props:{
        hr_items: Array,
    },
    watch:{}

}
</script>