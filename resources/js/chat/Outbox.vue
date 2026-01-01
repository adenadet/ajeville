<template>
<section class="overlay-wrapper">
    <div class="col-md-12">
          <div class="card card-primary card-outline">
            <div class="card-header">
              <h3 class="card-title">Inbox</h3>

              <div class="card-tools">
                <div class="input-group input-group-sm">
                  <input type="text" class="form-control" placeholder="Search Mail">
                  <div class="input-group-append">
                    <div class="btn btn-primary">
                      <i class="fas fa-search"></i>
                    </div>
                  </div>
                </div>
              </div>
              <!-- /.card-tools -->
            </div>
            <div class="card-body p-0">
                <div class="mailbox-controls">
                    <button type="button" class="btn btn-default btn-sm checkbox-toggle"><i class="far fa-square"></i>
                    </button>
                    <div class="btn-group">
                        <button type="button" class="btn btn-default btn-sm"><i class="far fa-trash-alt"></i></button>
                        <button type="button" class="btn btn-default btn-sm"><i class="fas fa-reply"></i></button>
                        <button type="button" class="btn btn-default btn-sm"><i class="fas fa-share"></i></button>
                    </div>
                    <button type="button" class="btn btn-default btn-sm"><i class="fas fa-sync-alt"></i></button>
                        <div class="float-right">
                        1-50/200
                        <div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm"><i class="fas fa-chevron-left"></i></button>
                            <button type="button" class="btn btn-default btn-sm"><i class="fas fa-chevron-right"></i></button>
                        </div>
                    </div>
                </div>
            </div>
            <div class="card-footer p-0">
                <div class="mailbox-controls">
                    <button type="button" class="btn btn-default btn-sm checkbox-toggle"><i class="far fa-square"></i></button>
                    <div class="btn-group">
                        <button type="button" class="btn btn-default btn-sm"><i class="far fa-trash-alt"></i></button>
                        <button type="button" class="btn btn-default btn-sm"><i class="fas fa-reply"></i></button>
                        <button type="button" class="btn btn-default btn-sm"><i class="fas fa-share"></i></button>
                    </div>
                    <button type="button" class="btn btn-default btn-sm"><i class="fas fa-sync-alt"></i></button>
                    <div class="float-right">1-50/200
                        <div class="btn-group">
                            <button type="button" class="btn btn-default btn-sm"><i class="fas fa-chevron-left"></i></button>
                            <button type="button" class="btn btn-default btn-sm"><i class="fas fa-chevron-right"></i></button>
                        </div>
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
            current_page: 1,
            loading: false,
            message: {},
            user: {},
        }
    },
    methods:{
        getAllInitials(){
            this.loading = true;
            axios.get('/api/chats/messages?t=sent&page='+this.current_page)
            .then(response =>{
                this.loading = false;
                this.messages       = response.data.messages;
            })
            .catch(()=>{
                this.loading = false;
                this.$toast.fire({icon: 'error', title: 'Dashboard not loaded successfully',});
            });
        },
        scrollHanle(evt) {
            console.log(evt)
        },
    },
    mounted() {
        this.getAllInitials();
    }
}
</script>