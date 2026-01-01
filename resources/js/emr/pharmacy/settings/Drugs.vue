<template>
    <section class="container-fluid overlay-wrapper">
        <div class="row">
            <div class="col-12">
                <div class="card">
                    <div class="card-header">
                        <h4 class="card-title">Drugs/Formulary</h4>
                        <div class="card-tools">
                            <div class="card-tools">
                                <div class="input-group input-group-sm" style="width: 150px;">
                                    <select type="text" name="view_type" id="view_type" class="form-control float-right" v-model="view_type" required>
                                        <option value=''>View Type</option>
                                        <option value="list">List Style</option>
                                        <option value="excel">Workbook Style</option>
                                    </select>
                                </div>
                            </div>
                        </div>
                    </div>
                    <div class="card-body" v-if="view_type == 'list'">
                        <PharmacySettingDrugsList />
                    </div>
                    <div class="card-body" v-else>
                        <PharmacySettingDrugsExcel />
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
            editMode: false,
            loading: false,
            view_type: 'list',
        }
    },
    mounted() {},
    methods: {},
}
</script>