<template>
<div class="card card-outline card-primary">
    <div class="card-header">
        <h5 class="card-title">Cannula Assessment</h5>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Was Cannula Placed?</label>
                    <select class="form-control" v-model="form.was_placed">
                        <option value="">--Select Option--</option>
                        <option :value="false">No</option>
                        <option :value="true">Yes</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="row" v-if="form.was_placed">
            <div class="col-md-4">
                <div class="form-group">
                    <label>Cannula Site</label>
                    <select class="form-control" v-model="form.site">
                        <option value="">Select Site</option>
                        <option>Dorsum of Hand</option>
                        <option>Forearm</option>
                        <option>Antecubital Fossa</option>
                        <option>Upper Arm</option>
                        <option>Foot</option>
                        <option>Other</option>
                    </select>
                </div>    
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Assessment Date & Time</label>
                    <input type="datetime-local" class="form-control" v-model="form.assessed_at">
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>PIVCAS Score (0 - 4)</label>
                    <select class="form-control" v-model.number="form.pivcas_score">
                        <option :value="0">0 - No signs of phlebitis</option>
                        <option :value="1">1 - Slight pain / redness</option>
                        <option :value="2">2 - Pain + redness + swelling</option>
                        <option :value="3">3 - Palpable venous cord</option>
                        <option :value="4">4 - Extensive phlebitis / purulent discharge</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Patency</label>
                    <select class="form-control" v-model="form.patency">
                        <option value="flushes">Flushes Easily</option>
                        <option value="resistant">Resistance on Flush</option>
                        <option value="blocked">Blocked / Occluded</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Securement & Dressing</label>
                    <select class="form-control" v-model="form.dressing">
                        <option value="intact">Clean, Dry & Intact</option>
                        <option value="loose">Loose / Needs Reinforcement</option>
                        <option value="soiled">Soiled / Wet</option>
                        <option value="dislodged">Dislodged</option>
                    </select>
                </div>
            </div>
            <div class="col-md-4">
                <div class="form-group">
                    <label>Is Cannula Still Required?</label>
                    <select class="form-control" v-model="form.still_required">
                        <option :value="true">Yes</option>
                        <option :value="false">No</option>
                    </select>
                </div>
            </div>
            <div class="col-md-12">
                <h6><b>Observed Complications</b></h6>
                <div class="form-check" v-for="c in complicationOptions" :key="c">
                    <input class="form-check-input" type="checkbox"  :value="c" v-model="form.complications">
                    <label class="form-check-label">{{ c }}</label>
                </div>
            </div>
            <div class="col-md-12 alert alert-danger mt-3" v-if="shouldRemove">
                <strong>Immediate Removal Recommended.</strong> Clinical indicators suggest device should be removed.
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group mt-3">
                    <label>Clinical Notes</label>
                    <QuillEditor class="form-control" rows="3" v-model:content="form.notes" content-type="html" />
                </div>
            </div>
        </div>
    </div>
</div>
</template>
<script>
export default {
    computed: {
        shouldRemove() {
            if (!this.form.was_placed) return false;
            if (this.form.pivcas_score >= 2) return true;
            if (!this.form.still_required) return true;
            if (this.form.complications.length > 0) return true;
            if (this.form.patency === 'blocked') return true;

            return false;
        }

    },
    data() {
        return {
            complicationOptions: ['Phlebitis', 'Infiltration', 'Extravasation', 'Occlusion', 'Pain / Tenderness', 'Infection Signs'],
            form: {was_placed: '', site: '', assessed_at: '', pivcas_score: 0, patency: 'flushes', dressing: 'intact', still_required: true, complications: [], notes: ''},
        }
    },
    methods:{
        emitUpdate() {
            this.$emit('update', {
                name: 'Cannula Assessment',
                result: this.shouldRemove ? 'Maintain and Monitor' : 'Immediate Removal Recommended.',
                notes: this.form.notes,
                meta: this.form
            });
        }
    },
    props: {
        modelValue: {
            type: Object,
            default: () => ({})
        }
    },
    watch: {
        form: {
            deep: true,
            handler(val) {
                this.emitUpdate();
            }
        }
    }
}
</script>
