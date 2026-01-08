<template>
    <section class="container-fluid">
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Complaints</label>
                    <multiselect v-model="itemForm.symptoms" tag-placeholder="Add this as new tag" placeholder="Search or add a tag" label="name" track-by="code" :options="symptoms" :multiple="true" :taggable="true" @tag="addTag"></multiselect>
                </div>
            </div>
            <div class="col-md-9">
                <div class="card-header p-0 pt-1">
                    <ul class="nav nav-pills" id="custom-tabs-one-tab" role="tablist">
                        <li class="nav-item">
                            <a class="nav-link active" id="custom-tabs-one-home-tab" data-toggle="pill"
                                @click="changeSocrates('site')">Site</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="custom-tabs-one-profile-tab" data-toggle="pill"
                                href="#custom-tabs-one-profile" role="tab" aria-controls="custom-tabs-one-profile"
                                aria-selected="false" @click="changeSocrates('onset')">Onset</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="custom-tabs-one-messages-tab" data-toggle="pill"
                                href="#custom-tabs-one-messages" role="tab" aria-controls="custom-tabs-one-messages"
                                aria-selected="false" @click="changeSocrates('character')">Character</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="custom-tabs-one-settings-tab" data-toggle="pill"
                                href="#custom-tabs-one-settings" role="tab" aria-controls="custom-tabs-one-settings"
                                aria-selected="false" @click="changeSocrates('radiation')">Radiation</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="custom-tabs-one-home-tab" data-toggle="pill"
                                href="#custom-tabs-one-home" role="tab" aria-controls="custom-tabs-one-home"
                                aria-selected="true" @click="changeSocrates('associated')">Associated</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="custom-tabs-one-profile-tab" data-toggle="pill"
                                href="#custom-tabs-one-profile" role="tab" aria-controls="custom-tabs-one-profile"
                                aria-selected="false" @click="changeSocrates('timing')">Timing</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="custom-tabs-one-messages-tab" data-toggle="pill"
                                href="#custom-tabs-one-messages" role="tab" aria-controls="custom-tabs-one-messages"
                                aria-selected="false" @click="changeSocrates('exacerbating')">Exacerbating</a>
                        </li>
                        <li class="nav-item">
                            <a class="nav-link" id="custom-tabs-one-messages-tab" data-toggle="pill"
                                href="#custom-tabs-one-messages" role="tab" aria-controls="custom-tabs-one-messages"
                                aria-selected="false" @click="changeSocrates('severity')">Severity</a>
                        </li>
                    </ul>
                </div>
                <table class="table table-striped">
                    <thead v-if="socrates.active == 'site'">
                        <tr>
                            <th>#</th>
                            <th>Symptom</th>
                            <th>Site</th>
                            <th>Position</th>
                            <th></th>
                        </tr>
                    </thead>
                    <thead v-if="socrates.active == 'onset'">
                        <tr>
                            <th>#</th>
                            <th>Symptom</th>
                            <th>Amount</th>
                            <th>Duration</th>
                            <th>Intensity</th>
                            <th></th>
                        </tr>
                    </thead>
                    <thead v-if="socrates.active == 'character'">
                        <tr>
                            <th>#</th>
                            <th>Symptom</th>
                            <th>Description</th>
                            <th>Character Type</th>
                            <th></th>
                        </tr>
                    </thead>
                    <thead v-if="socrates.active == 'radiation'">
                        <tr>
                            <th>#</th>
                            <th>Symptom</th>
                            <th>Spreads To</th>
                            <th></th>
                        </tr>
                    </thead>
                    <thead v-if="socrates.active == 'associated'">
                        <tr>
                            <th>#</th>
                            <th>Symptom</th>
                            <th>Associated Symptoms</th>
                            <th></th>
                        </tr>
                    </thead>
                    <thead v-if="socrates.active == 'timing'">
                        <tr>
                            <th>#</th>
                            <th>Symptom</th>
                            <th colspan="2">Experience Changes with time</th>
                            <th></th>
                        </tr>
                    </thead>
                    <thead v-if="socrates.active == 'exacerbating'">
                        <tr>
                            <th>#</th>
                            <th>Symptom</th>
                            <th colspan="2">Exacerbating Factors</th>
                            <th></th>
                        </tr>
                    </thead>
                    <thead v-if="socrates.active == 'severity'">
                        <tr>
                            <th>#</th>
                            <th>Symptom</th>
                            <th>Severity</th>
                            <th></th>
                        </tr>
                    </thead>
                    <tbody v-if="socrates.active == 'site'">
                        <tr v-for="(symptom, index) in itemForm.symptoms" :key="symptoms.id">
                            <td>{{ addOne(index)  }}</td>
                            <td>{{ symptom.name }}</td>
                            <td><ModelListSelect :list="locations" v-model="itemForm.symptoms[index].location" option-value="name" option-text="name" /></td>
                            <td><ModelListSelect :list="positions" v-model="itemForm.symptoms[index].position" option-value="name" option-text="name" /></td>
                        </tr>
                    </tbody>
                    <tbody v-if="socrates.active == 'onset'">
                        <tr v-for="(symptom, index) in itemForm.symptoms" :key="symptoms.id">
                            <td>{{ addOne(index) }}</td>
                            <td>{{ symptom.name }}</td>
                            <td><input type="number" class="form-control" v-model="itemForm.symptoms[index].onset_duration" name="duration" id="duration" /></td>
                            <td><ModelListSelect :list="durations" v-model="itemForm.symptoms[index].duration" option-value="name" option-text="name" /></td>
                            <td>
                                <select class="form-control" v-model="itemForm.symptoms[index].onset_style">
                                    <option value="gradually">Gradual</option>
                                    <option value="immediately">Immediate</option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                    <tbody v-if="socrates.active == 'character'">
                        <tr v-for="(symptom, index) in itemForm.symptoms" :key="symptoms.id">
                            <td>{{ addOne(index) }}</td>
                            <td>{{ symptom.name }}</td>
                            <td><textarea class="form-control" id="character" name="character" v-model="itemForm.symptoms[index].character"></textarea></td>
                            <td>
                                <select class="form-control" v-model="itemForm.symptoms[index].character_type">
                                    <option value=""></option>
                                    <option value="come and go">Come and Go</option>
                                    <option value="constant">Constant</option>
                                </select>
                            </td>
                        </tr>
                    </tbody>
                    <tbody v-if="socrates.active == 'radiation'">
                        <tr v-for="(symptom, index) in itemForm.symptoms" :key="symptoms.id">
                            <td>{{ addOne(index) }}</td>
                            <td>{{ symptom.name }}</td>
                            <td>
                                <multiselect v-model="itemForm.symptoms[index].radiation_locations" :options="locations" :multiple="true" :close-on-select="false" :clear-on-select="false" :preserve-search="true" placeholder="Pick some" label="name" track-by="name" :preselect-first="false">
    <template slot="selection" slot-scope="{ values, search, isOpen }"><span class="multiselect__single" v-if="values.length" v-show="!isOpen">{{ values.length }} options selected</span></template>
                                </multiselect>
                            </td>
                        </tr>
                    </tbody>
                    <tbody v-if="socrates.active == 'associated'">
                        <tr v-for="(symptom, index) in itemForm.symptoms" :key="symptoms.id">
                            <td>{{ addOne(index) }}</td>
                            <td>{{ symptom.name }}</td>
                            <td><ModelListSelect :list="locations" v-model="itemForm.symptoms[index].associated" option-value="name" option-text="name" /></td>
                            <td></td>
                        </tr>
                    </tbody>
                    <tbody v-if="socrates.active == 'timing'">
                        <tr v-for="(symptom, index) in itemForm.symptoms" :key="symptoms.id">
                            <td>{{ index | addOne }}</td>
                            <td>{{ symptom.name }}</td>
                            <td><select class="form-control" name="timing" id="timing" v-model="itemForm.symptoms[index].timing"><option value="Yes">Yes</option><option value="No">No</option></select></td>
                            <td><textarea v-show="itemForm.symptoms[index].timing == 'Yes'" class="form-control" name="timing_details" id="timing_details" v-model="itemForm.symptoms[index].timing_details"></textarea></td>
                            <td></td>
                        </tr>
                    </tbody>
                    <tbody v-if="socrates.active == 'exacerbating'">
                        <tr v-for="(symptom, index) in itemForm.symptoms" :key="symptoms.id">
                            <td>{{ index | addOne }}</td>
                            <td>{{ symptom.name }}</td>
                            <td>
                                <select class="form-control" name="exacerbating_type" id="exacerbating_type" v-model="itemForm.symptoms[index].exacerbating_type">
                                    <option value="reduces">Reduces</option>
                                    <option value="increases">Increases</option>
                                </select>
                            </td>
                            <td>
                                <input type="text" class="form-control" name="exacerbating_factor" id="exacerbating_factor" v-model="itemForm.symptoms[index].exacerbating_factor" />
                             </td>
                             <td></td>
                        </tr>
                    </tbody>
                    <tbody v-if="socrates.active == 'severity'">
                        <tr v-for="(symptom, index) in itemForm.symptoms" :key="symptoms.id">
                            <td>{{ index | addOne }}</td>
                            <td>{{ symptom.name }}</td>
                            <td><input type="number" class="form-control" v-model="itemForm.symptoms[index].severity" name="severity" id="severity" /></td>
                            <td></td>
                        </tr>
                    </tbody>
                </table>
                <button class="btn btn-success btn-sm float-right" @click="generateNote()" :disabled="itemForm.symptoms.length==0">Generate</button>
            </div>
        </div>
    </section>
</template>
<script>
import { MultiSelect } from 'vue-search-select'
import { ModelListSelect } from 'vue-search-select'
//import { ajaxFindCountry } from './countriesApi';
export default {
    components: {
        MultiSelect, ModelListSelect
    },
    data() {
        return {
            durations: [],
            final: false,
            history_type: 'assisted',
            locations: [],
            modal: true,
            patient: {},
            positions: [],
            serviceName: '',
            serviceId: '',
            services: [],
            serviceForms: [],
            socrates: {active: 'site',},
            icd_10_codes: [],
            itemForm: new Form({description: '', detail: '', service_id: '', service_name: '', quantity: '', symptoms: [],}),
            investigationForm: new Form({id: '', doctor_id: '', doctor_name: '', start_date: '', patient_id: '', services: [],}),
            review: false,
            symptoms: [],
            value: [],
        }
    },
    emits: ['update:modelValue'],
    methods: {
        addTag(newTag) {
            const tag = {
                name: newTag,
                code: newTag.substring(0, 2) + Math.floor((Math.random() * 10000000))
            };
            this.options.push(tag)
            this.value.push(tag);
        },
        generateNote(){
            var note = '<p>The patient presented complaining of: <ul>';

            var sub_note = '';
            for (let i = 0; i < this.itemForm.symptoms.length; i++) {
                sub_note = '<li>'+this.itemForm.symptoms[i].name;
                if(this.itemForm.symptoms[i].location != null){
                    sub_note += ' on the '+ (this.itemForm.symptoms[i].position != null ? ' '+this.itemForm.symptoms[i].position+' part of the ' : '') +this.itemForm.symptoms[i].location;
                }
                if(this.itemForm.symptoms[i].onset_duration != null){
                    sub_note += ' for '+this.itemForm.symptoms[i].onset_duration+' '+itemForm.symptoms[index].duration+ (itemForm.symptoms[index].onset_style == 'gradually' ? ' the symptom started gradually.' : (itemForm.symptoms[index].onset_style == 'immediately' ? ' the symptoms started sharply.' : '.'));
                }
                if((this.itemForm.symptoms[i].character != null) || (this.itemForm.symptoms[i].character != null)){
                    sub_note += ' It feels as '+this.itemForm.symptoms[i].character != null ? this.itemForm.symptoms[i].character : '';
                    sub_note += 'the feeling '+this.itemForm.symptoms[i].character_type == 'come and go' ? 'tends to appear sporadically' : (this.itemForm.symptoms[i].character_type == 'constant' ? 'is constant': ''); 
                }
                if((this.itemForm.symptoms[i].character != null) || (this.itemForm.symptoms[i].character != null)){
                    sub_note += ' It feels as '+this.itemForm.symptoms[i].character != null ? this.itemForm.symptoms[i].character : '';
                    sub_note += 'the feeling '+this.itemForm.symptoms[i].character_type == 'come and go' ? 'tends to appear sporadically' : (this.itemForm.symptoms[i].character_type == 'constant' ? 'is constant': ''); 
                }
                sub_note = sub_note + '</li>';
                note = note + sub_note;
            }
            note = note + '</ul></p>'
            
        },
        getAllInitials() {
            axios.get('/api/emr/hims/consultations/initials')
            .then((response) => {
                this.refreshPage(response);
            })
            .catch(() => { });
        },
        limitText(count) {
            return `and ${count} other symptoms`
        },
        changeSocrates(id) {
            this.socrates.active = id;
        },

        clearAll() {
            this.selectedCountries = []
        },
        refreshPage(response) {
            this.durations = response.data.durations;
            this.frequencies = response.data.frequencies;
            this.locations = response.data.locations;
            this.positions = response.data.positions;
            this.symptoms = response.data.symptoms;
        },
        removeService(service) { this.investigationForm.services.pop(service); },
        searchServices() {
            axios.get('/api/emr/hims/services/search?q=' + this.itemForm.service_name)
            .then((response) => { this.drugs = response.data.services; })
            .catch(() => { });
        },
        submitSymptoms() {
            this.itemForm.symptoms = this.value;
            this.value = [];
        },
    },
    mounted() {
        this.getAllInitials();
    },
    props: {
        modelValue: {
            type: [Object, Array],
            default: () => ({}),
        },
    },
}
</script>