<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading">
        <i class="fas fa-3x fa-sync-alt fa-spin"></i>
        <div class="text-bold pt-2">Loading...</div>
    </div>
    <form role="form" @submit.prevent="uploadCustomers">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Type</label>
                    <select class="form-control" name="upload_type" id="upload_type" v-model="customerUploadData.type" required>
                        <option value="">--Select Uplad Type</option>
                        <option value="csv">CSV</option>
                        <option value="excel">Excel</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>File</label>
                    <input type="file" class="form-control" name="upload_file" id="upload_file" @change="uploadFile">
                </div>
            </div>
            <div class="col-md-12">
                <button type="submit" class="btn btn-primary">Submit</button>
            </div>
        </div>    
    </form>
</section>
</template>

<script>
export default {
    data() {
        return {
            customerUploadData: new Form({
                type: '',
                file: '',
            }),
            loading: false,
        }
    },
    methods: {
        uploadCustomers(){
            this.loading = true;
            this.customerUploadData.post('/api/crm/customers/import')
            .then(response =>{
                this.$emit('refreshPage', response);
                this.loading = false;
                this.$swal.fire({icon: 'success', title: 'The Employees have been created', showConfirmButton: false, timer: 1500});
            })
            .catch(()=>{
                this.loading = false;
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });
        },
        uploadFile(e){
            let file = e.target.files[0];
            let reader = new FileReader();
            if (file['size'] < 2000000){
                reader.onloadend = (e) => {
                    this.customerUploadData.file = reader.result;
                }
                reader.readAsDataURL(file)
            }
            else{this.$swal.fire({type: 'error', title: 'File is too large'});}
        },
    },
};
</script>

<style scoped>
.card-title {
  margin-bottom: 0;
}
</style>
