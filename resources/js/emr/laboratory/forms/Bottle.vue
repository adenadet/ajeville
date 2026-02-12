<template>
    <section>
        <form class="row" @submit.prevent="editMode ? updateBottle() : createBottle()">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Bottle Name</label>
                    <input type="text" class="form-control" id="name" placeholder="Enter name" v-model="bottleForm.name">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Bottle Colour</label>
                    <input type="text" class="form-control" id="colour" placeholder="Enter colour" v-model="bottleForm.colour">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Bottle Size</label>
                    <input type="text" class="form-control" id="size" placeholder="Enter Size" v-model="bottleForm.size">
                </div>
            </div>
            <div class="col-md-9">
                <div class="form-group">
                    <label>Additive</label>
                    <input type="text" class="form-control" id="additive" placeholder="Enter additive" v-model="bottleForm.additive">
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" id="status" name="status" v-model="bottleForm.status">
                        <option value="">--Select Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>Description</label>
                    <QuillEditor content-type="html" theme="snow" v-model:content="bottleForm.description" class="form-control" />
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
            bottleForm: new Form({
                additive: '',
                colour: '',
                description: '',
                id: '',
                name: '',
                status: '',
                size: '',
            }),
        }
    },
    emits:['refreshBottleForm'],
    mounted() {
        //this.getInitials();
    },
    methods: {
        createBottle(){
            this.loading = true;
            this.bottleForm.post('/api/emr/laboratory/bottles')
            .then(response => {
                this.$swal.fire('Deleted!', 'Bottle has been created.', 'success');
                this.$emit('refreshBottleForm')
            })
            .catch(()=>{
                this.$toast.fire({icon: 'error', title: 'Bottle was not created successfully',});
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
                this.$toast.fire({icon: 'error', title: 'Your appointments did not loaded successfully',});
            });
        },
        refreshQueue(response) {
            this.requests = response.data.requests;
            this.request = response.data.requests.data[0]
        },
        updateBottle(){
            this.loading = true;
            this.bottleForm.put('/api/emr/laboratory/bottles/'+this.bottleForm.id)
            .then(response => {
                this.$swal.fire('Updated!', 'Bottle has been updated.', 'success');
                this.$emit('refreshBottleForm');
            })
            .catch(()=>{
                this.$toast.fire({icon: 'error', title: 'Bottle update was not successful',});
            })
            .finally(()=>{
                this.loading = false;
            })
        }
    },
    props: {
        editMode: Boolean,
        bottle: Object,
    },
    watch:{
        bottle(){
            this.bottleForm.fill(this.bottle);
        }
    }
}
</script>