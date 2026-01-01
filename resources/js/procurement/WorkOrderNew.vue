<template>
    <ProcurementFormWorkOrder :editMode="false" />
</template>
<script>
import ProcurementFormWorkOrder from './forms/WorkOrder.vue';
export default {
    components:{ProcurementFormWorkOrder},
}
</script>