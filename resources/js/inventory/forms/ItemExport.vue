<template>
    <section class="col-md-12">
        <div class="card card-primary">
            <div class="card-header"><h3 class="card-title">Item Upload</h3></div>
            <form role="form" @submit.prevent="uploadItems">
                <div class="card-body">
                    <div class="form-group">
                        <label>Type</label>
                        <select type="email" class="form-control" name="upload_type" id="upload_type">
                            <option value="">--Select Uplad Type</option>
                            <option value="csv">CSV</option>
                            <option value="excel">Excel</option>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>File</label>
                        <input type="file" class="form-control" name="upload_file" id="upload_file" @change="uploadFile">
                    </div>
                </div>
                <div class="card-footer">
                    <button type="submit" class="btn btn-primary">Submit</button>
                </div>
            </form>
        </div>
    </section>
</template>
<script>
export default {
    data() {
        return {
            ItemImportData: new Form({
                type: '',
                file: '',
            }),
            loading: false,
        }
    },
    emits:['refreshPage'],
    mounted() {
        //this.getAllInitials();
    },
    methods: {
        uploadItems(){
            this.loading = true;
            this.ItemImportData.post('/api/inventory/items/import')
            .then(response =>{
                this.$emit('refreshPage', response);
                this.loading = false;
                this.$swal.fire({icon: 'success', title: 'The Items have been created', showConfirmButton: false, timer: 1500});
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
                    this.ItemImportData.file = reader.result;
                    //console.log(reader.result);
                    }
                reader.readAsDataURL(file)
            }
            else{this.$swal.fire({type: 'error', title: 'File is too large'});}
        },
    },
}
</script>