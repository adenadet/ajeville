<template>
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">Consultation</h3>
            </div>
            <div class="card-body overlay-wrapper p-0">
                <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                <Consultant v-model="consultation" @submitted="onSubmitted" />
            </div>
        </div>
    </div>
</template>
<script>
import Consultant from '@/emr/consultant/forms/Consultant.vue';
import { createEmptyConsultation } from './special_functions/consultation';
export default {
    components: { Consultant },
    data() {
        return {
            consultation: createEmptyConsultation(this.$route.params.id),//this.createConsultation(),
            loading: false,
        };
    },
    methods: {
        onSubmitted() {
            this.loading = true;
            axios.put('/api/emr/consultations/consultants/'+this.$route.params.id, this.consultation)
            .then((response) => {
                this.refreshPage(response);
                this.$toast.fire({icon: 'success', title: 'Consultation Form was loaded successfully',});
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Consultation Form was not loaded successfully',
                })
            })
            .finally(()=> {
                this.loading = false;
            });
        },
        reloadConsultation() {
            this.loading = true;
            axios.get(`/api/emr/consultations/consultants/${this.$route.params.id}`)
            .then(response => {
                this.consultation = {
                    ...createEmptyConsultation(response.data.consultation.id),
                    ...response.data.consultation,
                };
            })
            .finally(() => {
                this.loading = false;
            });
        }
    },
    mounted() {
        if (this.$route.params.id) {
            this.reloadConsultation();
        }
    },
};
</script>
