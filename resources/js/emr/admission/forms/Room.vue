<template>
<section class="overlay-wrapper p-0">
    <form @submit.prevent="editMode ? updateRoom() : createRoom()">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label for="name">Name {{ editMode ? 1: 0 }}</label>
                    <input type="text" class="form-control" name="name" id="name" v-model="roomData.name" required/>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label for="name">Ward</label>
                    <select class="form-control" name="ward_id" id="ward_id" v-model="roomData.ward_id" required>
                        <option value="">--Select Ward--</option>
                        <option v-for="ward in wards" :key="ward.id" :value="ward.id">
                            {{ ward.name }}
                        </option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <label>Room Type</label>
                <select class="form-control" name="room_type_id" id="room_type_id" v-model="roomData.room_type_id" required>
                    <option value="">-- Select Room Type --</option>
                    <option v-for="rt in room_types" :key="rt.id" :value="rt.id">
                        {{ rt.name }}
                    </option>
                </select>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" name="status" id="status" v-model="roomData.status" required>
                        <option value="">--Select Status--</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>Description</label>
                    <QuillEditor v-model:content="roomData.description" class="form-control" theme="snow" content-type="html" />
                </div>
            </div>
        </div>
        <button class="btn btn-primary">{{ editMode ? 'Update' : 'Create' }}</button>
    </form>
</section>
</template>
<script>
export default { 
    data() {
        return {
            loading: false,
            roomData: new Form({
                description: '',
                name: '',
                ward_id: '',
                room_type_id: '',
                status: '',
                id: '',
            }),
            room_types: [],
            wards: [],
        }
    },
    emits:['refreshRoomForm'],
    methods: {
        createRoom(){
            this.loading = true;
            this.roomData.post('/api/emr/admissions/rooms')
            .then(response => {
                this.$swal.fire({ icon: 'success', title: 'The Room has been created', showConfirmButton: false, timer: 1500 });
                this.$emit('refreshRoomForm');
            })
            .catch(() => {
                this.$swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!' });
            })
            .finally(() => {
                this.loading = false;
            });
        },
        getInitials(){
            axios.get('/api/emr/admissions/rooms/initials')
            .then((response) => {
                this.wards = response.data.wards;
                this.room_types = response.data.room_types;
                this.$toast.fire({
                    icon: 'success',
                    title: 'Room Form was loaded successfully',
                })
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Room Form was not loaded successfully',
                })
            });
        },
        updateRoom(){
            this.loading = true;
            this.roomData.put('/api/emr/admissions/rooms/'+this.roomData.id)
            .then(response => {
                this.$swal.fire({ icon: 'success', title: 'The Room has been updated', showConfirmButton: false, timer: 1500 });
                this.$emit('refreshRoomForm');
            })
            .catch(() => {
                this.$swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!' });
            })
            .finally(() => {
                this.loading = false;
            });
        },
    },
    mounted() {
        this.getInitials();
    },
    props: {
        editMode: {
            type: Boolean,
            default: false
        },
        room: {
            type: Object,
            default: null
        }
    },
    watch: {
        room(){
            this.roomData.fill(this.room)    
        },
    }
}
</script>