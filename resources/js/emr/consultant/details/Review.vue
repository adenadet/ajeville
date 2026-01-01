<template>
    <section>
        <div class="ribbon-wrapper">
            <div class="ribbon bg-primary">
                Admission Adviced
            </div>
        </div>
        <div class="card-body">
            <div class="row">
                Advice to Admit banner true or false
                <div class="col-md-9">
                    <VisitDetailSummary />
                </div>
                <div class="col-md-3">

                </div>
                <div class="col-md-4">
                    Current Medication List
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-dark">
                            Presenting Complaint
                        </div>
                        <div class="card-body" v-html="consultationForm.complaint"></div>
                    </div>
                </div>
                <div class="col-md-6">
                    <div class="card">
                        <div class="card-header bg-dark">
                            Complaint History
                        </div>
                        <div class="card-body" v-html="consultationForm.history"></div>
                    </div>
                </div>
                <div class="col-md-12">
                    <div class="card-header">
                        <h3 class="card-title">Further Actions</h3>
                    </div>
                    <div class="card-body">
                        <div id="accordion">
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title w-100">
                                        <a class="d-block w-100" data-toggle="collapse" href="#collapseOne">
                                            Prescriptions
                                        </a>
                                    </h4>
                                </div>
                                <div id="collapseOne" class="collapse show" data-parent="#accordion">
                                    <div class="card-body table-responsive p-0">
                                        <table class="table table-hover text-nowrap">
                                            <thead>
                                                <tr>
                                                    <th>#</th>
                                                    <th>Drug</th>
                                                    <th>Specific Drug</th>
                                                    <th>Dose</th>
                                                    <th>Form</th>
                                                    <th>Quantity</th>
                                                    <th>Duration</th>
                                                    <th>Frequency</th>
                                                    <th>Route</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(drug, index) in consultationForm.prescriptions"
                                                    :key="drug.id">
                                                    <td>{{ index | addOne }}</td>
                                                    <td>{{ drug.drug_name }}</td>
                                                    <td>{{ drug.specific_drug != null ? drug.specific_drug.name : '' }}
                                                    </td>
                                                    <td>{{ drug.dose }}</td>
                                                    <td>{{ drug.form }}</td>
                                                    <td>{{ drug.quantity }}</td>
                                                    <td>{{ drug.duration }}</td>
                                                    <td>{{ drug.frequency }}</td>
                                                    <td>{{ drug.route }}</td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title w-100">
                                        <a class="d-block w-100" data-toggle="collapse" href="#collapseTwo">Laboratory
                                            Investigations</a>
                                    </h4>
                                </div>
                                <div id="collapseTwo" class="collapse" data-parent="#accordion">
                                    <div class="card-body">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Name </th>
                                                    <th>Quantity </th>
                                                    <th>Description</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(item, index) in consultationForm.laboratory" :key="item.id">
                                                    <td>{{ item.name }}</td>
                                                    <td>
                                                        <div class="form-control" v-html="item.quantity"></div>
                                                    </td>
                                                    <td>
                                                        <div class="form-control" v-html="item.description"></div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                            <div class="card">
                                <div class="card-header">
                                    <h4 class="card-title w-100">
                                        <a class="d-block w-100" data-toggle="collapse" href="#collapseThree">Radiology
                                            Investigations</a>
                                    </h4>
                                </div>
                                <div id="collapseThree" class="collapse" data-parent="#accordion">
                                    <div class="card-body">
                                        <table class="table table-striped">
                                            <thead>
                                                <tr>
                                                    <th>Name </th>
                                                    <th>Quantity </th>
                                                    <th>Description</th>
                                                </tr>
                                            </thead>
                                            <tbody>
                                                <tr v-for="(item, index) in  consultationForm.radiology" :key="item.id">
                                                    <td>{{ item.name }}</td>
                                                    <td>
                                                        <div class="form-control" v-html="item.quantity"></div>
                                                    </td>
                                                    <td>
                                                        <div class="form-control" v-html="item.description"></div>
                                                    </td>
                                                </tr>
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<script>
import { MultiSelect } from 'vue-search-select'
import { ModelListSelect } from 'vue-search-select'

export default {
    components: {
        MultiSelect, ModelListSelect
    },
    data() {
        return {
            
        }
    },
    methods: {

    },
    mounted() {
    },
    props: {
        consultationForm: Object,
    },
}
</script>