<template>
<form @submit.prevent="editMode ? updateNotice() : createNotice()"  enctype="multipart/form-data" class="overlay-wrapper">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <alert-error :form="noticeData"></alert-error> 
    <div class="row">
        <div class="col-sm-12">
            <div class="form-group">
                <label>Topic</label>
                <input type="text" class="form-control" id="name" name="name" placeholder="Name *" v-model="noticeData.topic" required>
            </div>
        </div>
        <div class="col-sm-12">
            <div class="form-group">
                <label>Image</label>
                <input type="file" class="form-control" id="document" name="document" @change="updateFile"/> 
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label>Start Date</label>
                <input type="date" class="form-control" id="start_date" name="start_date" v-model="noticeData.start_date" required/> 
            </div>
        </div>
        <div class="col-sm-6">
            <div class="form-group">
                <label>End Date</label>
                <input type="date" class="form-control" id="end_date" name="end_date" v-model="noticeData.end_date" required/> 
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-md-12">
            <div class="form-group">
                <label>Content</label>
                <QuillEditor theme="snow" class="form-control" id="description" name="description" rows="5" v-model:content="noticeData.content" content-type="html"></QuillEditor>
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
import Form from 'vform';
export default {
    data(){
        return {   
            loading: false,
            noticeData: new Form({
                id:'', 
                topic: '', 
                end_date:'', 
                start_date: '', 
                content: '',
                editMode: '',
                file: '',
                file_type: '',
            }),
        }
    },
    emits: ['reloadList'],
    methods:{
        createNotice(e){
            this.loading = true;
            this.noticeData.post('/api/notices')
            .then((response) => {
                this.$emit('reloadList', response);
                var message = editMode ? 'Policy was successfully updated!' : 'Policy was successfully added!';
                this.$swal.fire({icon: 'success', title: message,});
            })
            .catch((error) =>{
                this.$swal.fire({icon: 'error', title: 'Something went wrong',});
            });
        },
        updateFile(e){
            let file = e.target.files[0];
            let reader = new FileReader();
            var acceptFileTypes = ['image/jpeg', 'image/png', 'image/jpg', 'image/bmp'];
            if (acceptFileTypes.includes(file['type'])){
                if (file['size'] < 2000000){
                    reader.onloadend = (e) => {
                        this.noticeData.file = reader.result;
                        this.noticeData.file_type = file['type'];
                        console.log(this.noticeData.file_type);
                        console.log(file['type']);
                        }
                    reader.readAsDataURL(file)
                }
                else{
                    this.$swal.fire({type: 'error', title: 'File is too large'});
                }
            }
            else{
                this.$swal.fire({type: 'error', title: 'File Type is not acceptable'})
            }
        },
        updateNotice(){
            this.loading = true;
            this.noticeData.put('/api/notices/'+ this.noticeData.id)
            .then((response) => {
                this.loading = false;
                this.$emit('reloadList', response);
                this.$swal.fire({icon: 'success', title: 'Policy was successfully updated!',});
            })
            .catch((error) =>{
                this.loading = false;
                this.$swal.fire({icon: 'error', title: 'Something went wrong',});
            });
        },
        
    },
    mounted() {},
    props: {
        editMode: Boolean,
        notice: Object,
    },
    watch:{
        notice(){
            this.noticeData.fill(this.notice);
        }
    }
}
</script>