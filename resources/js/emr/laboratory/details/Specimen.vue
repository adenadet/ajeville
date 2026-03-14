<template>
<section>
    <div class="row">
        <div class="col-md-6">
            <div class="card card-primary card-outline">
                <div class="card-body box-profile">
                    <h3 class="profile-username text-center">Nina Mcintire</h3>
                    <p class="text-muted text-center">Software Engineer</p>
                    <ul class="list-group list-group-unbordered mb-3">
                        <li class="list-group-item">
                            <b>Followers</b> <a class="float-right">1,322</a>
                        </li>
                        <li class="list-group-item">
                            <b>Following</b> <a class="float-right">543</a>
                        </li>
                        <li class="list-group-item">
                            <b>Friends</b> <a class="float-right">13,287</a>
                        </li>
                    </ul>
                    <button class="btn btn-primary btn-block"><b>Follow</b></button>
                    <button class="btn btn-primary btn-block"><b>Follow</b></button>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
import EMRLaboratoryFormSpecimenAction from '@/emr/laboratory/forms/SpecimenAction.vue';
export default {
    components:{
        EMRLaboratoryFormSpecimenAction
    },
    data() {
        return {
            editMode: true,
            loading: false,
        }
    },
    emits:['refreshSpecimenList'],
    mounted() {},
    methods: {
        actionSpecimen(specimen){
            this.loading = true;
            this.specimen = specimen;
            $('#specimenActionFormModal').modal('show');
            this.loading = false;
        },
        closeModals(){
            $('#specimenActionFormModal').modal('hide');
            this.$emit('refreshSpecimenList');
        },
    },
    props: {
        specimen: Object,
        source: String,
    }
}
</script>