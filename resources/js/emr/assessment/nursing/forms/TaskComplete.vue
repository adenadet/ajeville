<template>
<section>
    <div class="row" v-if="(task.task_details.id == 1)" :task="task" :editMode="editMode" :staffs="staffs"><p>Activity Completion Form</p></div>
    <p v-else>Complete Form Here</p>
</section>
</template>
<script>
export default {
    data() {
        return {}
    },
    mounted() {
    },
    props: {
        editMode: Boolean,
        task: Object,
        staffs: Array,
    }
}
</script>