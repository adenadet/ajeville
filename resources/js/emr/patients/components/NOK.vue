<template>
    <div class="card">
        <div class="card-header bg-dark"><h4>Next of Kin</h4></div>
        <div class="card-body">
            <div class="row">
                <div class="col-md-6">
                    <label>Name</label>
                    <input class="form-control" v-model="modelValue.name" placeholder="Name" />
                </div>
                <div class="col-md-6">
                    <label>Address</label>
                    <input class="form-control" v-model="modelValue.address" placeholder="Address" />
                </div>
            </div>
            <div class="row mt-2">
                <div class="col-md-4">
                    <label>Phone number</label>
                    <input class="form-control" v-model="modelValue.phone" placeholder="Phone" />
                </div>
                <div class="col-md-4">
                    <label>Email Address</label>
                    <input class="form-control" v-model="modelValue.email_address" placeholder="Email" />
                </div>
                <div class="col-md-4">
                    <label>Relationship</label>
                    <input class="form-control" v-model="modelValue.relationship" placeholder="Relationship" />
                </div>
            </div>
        </div>
    </div>
</template>

<script>
export default { props: ['modelValue'], emits: ['update:modelValue'] }
</script>
