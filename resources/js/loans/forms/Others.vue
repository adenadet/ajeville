<template>
<section>
    <form action="POST" @submit.prevent="createUserKYCData()" enctype="multipart/form-data">
        <alert-error :form="LoanOtherData"></alert-error>
        <div class="row">
            <div class="col-12">
                <div class="form-group">
                    <label class="control-label">File Name</label>
                    <input type="text" name="file_name" id="file_name" class="form-control" v-model="LoanOtherData.file_name" />
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <label for="bvn" class="control-label">File</label>
                    <input type="file" name="file" id="file" class="form-control" @change="addFile()" />
                </div>
            </div>
            <div class="col-12">
                <div class="form-group">
                    <label class="control-label">Comment</label>
                    <wysiwyg name="file_comment" id="file_comment" rows="5" v-model="LoanOtherData.file_comment"/>
                </div>
            </div>
            <button class="btn btn-sm btn-success" type="submit">Submit</button>
        </div>
    </form>
</section>
</template>
<script>
export default {
    data(){
        return  {
            kyc_items: [],
            LoanOtherData: new Form({
                account_id: '',
                file_name: '',
                file: '',
                file_comment:'', 
            }),
        }
    },
    mounted() {
        //this.getInitials();
        Fire.$on('UserKYCDataFill', details =>{
            this.UserKYCData.user_id = details.user_id;
            this.kyc_items = details.kyc_list;
            this.UserKYCData.kyc_items = details.kyc_items;
            for (var i = 0; i < details.kyc_items.length; i++){
                this.UserKYCData.kyc_items[i].name = details.kyc_items[i].name;
                this.UserKYCData.kyc_items[i].kyc_type = details.kyc_items[i].kyc_type; 
                this.UserKYCData.kyc_items[i].kyc_identification = details.kyc_items[i].kyc_identification;
                this.UserKYCData.kyc_items[i].kyc_file = details.kyc_items[i].kyc_file;
                this.UserKYCData.kyc_items[i].kyc_file_type = details.kyc_items[i].kyc_file_type;
                this.UserKYCData.kyc_items[i].kyc_expiry_date = details.kyc_items[i].kyc_expiry_date;
            }
        });
    },
    methods:{
        addFile(fileKey, event) {
            let file = event.target.files[0];
            let reader = new FileReader();
            if (file['size'] > 2000000){
                Swal.fire({icon: 'error', title: 'File is too large'});
            }
            else if((file['type'] != 'image/png') && (file['type'] != 'image/jpg') && (file['type'] != 'image/jpeg') && (file['type'] != 'application/pdf')){
                Swal.fire({icon: 'error', title: 'Invalid File Type'});
            }
            else{
                reader.onloadend = (event) => {
                    this.UserKYCData.kyc_items[fileKey].kyc_file = reader.result;
                    this.UserKYCData.kyc_items[fileKey].kyc_file_type = ((file['type'] == 'image/png') || (file['type'] == 'image/jpg') || (file['type'] == 'image/jpeg')) ? 'Image': 'PDF';
                    }
                reader.readAsDataURL(file)
            }
        },
        createOtherFile(){
            this.$Progress.start();
            this.UserKYCData.user_id = this.user.id;
            this.UserKYCData.post('/api/loans/others')
            .then(response =>{
                this.$Progress.finish();
                Fire.$emit('Reload', response);
                Swal.fire({
                    icon: 'success',
                    title: 'The User KYC has been created',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
                this.$Progress.fail();
            });
        },
        getInitials(){
            axios.get('/api/ums/kyc_items').then(response =>{
                this.kyc_items = response.data.kyc_items;
                for (let i=0; i < this.kyc_items.length; i++){
                    var item = {id: this.kyc_items[i].id, name: this.kyc_items[i].name, file: '', expiry_date: '', type: '', identification: '',};
                    this.UserKYCData.kyc_items.push(item);
                }
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({icon: 'error', title: 'User KYC items not loaded successfully',});
            });
        },
        updateOtherFile(){
            this.$Progress.start();
            this.UserKYCData.put('/api/loans/others/'+ this.UserKYCData.id)
            .then(response =>{
                this.$Progress.finish();
                Fire.$emit('Reload', response);
                Swal.fire({
                    icon: 'success',
                    title: 'The User'+ response.data.user.first_name+' '+  response.data.user.last_name+' has been updated',
                    showConfirmButton: false,
                    timer: 1500
                });
            })
            .catch(()=>{
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
                this.$Progress.fail();
            });            
        },
        updateFile(e){
            alert('Test');
        }
    },
    props:{
        user: Object,
        editMode: Boolean,
        account: Object,
    }
}
</script>
