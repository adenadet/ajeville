<template>
<section class="overlay-wrapper p-0">
    <div class="overlay dark" v-if="loading"><i class="fas fa-3x fa-sync-alt fa-spin"></i><div class="text-bold pt-2">Loading...</div></div>
    <div class="row">
        <div class="col-lg-3 col-6">
            <div class="small-box bg-info">
                <div class="inner">
                    <h3>{{ branches }}2</h3><p>Branches</p>
                </div>
                <div class="icon">
                    <i class="fa fa-building"></i>
                </div>
                <router-link to="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></router-link>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-success">
                <div class="inner">
                    <h3>{{ stores }} 2</h3><p>Stores</p>
                </div>
                <div class="icon"><i class="fa fa-warehouse"></i></div>
                <router-link to="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></router-link>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner"><h3>44</h3><p>Pricelists</p></div>
                <div class="icon"><i class="fa fa-cash-register"></i></div>
                <router-link to="/operations/price_lists" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></router-link>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>65</h3>

                    <p>Modules</p>
                </div>
                <div class="icon">
                    <i class="fa fa-users"></i>
                </div>
                <router-link to="/operations/modules" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></router-link>
            </div>
        </div>
                <div class="col-lg-3 col-6">
            <div class="small-box bg-purple">
                <div class="inner">
                    <h3>{{ branches }}2</h3><p>Departments</p>
                </div>
                <div class="icon">
                    <i class="fa fa-building"></i>
                </div>
                <router-link to="/operations/departments" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></router-link>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-default">
                <div class="inner">
                    <h3>{{ stores }} 2</h3><p>User Roles</p>
                </div>
                <div class="icon"><i class="fa fa-user-cog"></i></div>
                <router-link to="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></router-link>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-warning">
                <div class="inner"><h3>44</h3><p>Pricelists</p></div>
                <div class="icon"><i class="fa fa-cash-register"></i></div>
                <router-link to="/operations/price_lists" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></router-link>
            </div>
        </div>
        <div class="col-lg-3 col-6">
            <div class="small-box bg-primary">
                <div class="inner">
                    <h3>65</h3>

                    <p>Users</p>
                </div>
                <div class="icon">
                    <i class="fa fa-users"></i>
                </div>
                <router-link to="#" class="small-box-footer">More info <i class="fas fa-arrow-circle-right"></i></router-link>
            </div>
        </div>
    </div>
    <div class="row">
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header border-0">
                    <div class="d-flex justify-content-between">
                        <h3 class="card-title">Online Store Visitors</h3>
                        <router-link to="javascript:void(0);">View Report</router-link>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex">
                        <p class="d-flex flex-column">
                            <span class="text-bold text-lg">820</span>
                            <span>Visitors Over Time</span>
                        </p>
                        <p class="ml-auto d-flex flex-column text-right">
                            <span class="text-success"><i class="fas fa-arrow-up"></i> 12.5%</span>
                            <span class="text-muted">Since last week</span>
                        </p>
                    </div>
                    
                    <div class="position-relative mb-4">
                        <canvas id="visitors-chart" height="200"></canvas>
                    </div>

                    <div class="d-flex flex-row justify-content-end">
                        <span class="mr-2">
                            <i class="fas fa-square text-primary"></i> This Week
                        </span>

                        <span>
                            <i class="fas fa-square text-gray"></i> Last Week
                        </span>
                    </div>
                </div>
            </div>
            <div class="card">
                <div class="card-header border-0">
                    <h3 class="card-title">Products</h3>
                    <div class="card-tools">
                        <router-link to="#" class="btn btn-tool btn-sm">
                            <i class="fas fa-download"></i>
                        </router-link>
                        <router-link to="#" class="btn btn-tool btn-sm">
                            <i class="fas fa-bars"></i>
                        </router-link>
                    </div>
                </div>
                <div class="card-body table-responsive p-0">
                    <table class="table table-striped table-valign-middle">
                        <thead>
                            <tr>
                                <th>Product</th>
                                <th>Price</th>
                                <th>Sales</th>
                                <th>More</th>
                            </tr>
                        </thead>
                        <tbody>
                            <tr>
                                <td><img :src="'dist/img/default-150x150.png'" alt="Product 1" class="img-circle img-size-32 mr-2">Some Product</td>
                                <td>$13 USD</td>
                                <td><small class="text-success mr-1"><i class="fas fa-arrow-up"></i>12%</small>12,000 Sold</td>
                                <td><router-link to="#" class="text-muted"><i class="fas fa-search"></i></router-link></td>
                            </tr>
                            <tr>
                                <td><img :src="'dist/img/default-150x150.png'" alt="Product 1" class="img-circle img-size-32 mr-2">Another Product</td>
                                <td>$29 USD</td>
                                <td><small class="text-warning mr-1"><i class="fas fa-arrow-down"></i>0.5%</small>123,234 Sold</td>
                                <td><router-link to="#" class="text-muted"><i class="fas fa-search"></i></router-link></td>
                            </tr>
                            <tr>
                                <td><img :src="'dist/img/default-150x150.png'" alt="Product 1" class="img-circle img-size-32 mr-2">Amazing Product</td>
                                <td>$1,230 USD</td>
                                <td><small class="text-danger mr-1"><i class="fas fa-arrow-down"></i>3%</small>198 Sold</td>
                                <td><router-link to="#" class="text-muted"><i class="fas fa-search"></i></router-link></td>
                            </tr>
                            <tr>
                                <td><img :src="'dist/img/default-150x150.png'" alt="Product 1" class="img-circle img-size-32 mr-2">Perfect Item<span class="badge bg-danger">NEW</span></td>
                                <td>$199 USD</td>
                                <td><small class="text-success mr-1"><i class="fas fa-arrow-up"></i>63%</small>87 Sold</td>
                                <td><router-link to="#" class="text-muted"><i class="fas fa-search"></i></router-link></td>
                            </tr>
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
        <div class="col-lg-6">
            <div class="card">
                <div class="card-header border-0">
                    <div class="d-flex justify-content-between">
                        <h3 class="card-title">Sales</h3>
                        <router-link to="javascript:void(0);">View Report</router-link>
                    </div>
                </div>
                <div class="card-body">
                    <div class="d-flex">
                        <p class="d-flex flex-column">
                            <span class="text-bold text-lg">$18,230.00</span>
                            <span>Sales Over Time</span>
                        </p>
                        <p class="ml-auto d-flex flex-column text-right">
                            <span class="text-success">
                            <i class="fas fa-arrow-up"></i> 33.1%
                            </span>
                            <span class="text-muted">Since last month</span>
                        </p>
                    </div>
                    <div class="position-relative mb-4">
                        <canvas id="sales-chart" height="200"></canvas>
                    </div>

                    <div class="d-flex flex-row justify-content-end">
                        <span class="mr-2">
                        <i class="fas fa-square text-primary"></i> This year
                        </span>

                        <span>
                        <i class="fas fa-square text-gray"></i> Last year
                        </span>
                    </div>
                    </div>
                </div>
                <!-- /.card -->

                <div class="card">
                    <div class="card-header border-0">
                        <h3 class="card-title">Online Store Overview</h3>
                        <div class="card-tools">
                            <router-link to="#" class="btn btn-sm btn-tool">
                            <i class="fas fa-download"></i>
                            </router-link>
                            <router-link to="#" class="btn btn-sm btn-tool">
                            <i class="fas fa-bars"></i>
                            </router-link>
                        </div>
                    </div>
                    <div class="card-body">
                    <div class="d-flex justify-content-between align-items-center border-bottom mb-3">
                        <p class="text-success text-xl">
                        <i class="ion ion-ios-refresh-empty"></i>
                        </p>
                        <p class="d-flex flex-column text-right">
                        <span class="font-weight-bold">
                            <i class="ion ion-android-arrow-up text-success"></i> 12%
                        </span>
                        <span class="text-muted">CONVERSION RATE</span>
                        </p>
                    </div>
                    <!-- /.d-flex -->
                    <div class="d-flex justify-content-between align-items-center border-bottom mb-3">
                        <p class="text-warning text-xl">
                        <i class="ion ion-ios-cart-outline"></i>
                        </p>
                        <p class="d-flex flex-column text-right">
                        <span class="font-weight-bold">
                            <i class="ion ion-android-arrow-up text-warning"></i> 0.8%
                        </span>
                        <span class="text-muted">SALES RATE</span>
                        </p>
                    </div>
                    <!-- /.d-flex -->
                    <div class="d-flex justify-content-between align-items-center mb-0">
                        <p class="text-danger text-xl">
                        <i class="ion ion-ios-people-outline"></i>
                        </p>
                        <p class="d-flex flex-column text-right">
                        <span class="font-weight-bold">
                            <i class="ion ion-android-arrow-down text-danger"></i> 1%
                        </span>
                        <span class="text-muted">REGISTRATION RATE</span>
                        </p>
                    </div>
                </div>
            </div>
        </div>
    </div>
</section>

</template>