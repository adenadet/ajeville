<template>
<section class="col-md-12">
    <div class="card">
        <div class="card-header">
            <h3 class="card-title">My Assessments</h3>
            <div class="card-tools">
                <div class="input-group input-group-sm" style="width: 350px;">
                    <input type="text" name="table_search" class="form-control float-right" placeholder="Search">
                    <div class="input-group-append">
                        <button type="submit" class="btn btn-default"><i class="fas fa-search"></i></button>
                        <select class="form-control" v-model="query">
                            <option value=''>All</option>
                            <option value=2>Completed</option>
                            <option value=1>Unconfirmed</option>
                            <option value=0>Drafts</option>
                        </select>
                    </div>
                </div>
            </div>
        </div>
        <div class="card-body table-responsive p-0 overlay-wrapper">
            <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
            <HrmsDetailAssessmentList :assessments.sync="assessments.data" />
        </div>
    </div>
</section>
</template>