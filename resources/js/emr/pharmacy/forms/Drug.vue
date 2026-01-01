<template>
<section>
    <form @submit.prevent="editMode ? editDrug() : createDrug()">
        <alert-error :form="drugForm"></alert-error>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Drug Name*</label>
                    <input type="date" class="form-control" id="name" name="name" v-model="drugForm.name" :class="{ 'is-invalid': drugForm.errors.has('name') }" :min="today" />
                    <has-error :form="drugForm" field="name"></has-error>
                </div>
            </div>
            <div class="col-sm-3">
                <div class="form-group">
                    <label>Domiciliary Service</label>
                    <select class="form-control" id="domiciliary" name="domiciliary"
                        :class="{ 'is-invalid': drugForm.errors.has('domiciliary') }">
                        <option value=''>--Select If Domiciliary--</option>
                        <option value='1'>Yes</option>
                        <option value='0'>No</option>
                    </select>
                    <has-error :form="drugForm" field="domiciliary"></has-error>
                </div>
            </div>  
        </div>
        <div class="row">
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Task</label>
                    <select class="form-control" id="task_id" name="task_id" v-model="drugForm.task_id"
                        :class="{ 'is-invalid': drugForm.errors.has('task_id') }">
                        <option value=''>--Select Task--</option>
                        <option v-for="task in tasks" :value='task.id'>{{ task.name }}</option>
                    </select>
                    <has-error :form="drugForm" field="task_id"></has-error>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Start Date</label>
                    <input type="date" class="form-control" id="start_date" name="start_date" v-model="drugForm.start_date" :class="{ 'is-invalid': drugForm.errors.has('start_date') }" :min="today" />
                    <has-error :form="drugForm" field="start_date"></has-error>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-4">
                <div class="form-group">
                    <label>Repeating </label>
                    <select class="form-control" id="repeating" name="repeating" v-model="drugForm.repeating"
                        :class="{ 'is-invalid': drugForm.errors.has('repeating') }">
                        <option value=''>--Select If Repeating</option>
                        <option value='1'>Yes</option>
                        <option value='0'>No</option>
                    </select>
                    <has-error :form="drugForm" field="repeating"></has-error>
                </div>
            </div>
            <div class="col-sm-4" v-show="drugForm.repeating == 1">
                <div class="form-group">
                    <label>Frequency</label>
                    <select class="form-control" id="frequency_id" name="frequency_id" v-model="drugForm.frequency_id"
                        :class="{ 'is-invalid': drugForm.errors.has('frequency_id') }">
                        <option value=''>--Select Frequency--</option>
                        <option v-for="frequency in frequencies" :value='frequency.id'>{{ frequency.name }}</option>
                    </select>
                    <has-error :form="drugForm" field="frequency_id"></has-error>
                </div>
            </div>
            <div class="col-sm-4" v-show="drugForm.repeating == 1">
                <div class="form-group">
                    <label>Amount</label>
                    <input type="number" class="form-control" id="quantity" name="quantity" v-model="drugForm.quantity" :class="{ 'is-invalid': drugForm.errors.has('quantity') }" :min="today" />
                    <has-error :form="drugForm" field="quantity"></has-error>
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Details</label>
                    <wysiwyg id="details" name="details" v-model="drugForm.details" :class="{ 'is-invalid': drugForm.errors.has('details') }"/>
                    <has-error :form="drugForm" field="details"></has-error>
                </div>
            </div>
        </div>
        <button class="btn btn-sm btn-primary" type="submit">Submit</button>
    </form>
</section>

</template>
<script>

</script>