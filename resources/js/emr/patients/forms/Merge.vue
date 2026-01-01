<template>
<section class="overlay-wrapper">
    <div class="container mt-4">
        <div class="card shadow-sm">
            <div class="card-header bg-dark text-white">
                <h5 class="mb-0">Merge Patient Records</h5>
            </div>
            <div class="card-body">
                <div class="row mb-3">
                    <div class="col-md-5">
                        <label class="form-label">Patient Unique ID (A)</label>
                        <input v-model="patientAId" class="form-control" placeholder="e.g. PAT-000123" />
                    </div>

                    <div class="col-md-5">
                        <label class="form-label">Patient Unique ID (B)</label>
                        <input v-model="patientBId" class="form-control" placeholder="e.g. PAT-000456" />
                    </div>

                    <div class="col-md-2 d-flex align-items-end">
                        <button
                            class="btn btn-primary w-100"
                            :disabled="loading || !patientAId || !patientBId"
                            @click="fetchPatients"
                        >
                            Fetch
                        </button>
                    </div>
                </div>

                <!-- Loader -->
                <div v-if="loading" class="text-center my-4">
                    <div class="spinner-border"></div>
                </div>

                <!-- Step 2: Patient Comparison -->
                <div v-if="patientA && patientB" class="row">
                    <div class="col-md-6">
                        <div class="card h-100" :class="primary === 'A' ? 'border-success' : ''">
                            <div class="card-header d-flex justify-content-between">
                                <strong>Patient A</strong>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" value="A" v-model="primary"/>
                                    <label class="form-check-label"> Primary</label>
                                </div>
                            </div>

                            <div class="card-body">
                                <p><strong>Name:</strong> {{ patientA.name }}</p>
                                <p><strong>Gender:</strong> {{ patientA.gender }}</p>
                                <p><strong>DOB:</strong> {{ patientA.dob }}</p>
                                <p><strong>Phone:</strong> {{ patientA.phone }}</p>
                                <p><strong>Unique ID:</strong> {{ patientA.unique_id }}</p>
                            </div>
                        </div>
                    </div>

                    <div class="col-md-6">
                        <div class="card h-100" :class="primary === 'B' ? 'border-success' : ''">
                            <div class="card-header d-flex justify-content-between">
                                <strong>Patient B</strong>
                                <div class="form-check">
                                    <input class="form-check-input" type="radio" value="B" v-model="primary"/>
                                    <label class="form-check-label">Primary</label>
                                </div>
                            </div>
                            <div class="card-body">
                                <p><strong>Name:</strong> {{ patientB.name }}</p>
                                <p><strong>Gender:</strong> {{ patientB.gender }}</p>
                                <p><strong>DOB:</strong> {{ patientB.dob }}</p>
                                <p><strong>Phone:</strong> {{ patientB.phone }}</p>
                                <p><strong>Unique ID:</strong> {{ patientB.unique_id }}</p>
                            </div>
                        </div>
                    </div>
                </div>
                <div v-if="patientA && patientB" class="mt-4 text-end">
                    <button class="btn btn-danger" :disabled="!primary" @click="mergePatients">Merge Patients</button>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            patientAId: '',
            patientBId: '',
            patientA: null,
            patientB: null,
            primary: null,
            loading: false,
        }
    },

    methods: {
        async fetchPatients() {
            this.loading = true
            this.primary = null

            try {
                // Replace with your API endpoints
                const [a, b] = await Promise.all([
                    axios.get(`/api/patients/${this.patientAId}`),
                    axios.get(`/api/patients/${this.patientBId}`)
                ])

                this.patientA = a.data
                this.patientB = b.data
            } catch (e) {
                alert('Unable to fetch patient records')
            } finally {
                this.loading = false
            }
        },

        async mergePatients() {
            if (!this.primary) return

            const payload = {
                primary_unique_id:
                    this.primary === 'A'
                        ? this.patientA.unique_id
                        : this.patientB.unique_id,
                secondary_unique_id:
                    this.primary === 'A'
                        ? this.patientB.unique_id
                        : this.patientA.unique_id,
            }

            try {
                await axios.post('/api/patients/merge', payload)
                alert('Patients merged successfully')
                this.reset()
            } catch (e) {
                alert('Merge failed')
            }
        },

        reset() {
            this.patientAId = ''
            this.patientBId = ''
            this.patientA = null
            this.patientB = null
            this.primary = null
        },
    }
}
</script>
