<template>
<section>
    <div class="card card-primary card-outline">
        <div class="card-header">
            <h3 class="card-title"><i class="fas fa-edit"></i>Vertical Tabs Examples</h3>
        </div>
        <div class="card-body">
            <h4>Left Sided</h4>
            <div class="row">
                <div class="col-5 col-sm-3">
                    <div class="nav flex-column nav-tabs h-100" id="request-tab" role="tablist" aria-orientation="vertical">
                        <a class="nav-link active" id="prescription-tab" data-toggle="pill" href="#prescription" role="tab" aria-controls="prescription" aria-selected="true">Home</a>
                        <a class="nav-link" id="laboratory-tab" data-toggle="pill" href="#laboratory" role="tab" aria-controls="laboratory" aria-selected="false">Profile</a>
                        <a class="nav-link" id="radiology-tab" data-toggle="pill" href="#radiology" role="tab" aria-controls="radiology" aria-selected="false">Radiology</a>
                        <a class="nav-link" id="procedure-tab" data-toggle="pill" href="#procedure" role="tab" aria-controls="procedure" aria-selected="false">Procedure</a>
                    </div>
                </div>
                <div class="col-7 col-sm-9">
                    <div class="tab-content" id="request-tabContent">
                        <div class="tab-pane text-left fade show active" id="prescription" role="tabpanel" aria-labelledby="prescription-tab">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>#</th>
                                        <th>Drug</th>
                                        <th>Specific Drug</th>
                                        <th>Dose</th>
                                        <th>Total Quantity</th>
                                        <th>Drug Form</th>
                                        <th>Duration</th>
                                        <th>Freq.</th>
                                        <th>Route</th>
                                        <th>Details</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(drug, index) in template_request.prescription" :key="'pre-'+index">
                                        <td>{{ addOne(index)  }}</td>
                                        <td>{{ drug.drug_name }}</td>
                                        <td>{{ drug.specific_drug != null ? drug.specific_drug.name : '' }}</td>
                                        <td>{{ drug.dose }}</td>
                                        <td>{{ drug.total_quantity }}</td>
                                        <td>{{ drug.drug_form }}</td>
                                        <td>{{ drug.duration }}</td>
                                        <td>{{ drug.frequency }}</td>
                                        <td>{{ drug.route }}</td>
                                        <td v-html="readMore(drug.detail, 25, '...')"></td>
                                        <td><div class="btn-group"><button class="btn btn-sm btn-default" @click=removeDrug(index)><i class="fa fa-trash"></i></button></div></td>
                                    </tr>
                                </tbody>
                            </table> 
                        </div>
                        <div class="tab-pane fade" id="laboratory" role="tabpanel" aria-labelledby="laboratory-tab">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                    <th>Code</th>
                                    <th>Name</th>
                                    <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in template_request.laboratory" :key="'lab-' + index">
                                    <td>{{ item.code }}</td>
                                    <td>{{ item.name }}</td>
                                    <td>{{ item.notes }}</td>
                                    </tr>
                                </tbody>
                            </table>    
                        </div>
                        <div class="tab-pane fade" id="radiology" role="tabpanel" aria-labelledby="radiology-tab">
                            <table class="table table-sm table-bordered">
                                <thead>
                                    <tr>
                                        <th>Code</th>
                                        <th>Name</th>
                                        <th>Notes</th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in template_request.radiology" :key="'rad-' + index">
                                        <td>{{ item.code }}</td>
                                        <td>{{ item.name }}</td>
                                        <td>{{ item.notes }}</td>
                                    </tr>
                                </tbody>
                            </table> 
                        </div>
                        <div class="tab-pane fade" id="procedure" role="tabpanel" aria-labelledby="procedure-tab">
                            Pellentesque vestibulum commodo nibh nec blandit. Maecenas neque magna, iaculis tempus turpis ac, ornare sodales tellus. Mauris eget blandit dolor. Quisque tincidunt venenatis vulputate. Morbi euismod molestie tristique. Vestibulum consectetur dolor a vestibulum pharetra. Donec interdum placerat urna nec pharetra. Etiam eget dapibus orci, eget aliquet urna. Nunc at consequat diam. Nunc et felis ut nisl commodo dignissim. In hac habitasse platea dictumst. Praesent imperdiet accumsan ex sit amet facilisis. 
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data() {
        return {
            consultations: {},
            editMode: true,
            form: new Form({}),
            loading: false,
        }
    },
    mounted() {
        this.getInitials();
    },
    methods: {
        
    },
    props: {
        request_template: Object,
        type: String,
        view: String,
    }
}
</script>