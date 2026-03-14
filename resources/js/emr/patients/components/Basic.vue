<template>
    <div class="card">
        <div class="card-header bg-dark"><h4 class="card-title">Basic Details</h4></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-2">
                    <label>Title</label>
                    <input class="form-control" placeholder="Title" v-model="form.title" />
                </div>
                <div class="col-md-4">
                    <label>Last Name</label>
                    <input class="form-control" placeholder="Last Name" v-model="form.last_name" />
                </div>
                <div class="col-md-3">
                    <label>First Name</label>
                    <input class="form-control" placeholder="First Name" v-model="form.first_name" />
                </div>
                <div class="col-md-3">
                    <label>Other Names</label>
                    <input class="form-control" placeholder="Other Names" v-model="form.middle_name" />
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-3">
                    <label>Date of Birth</label>
                    <input type="date" class="form-control" v-model="form.dob" />
                    <span class="text-info">{{ current_age }}</span>
                </div>
                <div class="col-md-3">
                    <label>Sex</label>
                    <select class="form-control" v-model="form.sex">
                        <option value="">Select Sex</option>
                        <option>Male</option>
                        <option>Female</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Nationality</label>
                    <select class="form-control" v-model="form.nationality_id">
                        <option value="">Nationality</option>
                        <option v-for="n in nations" :key="n.id" :value="n.id">{{ n.name }}</option>
                    </select>
                </div>
                <div class="col-md-3">
                    <label>Occupation</label>
                    <input class="form-control" placeholder="Occupation" v-model="form.occupation" />
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-6">
                    <label>Street</label>
                    <input class="form-control" placeholder="Address" v-model="form.street" />
                </div>
                <div class="col-md-6">
                    <label>Street 2</label>
                    <input class="form-control" placeholder="Address 2" v-model="form.street2" />
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-4">
                    <label>City</label>
                    <input class="form-control" placeholder="City" v-model="form.city" />
                </div>
                <div class="col-md-4">
                    <label>State</label>
                    <select class="form-control" v-model="form.state_id" @change="$emit('state-changed', form.state_id)">
                        <option value="">State</option>
                        <option v-for="s in states" :key="s.id" :value="s.id">{{ s.name }}</option>
                    </select>
                </div>
                <div class="col-md-4">
                    <label>Area</label>
                    <select class="form-control" v-model="form.area_id">
                        <option value="">Area</option>
                        <option v-for="a in areas" :key="a.id" :value="a.id">{{ a.name }}</option>
                    </select>
                </div>
            </div>

            <div class="row mt-2">
                <div class="col-md-4">
                    <label>Phone</label>
                    <input class="form-control" placeholder="Phone" v-model="form.phone" />
                </div>
                <div class="col-md-4">
                    <label>Alternate Phone</label>
                    <input class="form-control" placeholder="Alt Phone" v-model="form.alt_phone" />
                </div>
                <div class="col-md-4">
                    <label>Email</label>
                    <input class="form-control" placeholder="Email" v-model="form.email" />
                </div>
            </div>
        </div>
    </div>
</template>
<script>
export default { 
    computed:{
        current_age(){
            if(!this.form.dob) return ''
            let diff = new Date() - new Date(this.form.dob)
            return Math.floor(diff / (365.25*24*60*60*1000))
        }
    },
    props: ['form', 'nations', 'states', 'areas'] 
}
</script>
