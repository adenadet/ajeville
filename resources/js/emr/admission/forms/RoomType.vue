<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form @submit.prevent="editMode ? updateRoomType() : createRoomType()">
        {{ room_type }}
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Name</label>
                    <input type="text" class="form-control" name="name" id="name" v-model="roomTypeData.name" required/>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Status</label>
                    <select class="form-control" name="status" id="status" v-model="roomTypeData.status" required>
                        <option value="">--Select Status</option>
                        <option value="1">Active</option>
                        <option value="0">Inactive</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Create New Item</label>
                    <select class="form-control" name="item_type" id="item_type" v-model="roomTypeData.item_type" required>
                        <option value="">--Select Status</option>
                        <option value="existing">Existing</option>
                        <option value="new">New</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4" v-if="roomTypeData.item_type == 'existing'">
                <div class="form-group">
                    <label>Existing Item {{ roomTypeData.item_id }}</label>
                    <select class="form-control" name="item_type" id="item_type" v-model="roomTypeData.item_id" required>
                        <option value="">--Select Item--</option>
                        <option v-for="item in items" :value="item.id">{{ item.name }}</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row" v-if="roomTypeData.item_type == 'new'">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Landing Cost</label>
                    <input type="number" step="0.01" class="form-control" name="landing_cost" id="landing_cost" v-model="roomTypeData.item.landing_cost" required/>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Barcode</label>
                    <input type="text" class="form-control" name="barcode" id="barcode" v-model="roomTypeData.item.barcode"/>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Billable</label>
                    <select class="form-control" name="billable" id="billable" v-model="roomTypeData.item.billable">
                        <option value="">--Select Billable Status--</option>
                        <option value="1">Yes</option>
                        <option value="0">No</option>
                    </select>    
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Description</label>
                    <QuillEditor class="form-control" name="description" id="description" v-model:content="roomTypeData.description" theme="snow" content-type="html" required/>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="text-end">
                    <button class="btn btn-primary" type="submit" :disabled="loading">{{ loading ? 'Saving...' : 'Save' }}</button>
                </div>
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
            roomTypeData: new Form({
                id: null,
                name: '',
                description: '',
                status: 1,
                item_type: 'new',
                item_id: null,
                item: {
                    id: null,
                    name: '',
                    description: '',
                    barcode: '',
                    landing_cost: 0,
                    billable: 1,
                    status: 1
                }
            }),
            rt: {}, 
        }
    },
    emits: ['refreshRoomTypeForm'],
    methods: {
        createRoomType() {
            this.roomTypeData.post('/api/emr/admissions/room_types')
            .then(response => {
                this.$swal.fire({ icon: 'success', title: 'The Room Type has been created', showConfirmButton: false, timer: 1500 });
                this.$emit('refreshRoomTypeForm');
            })
            .catch(() => {
                this.$swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!' });
            })
            .finally(() => {
                this.loading = false;
            });
        },
        getInitials() {
            axios.get('/api/emr/admissions/room_types/initials')
            .then((response) => {
                this.refreshPage(response);
                this.$toast.fire({
                    icon: 'success',
                    title: 'Consultation Form was loaded successfully',
                })
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Consultation Form was not loaded successfully',
                })
            });
        },
        normalizeRoomType(rt) {
            var emr = rt.admission_service?.emr_service ?? {};
            var item = emr.item ?? {};

            this.roomTypeData.id = rt.id;
            this.roomTypeData.name = rt.name;
            this.roomTypeData.description = rt.description;
            this.roomTypeData.status = rt.status;

            if (item.id) {
                this.roomTypeData.item_type = 'existing';
                this.roomTypeData.item_id = item.id;
            }
            
            else if (rt.item_id != null){
                this.roomTypeData.item_type = 'existing';
                this.roomTypeData.item_id = rt.item_id;
            }

            else{
                this.roomTypeData.item_type = 'new';
                this.roomTypeData.item_id = '';
            }

            this.roomTypeData.item = {
                id: item.id ?? null,
                name: rt.name,
                description: rt.description,
                barcode: item.barcode ?? '',
                landing_cost: item.last_landing_cost ?? 0,
                billable: item.billable ?? 1,
                status: rt.status
            };
        },
        refreshPage(response){
            this.items = response.data.items;
        },
        updateRoomType() {
            this.loading = true;
            this.roomTypeData.put('/api/emr/admissions/room_types/' + this.roomTypeData.id)
            .then(response => {
                this.$emit('refreshRoomTypeForm');
                this.$swal.fire({ icon: 'success', title: 'The Room Type has been updated', showConfirmButton: false, timer: 1500 });
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
        editMode: {type: Boolean, default: false},
        room_type: {type: Object, required: true},
    },
    watch:{
        room_type(){
            this.roomTypeData.fill(this.normalizeRoomType(this.room_type)) 
            this.rt = this.normalizeRoomType(this.room_type);
            console.log(this.normalizeRoomType(this.room_type));
        }
    },
}
</script>