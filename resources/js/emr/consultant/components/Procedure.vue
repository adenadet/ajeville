<template>
<div class="card">
    <div class="card-body">

        <!-- Procedure -->
        <div class="row mb-2">
            <div class="col-md-6">
                <label class="form-label fw-bold">
                    Procedure / Service
                </label>
                <ModelListSelect
                    class="form-control"
                    :list="services"
                    v-model="local.service_id"
                    option-value="id"
                    option-text="name"
                    placeholder="Select procedure"
                />
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold">
                    Quantity / Sessions
                </label>
                <input
                    type="number"
                    min="1"
                    class="form-control"
                    v-model="local.quantity"
                />
            </div>

            <div class="col-md-3">
                <label class="form-label fw-bold">
                    Urgency
                </label>
                <select class="form-control" v-model="local.urgency">
                    <option value="routine">Routine</option>
                    <option value="urgent">Urgent</option>
                    <option value="emergency">Emergency</option>
                </select>
            </div>
        </div>

        <!-- Indication -->
        <div class="mb-2">
            <label class="form-label fw-bold">
                Clinical Indication
            </label>
            <textarea
                class="form-control"
                rows="2"
                v-model="local.indication"
                placeholder="Why is this procedure required?"
            ></textarea>
        </div>

        <!-- Scheduling -->
        <div class="row mb-2">
            <div class="col-md-6">
                <label class="form-label fw-bold">
                    Preferred Date
                </label>
                <input
                    type="date"
                    class="form-control"
                    v-model="local.preferred_date"
                />
            </div>

            <div class="col-md-6">
                <label class="form-label fw-bold">
                    Additional Notes
                </label>
                <input
                    type="text"
                    class="form-control"
                    v-model="local.notes"
                />
            </div>
        </div>

        <!-- Actions -->
        <div class="text-end">
            <button class="btn btn-sm btn-dark" @click="emitRequest">
                Add Procedure
            </button>
        </div>

    </div>
</div>
</template>

<script>
export default {
    data() {
        return {
            local: this.empty(),
        }
    },
    emits: ['update:modelValue'],
    methods: {
        empty() {
            return {
                service_id: null,
                service_name: '',
                category: '',
                quantity: 1,
                urgency: 'routine',
                preferred_date: null,
                indication: '',
                notes: '',
            }
        },

        emitRequest() {
            const service = this.services.find(
                s => s.id === this.local.service_id
            )

            if (!service) return

            this.$emit('add', {
                ...this.local,
                service_name: service.name,
                category: service.category || null,
            })

            this.local = this.empty()
        },
    },
    props: {
        modelValue: {
            type: [Array, Object],
            default: () => ([]),
        },
    },
}
</script>
