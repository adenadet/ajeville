<template>
    <section class="container-fluid overlay-wrapper">
        <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <div class="row">
            <div class="col-md-12">
                <form @submit.prevent="uploadDrugItemList()">
                    <div class="form-group">
                        <label for="exampleInputEmail1"> Drug Items List</label>
                        <div class="form-control" >Drug List</div>
                    </div>
                    <div class="form-group">
                        <label for="exampleInputFile">File input</label>
                        <div class="input-group">
                            <div class="custom-file">
                                <input type="file" class="form-control" id="exampleInputFile" @change="updateFile">
                            </div>
                        </div>
                    </div>
                    <button type="button" class="btn btn-light"><i class="fa fa-download mr-1"></i>Download Template</button>
                    <button type="submit" class="btn btn-primary">Submit</button>
                </form>
            </div>
        </div>
    </section>
</template>
<script>
export default {
    data(){
        return  {
            branches: [],
            uploadData: new Form({
                id: '',
                uploaded_file: '',
                description: '',
            }),
            loading: false,
        }
    },
    mounted() {
        Fire.$on('drugItemFormReset', () => {
            this.uploadData.reset();
            //this.uploadData.clear();
        });
    },
    methods:{
        updateFile(e){
            let file = e.target.files[0];
            let reader = new FileReader();
            if (file['size'] < 2000000){
                reader.onloadend = (e) => {
                    this.uploadData.uploaded_file = reader.result
                    }
                reader.readAsDataURL(file)
            }
            else{
                Swal.fire({
                    type: 'error',
                    title: 'File is too large'
                })
            }
        },
        uploadDrugItemList(){
            this.$Progress.start();
            this.loading = true;
            this.uploadData.post('/api/emr/pharmacy/drug_items/import')
            .then(response =>{
                Fire.$emit('PriceListRefresh', response);
                Swal.fire({
                    icon: 'success',
                    title: 'The PriceList '+this.priceList.name+' has been uploaded',
                    showConfirmButton: false,
                    timer: 1500
                });
                this.$Progress.finish();
                this.loading = false;
                this.uploadData.clear();
            })
            .catch(()=>{
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
                this.$Progress.fail();
                this.loading = false;
            });            
        },
    },
}
</script>