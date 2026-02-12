<template>
<div>
    <h5 class="font-weight-bold mb-3">
        Bed Rail Risk Assessment
    </h5>
    <div class="card card-outline card-primary mb-3">
        <div class="card-header">
            <strong>Section 1 – Overview</strong>
        </div>
        <div class="card-body">
            <div v-for="item in section1" :key="item.key" class="mb-2">
                <label>{{ item.label }}</label>
                <div>
                    <select v-model="form.section1[item.key]"
                            class="form-control">
                        <option value="">Select</option>
                        <option value="yes">Yes</option>
                        <option value="no">No</option>
                    </select>
                </div>
            </div>
            <textarea v-model="form.section1_comments" class="form-control mt-2" placeholder="Comments"></textarea>
        </div>
    </div>
    <div class="card card-outline card-warning mb-3">
        <div class="card-header">
            <strong>Section 2 – Bed Occupant Factors</strong>
        </div>
        <div class="card-body">
            <div v-for="item in section2" :key="item.key" class="mb-2">
                <label>{{ item.label }}</label>
                <select v-model="form.section2[item.key]" class="form-control">
                    <option value="">Select</option>
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                </select>
            </div>

            <textarea v-model="form.section2_comments"
                      class="form-control mt-2"
                      placeholder="Comments"></textarea>

        </div>
    </div>
    <div class="card card-outline card-info mb-3">
        <div class="card-header">
            <strong>Section 3 – Equipment Factors</strong>
        </div>
        <div class="card-body">
            <div v-for="item in section3" :key="item.key" class="mb-2">
                <label>{{ item.label }}</label>
                <select v-model="form.section3[item.key]"
                        class="form-control">
                    <option value="">Select</option>
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                    <option value="na">N/A</option>
                </select>
            </div>
            <textarea v-model="form.section3_comments"class="form-control mt-2" placeholder="Comments"></textarea>
        </div>
    </div>

    <!-- SECTION 4 RECOMMENDATION -->
    <div class="card mb-3" :class="recommendationCardClass">
        <div class="card-header">
            <strong>Section 4 – Assessment Recommendation</strong>
        </div>
        <div class="card-body">
            <h5>
                Recommended:
                <span class="badge ml-2"
                      :class="recommendationBadgeClass">
                      {{ recommendation }}
                </span>
            </h5>
            <div class="form-group mt-3">
                <label>Person Agreed?</label>
                <select v-model="form.person_agreed"
                        class="form-control">
                    <option value="">Select</option>
                    <option value="yes">Yes</option>
                    <option value="no">No</option>
                </select>
            </div>

        </div>
    </div>
    <div class="card card-outline card-secondary">
        <div class="card-header">
            <strong>Assessor Information</strong>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-4">
                    <input v-model="form.assessor_name"
                           class="form-control"
                           placeholder="Assessor Name" />
                </div>
                <div class="col-md-4">
                    <input v-model="form.designation"
                           class="form-control"
                           placeholder="Designation" />
                </div>
                <div class="col-md-4">
                    <input type="date"
                           v-model="form.date"
                           class="form-control" />
                </div>
            </div>

        </div>
    </div>

</div>
</template>

<script>
export default {
    computed: {

        recommendation() {

            const noInSection1 = Object.values(this.form.section1)
                .includes('no');

            const highRiskFactors = Object.values(this.form.section2)
                .filter(v => v === 'yes').length;

            const equipmentIssue = Object.values(this.form.section3)
                .includes('no');

            if (noInSection1) return 'NOT APPROPRIATE';

            if (equipmentIssue) return 'REVIEW EQUIPMENT';

            if (highRiskFactors >= 2) return 'CAUTION – ALTERNATIVES ADVISED';

            return 'APPROPRIATE';
        },

        recommendationBadgeClass() {
            if (this.recommendation === 'APPROPRIATE')
                return 'badge-success';
            if (this.recommendation === 'CAUTION – ALTERNATIVES ADVISED')
                return 'badge-warning';
            return 'badge-danger';
        },

        recommendationCardClass() {
            if (this.recommendation === 'APPROPRIATE')
                return 'card-success';
            if (this.recommendation === 'CAUTION – ALTERNATIVES ADVISED')
                return 'card-warning';
            return 'card-danger';
        }
    },
    data() {
        return {
            section1: [
                { key: 'likely_to_fall', label: 'Likely to fall from bed?' },
                { key: 'alternatives_considered', label: 'Alternatives considered?' },
                { key: 'strategies_considered', label: 'Fall strategies considered?' },
                { key: 'risks_explained', label: 'Risks explained to person/carer?' }
            ],

            section2: [
                { key: 'restlessness', label: 'Restlessness causing injury?' },
                { key: 'confusion', label: 'Confusion?' },
                { key: 'mobility_issue', label: 'Restricted mobility?' },
                { key: 'unsupervised_exit', label: 'Likely unsupervised bed exit?' },
                { key: 'entrapment_risk', label: 'Entrapment risk due to build?' },
                { key: 'injury_risk_from_rails', label: 'Rails increase injury risk?' }
            ],

            section3: [
                { key: 'environment_checked', label: 'Environmental factors considered?' },
                { key: 'equipment_checked', label: 'Equipment interaction considered?' },
                { key: 'bed_condition', label: 'Bed in good condition?' },
                { key: 'mattress_fit', label: 'Mattress correct size?' }
            ],

            form: {
                section1: {},
                section2: {},
                section3: {},
                section1_comments: '',
                section2_comments: '',
                section3_comments: '',
                person_agreed: '',
                assessor_name: '',
                designation: '',
                date: ''
            }
        }
    },
    methods: {
        emitUpdate() {
            this.$emit('update', {
                name: 'Bed Rail Risk Assessment',
                result: this.recommendation,
                notes: this.form.section1_comments,
                meta: this.form
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
