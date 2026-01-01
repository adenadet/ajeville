<template>
<section>
    <form @submit.prevent="createConfirmation()">
        <alert-error :form="ScoreRequestData"></alert-error>
        <div class="row">
            <div class="col-8">
                <div class="form-group">
                    <label class="control-label">Customer Name</label>
                    <div class="form-control">{{ account.user | FullName }}</div>
                </div>
            </div>
            <div class="col-4">
                <div class="form-group">
                    <label for="bvn" class="control-label">BVN</label>
                    <div class="form-control">{{ account.user != null ? account.user.bvn : 'No User Supplied'}}</div>
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label class="control-label">Credit Bureau</label>
                    <select class="form-control" id="bureau_id" name="bureau_id" v-model="ScoreRequestData.bureau_id" required>
                        <option value="">--Select Credit Bureau--</option>
                        <option v-for="(bureau,index) in bureaus" :value="index">{{ bureau.name }}</option>
                    </select>
                </div>
            </div>
            <div class="col-6">
                <div class="form-group">
                    <label class="control-label">Check Type</label>
                    <select class="form-control" id="product_id" name="product_id" v-model="ScoreRequestData.product_id" required>
                        <option value="">--Select Search Type--</option>
                        <option v-for="(product, index) in bureau_products" :value="index">{{ product.name }}</option>
                    </select>
                </div>
            </div>
            <button class="btn btn-sm btn-success" type="submit">Get Score</button>
        </div>
    </form>
</section>
</template>
<script>
export default{
	created(){
		//this.getInitials();
	},
    data () {
        return {
            ScoreRequestData: new Form({
                EnquiryReason:"Test",
                ConsumerName:"",
                DateOfBirth:"",
                Identification:"",
                Accountno:"",
                bureau_id: "",
                product_id: "",
                ProductID:"45"
            }),
        };
    },
    methods: {
        createConfirmation(){
            this.$Progress.start();
            this.ScoreRequestData.DataTicket = "eyJhbGciOiJIUzI1NiIsInR5cCI6IkpXVCJ9.eyJ1c2VybmFtZSI6ImRlbW8iLCJwYXNzd29yZCI6ImRlbW9AMTIzIiwiaWF0IjoxNjk3NTc3NzM1LCJleHAiOjE3MTU1Nzc3MzV9.hp4Kh6e2Yu00fqY5clP0A7NTJARbF-sgvtSpUDxsN8U"
            this.ScoreRequestData.Identification = this.account.user.bvn;
            this.ScoreRequestData.ProductID = this.bureau_products[this.ScoreRequestData.product_id].id;
            this.ScoreRequestData.post("https://uat.firstcentralcreditbureau.com/firstcentralrestv2/ConnectConsumerMatch")
            .then(response =>{
                this.$Progress.finish();
                alert(response.data);
                Fire.$emit('refreshRepayment', response);
                this.RescheduleData.reset();
                Swal.fire({icon: 'success', title: 'The Checklist has been saved', showConfirmButton: false, timer: 1500});
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
        createCheckList(){
            this.$Progress.start();
            this.loanCheckData.loan_id = this.account.id;
            this.loanCheckData.post('/api/loans/checklists')
            .then(response =>{
                this.$Progress.finish();
                Fire.$emit('refreshRepayment', response);
                this.RescheduleData.reset();
                Swal.fire({icon: 'success', title: 'The Checklist has been saved', showConfirmButton: false, timer: 1500});
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
		updateProfilePic(){

		},
    },
    props:{
        account: Object,
        bureau_products: Array,
        bureaus: Array,
        editMode: Boolean,    
    }
}
</script>