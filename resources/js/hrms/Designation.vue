<template>
    <section class="p-0 overlay-wrapper">
        <div class="overlay" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
        <div class="row p-0">
            <div class="col-md-12"><HrmsDetailDesignation :designation.sync="designation"/></div>
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-navy">
                        Job Descriptions
                    </div>
                    <div class="card-body table-responsive p-0" style="height:400px;">
                        <HrmsDetailDesignationKpiList :kpis.sync="designation.kpis"/>
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
            current_page: 1,
            designation: {kpis: [],},
            designations: {},
            editMode: false,
            form: new Form({}),
            loading: false,
            query: '',
        }
    },
    methods:{
        addDesignation(){
            this.editMode = false;
            this.designation = {};
            $('#designationFormModal').modal('show');
        },
        closeModals(){
            $('#designationModal').modal('hide');
            $('#designationFormModal').modal('hide');
        },
        deleteDesignation(id){
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
                    this.loading = true;
                    this.form.delete('/api/hrms/designations/'+id)
                    .then(response=>{
                        this. $swal.fire('Deleted!', response.data.message, 'success');
                        this.getAllInitials();
                        this.loading = false;   
                    })
                    .catch(()=>{
                        Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });  
        },
        editDesignation(designation){
            this.loading = true;
            this.editMode = true;
            this.designation = designation;
            $('#designationFormModal').modal('show');
            this.loading = false;
        },
        getAllInitials(){
            this.loading = true
            axios.get('/api/hrms/designations/'+this.$route.params.id).then(response =>{
                this.refreshPage(response);
                this.loading = false;
                this.$toast.fire({icon: 'success', title: 'Designations loaded successfully',
                });
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'Designations not loaded successfully',})
            });
        },
        refreshPage(response){
            this.designation = response.data.designation;
            this.closeModals();
        },
        searchDesignation(){
            axios.get('/api/hrms/designations/search/'+this.query)
            .then((response ) => {this.designations = response.data.designations;})
            .catch(()=>{});
        },
        viewDesignation(designation){
            this.designation = designation;
            $('#designationModal').modal('show');
        },
    },
    mounted(){ 
        this.getAllInitials();
    },
}
</script>