<template>
<section class="overlay-wrapper p-0">
    <div class="card">
        <div class="card-header bg-dark ">
            <h3 class="card-title mb-0">Complaining Conditions</h3>
            <div class="card-tools">
                <select v-model="mode" class="form-control form-control-sm">
                    <option value="assisted">Assisted</option>
                    <option value="unassisted">Unassisted</option>
                </select>
            </div>
        </div>
        <div v-if="mode === 'assisted' && !hasFinalHistory" class="card-body row">
            <div class="col-md-3">
                <label>Complaints</label>
                <multiselect id="multiselect" v-model="all_symptoms" :options="local_symptoms" :multiple="true" :close-on-select="false" :clear-on-select="false" :preserve-search="true" placeholder="Search or add symptom" label="name" track-by="name" :preselect-first="true"></multiselect>
            </div>
            <div class="col-md-9">
                <table class="table table-striped">
                    <thead>
                        <tr>
                            <th>#</th>
                            <th>Symptom</th>
                            <th colspan="2">Duration</th>
                            <th>Intensity (1–10)</th>
                            <th colspan="2">Change Over Time</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr v-for="(s, i) in all_symptoms" :key="s.code">
                            <td>{{ i + 1 }}</td>
                            <td>{{ s.name }}</td>
                            <td><input type="number" class="form-control" v-model="s.number" /></td>
                            <td>
                                <select class="form-control" v-model="s.duration">
                                    <option value="">-- Duration --</option>
                                    <option v-for="d in durations" :key="d.id" :value="d.name">{{ d.name }}</option>
                                </select>
                            </td>
                            <td><input type="number" class="form-control" v-model="s.pain_level" /></td>
                            <td>
                                <select class="form-control" v-model="s.experience_changes">
                                    <option value="">-- Changes --</option>
                                    <option value="yes">Yes</option>
                                    <option value="no">No</option>
                                </select>
                            </td>
                            <td>
                                <textarea class="form-control" v-model="s.experience_change_character" :disabled="s.experience_changes !== 'yes'"></textarea>
                            </td>
                        </tr>
                    </tbody>
                </table>
                <button class="btn btn-success btn-sm float-end" :disabled="all_symptoms.length === 0" @click="generateComplaintNote">Generate Complaint Note</button>
            </div>
        </div>

      <!-- Unassisted Mode: Quill Editor -->
      <div v-else class="card-body">
        <QuillEditor theme="snow" content-type="html" v-model:content="finalHistory"/>
      </div>
    </div>
  </section>
</template>

<script>
import { all } from 'axios';

export default {
    computed: {
        hasFinalHistory() {
            return !!this.modelValue;
        },
        finalHistory: {
            get() {
                return this.modelValue;
            },
            set(val) {
                this.$emit('update:modelValue', val);
            },
        },
    },
    data() {
        return {
            all_symptoms: [],                // selected symptoms
            local_symptoms: [],               // local copy of all symptoms (options)
            mode: 'assisted',                // assisted/unassisted mode
        };
    },
    emits: ['update:modelValue'],
    methods: {
        addTag(name) {
            const tag = {
                name,
                code: 'TAG_' + Date.now() + '_' + Math.random().toString(36).slice(2),
            };
            this.local_symptoms.push(tag);   // add to options
            this.all_symptoms.push(tag);    // add to selected
        },
        generateComplaintNote() {
            var html = `<p>The patient presented complaining of:</p>`;
            this.all_symptoms.forEach(s => {
                html += `<p>${s.name}`;
                if (s.duration && s.number) html += ` for ${s.number} ${s.duration}`;
                if (s.pain_level) html += `. Pain level ${s.pain_level}/10`;
                if (s.experience_changes) {
                html += s.experience_changes === 'yes'
                    ? `. Changes described as ${s.experience_change_character || 'not specified'}`
                    : `. No change over time`;
                }
                html += `.</p>`;
            });
            this.finalHistory = html;
            this.mode = 'unassisted'; // switch to Quill editor
        },
    },
    mounted() {
        if (this.modelValue) this.mode = 'unassisted';
    },
    props: {
        symptoms: {
            type: Array,
            default: () => [],
        },
        durations: {
            type: Array,
            default: () => [],
        },
        modelValue: {
            type: String,
            default: '',
        },
    },
    watch:{
        symptoms(){
            this.local_symptoms = [...this.symptoms];
        }
    }
    
};
</script>
