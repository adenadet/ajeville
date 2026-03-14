<template>
<form class="overlay-wrapper">
    <div class="row">
        <div class="col-md-5">
            <div class="card">
                <div class="card-header bg-primary">
                    <h3 class="card-title">Request Detail</h3>
                </div>
                <div class="card-body">
                    <strong><i class="fas fa-user-injured mr-1"></i> Patient</strong>
                    <p class="text-muted">{{ patientName(request?.patient) }}</p>
                    <hr>
                    <strong><i class="fas fa-flask mr-1"></i>Service Name</strong>
                    <p class="text-muted">{{ request?.item?.name }}</p>
                    <hr>
                    <strong><i class="fas fa-vial mr-1"></i>Recommended Bottle</strong>
                    <p class="text-muted">{{ request?.lab_service?.bottle_type?.name }}</p>
                    <hr>
                    <strong><i class="fas fa-vials mr-1"></i> Specimen</strong>
                    <p class="text-muted">{{ request?.lab_service?.specimen_type?.name }}</p>
                    <hr>
                    <strong><i class="far fa-file-alt mr-1"></i> Notes</strong>
                    <p class="text-muted" v-html="request?.description"></p>
                </div>
            </div>
        </div>
        <div class="col-md-7">
            <div class="card">
                <div class="card-header bg-primary">
                    <h3 class="card-title">Specimen Detail</h3>
                </div>
                <div class="card-body overlay-wrapper">
                    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
                    <div class="row">
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Bottle Type confirmed</label>
                                <select v-model="collectionForm.bottle_id" class="form-control"> 
                                    <option v-for="bottle in bottles" :value="bottle.id"> {{ bottle.name }} ({{ bottle.colour }}) </option> 
                                </select>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Specimen</label>
                                <div v-html="request?.lab_service?.specimen_type?.name" class="form-control"></div>
                            </div>
                        </div>
                        <div class="col-md-12">
                            <div class="form-group">
                                <label>Description</label>
                                <QuillEditor v-model:content="collectionForm.remark" theme="snow" content-type="html" class="form-control" />
                            </div>
                        </div>
                        <div class="col-md-12">
                            <button class="btn btn-success btn-sm float-left" @click="createSpecimen" type="button"><i class="fa fa-save mr-1"></i>Create Specimen</button>
                            <button class="btn btn-primary btn-sm float-right" @click="printLabel" type="button" :disabled="!specimen"><i class="fa fa-print"></i>Print Label</button>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</form>
</template>
<script>
import { QuillEditor } from '@vueup/vue-quill';

export default {
    data() {
        return {
            bottles: [],
            collectionForm: new Form({
                request_id: '',
                remarks: '',
                bottle_id: '',
                specimen_type_id: '',
            }),
            editMode: true,
            loading: false,
            specimen: null,
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        createSpecimen(){
            if (!this.collectionForm.bottle_id) {
                this.$toast.fire({
                    icon: 'error',
                    title: 'Please confirm the bottle type'
                })
                return
            }
            this.loading = true
            this.collectionForm.request_id = this.request.id;
            this.collectionForm.specimen_type_id = this.request?.lab_service?.specimen_type?.id;
            this.collectionForm.post('/api/emr/laboratory/specimens')
            .then(response => {
                this.specimen = response.data.specimen;
                this.$toast.fire({icon: 'success', title: 'Specimen collected successfully'});
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Specimen could not be created'});
            })
            .finally(() => {
                this.loading = false
            })
        },
        getInitials() {
            axios.get('/api/emr/laboratory/specimens/'+this.$route.params.id+'/initials')
            .then(response => {
                this.refreshPage(response)
            })
            .catch(() => {
                this.$toast.fire({icon: 'error', title: 'Collection form did not loaded successfully',})
            });
        },
        printLabel() {
            if (!this.specimen) {
                this.$toast.fire({
                    icon: 'error',
                    title: 'No specimen available to print'
                })
                return
            }

            const patient = this.request?.patient;
            const service = this.request?.item?.name;
            const specimenType = this.request?.lab_service?.specimen_type?.name;
            const bottle = this.request?.lab_service?.bottle_type?.name;
            const specimenId = this.specimen?.unique_id;
            const patientName = this.patientName(patient);

            const collectedAt = new Date().toLocaleString()

            const labelHTML = `
                <html>
                <head>
                    <title>Specimen Label</title>
                    <style>
                        body{
                            font-family: Arial, sans-serif;
                            padding:5px;
                        }
                        .label{
                            width:300px;
                            border:1px solid #000;
                            padding:10px;
                        }
                        .title{
                            font-size:16px;
                            font-weight:bold;
                            margin-bottom:5px;
                        }
                        .line{
                            font-size:13px;
                            margin-bottom:3px;
                        }
                        .barcode{
                            margin-top:8px;
                            font-size:18px;
                            letter-spacing:2px;
                        }
                    </style>
                </head>
                <body onload="window.print(); window.close();">
                    <div class="label">
                        <div class="title">${patientName}</div>

                        <div class="line"><b>Specimen:</b> ${specimenType}</div>
                        <div class="line"><b>Test:</b> ${service}</div>
                        <div class="line"><b>Bottle:</b> ${bottle}</div>

                        <hr>

                        <div class="line"><b>Collected:</b> ${collectedAt}</div>
                        <div class="line"><b>Specimen ID:</b> ${specimenId}</div>

                        <div class="barcode">*${specimenId}*</div>
                    </div>
                </body>
                </html>
            `

            const printWindow = window.open('', '', 'width=400,height=300')
            printWindow.document.write(labelHTML)
            printWindow.document.close()
        },
        refreshPage(response) {
            this.bottles = response.data.bottles;
            this.specimen = response.data.specimen;
        }
    },
    props: {
        request: Object,
    }
}
</script>