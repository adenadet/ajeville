<template>
    <div class="card">
            <div class="card-header bg-dark">
            <h4 class="card-title">{{ title }}</h4>
            <div class="card-tools">
            <button class="btn btn-sm btn-light float-right" type="button" @click="add">Add</button></div>
        </div>
        <div class="card-body table-responsive p-0">
            <table class="table table-head-fixed table-bordered table-striped text-nowrap">
                <thead>
                    <tr>
                        <th>Name</th>
                        <th>Address</th>
                        <th>Phone</th>
                        <th>Email</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="(c, i) in modelValue" :key="i" class="p-0">
                        <td><input class="form-control" v-model="c.name" placeholder="Name" /></td>
                        <td><input class="form-control" v-model="c.address" placeholder="Address" /></td>
                        <td><input class="form-control" v-model="c.phone" placeholder="Phone" /></td>
                        <td><input class="form-control" v-model="c.email" placeholder="Email" /></td>
                        <td><button class="btn btn-danger btn-sm" @click="remove(i)"><i class="fa fa-trash"></i></button></td>
                    </tr>
                </tbody>
            </table>
        </div>
    </div>
</template>

<script>
export default {
    props: ['modelValue', 'title'],
    emits: ['update:modelValue'],
    methods: {
        add() {
            this.$emit('update:modelValue', [...this.modelValue, { name: '', address: '', phone: '', email: '' }])
        },
        remove(i) {
            const copy = [...this.modelValue]
            copy.splice(i, 1)
            this.$emit('update:modelValue', copy)
        }
    }
}
</script>