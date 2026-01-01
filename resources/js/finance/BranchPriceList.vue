<template>
    <section class="container-fluid">
        <div class="row">
            <div class="col-3">
                <div class="card">
                    <div class="card-body">
                        <div class="d-sm-flex align-items-center mb-2">
                            <h4 class="card-title mb-sm-0">Search</h4>
                        </div>
                        <div class="">   
                            <form>
                                <alert-error :form="searchData"></alert-error>
                                <div class="row" >
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Branch</label>
                                            <select class="form-control" id="branch_id" name="branch_id" v-model="searchData.branch_id">
                                            </select>
                                            <has-error :form="searchData" field="branch_id"></has-error>
                                        </div>
                                    </div> 
                                </div>
                                <div class="row" >
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Service Category</label>
                                            <select class="form-control" id="service_type_id" name="service_type_id" v-model="searchData.service_type_id">
                                            </select>
                                            <has-error :form="searchData" field="service_type_id"></has-error>
                                        </div>
                                    </div> 
                                </div>
                                <div class="row">
                                    <div class="col-sm-12">
                                        <div class="form-group">
                                            <label>Sub Category</label>
                                            <select class="form-control" id="category_id" name="category_id" v-model="searchData.category_id" required>
                                                <option value="">--Select Sub Category--</option>
                                                <option v-for="category in categories" :key="category.id" :value="category.id">{{ category.name }}</option>
                                            </select>
                                            <has-error :form="searchData" field="category_id"></has-error>
                                        </div>
                                    </div> 
                                    <button @click.prevent="editMode ? updateProvider() : createProvider()" type="submit" name="submit" class="submit btn btn-primary">Submit</button>
                                </div>
                            </form>
                        </div>
                    </div>
                </div>
            </div>
            <div class="col-9">
                <div class="card">
                    <div class="card-body">
                        <div class="d-sm-flex align-items-center mb-2">
                            <h4 class="card-title mb-sm-0">Price Lists</h4>
                            <button @click="updatePlan()" class="text-dark btn btn-primary btn-sm ml-auto mb-3 mb-sm-0"> Save</button>
                        </div>
                        <div class="table-responsive border rounded p-1">
                            <vue-excel-editor v-model="jsondata">
                                <vue-excel-column field="user"   label="User ID"       type="string" width="80px" />
                                <vue-excel-column field="name"   label="Name"          type="string" width="150px" />
                                <vue-excel-column field="phone"  label="Contact"       type="string" width="130px" />
                                <vue-excel-column field="gender" label="Gender"        type="select" width="50px" :options="['F','M','U']" />
                                <vue-excel-column field="age"    label="Age"           type="number" width="70px" />
                                <vue-excel-column field="birth"  label="Date Of Birth" type="date"   width="80px" />
                            </vue-excel-editor>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </section>
</template>
<script>
</script>