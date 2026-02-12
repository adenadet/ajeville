<template>
    <section>
        <form class="row" @submit.prevent="editMode ? updateAnalyte() : createAnalyte()">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" id="name" placeholder="Enter name" v-model="analyteForm.name">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" id="status" name="status" v-model="analyteForm.status">
                        <option value="">--Select Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Default Unit</label>
                    <input type="text" class="form-control" id="default_unit" placeholder="Enter default_unit" v-model="analyteForm.default_unit">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Input Type</label>
                    <select class="form-control" id="input_type" name="input_type" v-model="analyteForm.input_type">
                        <option value="">--Select Input Type--</option>
                        <option value="select">Select from Options</option>
                        <option value="number">Enter Value</option>
                        <option value="text">Describe Result</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12" v-if="analyteForm.input_type == 'select'">
                <div class="form-group">
                    <label>Options <span class="text-small text-danger">(separate value by comma)</span></label>
                    <input type="text" class="form-control" id="options" placeholder="Enter options" v-model="analyteForm.options">
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>Description</label>
                    <QuillEditor content-type="html" theme="snow" v-model:content="analyteForm.description" class="form-control" />
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
            loading: false,
            analyteForm: new Form({
                default_unit: '',
                description: '',
                id: '',
                input_type: '',
                name: '',
                options: '',
                status: '',
            }),
        }
    },
    emits:['refreshAnalyteForm'],
    mounted() {
        //this.getInitials();
    },
    methods: {
        createAnalyte(){
            this.loading = true;
            this.analyteForm.post('/api/emr/laboratory/analytes')
            .then(response => {
                this.$swal.fire('Deleted!', 'Analyte has been created.', 'success');
                this.$emit('refreshAnalyteForm')
            })
            .catch(()=>{
                this.$toast.fire({icon: 'error', title: 'Analyte was not created successfully',});
            })
            .finally(()=>{
                this.loading = false;
            })
        },
        getInitials(page=1) {
            axios.get('/api/emr/laboratory/analytes?page='+page)
            .then(response => {
                this.refreshQueue(response)
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Your appointments did not loaded successfully',});
            });
        },
        refreshQueue(response) {

            this.analytes = response.data.analytes;
        },
        updateAnalyte(){
            this.loading = true;
            this.analyteForm.put('/api/emr/laboratory/analytes/'+this.analyteForm.id)
            .then(response => {
                this.$swal.fire('Updated!', 'Analyte has been updated.', 'success');
                this.$emit('refreshAnalyteForm');
            })
            .catch(()=>{
                this.$toast.fire({icon: 'error', title: 'Analyte update was not successful',});
            })
            .finally(()=>{
                this.loading = false;
            })
        }
    },
    props: {
        editMode: Boolean,
        analyte: Object,
    },
    watch:{
        analyte(){
            this.analyteForm.fill(this.analyte);
        }
    }
}
</script>