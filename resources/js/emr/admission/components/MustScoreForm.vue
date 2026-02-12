<template>
<div>

    <h5 class="font-weight-bold mb-3">
        Malnutrition Universal Screening Tool (MUST)
    </h5>

    <!-- STEP 1 BMI -->
    <div class="card card-outline card-primary mb-3">
        <div class="card-header">
            <strong>Step 1 – BMI Score</strong>
        </div>
        <div class="card-body">

            <div class="row">
                <div class="col-md-6">
                    <label>BMI (kg/m²)</label>
                    <input type="number"
                           step="0.1"
                           v-model.number="form.bmi"
                           class="form-control" />
                </div>
                <div class="col-md-6">
                    <label>Score</label>
                    <input type="text"
                           :value="bmiScore"
                           class="form-control"
                           readonly />
                </div>
            </div>

        </div>
    </div>

    <!-- STEP 2 WEIGHT LOSS -->
    <div class="card card-outline card-primary mb-3">
        <div class="card-header">
            <strong>Step 2 – Unplanned Weight Loss (Past 3–6 Months)</strong>
        </div>
        <div class="card-body">

            <div class="row">
                <div class="col-md-6">
                    <label>% Weight Loss</label>
                    <input type="number"
                           step="0.1"
                           v-model.number="form.weight_loss"
                           class="form-control" />
                </div>
                <div class="col-md-6">
                    <label>Score</label>
                    <input type="text"
                           :value="weightLossScore"
                           class="form-control"
                           readonly />
                </div>
            </div>

        </div>
    </div>

    <!-- STEP 3 ACUTE DISEASE -->
    <div class="card card-outline card-primary mb-3">
        <div class="card-header">
            <strong>Step 3 – Acute Disease Effect</strong>
        </div>
        <div class="card-body">

            <div class="form-group">
                <label>
                    Patient acutely ill AND no nutritional intake > 5 days?
                </label>
                <select v-model="form.acute_disease"
                        class="form-control">
                    <option :value="false">No</option>
                    <option :value="true">Yes</option>
                </select>
            </div>

            <div>
                <strong>Score: {{ acuteDiseaseScore }}</strong>
            </div>

        </div>
    </div>

    <!-- STEP 4 TOTAL -->
    <div class="card mb-3"
         :class="riskCardClass">
        <div class="card-header">
            <strong>Step 4 – Overall Risk of Malnutrition</strong>
        </div>
        <div class="card-body">

            <h4>Total Score: {{ totalScore }}</h4>

            <h5>
                Risk Category:
                <span :class="riskBadgeClass" class="badge ml-2">
                    {{ riskCategory }}
                </span>
            </h5>

        </div>
    </div>

    <!-- STEP 5 MANAGEMENT -->
    <div class="card card-outline card-secondary">
        <div class="card-header">
            <strong>Step 5 – Management Guidance</strong>
        </div>
        <div class="card-body">

            <div v-if="totalScore === 0">
                <h6>Low Risk – Routine Clinical Care</h6>
                <ul>
                    <li>Repeat screening weekly (hospital)</li>
                    <li>Care home – monthly</li>
                    <li>Community – annually</li>
                </ul>
            </div>

            <div v-if="totalScore === 1">
                <h6>Medium Risk – Observe</h6>
                <ul>
                    <li>Document dietary intake for 3 days</li>
                    <li>Repeat screening weekly</li>
                    <li>Follow local nutrition protocol</li>
                </ul>
            </div>

            <div v-if="totalScore >= 2">
                <h6>High Risk – Treat</h6>
                <ul>
                    <li>Refer to dietitian / Nutrition Support Team</li>
                    <li>Set nutritional goals</li>
                    <li>Monitor and review weekly</li>
                </ul>
            </div>

        </div>
    </div>

</div>
</template>

<script>
export default {

    data() {
        return {
            form: {
                bmi: null,
                weight_loss: null,
                acute_disease: false
            }
        }
    },

    computed: {

        /* Step 1 */
        bmiScore() {
            if (this.form.bmi === null) return 0;
            if (this.form.bmi > 20) return 0;
            if (this.form.bmi >= 18.5) return 1;
            return 2;
        },
        weightLossScore() {
            if (this.form.weight_loss === null) return 0;
            if (this.form.weight_loss < 5) return 0;
            if (this.form.weight_loss <= 10) return 1;
            return 2;
        },
        acuteDiseaseScore() {
            return this.form.acute_disease ? 2 : 0;
        },
        totalScore() {
            return (
                this.bmiScore +
                this.weightLossScore +
                this.acuteDiseaseScore
            );
        },

        riskCategory() {
            if (this.totalScore === 0) return 'Low Risk';
            if (this.totalScore === 1) return 'Medium Risk';
            return 'High Risk';
        },

        riskBadgeClass() {
            if (this.totalScore === 0) return 'badge-success';
            if (this.totalScore === 1) return 'badge-warning';
            return 'badge-danger';
        },

        riskCardClass() {
            if (this.totalScore === 0) return 'card-success';
            if (this.totalScore === 1) return 'card-warning';
            return 'card-danger';
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

    methods: {
        emitUpdate() {
            this.$emit('update', {
                name: 'Malnutrition MUST Score',
                result: this.totalScore,
                notes: this.riskCategory,
                meta: {
                    bmi: this.form.bmi,
                    bmi_score: this.bmiScore,
                    weight_loss: this.form.weight_loss,
                    weight_loss_score: this.weightLossScore,
                    acute_disease: this.form.acute_disease,
                    acute_disease_score: this.acuteDiseaseScore
                }
            });
        }
    }
}
</script>
