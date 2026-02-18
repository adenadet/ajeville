<template>
<section class="overlay-wrapper p-0">
    <form @submit.prevent="admitPatient()">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="name">Admission Note</label>
                    <QuillEditor theme="snow" content-type="html" v-model:content="admitData.admission_notes" />
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <button class="btn btn-primary" type="submit">Complete</button>
            </div>
        </div>
    </form>
</section>
</template>
<script>
export default { 
    data() {
        return {
            loading: false,
            admitData: new Form({
                admission_notes: '',
                admitted_at: '',
            }),
            rooms: [],
        }
    },
    emits:['refreshAdmitForm'],
    methods: {
        admitPatient(){
            this.loading = true;
            this.admitData.put('/api/emr/admissions/requests/'+this.admission.id+'/admit')
            .then(response => {
                this.$swal.fire({ icon: 'success', title: 'The Bed has been created', showConfirmButton: false, timer: 1500 });
                this.$emit('refreshAdmitForm');
            })
            .catch(() => {
                this.$swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!' });
            })
            .finally(() => {
                this.loading = false;
            });
        },
        getInitials(){},
    },
    mounted() {
    },
    props: {
        admission: Object,
    },
}
</script>
