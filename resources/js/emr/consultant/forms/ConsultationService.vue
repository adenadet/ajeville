<template>
    <!--A form that allows you create or edit a Service <br />
    1. Name of the Consultation Service
    2. Specialty the Service belongs to
    3. Description of the Service
    4. Consultant handling the service
    5. Status of the Service
    <template-->
    <div class="border-top pt-3 mt-3">
        <h6>Consultation Details</h6>
        <div class="row">
            <div class="col-md-4">
                <label class="form-label">Specialty</label>
                <select v-model="model.specialty_id" class="form-select">
                    <option value="">-- Select --</option>
                    <option v-for="s in specialties" :key="s.id" :value="s.id">
                        {{ s.name }}
                    </option>
                </select>
            </div>

            <div class="col-md-4">
                <label class="form-label">Consultant</label>
                <select v-model="model.consultant_id" class="form-select">
                    <option value="">-- Optional --</option>
                    <option v-for="c in consultants" :key="c.id" :value="c.id">
                        {{ c.name }}
                    </option>
                </select>
            </div>
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
            consultants: []
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
        getInitials(){
            axios.get('/api/emr/consultants/initials')
            .then(response => {
                this.specialties = response.data.specialties;
                this.consultants = response.data.consultants;
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Could not load specialties and consultants'
                })
            });
        },
    },
    mounted() {
        this.getInitials();
    }
}
</script>