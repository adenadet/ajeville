<template>
<section class="overlay-wrapper p-0">
    <div class="overlay" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form @submit.prevent="editMode ? updateHrItem() : createHrItem()">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" name="title" id="title" placeholder="Enter Name" v-model="hrItemData.title" >
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Maximum Score</label>
                    <input type="number" class="form-control" name="max_score" id="max_score" v-model="hrItemData.max_score">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" name="status" id="status"  v-model="hrItemData.status">
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
                    <QuillEditor v-model:content="hrItemData.description" theme="snow" content-type="html" class="form-control"></QuillEditor>
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
            hrItemData : new Form({
                id: '',
                max_score: '',
                description: '',
                title: '',
                status: '',
            }),
        }
    },
    emits: ['reloadHrItem'],
    mounted() {
        //this.getAllInitials();
    },
    methods: {
        createHrItem() {
            this.loading = true;
            this.hrItemData.post('/api/hrms/assessment_hr_items')
                .then(response => {
                    this.$swal.fire({
                        icon: 'success',
                        title: 'Success',
                        text: 'Period created successfully!',
                    });
                    this.$emit('reloadHrItem', response);
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
        updateHrItem() {
            this.loading = true;
            console.log(this.hrItemData.id);
            this.hrItemData.put(`/api/hrms/assessment_hr_items/${this.hrItemData.id}`)
            .then(response => {
                this.$swal.fire({
                    icon: 'success',
                    title: 'Success',
                    text: 'Period updated successfully!',
                });
                this.$emit('reloadHrItem', response);
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
        hr_item: Object,
        source: String,
    },
    watch: {
        hr_item(){
            this.loading = true;
            this.hrItemData.fill(this.hr_item);
            this.loading = false;
        }
    }
}
</script>