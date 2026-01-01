<template>
    <section>
        <form class="card" @submit.prevent="editMode ? updateBottle() : createBottle()">
            <div class="card-header">
                <h3 class="card-title">{{ editMode ? 'Edit Bottle Detail' : 'New Bottle Detail' }}</h3>
            </div>
            <div class="card-body">
                <div class="form-group">
                    <label>Bottle Name</label>
                    <input type="text" class="form-control" id="name" placeholder="Enter name" v-model="bottleForm.name">
                </div>
                <div class="form-group">
                    <label>Bottle Colour</label>
                    <input type="text" class="form-control" id="colour" placeholder="Enter colour" v-model="bottleForm.colour">
                </div>
                <div class="form-group">
                    <label>Bottle Size</label>
                    <input type="text" class="form-control" id="size" placeholder="Enter Size" v-model="bottleForm.size">
                </div>
            </div>
            <div class="card-footer">
                <button type="submit" class="btn btn-primary" >Submit</button>
            </div>
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
                name: '',
                colour: '',
                size: '',
                id: '',
            }),
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        createBottle(){
            this.bottleForm.post('/api/emr/laboratory/bottles')
            .then(response => {
                this.refreshQueue(response)
            })
        },
        getInitials(page=1) {
            axios.get('/api/emr/laboratory/bottles?page='+page)
            .then(response => {
                this.refreshQueue(response)
            })
            .catch(() => {
                this.$Progress.fail();
                toast.fire({
                    icon: 'error',
                    title: 'Your appointments did not loaded successfully',
                })
            });
        },
        refreshQueue(response) {
            this.requests = response.data.requests;
            this.request = response.data.requests.data[0]
        },
        updateRequest(request){
            this.request = request;
        }
    },
    props: {
        editMode: Boolean,
        //bottle: Object,
    }
}
</script>