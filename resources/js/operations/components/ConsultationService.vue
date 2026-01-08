<template>
    <div class="row">
        <div class="col-md-6 mb-3">
            <label class="form-label">Specialty</label>
            <select v-model="modelValue.specialty_id" class="form-control" @change="filterConsultants">
                <option value="">-- Select Specialty --</option>
                <option v-for="s in specialties" :key="s.id" :value="s.id">{{ s.name }}</option>
            </select>
        </div>

        <div class="col-md-6 mb-3">
            <label class="form-label">Consultant</label>
            <select v-model="modelValue.consultant_id" class="form-control">
                <option value="">-- Select Consultant --</option>
                <option v-for="c in filteredConsultants" :key="c.id" :value="c.id">{{ FullName(c) }}</option>
            </select>
        </div>
    </div>
</template>

<script>
export default {
    props: ['modelValue'],
    emits: ['update:modelValue'],

    data() {
        return {
            specialties: [],
            consultants: [],
            filteredConsultants: [],
        }
    },

    computed: {
        model: {
            get() {
                return this.modelValue
            },
            set(val) {
                this.$emit('update:modelValue', val)
            }
        }
    },
    methods:{
        filterConsultants() {
            this.filteredConsultants = []

            if (this.modelValue.specialty_id == '') {
                this.filteredConsultants = this.consultants;
            }
            else{
                const specialty = this.specialties.find(s => s.id === this.modelValue.specialty_id)
                if (!specialty || !specialty.doctors) {this.filteredConsultants = []; return;}
                // Extract ONLY the user objects
                this.filteredConsultants = specialty.doctors.filter(d => d.user).map(d => d.user)        // safety check
            }
        },
        getInitials(){
            this.loading = true;
            axios.get('/api/operations/services/initials')
            .then(res =>  {
                this.consultants = res.data.consultants;
                this.specialties = res.data.specialties;
            })
        }
    },
    mounted() {
        this.getInitials(); 
    }
}
</script>
