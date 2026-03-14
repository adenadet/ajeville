<template>
<div class="card">
    <div class="card-header bg-dark">
        <h4 class="card-title">Contacts</h4>
        <div class="card-tools">
            <button class="btn btn-sm btn-light" @click="$emit('add')">Add</button>
        </div>
    </div>

    <div class="card-body table-responsive p-0">
        <table class="table table-head-fixed text-nowrap">
            <thead>
                <tr>
                    <th>S/N</th>
                    <th>Name</th>
                    <th>Address</th>
                    <th>Phone</th>
                    <th>Email</th>
                    <tr></tr>
                </tr>
            </thead>
            <tbody>
                <tr v-for="(c,index) in contacts" :key="index">
                    <td>{{ addOne(index) }}</td>
                    <td><input type="text" v-model="c.name" class="form-control"></td>
                    <td><input type="text" v-model="c.address" class="form-control"></td>
                    <td><input type="tel" v-model="c.phone" class="form-control"></td>
                    <td><input type="email" v-model="c.email" class="form-control"></td>
                    <td><button class="btn btn-danger" @click="$emit('remove',index)"><i class="fa fa-trash mr-1"></i>Delete </button></td>
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