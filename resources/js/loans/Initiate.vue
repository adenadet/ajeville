<template>
<section class="col-12">
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title">
                <i class="fas fa-edit"></i>
                Loan Application
            </h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-12">
                    <div class="tab-content" id="vert-tabs-tabContent">
                        <div class="tab-pane text-left fade show active" id="customer" role="tabpanel" aria-labelledby="customer">
                            <PMFormBasic :editMode="editMode" :user="user" />
                        </div>
                        <div class="tab-pane text-left fade" id="address" role="tabpanel" aria-labelledby="address">
                            <PMFormAddress :editMode="editMode" :user="user" :areas="areas" :states="states" />
                        </div>
                        <div class="tab-pane text-left fade" id="loan" role="tabpanel" aria-labelledby="loan">
                            <LoanForm :loan="current_loan" :user="user" :loan_types="loan_types" :banks="banks"/>
                        </div>
                        <div class="tab-pane fade" id="kyc" role="tabpanel" aria-labelledby="kyc">
                            <UserFormKYC :user="user"/>
                        </div>
                        <div class="tab-pane fade" id="vert-tabs-profile" role="tabpanel" aria-labelledby="vert-tabs-profile-tab">
                            <GuarantorFormRequest :loan="current_loan" />    
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
    data(){
        return {
            areas: [],
            states: [],
            banks: [],
            loan_types: [],
            current_loan: {},
            user: {},

        }
    },
    methods:{
        createLoan(){
            this.loanData.post('/api/loans/accounts')
            .then(response=>{
                Fire.$emit('GetCourse', response);
                Swal.fire({
                    icon: 'success',
                    title: response.data.message,
                });
                this.courseData.reset();
                Fire.emit('')
            })
            .catch(()=>{
                this.$Progress.fail();
                Swal.fire({
                    icon: 'error',
                    title: 'Your form was not sent try again later!',
                });
            });
        },
        getInitials(){
            axios.get('/api/loans/accounts/initials').then(response =>{
                this.reloadPage(response);
            })
            .catch(()=>{
                toast.fire({
                    icon: 'error',
                    title: 'Loan Form Initialization failed',
                })
            });
        },
        reloadPage(response){
            this.areas = response.data.areas;
            this.banks = response.data.all_banks;
            this.current_loan = response.data.current_loan;
            this.loan_types = response.data.loan_types;
            this.states = response.data.states;
            this.user = response.data.user;
            this.kyc_items = response.data.kyc_items;

            Fire.$emit('BioDataFill', this.user);
            Fire.$emit('NextOfKinFill', this.nok); 
            Fire.$emit('AddressDataFill', this.user.customer_address);
            Fire.$emit('LoanDataFill', this.current_loan);
            Fire.$emit('UserKYCDataFill', {user_id: this.user.id, kyc_items: this.kyc_items});
            if (this.current_loan != null){Fire.$emit('GuarantorDataFill', this.current_loan)}
        },
        updateLoan(id){
            this.loanData.put('/api/loans/accounts/'+this.loanData.id)
            .then(response=>{
                Swal.fire({
                    icon: 'success',
                    title: response.data.message,
                });
            })
            .catch(()=>{
                this.$Progress.fail();
                Swal.fire({
                    icon: 'error',
                    title: 'Your form was not sent try again later!',
                });
            });
        }, 
    },
    mounted() {
        this.getInitials();
        Fire.$on('CourseDataFill', course =>{
            this.courseData.reset();
            this.courseData.fill(course);
            if ((course.category_id !== null)&&(typeof course.category !== 'undefined')){
                this.prev_category = course.category;
                this.sub_categories = course.category.sub_categories;
                
                }
            else{
                this.courseData.category_id = "";
                this.courseData.sub_category_id = "";
                this.courseData.certificate_type_id = "";
                this.courseData.exam_type_id = "";
            }
        });     
    },
    props: {
        editMode: Boolean,
        loan: Object,
    },
}
</script>