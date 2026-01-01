<template>
<section>
    <div class="card">
        <div class="card-header bg-navy">
            <h3 class="card-title">Policy: {{policy.name}}</h3>
        </div>
        <div class="card-body p-0 overlay-wrapper">
            <div class="row"><!--iframe src="{{ asset($policy->file) }}" class="col-12" style="min-height: 1000px"></iframe-->
                <div class="col-md-12">
                    <PDFViewer :source="'/uploads/files/1-1725633951.pdf'" @download="handleDownload"></PDFViewer>
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
            loading: false,
            policy: {},
        }
    },
    methods:{
        closeModals(){
            $('#assignModal').modal('hide');
            $('#policyModal').modal('hide');
        },
        createPolicy(){
            this.editMode = false;
            this.policy = {};
            Fire.$emit('policyDataFill', this.policy);
            $('#policyModal').modal('show');
        },
        deletePolicy(id){
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
                    this.form.delete('/api/policies/'+id)
                    .then(response=>{
                    Swal.fire('Deleted!', 'Policies has been deleted.', 'success');
                    Fire.$emit('CatRefresh', response);   
                    })
                    .catch(()=>{Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!',});});
                }
            }); 
        },
        editPolicy(policy){
            this.editMode = true;
            this.policy = policy;
            //Fire.$emit('policyDataFill', policy);
            
        },
        getAllInitials(){
            this.loading = true;
            axios.get('/api/policies/'+this.$route.params.id)
            .then(response =>{
                this.policy = response.data.policy;
                this.loading = false;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({
                    icon: 'error',
                    title: 'Policies were not loaded successfully',
                })
            });
        },
        reset(response){
            this.categories = response.data.categories;
            this.departments = response.data.departments;
            this.policies = response.data.policies;

            this.closeModals();
        }
    },
    mounted() {this.getAllInitials();},
    watch:{
        policies(){

        },
        style(){}
    }
}
</script>