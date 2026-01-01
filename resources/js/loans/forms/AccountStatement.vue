<template>
    <form @submit.prevent="createAccountStatement"  enctype="multipart/form-data">
        <alert-error :form="accountStatementData"></alert-error> 
        <div class="row">
            <div class="col-sm-12">
                <div class="form-group">
                    <label>Policy Name</label>
                    <input type="text" class="form-control" id="name" name="name" placeholder="Name *" v-model="accountStatementData.name" required>
                </div>
            </div>
            <div class="col-sm-6">
                <div class="form-group">
                    <label>Upload File</label>
                    <input type="file" class="form-control" id="document" name="document" v-on:change="onFileChange"/> 
                </div>
            </div>
        </div>
        <div class="row">
            <div class="col-md-12">
                <div class="form-group">
                    <label>Description</label>
                    <textarea class="form-control" id="description" name="description" rows="5" v-model="accountStatementData.description"></textarea>
                </div>
            </div>
            <input type="hidden" name="editMode" id="editMode" :value="editMode" />
            <div class="col-md-4 col-sm-12">
                <input type="submit" name="submit" class="submit btn btn-success" :value="editMode ? 'Update' : 'Create'" />
            </div>
        </div>
    </form>
</template>
<script>
export default {
    data(){
        return {   
            accountStatementData: new Form({
                id:'', 
                category_id: '', 
                name:'', 
                file: '', 
                description: '',
                editMode: '',
            }),
            file: '',
            filename: '',
        }
    },
    methods:{
        onFileChange(e){
            this.filename = "Selected File: " + e.target.files[0].name;
            this.file = e.target.files[0];
        },
        createAccountStatement(e){
            const json = JSON.stringify({
                id: this.accountStatementData.id, 
                name: this.accountStatementData.name, 
                category_id: this.accountStatementData.category_id, 
                description: this.accountStatementData.description,
                
            });
            let currentObj = this;
            const config = {
                headers: {
                'content-type': 'multipart/form-data',
                //'X-CSRF-TOKEN': document.querySelector('meta[name="csrf-token"]').content,
                }
            };
            // form data
            let formData = new FormData();
            formData.append('file', this.file);
            formData.append('data', json );

            console.log(formData);
            
            axios.post('/api/policies', formData, config)
            .then(function (response) {
                currentObj.success = response.data.success;
                currentObj.filename = "";
                Fire.$emit('reloadLoan', response);
                var message = editMode ? 'Policy was successfully updated!' : 'Policy was successfully added!';
                Swal.fire({
                    icon: 'success',
                    title: message,
                });
            })
            .catch(function (error) {currentObj.output = error;});
        },
        uploadFile(e){
            this.file = e.target.files[0];
        },
        uploadFiles(e) {
            e.preventDefault();
            
            const json = JSON.stringify({
                id: this.policyData.id, 
                category_id: this.policyData.category_id, 
                description: this.policyData.description,
                name: this.policyData.name, 
                editMode: this.editMode,
            });

            let currentObj = this;
            
            const config = {headers: { 'content-type': 'multipart/form-data'}}

            let formData = new FormData();
            if (!(this.editMode)){formData.append('file', this.file);}
            formData.append('data', json);

            console.log(formData.data);
            if (editMode){
                axios.put('/api/policies/'+this.policyData.id, formData, config)
                .then(function (response) {
                    Fire.$emit('CourseRefresh', response.data.course);
                    Swal.fire({icon: 'success', title: response.data.success,});   
                })
                .catch(function (error) {
                    currentObj.output = error;
                });
            }
            else{
                axios.post('/api/policies', formData, config)
                .then(function (response) {
                    //Fire.$emit('refresh', response);
                    Fire.$emit('CourseRefresh', response.data.course);
                    Swal.fire({icon: 'success', title: response.data.success,});   
                })
                .catch(function (error) {
                    currentObj.output = error;
                });
            }
            
        },
    },
    mounted() {
        Fire.$on('policyDataFill', policy =>{
            this.policyData.reset();
            this.policyData.fill(policy)
        });
    },
    props: {
        'departments': Array,
        'editMode': Boolean,
        'loan': Object,
    },
}
</script>