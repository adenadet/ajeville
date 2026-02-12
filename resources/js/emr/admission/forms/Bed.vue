<template>
<section class="overlay-wrapper p-0">
    <form @submit.prevent="editMode ? updateBed() : createBed()">
        <div class="row">
            <div class="col-md-8">
                <div class="form-group">
                    <label for="name">Name</label>
                    <input type="text" class="form-control" name="name" id="name" v-model="bedData.name" placeholder="Bed Name" required/>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="name">Code</label>
                    <input type="text" class="form-control" name="bed_code" id="bed_code" v-model="bedData.bed_code" placeholder="Bed Code" required/>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="name">Room</label>
                    <div class="form-control" name="room_id" id="room_id" v-html="room.name" v-if="room != null && room.id != null"></div> 
                    <select v-else class="form-control" name="ward_id" id="ward_id" v-model="bedData.room_id" required>
                        <option value="">--Select Ward--</option>
                        <option v-for="room in rooms" :key="room.id" :value="room.id">
                            {{ room.name }}
                        </option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label for="name">Status</label>
                    <select class="form-control" name="status" id="status" v-model="bedData.status" required>
                        <option value="">--Select Status--</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <button class="btn btn-primary" type="submit">{{ editMode ? 'Update' : 'Create' }}</button>
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
            bedData: new Form({
                name: '',
                bed_code: '',
                room_id: '',
                status: '',
                id: '',
            }),
            rooms: [],
        }
    },
    emits:['refreshBedForm'],
    mounted() {
        
    },

    methods: {
        createBed(){
            this.loading = true;
            if (this.room != null && this.room.id != null){this.bedData.room_id = this.room.id}
            this.bedData.post('/api/emr/admissions/beds')
            .then(response => {
                this.$swal.fire({ icon: 'success', title: 'The Bed has been created', showConfirmButton: false, timer: 1500 });
                this.$emit('refreshBedForm');
            })
            .catch(() => {
                this.$swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!' });
            })
            .finally(() => {
                this.loading = false;
            });
        },
        getInitials(){},
        updateBed(){
            this.loading = true;
            this.bedData.post('/api/emr/admissions/beds/'+this.bedData.id)
            .then(response => {
                this.$swal.fire({ icon: 'success', title: 'The Bed has been updated', showConfirmButton: false, timer: 1500 });
                this.$emit('refreshBedForm');
            })
            .catch(() => {
                this.$swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!' });
            })
            .finally(() => {
                this.loading = false;
            });
        },
    },
    props: {
        editMode: Boolean,
        bed: Object,
        room: Object,
    },
    watch: {
        bed(newVal) {
            if (newVal) {
                this.roomData.fill(newVal);
            }
        },
    }

}
</script>
