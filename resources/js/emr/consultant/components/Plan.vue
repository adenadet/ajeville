<template>
<div class="card mt-3">
    <div class="card-header bg-dark text-white">Plan</div>
    <div class="card-body">
        <div class="form-group mb-3">
            <label class="form-label fw-bold">Treatment / Management Plan</label>
            <textarea rows=5  class="form-control" v-model="localPlan.plan" @blur="update" placeholder="Describe the treatment plan, medications, procedures, counselling..."></textarea>
        </div>

        <div class="form-group mb-3">
            <label class="form-label fw-bold">
                Non-Drug Interventions
            </label>
            <textarea rows="5" class="form-control" v-model="localPlan.non_drug" @blur="update" placeholder="Physiotherapy, lifestyle modification, diet, counselling..."></textarea>
        </div>

        <!-- Follow-up -->
        <div class="row">
            <div class="col-md-4">
                <label class="form-label fw-bold">
                    Follow-up Date
                </label>
                <input type="date" @change="update" class="form-control"v-model="localPlan.follow_up_date"/>
            </div>

            <div class="col-md-8">
                <label class="form-label fw-bold">
                    Follow-up Instructions
                </label>
                <textarea class="form-control" @blur="update" rows="5" v-model="localPlan.follow_up_note"placeholder="Return if symptoms worsen, review lab results..."></textarea>
            </div>
        </div>
        <div class="row mt-3">
            <label class="col-md-12 form-label fw-bold">Care Intent</label>
            <div class="col-md-6">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" v-model="localPlan.intent.admission" id="intentAdmission"  @change="update"/>
                    <label class="form-check-label" for="intentAdmission">
                        Admission may be required
                    </label>
                </div>
            </div>
            <div class="col-md-6">
                <div class="form-check">
                    <input class="form-check-input" type="checkbox" v-model="localPlan.intent.referral" id="intentReferral" @change="update"/>
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
    data() {
        return {
            localPlan: {
                plan: '',
                non_drug: '',
                follow_up_date: null,
                follow_up_note: '',
                intent: {
                    admission: false,
                    referral: false,
                },
            },
        };
    },
    emits: ['update:modelValue'],
    methods: {
        update() {
            this.$emit('update:modelValue', this.clone(this.localPlan));
        },

        clone(val) {
            return {
                plan: val?.plan ?? '',
                non_drug: val?.non_drug ?? '',
                follow_up_date: val?.follow_up_date ?? null,
                follow_up_note: val?.follow_up_note ?? '',
                intent: {
                    admission: val?.intent?.admission ?? false,
                    referral: val?.intent?.referral ?? false,
                },
            };
        },
    },
    props: {
        modelValue: {
            type: Object,
            required: true,
        },
    },
    watch: {
        // Sync down from parent → child
        modelValue: {
            deep: true,
            handler(val) {
                this.localPlan = this.clone(val);
            },
        },
    },
};
</script>
