<template>
<section>
    <form>
        <alert-error :form="AssessmentForm"></alert-error>
        <div class="row" >
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Name of Assessment Group</label>
                    <input type="text" class="form-control" id="name" name="name" v-model="AssessmentForm.name" />
                    <has-error :form="AssessmentForm" field="name"></has-error>
                </div>
            </div> 
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Description</label>
                    <wysiwyg v-model="AssessmentForm.description" rows="4" name="description" id="description"></wysiwyg>
                    <has-error :form="AssessmentForm" field="description"></has-error>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-3" v-for="item in assessment_items" :key="item.id">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="items[]" id="items[]" v-model="assignData.items" :value="department.id" :checked="assignData.items.includes(department.id)">
                    <label class="form-check-label">{{item.name}}</label>
                </div>
            </div>
        </div>
        <button @click.prevent="editMode ? updateRequest() : createRequest()" type="submit" name="submit" class="submit btn btn-primary">Submit</button>
    </form>
</section>
</template>
<script>
export default {
    data() {
        return {
            assessment_types: {},
            assessment_items: {},
            AssessmentForm: new FormData({
                name: '',
                description: '',
                items: [],
            }),
        }
    },
    mounted() {
        this.getInitials();
        Fire.$on('refreshResponse', response => {
            this.refreshAssessmentTypes(response);
            this.closeModal();
        });
        Fire.$on('searchInstance', ()=>{
            let query = this.$parent.search;
            axios.get('api/emr/domiciliary/search?q='+query)
            .then((response ) => {this.applicants = response.data.applicants;})
            .catch(()=>{});
        });
    },
    methods: {
        addType(request){
            this.$Progress.start();
            this.request = request;
            this.editMode = false;
            Fire.$emit('requestDataFill', {});
            $('#requestModal').modal('show');
            this.$Progress.finish();
        },
        viewType(request){
            this.$Progress.start();
            this.request = request;
            Fire.$emit('assessRequestDataFill', this.request);
            $('#assignModal').modal('show');
            this.$Progress.finish();
        },
        closeModal(){
            $('#assignModal').modal('hide');
            $('#requestModal').modal('hide');
        },
        deleteType(id){
            Swal.fire({
                title: 'Are you sure?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, confirm it!'
                })
            .then((result) => {
                //Send Confirm request
                if(result.value){
                    this.form.put('/api/emr/domiciliary/requests/confirm/'+id)
                    .then(response=>{
                        Swal.fire('Confirmed!', 'The Domiciliary Request has been confirmed.', 'success');
                        this.refreshDomiciliaries(response);   
                    })
                    .catch(()=>{
                        Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        deleteRequest(id){
            Swal.fire({
                title: 'Are you sure, you want to delete this?',
                text: "You won't be able to revert this!",
                icon: 'warning',
                showCancelButton: true,
                confirmButtonColor: '#3085d6',
                cancelButtonColor: '#d33',
                confirmButtonText: 'Yes, confirm it!'
                })
            .then((result) => {
                //Send Confirm request
                if(result.value){
                    this.form.delete('/api/emr/domiciliary/requests/'+id)
                    .then(response=>{
                        Swal.fire('Confirmed!', 'The Domiciliary Request has been deleted.', 'success');
                        this.refreshDomiciliaries(response);   
                    })
                    .catch(()=>{
                        Swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: '<a href>Why do I have this issue?</a>'});
                    });
                }
            });
        },
        editType(request){
            this.$Progress.start();
            this.editMode = true;
            this.accessment_type = request;
            this.patient = request.patient;
            Fire.$emit('requestDataFill', request);
            $('#requestModal').modal('show');
            this.$Progress.finish();
        },
        getInitials(page=1){
            axios.get('/api/emr/assessments/assess?page='+page)
            .then(response=>{
                this.refreshAssessmentTypes(response); 
            });
        },
        refreshAssessmentTypes(response) {
            this.assessment_types = response.data.assessment_types;
        }
    },
    props: {}
}
</script>