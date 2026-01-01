<template>
    <div>
        <div class="form-group">
            <label for="contract">Contract</label>
            <QuillEditor :readOnly="editMode" id="contract_details" v-model:content="contract_details" contentType="html" placeholder="Contract_details"></QuillEditor>
        </div>
    </div>
</template>
<script>
export default {
    data() {
        return {
            editMode: false,
            contract_details: '',
        }
    },
    emits:['refreshPage'],
    mounted() {},
    methods: {},
    props: {
        contract: Object,
    },
    watch:{
        contract(){
            this.contract_details = this.contract.details;
        }
    }
}
</script>