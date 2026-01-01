<template>
<section>
    <div class="card m-0">
        <div class="card-header bg-dark">
            <h3 class="card-title">Transaction Contract</h3>
        </div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-12">
                    <div class="form-group">
                        <QuillEditor v-model:content="contract.content"  contentType="html" :readOnly="true" name="contract" id="contract" readonly></QuillEditor>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return {
            editMode: true,
        }
    },
    props:{
        contract: Object,
    },
}
</script>