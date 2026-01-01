<template>
<section>
    <div class="row">
        <div class="col-md-3">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    
                    <h3 class="profile-username text-center">{{ store_item.item != null ? store_item.item.name : 'Not Applicable' }}</h3>
                    <p class="text-muted text-center">{{ store_item.item != null && store_item.item.classification != null? store_item.item.classification.name : 'Not Applicable' }}</p>
                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item"><b>Classification:</b> <a class="float-right">{{ store_item.item != null && store_item.item.classification != null? store_item.item.classification.name : 'Not Applicable' }}</a></li>
                        <li class="list-group-item"><b>Category:</b> <a class="float-right">{{ store_item.item != null && store_item.item.category != null? store_item.item.category.name : 'Not Applicable' }}</a></li>
                        <li class="list-group-item"><b>Brand:</b> <a class="float-right">{{ store_item.item != null && store_item.item.brand != null? store_item.item.brand.name : 'Not Applicable' }}</a></li>
                    </ul>
                    <a href="#" class="btn btn-primary btn-block"><b>Follow</b></a>
                </div>
            </div>
        </div>
        <div class="col-md-9">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Available Batches</h3>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped">
                        <thead>
                            <tr>
                                <th>Batch ID</th>
                                <th>Expiry Date</th>
                                <th>Quantity Available</th>
                                <th>Received</th>
                                <th>Transferred</th>
                                <th>Issued</th>
                                <th>Sold</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr v-for="batch in store_item.batches">
                                <td>{{ batch.batch_number }}</td>
                                <td>{{ batch.expiry_date }}</td>
                                <td>{{ batch.balance }}</td>
                                <td class="text-success">{{ batch.received }}</td>
                                <td class="text-danger">{{ batch.transferred }}</td>
                                <td class="text-danger">{{ batch.issued }}</td>
                                <td class="text-danger">{{ batch.sold }}</td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</section>
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
            loading: false,
        }
    },
    methods:{
        createNotice(){
            this.editMode = false;
            this.notice = {};
            $('#noticeModal').modal('show');
        },
        deleteNotice(id){
            Swal.fire({
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
                    Swal.fire('Deleted!', 'Notice has been deleted.', 'success');
                    Fire.$emit('CatRefresh', response);   
                    })
                    .catch(()=>{Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!',});});
                }
            }); 
        },
        editNotice(notice){
            this.editMode = true;
            this.notice = notice;
            //Fire.$emit('noticeDataFill', notice);
            $('#noticeModal').modal('show');
        },
        getAllInitials(store_item_id){
            this.loading = true;
            axios.get('/api/inventory/store_items/'+store_item_id).then(response =>{
                this.reset(response);
                this.loading = false;
                this.$toast.fire({icon: 'success', title: 'Notice loaded successfully',});
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'Notice not loaded successfully',});
            });
        },
    },
    mounted() {
        //this.getAllInitials();
    },
    props:{
        store_item: Object,
        view: String,
    }, 
}
</script>