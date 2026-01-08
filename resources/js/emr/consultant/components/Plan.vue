<template>
<div class="card mt-3">
    <div class="card-header bg-dark text-white">Plan</div>
    <div class="card-body">
        <div class="form-group mb-3">
            <label class="form-label fw-bold">Treatment / Management Plan</label>
            <QuillEditor theme="snow" content-type="html" v-model:content="localPlan.plan" placeholder="Describe the treatment plan, medications, procedures, counselling..."/>
        </div>

        <div class="form-group mb-3">
            <label class="form-label fw-bold">
                Non-Drug Interventions
            </label>
            <QuillEditor theme="snow" content-type="html" v-model:content="localPlan.non_drug" placeholder="Physiotherapy, lifestyle modification, diet, counselling..."/>
        </div>

        <!-- Follow-up -->
        <div class="row">
            <div class="col-md-4">
                <label class="form-label fw-bold">
                    Follow-up Date
                </label>
                <input type="date" class="form-control"v-model="localPlan.follow_up_date"/>
            </div>

            <div class="col-md-8">
                <label class="form-label fw-bold">
                    Follow-up Instructions
                </label>
                <textarea class="form-control" rows="5" v-model="localPlan.follow_up_note"placeholder="Return if symptoms worsen, review lab results..."></textarea>
            </div>
        </div>
        <div class="row mt-3">
            <label class="col-md-12 form-label fw-bold">Care Intent</label>
            <div class="col-md-6">
                <div class="form-check">
                    <input
                        class="form-check-input"
                        type="checkbox"
                        v-model="localPlan.intent.admission"
                        id="intentAdmission"
                    />
                    <label class="form-check-label" for="intentAdmission">
                        Admission may be required
                    </label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" v-model="localPlan.intent.referral" id="intentReferral"/>
                    <label class="form-check-label" for="intentReferral">
                        Referral to specialist
                    </label>
                </div>
            </div>
        </div>
    </div>
</div>
</template>

<script>
export default {
    name: 'SoapPlan',

    props: {
        modelValue: {
            type: Object,
            default: () => ({}),
        },
    },

    emits: ['update:modelValue'],

    computed: {
        localPlan: {
            get() {
                return {
                    plan: this.modelValue.plan || '',
                    non_drug: this.modelValue.non_drug || '',
                    follow_up_date: this.modelValue.follow_up_date || null,
                    follow_up_note: this.modelValue.follow_up_note || '',
                    intent: {
                        admission: this.modelValue.intent?.admission || false,
                        referral: this.modelValue.intent?.referral || false,
                    },
                }
            },
            set(value) {
                this.$emit('update:modelValue', value)
            },
        },
    },
}
</script>