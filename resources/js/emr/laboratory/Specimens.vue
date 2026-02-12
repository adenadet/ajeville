<template>
<section>
    <div class="row">
        <div class="col-12">
            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">Fixed Header Table</h3>

                    <div class="card-tools">
                        <div class="input-group" style="width: 550px;">
                            <input type="text" name="table_search" class="form-control float-right" placeholder="Search">
                            <div class="input-group-append"><button type="button" class="btn btn-default mr-1" @click="getInitials"><i class="fas fa-search"></i></button></div>
                        </div>
                    </div>
                </div>
                <div class="card-body table-responsive p-0" style="height: 300px;">
                    <table class="table table-head-fixed text-nowrap">
                        <thead>
                            <tr>
                                <th>S/N</th>
                                <th>Received Date</th>
                                <th>Patient</th>
                                <th>Service</th>
                                <th>Specimen</th>
                                <th>Status</th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody v-if="specimens.total > 0">
                            <tr v-for="(specimen, index) in specimens.data">
                                <td>{{ addOne(index) }}</td>
                                <td>{{ ExcelDate(specimen.received_at) }}</td>
                                <td>{{ patientName(specimen.patient) }}</td>
                                <td>{{ specimen.service.service.item.name }}</td>
                                <td>
                                    <span v-if="specimen.status == 0"class="badge badge-info">Pending</span>
                                    <span v-else-if="specimen.status == 1" class="badge badge-success">Approved</span>
                                    <span v-else-if="specimen.status == 10" class="badge badge-danger">Rejected</span>
                                </td>
                                <td>

                                </td>
                            </tr>
                        </tbody>
                        <tbody v-else>
                            <tr><td colspan="6">No Specimen meets your requirement</td></tr>
                        </tbody>
                    </table>
                </div>
                <div class="card-footer">
                    <div class="col-12">
                        <pagination v-model="current_page" @paginate="getInitials" :per-page="specimens.per_page != null ? specimens.per_page : 52" :records="specimens.total != null ? specimens.total : 550" ></pagination>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</template>