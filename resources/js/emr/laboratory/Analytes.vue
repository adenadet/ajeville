<template>
    <section class="container-fluid">
        <div class="row">
            <div class="col-md-12">
                <div class="card">
                    <div class="card-header bg-primary">
                        <h3 class="card-title">Laboratory Analytes</h3>
                    </div>
                    <div class="card-body table-responsive p-0">
                        <EMRLaboratoryDetailAnalyteList :analytes.sync="analytes.data" />
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<script>
import EMRLaboratoryDetailAnalyteList from '@/emr/laboratory/details/AnalyteList.vue';
import EMRLaboratoryFormAnalyte from '@/emr/laboratory/forms/Analyte.vue';

export default {
    components:{EMRLaboratoryDetailAnalyteList, EMRLaboratoryFormAnalyte},
    data() {
        return {
            analytes: {},
            analyte: {},
            current_page: 1,
            editMode: true,
            form: new Form({}),
            loading: false,
            query: '',
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        createAnalyte(){
            this.loading = true;
            this.editMode = false;
            this.analyte = {};
            $('#analyteFormModal').modal('show');
            this.loading = false;
        },
        deactivateAnalyte(id){
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
                if(result.value){
                    this.form.delete('/api/emr/laboratory/analytes/'+id)
                    .then(response=>{
                        this.$emit('')
                        this.$swal.fire('Deleted!', 'Analyte has been deactivated/reactivated.', 'success');
                    })
                    .catch(()=>{
                        this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        editAnalyte(analyte){
            this.loading = true;
            this.editMode = true;
            this.analyte = analyte;
            $('#analyteFormModal').modal('show');
            this.loading = false;
        },
        getInitials(){
            this.loading = true; 
            axios.get('/api/emr/laboratory/analytes?page='+this.current_page+'query='+this.query)
            .then(response => {
                this.analytes = response.data.analytes;
                $('#analyteFormModal').modal('hide');
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Your analytes did not loaded successfully',})
            })
            .finally(()=>{
                this.loading = false;
            });
        },
        viewReferenceRange(analyte){
            this.loading = true;
            axios.get('/api/emr/laboratory/reference_ranges?page='+this.current_page+'query='+this.query)
            .then(response => {
                this.analytes = response.data.analytes;
                $('#analyteFormModal').modal('hide');
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Your analytes did not loaded successfully',})
            })
            .finally(()=>{
                this.loading = false;
            });
        }
    },
}
</script>