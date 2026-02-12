<template>
    <section class="card">
        <div class="card-body">
            <h5 class="mb-3">Assign Specialty to Consultants</h5>

            <form @submit.prevent="submitForm">
                <div class="form-group mb-3">
                    <label class="form-label">Consultants</label>
                    <multiselect v-model="form.consultants" :options="users" :multiple="true" :close-on-select="false" :clear-on-select="false" :preserve-search="true" label="name" track-by="id" placeholder="Select consultants"/>
                </div>
                <div class="form-group mb-3">
                    <label class="form-label">Specialty</label>
                    <multiselect v-model="form.specialty" :options="specialties" :multiple="false" label="name" track-by="id" placeholder="Select specialty"/>
                </div>
                <div class="text-end">
                    <button class="btn btn-primary" type="submit" :disabled="loading">{{ loading ? 'Saving...' : 'Assign Specialty' }}</button>
                </div>
            </form>
        </div>
    </section>
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
</script><script>
import Multiselect from 'vue-multiselect'
import axios from 'axios'

export default {
    components: {
        Multiselect
    },

    data() {
        return {
            loading: false,
            users: [],
            specialties: [],
            form: {
                consultants: [],
                specialty: null
            }
        }
    },

    mounted() {
        this.getAllInitials()
    },

    methods: {
        async getAllInitials() {
            try {
                const response = await axios.get(
                    '/api/emr/consultant_specialties'
                )

                this.users = response.data.users
                this.specialties = response.data.specialties
            } catch (error) {
                console.error(error)
                this.$toast?.error('Failed to load consultants and specialties')
            }
        },

        async submitForm() {
            if (!this.form.consultants.length || !this.form.specialty) {
                this.$toast?.error('Please select consultants and a specialty')
                return
            }

            this.loading = true

            try {
                await axios.post('/api/emr/consultant_specialties', {
                    consultant_ids: this.form.consultants.map(c => c.id),
                    specialty_id: this.form.specialty.id
                })

                this.$toast?.success('Specialty assigned successfully')

                // reset form
                this.form.consultants = []
                this.form.specialty = null
            } catch (error) {
                console.error(error)
                this.$toast?.error('Failed to assign specialty')
            } finally {
                this.loading = false
            }
        }
    }
}
</script>