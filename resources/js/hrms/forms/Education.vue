<template>
<section class="overlay-wrapper p-0">
    <div class="overlay" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form @submit.prevent="editMode ? updateEducation() : createEducation()">
        <div class="row">
            <div class="col-md-12">
                
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Detail</label>
                    <input type="text" class="form-control" name="name" id="name" placeholder="Enter Training Name" v-model="educationData.name">
                </div>
                <div class="form-group">
                    <label>Institution</label>
                    <input type="text" class="form-control" name="institution" id="institution" placeholder="Institution Name"  v-model="educationData.institution" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Educational Level</label>
                    <select v-model="educationData.qualification_id" class="form-control" name="educational_level" id="educational_level">
                        <option value="primary">Primary</option>
                        <option value="jsce">Secondary</option>
                        <option value="ssce">Higher Secondary</option>
                        <option value="diploma">Diploma</option>
                        <option value="associate">Associate</option>
                        <option value="bsc">Bachelor Degree</option>
                        <option value="pgd">Post Graduate  Degree</option>
                        <option value="msc">Master's Degree</option>
                        <option value="phd">PhD</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Start Month</label>
                    <input v-model="educationData.end_month" type="month" class="form-control" name="start_month" id="start_month" placeholder="Start Month" required>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>End Month</label>
                    <input v-model="educationData.end_month" type="month" class="form-control" name="start_month" id="start_month" placeholder="End Month">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Certificate</label>
                    <div class="input-group">
                        <div class="custom-file">
                            <input type="file" class="custom-file-input" id="exampleInputFile">
                            <label class="custom-file-label" for="exampleInputFile">Choose file</label>
                        </div>
                        <div class="input-group-append">
                            <span class="input-group-text" id="">Upload</span>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <button class="btn btn-primary mt-3" type="submit">Submit</button>
            </div>
        </div>
    </form>
</section>
</template>
<script>
export default {
    data() {
        return {
            educationData : new Form({
                id: null,
                end_month: '',
                name: '',
                institution: '',
                start_month: '',
                file: null,
                file_type: null,
            }),
            loading: false,
        }
    },
    emits: ['refreshEducationForm'],
    mounted() {},
    methods: {
        createEducation() {
            this.loading = true;
            this.educationData.post('/hrms/educations')
                .then(response => {
                    this.$swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Education created successfully!',
                    });
                    this.$emit('refreshEducationForm');
                    this.loading = false;
                })
                .catch(error => {
                    this.$swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong while creating education.',
                    });
                    this.loading = false;
                });
        },
        getInitials(){
            this.loading = true;
            axios.get('/api/hrms/educations/initials')
            .then(response => {
                this.refreshForm(response); this.loading = false;       
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Your employee form did not loaded successfully',})
                this.loading = false;
            });
        },
        updateEducation() {
            this.loading = true;
            this.educationData.put(`/hrms/educations/${this.educationData.id}`)
                .then(response => {
                    this.$swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Education updated successfully!',
                    });
                    this.$emit('refreshEducationForm');
                    this.loading = false;
                })
                .catch(error => {
                    this.$swal.fire({
                        icon: 'error',
                        title: 'Error',
                        text: 'Something went wrong while creating education.',
                    });
                    this.loading = false;
                });
        },

    },
    props: {
        editMode: Boolean,
        education: Object,
        source: String,
    },
}
</script>