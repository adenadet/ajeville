<template>
<section class="overlay-wrapper p-0">
    <div class="overlay" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form @submit.prevent="editMode ? updatePeriod() : createPeriod()">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" name="name" id="name" placeholder="Enter Name" v-model="periodData.name" >
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Start Date</label>
                    <input type="date" class="form-control" name="start_date" id="start_date" v-model="periodData.start_date">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>End Date</label>
                    <input type="date" class="form-control" name="end_date" id="end_date" v-model="periodData.end_date">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" name="status" id="status"  v-model="periodData.status">
                        <option value="">--Select Status---</option>
                        <option value=1>Active</option>
                        <option value=0>Inactive</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Description</label>
                    <QuillEditor v-model:content="periodData.notes" theme="snow" content-type="html" class="form-control"></QuillEditor>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <button class="btn btn-primary mt-3" type="submit">{{editMode ? 'Update' : 'Create'}}</button>
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
            periodData : new Form({
                id: '',
                end_date: '',
                name: '',
                notes: '',
                start_date: '',
                status: '',
            }),
        }
    },
    emits: ['refreshPeriod'],
    mounted() {
        //this.getAllInitials();
    },
    methods: {
        createPeriod() {
            this.loading = true;
            this.periodData.post('/api/hrms/assessment_periods')
                .then(response => {
                    this.$swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Period created successfully!',
                    });
                    this.$emit('refreshPeriod', response);
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
        updatePeriod() {
            this.loading = true;
            console.log(this.periodData.id);
            this.periodData.put(`/api/hrms/assessment_periods/${this.periodData.id}`)
            .then(response => {
                this.$swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Period updated successfully!',
                });
                this.$emit('refreshPeriod', response);
                this.loading = false;
            })
            .catch(error => {
                this.$swal.fire({
                    icon: 'error',
                    title: 'Error',
                    text: 'Something went wrong while updating period.',
                });
                this.loading = false;
            });
        },
    },
    props: {
        editMode: Boolean,
        period: Object,
        source: String,
    },
    watch: {
        period(){
            this.loading = true;
            this.periodData.fill(this.period);
            this.loading = false;
        }
    }
}
</script>