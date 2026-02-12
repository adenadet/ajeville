<template>
    <div>
        <h5 v-if="template.layout.show_header">Laboratory Result</h5>
        <table class="table table-sm table-striped">
            <thead>
                <tr>
                    <th>Test</th>
                    <th>Result</th>
                    <th>Unit</th>
                    <th>Flag</th>
                    <th>Range</th>
                    <th>Remarks</th>
                </tr>
            </thead>
            <tbody>
                <tr v-for="a in template.analytes" :key="a.key">
                    <td><div class="form-control" v-html="a.name"></div></td>
                    <td><div class="form-control"></div></td>
                    <td><div class="form-control" v-html="a.unit"></div></td>
                    <td><div class="form-control" v-html="a.show_flag ? '[Compute flag]': ''"></div></td>
                    <td><div class="form-control" v-html="a.show_range ? '[Enter relevant range]': ''"></div></td>
                    <td><div class="form-control"></div></td>
                </tr>
            </tbody>
        </table>
        <div v-if="template.layout.interpretation_position === 'bottom'">
            <strong>Interpretation</strong>
            <p>{{ template.interpretation_template }}</p>
        </div>
        <footer v-if="template.layout.show_footer">
            <small>Electronically generated report</small>
        </footer>
    </div>
</template>

<script>
export default {
    computed: {
        isEdit() {
            return !!this.modelValue
        }
    },
    props:{
        template: Object
    },
    watch:{
        template(){

        }
    }
}
</script>
