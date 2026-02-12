<template>
<div class="card card-primary">
    <div class="card-header">
        <h3 class="card-title">Waterlow Pressure Ulcer Risk Assessment</h3>
    </div>
    <div class="card-body">
        <div class="row">
            <div class="col-12">
                <h5 class="text-primary">1. Core Risk Factors</h5>
                <hr/>
            </div>
        </div>
        <div class="row">
            <div class="col-md-6">
                <div class="form-group">
                    <label>Build / BMI</label>        
                    <select class="form-control" v-model.number="form.bmi_score">
                        <option value="">--Select Range--</option>
                        <option :value="0">Normal (0)</option>
                        <option :value="1">Overweight (1)</option>
                        <option :value="2">Underweight (2)</option>
                        <option :value="3">Obese (3)</option>
                    </select>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-group">
                    <label>Sex</label>
                    <select class="form-control" v-model.number="form.sex_score">
                        <option value="">--Select Range--</option>
                        <option :value="1">Male (1)</option>
                        <option :value="2">Female (2)</option>
                    </select>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-3">
                <div class="form-group">
                    <label>Skin Type / Visual Risk</label>
                    <select class="form-control" v-model.number="form.skin_score">
                        <option value="">--Select Range--</option>
                        <option :value="0">Healthy (0)</option>
                        <option :value="1">Dry (1)</option>
                        <option :value="2">Oedematous (2)</option>
                        <option :value="3">Broken / Discoloured (3)</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Mobility</label>
                    <select class="form-control" v-model.number="form.mobility_score">
                        <option value="">--Select Range--</option>
                        <option :value="0">Fully Mobile (0)</option>
                        <option :value="2">Restless / Fidgety (2)</option>
                        <option :value="3">Restricted (3)</option>
                        <option :value="4">Chair-bound (4)</option>
                        <option :value="5">Bed-bound (5)</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Continence</label>
                    <select class="form-control" v-model.number="form.continence_score">
                        <option value="">--Select Range--</option>
                        <option :value="0">Complete (0)</option>
                        <option :value="1">Urine Incontinence (1)</option>
                        <option :value="2">Faecal Incontinence (2)</option>
                        <option :value="3">Double Incontinence (3)</option>
                    </select>
                </div>
            </div>
            <div class="col-md-3">
                <div class="form-group">
                    <label>Nutrition / Appetite</label>
                    <select class="form-control" v-model.number="form.nutrition_score">
                        <option value="">--Select Range--</option>
                        <option :value="0">Adequate (0)</option>
                        <option :value="1">Poor Appetite (1)</option>
                        <option :value="2">NG / Fluid Only (2)</option>
                        <option :value="3">Malnourished (3)</option>
                    </select>
                </div>
            </div>
        </div>
        
        <div class="row mt-4">
            <div class="col-12">
                <h5 class="text-primary">2. Special Risk Factors</h5>
                <hr/>
            </div>
        </div>
        <div class="form-group">
            <div class="form-check" v-for="risk in specialRiskOptions" :key="risk.label">
                <input class="form-check-input" type="checkbox" :value="risk" v-model="form.special_risks">
                <label class="form-check-label">{{ risk.label }} ({{ risk.score }})</label>
            </div>
        </div>
        <div class="row mt-4">
            <div class="col-12">
                <div class="alert"
                     :class="riskClass">
                    <h5>Total Score: {{ totalScore }}</h5>
                    <h5>Risk Level: {{ riskLevel }}</h5>
                </div>
            </div>
        </div>
        <div class="form-group">
            <label>Clinical Notes</label>
            <QuillEditor class="form-control" rows="3" v-model:content="form.notes" content-type="html" />
        </div>
    </div>
</div>
</template>

<script>
export default {
    data() {
        return {
            form: {
                bmi_score: '',
                age_score: '',
                sex_score: '',
                skin_score: '',
                mobility_score: '',
                continence_score: '',
                nutrition_score: '',
                special_risks: [],
                notes: ''
            },

            specialRiskOptions: [
                { label: 'Diabetes', score: 2 },
                { label: 'Neurological Deficit', score: 4 },
                { label: 'Major Surgery / Trauma', score: 5 },
                { label: 'Steroid Therapy', score: 4 },
                { label: 'Cardiovascular Disease', score: 2 }
            ]
        }
    },

    computed: {
        totalScore() {
            let base =
                Number(this.form.bmi_score) +
                Number(this.form.age_score) +
                Number(this.form.sex_score) +
                Number(this.form.skin_score) +
                Number(this.form.mobility_score) +
                Number(this.form.continence_score) +
                Number(this.form.nutrition_score);

            let special = this.form.special_risks.reduce(
                (sum, r) => sum + r.score, 0
            );

            return base + special;
        },

        riskLevel() {
            if (this.totalScore >= 20) return 'Very High Risk';
            if (this.totalScore >= 15) return 'High Risk';
            if (this.totalScore >= 10) return 'At Risk';
            return 'Low Risk';
        },

        riskClass() {
            if (this.totalScore >= 20) return 'alert-danger';
            if (this.totalScore >= 15) return 'alert-warning';
            if (this.totalScore >= 10) return 'alert-info';
            return 'alert-success';
        }
    },
    methods: {
        emitUpdate() {
            this.$emit('update', {
                name: 'Waterlow Score',
                result: this.totalScore,
                notes: this.form.notes,
                meta: {
                    bmi_score: this.form.bmi_score,
                    age_score: this.form.age_score,
                    sex_score: this.form.sex_score,
                    skin_score: this.form.skin_score,
                    mobility_score: this.form.mobility_score,
                    continence_score: this.form.continence_score,
                    nutrition_score: this.form.nutrition_score,
                    special_risks: this.form.bmi_special_risks,
                }
            });
        }
    },
    watch: {
        form: {
            deep: true,
            handler() {
                this.emitUpdate();
            }
        }
    },
}
</script>
