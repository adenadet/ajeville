<template>
    <div class="p-4">
      <h1 class="text-2xl font-bold mb-4">Vitals Dashboard</h1>
  
      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <!-- Consultations Without Vitals -->
        <div>
          <h2 class="text-xl font-semibold mb-2">Consultations</h2>
          <ul class="bg-white shadow rounded p-4 space-y-2">
            <li v-for="consultation in consultations" :key="consultation.id" class="border-b pb-2">
              <div class="flex justify-between items-center">
                <div>
                  <p><strong>Patient ID:</strong> {{ consultation.patient_id }}</p>
                  <p><strong>Consultation ID:</strong> {{ consultation.id }}</p>
                </div>
                <button @click="selectConsultation(consultation)" class="bg-blue-500 text-white px-3 py-1 rounded">Enter Vitals</button>
              </div>
            </li>
          </ul>
        </div>
  
        <!-- Past Vitals -->
        <div>
          <h2 class="text-xl font-semibold mb-2">Vitals</h2>
          <ul class="bg-white shadow rounded p-4 space-y-2 max-h-[600px] overflow-y-auto">
            <li v-for="vital in vitals" :key="vital.id" class="border-b pb-2">
              <p><strong>Patient ID:</strong> {{ vital.patient_id }}</p>
              <p><strong>NEWS2 Score:</strong> {{ vital.news2_score }}</p>
              <p><strong>Date:</strong> {{ formatDate(vital.created_at) }}</p>
            </li>
          </ul>
        </div>
      </div>
  
      <!-- Vitals Entry Modal -->
      <div v-if="selectedConsultation" class="fixed inset-0 bg-black bg-opacity-50 flex items-center justify-center z-50">
        <div class="bg-white p-6 rounded shadow max-w-xl w-full">
          <h3 class="text-xl font-bold mb-4">Enter Vitals for Patient {{ selectedConsultation.patient_id }}</h3>
  
          <form @submit.prevent="editMode ? updateVital() : createVital()">
            <div class="grid grid-cols-2 gap-4">
              <input v-model="vitalForm.respiration_rate" type="number" placeholder="Respiration Rate" class="input" required />
              <input v-model="vitalForm.spo2" type="number" placeholder="SpO2" class="input" required />
              <input v-model="vitalForm.bp_systolic" type="number" placeholder="BP Systolic" class="input" required />
              <input v-model="vitalForm.bp_diastolic" type="number" placeholder="BP Diastolic" class="input" required />
              <input v-model="vitalForm.pulse" type="number" placeholder="Pulse" class="input" required />
              <input v-model="vitalForm.temperature" type="number" step="0.1" placeholder="Temperature" class="input" required />
              <input v-model="vitalForm.blood_glucose" type="number" placeholder="Blood Glucose" class="input" />
              <input v-model="vitalForm.consciousness" type="text" placeholder="Consciousness (e.g., Alert)" class="input" required />
            </div>
  
            <div class="mt-4 flex justify-end space-x-2">
              <button type="button" @click="selectedConsultation = null" class="bg-gray-500 text-white px-4 py-2 rounded">Cancel</button>
              <button type="submit" class="bg-green-600 text-white px-4 py-2 rounded">Submit</button>
            </div>
          </form>
        </div>
      </div>
    </div>
  </template>
  
  <script>
  export default {
    data() {
        return {
            consultations: [],
            vitals: [],
            selectedConsultation: null,
            form: {
                blood_glucose: '',
                bp_systolic: '',
                bp_diastolic: '',
                consciousness: '',
                pulse: '',
                respiration_rate: '',
                spo2: '',
                temperature: '',
            },
        };
    },
    mounted() {
      this.fetchConsultations();
      this.fetchVitals();
    },
    methods: {
      fetchConsultations() {
        fetch('/api/consultations')
          .then(res => res.json())
          .then(data => this.consultations = data);
      },
      fetchVitals() {
        fetch('/api/vitals')
          .then(res => res.json())
          .then(data => this.vitals = data);
      },
      selectConsultation(consultation) {
        this.selectedConsultation = consultation;
        this.form.patient_id = consultation.patient_id;
        this.form.consultation_id = consultation.id;
      },
      submitVitals() {
        fetch('/api/vitals', {
          method: 'POST',
          headers: {
            'Content-Type': 'application/json'
          },
          body: JSON.stringify({
            ...this.form,
            patient_id: this.selectedConsultation.patient_id,
            consultation_id: this.selectedConsultation.id
          })
        })
          .then(res => res.json())
          .then(() => {
            this.fetchVitals();
            this.selectedConsultation = null;
          });
      },
      formatDate(dateStr) {
        const date = new Date(dateStr);
        return date.toLocaleDateString() + ' ' + date.toLocaleTimeString();
      },
    }
  };
  </script>
  
  <style scoped>
  .input {
    border: 1px solid #ccc;
    padding: 8px;
    border-radius: 6px;
    width: 100%;
  }
  </style>
  