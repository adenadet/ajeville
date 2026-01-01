<template>
<section >
    <div class="modal fade" id="creditModal">
        <div class="modal-dialog modal-lg">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">New Credit Score Search</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close"><span aria-hidden="true">&times;</span></button>
                </div>
                <div class="modal-body">
                    <LoanFormCreditScore :editMode="editMode" :account="account" :bureau_products="bureau_products" :bureaus="bureaus"/>
                </div>
            </div>
        </div>
    </div>
    <div class="card">
        <div class="card-header bg-dark">
            <h3 class="card-title">Loan Credit Score</h3>
            <button  v-if="source == 'risk'" class="card-tools btn btn-sm btn-primary" @click="addNew">Get New Credit Score</button>
        </div>
        <div class="card-body">
            <div class="card card-success card-outline card-tabs">
                <div class="card-header p-0 pt-1 border-bottom-0">
                    <ul class="nav nav-tabs" id="custom-tabs-three-tab" role="tablist">
                        <li class="nav-item"  v-for="(credit_score, index) in credit_scores.data" :key="credit_score.id">
                            <a class="nav-link" :class="index==0 ? 'active': ''" :id="'credit-score-'+index+'-tab'" data-toggle="pill" :href="'#credit-score-'+index" role="tab" :aria-controls="'credit-score-'+index" aria-selected="true">{{ credit_score.created_at | excelDate }}</a>
                        </li>
                    </ul>
                </div>
                <div class="card-body">
                    <div class="tab-content" id="custom-tabs-three-tabContent">
                        <div v-for="(credit_score, index) in credit_scores.data" :key="credit_score.id" class="tab-pane fade table-responsive p-0" :class="index==0 ? 'show active': ''" :id="'credit-score-'+index" role="tabpanel" :aria-labelledby="'credit-score-'+index+'-tab'">
                            <table class="table table-bordered table-striped text-no">
                                <tbody>
                                    <tr>
                                        <td>Provided By:</td>
                                        <td> {{credit_score.product.bureau.name}} </td>
                                    </tr>
                                    <tr>
                                        <td>Query Type:</td>
                                        <td> {{credit_score.product.name}} </td>
                                    </tr>
                                    <tr>
                                        <td>Created By:</td>
                                        <td> {{credit_score.creator | FullName}} </td>
                                    </tr>
                                    <tr>
                                        <td>Date:</td>
                                        <td>{{ credit_score.created_at | excelDate }}</td>
                                    </tr>
                                    <tr>
                                        <td colspan="2" class="text-wrap">
                                            <wysiwyg v-model="credit_score.response" disabled /> 
                                        </td>
                                    </tr>
                                </tbody>
                            </table>    
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    computed:{

    },
    data(){
        return  {
            account: {},
            bureaus: [],
            bureau_products: [],
            credit_scores: [],
            checkData: new Form({
                DataTicket: "",
                EnquiryReason:"",
                ConsumerName:"",
                DateOfBirth:"",
                Identification:"22471069115",
                Accountno:"",
                ProductID:"45",
            }),
            editMode: false,
            loader: null,
            loan: {},
            products: [],
        }
    },
    mounted(){
        this.getInitials();
        Fire.$on('refreshLoan', response =>{
            //this.getInitials();
            this.reloadPage(response);
            this.closeModals();
        });
    },
    methods:{
        addNew(){
            this.$Progress.start();
            this.editMode = false;
            $('#creditModal').modal('show');
            this.$Progress.finish();
        },
        closeModals(){
            $('#creditModal').modal('hide');
        },
        getInitials(){
            this.$Progress.start();
            this.showLoader();
            axios.get('/api/loans/credit_scores/'+this.$route.params.id).
            then(response =>{
                this.reloadPage(response);
                this.$Progress.finish();
            })
            .catch(()=>{
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Account Credit Score was not loaded successfully',
                })
            });
        },
        hideLoader(){
            loader.close();
        },
        reloadPage(response){
            this.account = response.data.account;
            this.bureaus = response.data.bureaus;
            this.bureau_products = response.data.bureau_products;
            this.credit_scores = response.data.credit_scores;
            this.editMode = response.data.credit_score.id != null ? false : true;
        },
        showLoader(text="Loading"){
           
        }

    },  
    props:{
        source: String,
    },
}
</script>