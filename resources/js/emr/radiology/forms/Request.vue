<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="row">
        <div class="col-md-6">                    
            <div class="card">
                <div class="card-header bg-dark">
                    <h3 class="card-title">New Radiology</h3>
                </div>
                <form class="card-body">
                    <alert-error :form="Form"></alert-error>
                    <div class="row">
                        <div class="col-md-6">
                            <div class="form-group">
                                <label>Radiology Service</label>
                                <model-list-select class="form-control" :list="services" v-model="investigation" option-value="id" option-text="name" />
                            </div>
                        </div>
                        <div class="col-md-6">
                            <label class="text-white">Add</label><br />
                            <button class="btn btn-success btn-sm" type="button" @click="addItem()" :disabled="investigation == ''">Add</button>
                        </div>
                    </div>
                    <div class="row">
                        <div class="col-sm-12">
                            <table class="table table-striped">
                                <thead>
                                    <tr>
                                        <th>Name </th>
                                        <th>Quantity </th>
                                        <th>Description</th>
                                        <th></th>
                                    </tr>
                                </thead>
                                <tbody>
                                    <tr v-for="(item, index) in RadiologyForm.investigations" :key="item.id">
                                        <td>{{ item.name}}</td>
                                        <td><input class="form-control" type="number" v-model=" RadiologyForm.investigations[index].quantity"/></td>
                                        <td><textarea class="form-control"  v-model=" RadiologyForm.investigations[index].description"></textarea></td>
                                        <td><button class="btn btn-xs btn-danger" type="button" @click="removeItem(index)"><i class="fa fa-trash"></i></button> </td>
                                    </tr>
                                </tbody>
                            </table>
                            <button @click.prevent="editMode ? updateLaboratory() : createLaboratory()" type="submit" name="submit" class="float-right mt-2 submit btn btn-primary btn-sm">Submit</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</section>
</template>