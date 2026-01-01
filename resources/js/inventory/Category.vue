<template>
<section class="row">
    <div class="col-md-4">
        <div class="card">
            <div class="card-header bg-dark">
                <h3 class="card-title">Category Details</h3>
            </div>
            <div class="card-body">
                <strong><i class="fas fa-book mr-1"></i> Name</strong><p class="text-muted">{{category.name}}</p>
                <hr>
                <strong><i class="fas fa-map-marker-alt mr-1"></i> Primary Category</strong><p class="text-muted">{{category.category != null ? category.category.name : 'N/A'}}</p>
                <hr>
                <strong><i class="fas fa-pencil-alt mr-1"></i> Status</strong><p class="text-muted">{{category.status != 1 ? 'Inactive' : 'Active'}}</p>
                <hr>
                <strong><i class="far fa-file-alt mr-1"></i> Notes</strong><p class="text-muted">{{category.description}}</p>
            </div>
        </div>
    </div>
    <div class="col-md-8">
        <div class="card">
            <div class="card-header bg-dark">
                <h3 class="card-title">Items</h3>
            </div>
            <div class="card-body overlay-wrapper table-responsive p-0" style="height: 300px;">
                <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                <table class="table table-head-fixed text-nowrap">
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>User</th>
                            <th>Date</th>
                            <th>Status</th>
                            <th>Reason</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody v-if="items != null && items.total != 0">
                        <tr v-for="item in items.data">
                            <td>183</td>
                            <td>{{item.name}}</td>
                            <td>11-7-2014</td>
                            <td><span class="tag tag-success">Approved</span></td>
                            <td>Bacon ipsum dolor sit amet salami venison chicken flank fatback doner.</td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return {
            category: {},
            current_page: 1,
            editMode: false,
            expired_items: {},
            form: new Form({}),
            loading: false,
            soon_to_expire_items: {},
            user_stores: {},
            items: {}, 
        }
    },
    methods:{
        createNotice(){
            this.editMode = false;
            this.notice = {};
            //Fire.$emit('noticeDataFill', this.notice);
            $('#noticeModal').modal('show');
        },
        deleteNotice(id){
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
                    this.form.delete('/api/notices/'+id)
                    .then(response=>{
                        this.$swal.fire('Deleted!', 'Inventory dashboard has been deleted.', 'success');
                        this.$emit('CatRefresh', response);   
                    })
                    .catch(()=>{this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!',});});
                }
            }); 
        },
        getAllInitials(page=1){
            this.loading = true;
            axios.get('/api/inventory/categories/'+this.$route.params.id+'?t=all&page='+page)
            .then(response =>{
                this.reset(response);
                this.loading = false;
                this.$toast.fire({icon: 'success', title: 'Inventory dashboard loaded successfully',});
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error', 
                    title: 'Inventory dashboard not loaded successfully',
                });
            });
        },
        reset(response){
            this.category = response.data.category;
            this.items = response.data.items;
        },
    },
    mounted() {
        this.getAllInitials();
    }   
}
</script>