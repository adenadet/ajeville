<template>
    <section class="container-fluid">
        <div class="row">
            <div class="col-md-4">
                <EMRLaboratoryDetailSummary :request.sync="request" :show_status="editMode" @refreshLaboratoryPage="getInitials"/>
            </div>
            <div class="col-md-8 p-0">
                <div class="card card-primary card-tabs">
                    <div class="card-header p-0 pt-1">
                        <ul class="nav nav-tabs" id="custom-tabs-one-tab" role="tablist">
                            <li class="nav-item">
                                <a class="nav-link active" id="result-tab" data-toggle="pill" href="#result" role="tab" aria-controls="result" aria-selected="true">Results</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="specimen-tab" data-toggle="pill" href="#specimen" role="tab" aria-controls="specimen" aria-selected="false">Specimen</a>
                            </li>
                            <!--li class="nav-item">
                                <a class="nav-link" id="custom-tabs-one-messages-tab" data-toggle="pill" href="#custom-tabs-one-messages" role="tab" aria-controls="custom-tabs-one-messages" aria-selected="false">Messages</a>
                            </li>
                            <li class="nav-item">
                                <a class="nav-link" id="custom-tabs-one-settings-tab" data-toggle="pill" href="#custom-tabs-one-settings" role="tab" aria-controls="custom-tabs-one-settings" aria-selected="false">Settings</a>
                            </li-->
                        </ul>
                    </div>
                    <div class="card-body">
                        <div class="tab-content" id="custom-tabs-one-tabContent">
                            <div class="tab-pane fade show active" id="result" role="tabpanel" aria-labelledby="result-tab">
                                <div class="row">
                                    <div class="col-2 col-sm-2">
                                        <div class="nav flex-column nav-tabs h-100" id="vert-tabs-tab" role="tablist" aria-orientation="vertical">
                                            <a v-for="(version,index) in request?.result?.versions" :key="version.id" class="nav-link" :class="{active: activeTab === index}" @click.prevent="activeTab = index">{{ addOne(index) }}</a>
                                        </div>
                                    </div>
                                    <div class="col-10 col-sm-10">
                                        <div class="tab-content" id="vert-tabs-tabContent">
                                            <div class="tab-pane text-left fade" v-for="(version,index) in request?.result?.versions" :key="version.id" :class="{active: activeTab === index, show: activeTab === index}" >
                                                <EMRLaboratoryDetailResult :version="version" :request.sync="request" /> 
                                            </div>
                                        </div>
                                    </div>
                                    </div>
                            </div>
                            <div class="tab-pane fade" id="specimen" role="tabpanel" aria-labelledby="specimen-tab">
                                <EMRLaboratoryDetailSpecimenList :specimens="request.specimens" /> 
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<script>
import EMRLaboratoryDetailResult from '@/emr/laboratory/details/Result.vue';
import EMRLaboratoryDetailSpecimenList from '@/emr/laboratory/details/SpecimenList.vue';
import EMRLaboratoryDetailSummary from '@/emr/laboratory/details/Summary.vue';
export default {
    components:{
        EMRLaboratoryDetailResult, EMRLaboratoryDetailSpecimenList, EMRLaboratoryDetailSummary
    },
    data() {
        return {
            activeTab: 0,
            editMode: true,
            request: {
                result:[],
                specimens: [],
            },
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        getInitials() {
            axios.get('/api/emr/laboratory/requests/'+this.$route.params.id)
            .then(response => {
                this.refreshDashboard(response)
            })
            .catch(() => {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Your appointments did not loaded successfully',
                })
            });
        },
        refreshDashboard(response) {
            this.request = response.data.request;
            this.$store.dispatch('setPatientCookie', response.data.request.patient);
            this.$store.dispatch('setVisitCookie', response.data.request.visit);
        }
    },
    props: {}
}
</script>