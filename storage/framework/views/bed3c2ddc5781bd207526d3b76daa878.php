<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item has-treeview"><a href="/inventory/dashboard" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>
        <?php if(Auth::user()->can('Store Manager') || Auth::user()->hasRole('Super Admin')): ?>
        <li class="nav-item"><a href="/inventory/stores" class="nav-link"><i class="nav-icon fa fa-warehouse"></i><p>Stores</p></a></li>
        <?php endif; ?>
        <li class="nav-item has-treeview">
            <a href="#" class="nav-link"><i class="nav-icon fas fa-house-user"></i><p>My Stores<i class="right fas fa-angle-left"></i></p></a>
            <ul class="nav nav-treeview">
            <?php $__currentLoopData = $user_stores; $__env->addLoop($__currentLoopData); foreach($__currentLoopData as $user_store): $__env->incrementLoopIndices(); $loop = $__env->getLastLoop(); ?>
                <li class="nav-item"><a href="/inventory/user_stores/<?php echo e($user_store->id); ?>" class="nav-link"><i class="fa fa-circle nav-icon"></i><p><?php echo e($user_store->name); ?></p></a></li>
            <?php endforeach; $__env->popLoop(); $loop = $__env->getLastLoop(); ?>
            </ul>
        </li>
        <li class="nav-item has-treeview">
            <a href="#" class="nav-link"><i class="nav-icon fas fa-cube"></i><p>Items <i class="right fas fa-angle-left"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><a href="/inventory/items" class="nav-link"><i class="fas fa-cubes nav-icon"></i><p>All Items</p></a></li>
                <li class="nav-item"><a href="/inventory/items_bulk" class="nav-link"><i class="fas fa-file-csv nav-icon"></i><p>Bulk Update</p></a></li>
                <li class="nav-item"><a href="/inventory/packages" class="nav-link"><i class="fas fa-box nav-icon"></i><p>Packages</p></a></li>
            </ul>
        </li>
        <!--li class="nav-item"><a href="/inventory/sales_orders" class="nav-link"><i class="nav-icon fas fa-cash-register"></i><p>Point of Sales</p></a></li>
        <li class="nav-item"><a href="/inventory/direct_purchases" class="nav-link"><i class="nav-icon fas fa-check-double"></i><p>Direct Purchases</p></a></li-->
        <li class="nav-item has-treeview">
            <a href="#" class="nav-link"><i class="nav-icon fas fa-clipboard-list"></i><p>Transfer Orders <i class="right fas fa-angle-left"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><a href="/inventory/transfer_orders/in" class="nav-link"><i class="fas fa-indent nav-icon"></i><p>Requests In</p></a></li>
                <li class="nav-item"><a href="/inventory/transfer_orders/out" class="nav-link"><i class="fas fa-outdent nav-icon"></i><p>Requests Out</p></a></li>
            </ul>
        </li>
        <?php if(Auth::user()->hasRole('Super Admin') || Auth::user()->can('inventory_management')): ?>
        <li class="nav-item has-treeview">
            <a href="#" class="nav-link"><i class="fa fa-cogs nav-icon"></i><p>Settings<i class="right fas fa-angle-left"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><a href="/inventory/settings/brands" class="nav-link"><i class="far fa-dot-circle nav-icon"></i><p>Brands</p></a></li>
                <li class="nav-item"><a href="/inventory/settings/categories" class="nav-link"><i class="far fa-dot-circle nav-icon"></i><p>Categories</p></a></li>
                <li class="nav-item"><a href="/inventory/settings/classifications" class="nav-link"><i class="far fa-dot-circle nav-icon"></i><p>Classifications</p></a></li>
                <li class="nav-item"><a href="/inventory/settings/item_types" class="nav-link"><i class="far fa-dot-circle nav-icon"></i><p>Item Types</p></a></li>
            </ul>
        </li>
        <?php endif; ?>
    </ul>
</nav><?php /**PATH C:\wamp64\www\laravel10\ajeville\resources\views/partials/lte/asides/inventory.blade.php ENDPATH**/ ?>