<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item has-treeview"><a href="/procurement/dashboard" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>
        <li class="nav-item has-treeview">
            <a href="#" class="nav-link"><i class="nav-icon fas fa-cube"></i><p>Items <i class="right fas fa-angle-left"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><a href="/procurement/items" class="nav-link"><i class="fas fa-cubes nav-icon"></i><p>All Items</p></a></li>
            </ul>
        </li>
        <li class="nav-item"><a href="/procurement/vendors" class="nav-link"><i class="nav-icon fas fa-user-friends"></i><p>Vendors</p></a></li>
        <li class="nav-item has-treeview">
            <a href="#" class="nav-link"><i class="nav-icon fas fa-cash-register"></i><p>Purchase Orders <i class="right fas fa-angle-left"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><a href="/procurement/purchase_orders/create" class="nav-link"><i class="fas fa-clipboard-list nav-icon"></i><p>Create New Item PO</p></a></li>
                <li class="nav-item"><a href="/procurement/purchase_orders" class="nav-link"><i class="fas fa-copy nav-icon"></i><p>All</p></a></li>
            </ul>
        </li>
        <li class="nav-item has-treeview">
            <a href="#" class="nav-link"><i class="nav-icon fas fa-concierge-bell"></i><p>Work Orders <i class="right fas fa-angle-left"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><a href="/procurement/work_orders/create" class="nav-link"><i class="fas fa-clipboard-list nav-icon"></i><p>Create New WO</p></a></li>
                <li class="nav-item"><a href="/procurement/work_orders" class="nav-link"><i class="fas fa-copy nav-icon"></i><p>All</p></a></li>
            </ul>
        </li>
        @if(Auth::user()->hasRole('Super Admin') || Auth::user()->can('procurement_management'))
        <li class="nav-item has-treeview">
            <a href="#" class="nav-link"><i class="fa fa-cogs nav-icon"></i><p>Settings<i class="right fas fa-angle-left"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><a href="/procurement/settings/approval_matrices" class="nav-link"><i class="far fa-dot-circle nav-icon"></i><p>Approval Matrix</p></a></li>                
                <li class="nav-item"><a href="/procurement/settings/general" class="nav-link"><i class="far fa-dot-circle nav-icon"></i><p>General Settings</p></a></li>                
                <li class="nav-item"><a href="/procurement/settings/payment_terms" class="nav-link"><i class="far fa-dot-circle nav-icon"></i><p>Payment Terms</p></a></li>
                <li class="nav-item"><a href="/procurement/settings/vendor_categories" class="nav-link"><i class="far fa-dot-circle nav-icon"></i><p>Vendor Categories</p></a></li>
            </ul>
        </li>
        @endif
    </ul>
</nav>
