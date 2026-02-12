<template>
<div>
    <form v-if="isEditable" @submit.prevent="editMode ? updatePreOp() : createPreOp()">
        <div class="row">
            <div class="col-md-6">
                <div class="form-group mb-2">
                    <label>Airway Score</label>
                    <input class="form-control" v-model="form.airway_score">
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-2">
                    <label>Fitness</label>
                    <select class="form-control" v-model="form.fitness">
                        <option value="fit">Fit</option>
                        <option value="not_fit">Not Fit</option>
                        <option value="deferred">Deferred</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group mb-2">
                    <label>
                        <input type="checkbox" v-model="form.consent_obtained">
                        Consent Obtained
                    </label>
                </div>
            </div>
            <div class="col-md-12">
                <div class="form-group">
                    <label>Risk Notes</label>
                    <QuillEditor class="form-control" v-model:content="form.risk_notes"  content-type="html" theme="snow"/>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-4">
                <button class="btn btn-primary" type="submit">Save</button>
            </div>
            <div class="col-md-4">

            </div>
            <div class="col-md-4">

            </div>
        

        <button class="btn btn-success ms-2" @click.prevent="clear">
            Clear for Anesthesia
        </button>
        </div>
        
    </form>

    <div v-else class="alert alert-info">
        Pre-Op assessment is locked.
    </div>
</div>
</template>

<script>

export default {
    name: 'PreOpAssessment',

    data() {
        return {
            preOpData: new Form({
                airway_score: '',
                risk_notes: '',
                fitness: '',
                consent_obtained: false
            })
        }
    },

    computed: {
        store() {return useAnesthesiaCaseStore()},
        isEditable() {return this.store.case?.status === 1}
    },
    emits:{},
    mounted() {
        if (this.store.preOp) {
            Object.assign(this.form, this.store.preOp)
        }
    },
    methods: {
        createPreOp(){},
        updatePreOp(){
            this.loading = true;
            this.preOpData.put()
            .then(()=>{

            })
            .catch(()=>{})
            .fially(()=>{
                this.loading = false;
            })
        },
        async save() {
            await axios.post(
                `/api/emr/anesthesia/cases/${this.store.case.id}/pre-op`,
                this.form
            )
        },

        async clear() {
            await axios.post(
                `/api/emr/anesthesia/cases/${this.store.case.id}/pre-op/clear`
            )
        }
    },
    props:{
        case: Object,
        editMode: Boolean,
        preOp: Object,
    },
    watch:{
        preOp(){
            if (this.preOp == null){
                this.preOpData.reset();
            }
            else{
                this.preOpData.fill(this.preOp);
            }
        },
    }
}
</script>
