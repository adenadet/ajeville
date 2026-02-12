<template>
    <section>
        <form class="row" @submit.prevent="editMode ? updateSpecimenType() : createSpecimenType()">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" id="name" placeholder="Enter name" v-model="specimenTypeData.name">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" id="status" name="status" v-model="specimenTypeData.status">
                        <option value="">--Select Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>Description</label>
                    <QuillEditor content-type="html" theme="snow" v-model:content="specimenTypeData.description" class="form-control" />
                </div>
            </div>
            <div class="col-md-12"><button type="submit" class="btn btn-primary" >Submit</button></div>
        </form>
    </section>
</template>
<script>
export default {
    data() {
        return {
            request: {},
            requests: {},
            specimenTypeData: new Form({
                description: '',
                id: '',
                name: '',
                status: '',
            }),
        }
    },
    emits:['refreshSpecimenTypeForm'],
    mounted() {
        //this.getInitials();
    },
    methods: {
        createSpecimenType(){
            this.loading = true;
            this.specimenTypeData.post('/api/emr/laboratory/specimen_types')
            .then(response => {
                this.$swal.fire('Created!', 'Specimen Type has been created.', 'success');
                this.$emit('refreshSpecimenTypeForm')
            })
            .catch(()=>{
                this.$toast.fire({icon: 'error', title: 'Specimen Type was not created successfully',});
            })
            .finally(()=>{
                this.loading = false;
            })
        },
        getInitials(page=1) {
            axios.get('/api/emr/laboratory/bottles?page='+page)
            .then(response => {
                this.refreshQueue(response)
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Specimen Type form did not load successfully',});
            });
        },
        refreshQueue(response) {
            this.requests = response.data.requests;
            this.request = response.data.requests.data[0]
        },
        updateSpecimenType(){
            this.loading = true;
            this.specimenTypeData.put('/api/emr/laboratory/specimen_types/'+this.specimenTypeData.id)
            .then(response => {
                this.$swal.fire('Updated!', 'Specimen Type has been updated.', 'success');
                this.$emit('refreshSpecimenTypeForm');
            })
            .catch(()=>{
                this.$toast.fire({icon: 'error', title: 'Specimen Type update was not successful',});
            })
            .finally(()=>{
                this.loading = false;
            })
        }
    },
    props: {
        editMode: Boolean,
        specimen_type: Object,
    },
    watch:{
        specimen_type(){
            this.specimenTypeData.fill(this.specimen_type);
        }
    }
}
</script>