<template>
<section class="overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="row">
        <!-- Basic Expense Information-->
        <div class="col-md-12 form-group">
            <label>Name</label>
            <input type="text" required class="form-control" name="name" id="name" v-model="ExpenseTypeData.name" :class="{'is-invalid' : ExpenseTypeData.errors.has('name') }"/>
            <has-error :form="ExpenseTypeData" field="name"></has-error>
        </div>
    </div>
    <div class="row">
        <!-- Basic Expense Information-->
        <div class="col-md-12 form-group">
            <label>Status</label>
            <select class="form-control" name="status" id="status" v-model="ExpenseTypeData.status" :class="{'is-invalid' : ExpenseTypeData.errors.has('status') }">
                <option value=''>--Select Status--</option>
                <option value=1>Active</option>
                <option value=0>Inactive</option>
            </select>
            <has-error :form="ExpenseTypeData" field="status"></has-error>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 form-group">
            <label>Description</label>
            <QuillEditor content-type="html" theme="snow" class="form-control" v-model:content="ExpenseTypeData.description" :class="{'is-invalid' : ExpenseTypeData.errors.has('description') }"></QuillEditor>
            <has-error :form="ExpenseTypeData" field="description"></has-error>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12 form-group">
            <button class="btn btn-success" @click="editMode ? updateExpenseTypeData() : createExpenseTypeData()">Submit</button>
        </div>
    </div>
</section>
</template>
<script>
export default {
    data(){
        return  {
            ExpenseTypeData: new Form({
                id: '',
                name: '',
                description:'', 
                status: '',
            }),
            loading: false,
        }
    },
    emits: ['reloadExpenseTypeForm'],
    mounted() {},
    methods:{
        createExpenseTypeData(){
            this.loading = true;
            this.ExpenseTypeData.post('/api/finance/expense_types')
            .then(response =>{
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Expense details has been submited',
                    showConfirmButton: false,
                    timer: 1500
                    });
                this.$emit('reloadExpenseTypeForm');
                })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });  
            this.loading = false;        
        },
        updateExpenseTypeData(){
            this.loading = true;
            this.ExpenseTypeData.put('/api/finance/expense_types/'+this.ExpenseTypeData.id)
            .then(response =>{
                this.$emit('reloadExpenseTypeForm');
                this.$swal.fire({
                    icon: 'success',
                    title: 'The Expense details has been updated',
                    showConfirmButton: false,
                    timer: 1500
                    });
                })
            .catch(()=>{
                this.$swal.fire({icon: 'error', title: 'Oops...', text: 'Something went wrong!', footer: 'Please try again later!'});
            });
            this.loading = true;            
        },
    },
    props:{
        editMode: Boolean,
        expense_type: Object,
    },
    watch:{
        expense_type(){
            this.loading = true;
            this.ExpenseTypeData.fill(this.expense_type);
            this.loading = false;
        }
    }
}
</script>
