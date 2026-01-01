<template>
<section>
    <form>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Name *</label>
                    <input type="text" required class="form-control" id="amount" name="amount" placeholder="First Name *" v-model="DepositData.amount" :class="{'is-invalid' : DepositData.errors.has('first_name') }" :max="trans_sum">
                    <has-error :form="DepositData" field="amount"></has-error> 
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Percentage Charge</label>
                    <model-list-select class="form-control" :list="banks" v-model="DepositData.bank_id" option-value="id" option-text="name" placeholder="Select Bank" />
                    <has-error :form="DepositData" field="bank_id"></has-error> 
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Fixed Charge</label>
                    <model-list-select class="form-control" :list="modes" v-model="DepositData.mode_id" option-value="id" option-text="name" placeholder="Select Payment Type" />
                    <has-error :form="DepositData" field="bank_id"></has-error> 
                </div>
            </div>
            <button @click.prevent="updateBioData" type="submit" name="submit" class="submit btn btn-primary">Submit</button>
        </div>
       
    </form>
</section>
</template>
<script>
export default {
    data(){
        return  {
            banks: [],
            DepositData: new Form({
                amount: '',
                mode_id: '',
                bank_id: '',
                patient_id:'', 
                transactions:[], 
            }),
            modes: [],
            trans_sum: 1,
        }
    },
    mounted() {
        this.getInitials();
    },
    methods:{
        createDepositData(){
            console.log("Tested");
            this.$Progress.start();
            this.DepositData.post('/api/hrms/bios')
            .then(response =>{
                this.$Progress.finish();
                Fire.$emit('Reload', response);
                Swal.fire({
                    icon: 'success',
                    title: 'The Profile details has been updated',
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
            axios.get('/api/hrms/bios')
            .then(response =>{
                this.nations = response.data.nations;
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Dashboard not loaded successfully',
                })
            });
        },
        updateDepositData(){
            this.$Progress.start();
            this.DepositData.put('/api/hrms/bios/'+this.DepositData.id)
            .then(response =>{
                this.$Progress.finish();
                Fire.$emit('Reload', response);
                Swal.fire({
                    icon: 'success',
                    title: 'The Profile details has been updated',
                    showConfirmButton: false,
                    timer: 1500
                    });
                })
            .catch(()=>{
                Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
                this.$Progress.fail();
            });            
        },
        getProfilePic(){
            let photo = (this.DepositData.image.length >= 150) ? this.DepositData.image : "./"+this.DepositData.image;
            return photo;
            },
        updateProfilePic(e){
            let file = e.target.files[0];
            let reader = new FileReader();
            if (file['size'] < 2000000){
                reader.onloadend = (e) => {this.DepositData.image = reader.result}
                reader.readAsDataURL(file)
            }
            else{
                Swal.fire({type: 'error', title: 'File is too large'});
            }
        },
    },
    props:{
        editMode: Boolean,
        patient: Object,
        source: String,
        transactions: Array,
    }
}
</script>
