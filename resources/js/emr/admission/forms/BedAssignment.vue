<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <form @submit.prevent="editMode ? updateBedAssignment() : createBedAssignment()">
        <div class="row">
            {{ admission }}
            <div class="col-md-4">
                <div class="form-group">
                    <label>Ward</label>
                    <select class="form-control" v-model="bedAssignmentData.ward_id">
                        <option value="">--Select Ward--</option>
                        <option v-for="ward in wards" :key="ward.id" :value="ward.id">
                            {{ ward.name }}
                        </option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Room</label>
                    <select class="form-control" v-model="bedAssignmentData.room_id">
                        <option value="">--Select Room--</option>
                        <option v-for="room in available_rooms" :key="room.id" :value="room.id">
                            {{ room.name }}
                        </option>
                    </select>
                </div>
            </div>

            <!-- BED -->
            <div class="col-md-4">
                <div class="form-group">
                    <label>Bed</label>
                    <select class="form-control" v-model="bedAssignmentData.bed_id">
                        <option value="">--Select Bed--</option>
                        <option v-for="bed in available_beds" :key="bed.id" :value="bed.id">
                            {{ bed.name }}
                        </option>
                    </select>
                </div>
            </div>

            <!-- PRICE DISPLAY -->
            <div class="col-md-12" v-if="pricing.price !== null">
                <div class="alert"
                     :class="{
                        'alert-success': pricing.state === 'covered',
                        'alert-warning': pricing.state === 'co-pay',
                        'alert-info': pricing.state === 'cash'
                     }">

                    <strong>Bed Charge:</strong> {{ pricing.price | currency }}

                    <span class="ml-2 badge"
                          :class="{
                            'badge-success': pricing.state === 'covered',
                            'badge-warning': pricing.state === 'co-pay',
                            'badge-info': pricing.state === 'cash'
                          }">
                        {{ pricing.state }}
                    </span>
                </div>
            </div>

            <div class="col-md-12">
                <button class="btn btn-primary" type="submit">
                    {{ loading ? 'Assigning...' : 'Assign' }}
                </button>
            </div>
        </div>
    </form>
</section>
</template>

<script>
export default {

    
    emits: ['refreshBedAssignmentForm'],

    data() {
        return {
            available_rooms: [],
            available_beds: [],
            beds: [],
            bedAssignmentData: new Form({
                admission_id: '',
                ward_id: '',
                room_id: '',
                bed_id: '',
                patient_id: '',
            }),
            loading: false,
            pricing: {price: null, state: null},
            rooms: [],
            wards: [],
        }
    },
    methods: {
        getInitials() {
            axios.get('/api/emr/admissions/bed_assignments/initials')
            .then(response => {

                this.wards = response.data.wards
                this.rooms = response.data.rooms
                this.beds = response.data.beds

                this.$toast.fire({
                    icon: 'success',
                    title: 'Bed Assignment Form Loaded'
                })
            })
        },
        evaluateBedPricing(bedId) {
            this.loading = true
            axios.post('/api/emr/admissions/bed_assignments/check-price', {
                bed_id: bedId,
                price_list_id: this.admission.visit.price_list_id,
                branch_id: this.admission.visit.branch_id
            })
            .then(res => {
                this.pricing.price = res.data.price
                this.pricing.state = res.data.state
            })
            .finally(() => {
                this.loading = false
            })
        },
        createBedAssignment() {
            this.loading = true
            this.bedAssignmentData.admission_id = this.admission.id
            this.bedAssignmentData.patient_id = this.admission.patient_id
            this.bedAssignmentData.post('/api/emr/admissions/bed_assignments')
            .then(() => {
                this.$swal.fire({icon: 'success', title: 'Bed Assigned Successfully', timer: 1500, showConfirmButton: false})
                this.$emit('refreshBedAssignmentForm')
            })
            .catch(() => {
                this.$swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!' });
            })
            .finally(() => {
                this.loading = false
            })
        },

        updateBedAssignment() {
            this.loading = true
            this.bedAssignmentData.admission_id = this.admission.id
            this.bedAssignmentData.post('/api/emr/admissions/bed_assignments')
            .then(() => {
                this.$swal.fire({icon: 'success', title: 'Bed Assigned', timer: 1500, showConfirmButton: false})
                this.$emit('refreshBedAssignmentForm')
            })
           .catch(() => {
                this.$swal.fire({ icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!' });
            })
            .finally(() => {
                this.loading = false
            })
        }

    },
        mounted(){
        this.getInitials()
    },
    watch: {
        'bedAssignmentData.ward_id'(val) {
            this.bedAssignmentData.room_id = ''
            this.bedAssignmentData.bed_id = ''
            this.available_beds = []
            this.pricing = { price: null, state: null }

            this.available_rooms = this.rooms.filter(room =>
                room.ward_id === val
            )
        },
        'bedAssignmentData.room_id'(val) {
            this.bedAssignmentData.bed_id = ''
            this.pricing = { price: null, state: null }

            this.available_beds = this.beds.filter(bed =>
                bed.room_id === val &&
                bed.assignment_status === 0 &&
                bed.status === 1
            )
        },
        'bedAssignmentData.bed_id'(val) {
            if (!val) return
            this.evaluateBedPricing(val)
        }
    },
    props: {
        admission: Object,
        bed_assignment: Object,
        editMode: Boolean,
    },
}
</script>
