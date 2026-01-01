<template>
<section>
    <form>
        <alert-error :form="AssessmentTypeForm"></alert-error> 
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" name="name" id="name" v-model="AssessmentTypeForm.name" />
                    <has-error :form="AssessmentTypeForm" field="name"></has-error> 
                </div>
            </div>
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Description: *</label>
                    <wysiwyg required id="description" name="description" v-model="AssessmentTypeForm.description"></wysiwyg>
                    <has-error :form="AssessmentTypeForm" field="description"></has-error> 
                </div>
            </div>
        </div>
        <div class="row">
            <label class="col-sm-12">Individual Assessments</label>
            <div class="col-sm-3" v-for="i in items" :key="i.id">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" name="assessments[]" id="assessments[]" v-model="AssessmentTypeForm.assessments" :value="i.id" :checked="AssessmentTypeForm.assessments.includes(i.id)">
                    <label class="form-check-label">{{i.name}}</label>
                </div>
            </div> 
        </div> 
        <button @click.prevent="editMode ? updateType() :createType()" type="submit" name="submit" class="submit btn btn-success">Submit</button>
    </form>
</section>
</template>
<script>
export default {
    data() {
        return {
            AssessmentTypeForm: new Form({
                name: '',
                description: '',
                assessments: [],
                id: '',
            }),
        }
    },
    mounted() {
        Fire.$on('AssessmentTypeDataFill', request => {
            if (request != null) {
                this.AssessmentTypeForm.name = request.name;
                this.AssessmentTypeForm.description = request.description;    
                this.AssessmentTypeForm.id = request.id;
                this.AssessmentTypeForm.assessments = [];
                for (let i = 0; i < request.assessments.length; i++) {
                    this.AssessmentTypeForm.assessments.push(request.assessments[i].id);
                }  
            }
            else { this.AssessmentTypeForm.reset(); }
        });
    },
    methods: {
        createType() {
            this.$Progress.start();
            this.AssessmentTypeForm.put('/api/emr/domiciliary/requests/assign/' + this.AssessmentTypeForm.domiciliary_id)
                .then(response => {
                    this.$Progress.finish();
                    Fire.$emit('refreshResponse', response);
                    Swal.fire({
                        icon: 'success',
                        title: 'The Domiliciary Request has been updated',
                        showConfirmButton: false,
                        timer: 1500
                    });
                })
                .catch(() => {
                    Swal.fire({
                        icon: 'error',
                        title: 'Oops...',
                        text: 'Something went wrong!',
                        footer: 'Please try again later!'
                    });
                    this.$Progress.fail();
                });
        },
        updateType() {
            this.$Progress.start();
            this.AssessmentTypeForm.put('/api/emr/assessments/types/' + this.AssessmentTypeForm.id)
            .then(response => {
                this.$Progress.finish();
                Fire.$emit('refreshResponse', response);
                Swal.fire({icon: 'success', title: 'The Assessment Type has been updated', showConfirmButton: false, timer: 1500});
            })
            .catch(() => {
                Swal.fire({
                    icon: 'error',
                    title: 'Oops...',
                    text: 'Something went wrong!',
                    footer: 'Please try again later!'
                });
                this.$Progress.fail();
            });
        },
    },
    props: {
        items: Array,
        editMode: Boolean,
    }
}
</script>