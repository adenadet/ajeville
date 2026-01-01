<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">  
        <li class="nav-item"><a href="/archives/dashboard" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>
        <li class="nav-item"><a href="/archives/categories" class="nav-link"><i class="nav-icon fas fa-boxes"></i><p>Categories</p></a></li>
        <li class="nav-item"><a href="/archives/documents" class="nav-link"><i class="nav-icon fas fa-copy"></i><p>Documents</p></a></li>
        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fa fa-shopping-cart"></i><p>Procurements<i class="fas fa-angle-left right"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><router-link to="/archives/purchase_orders" class="nav-link"><i class="nav-icon fas fa-circle"></i><p>Purchase Orders</p></router-link></li>
                <li class="nav-item"><router-link to="/archives/work_orders" class="nav-link"><i class="nav-icon fas fa-circle"></i><p>Work Orders</p></router-link></li>
                <li class="nav-item"><router-link to="/archives/purchase_requests" class="nav-link"><i class="nav-icon fas fa-circle"></i><p>Purchase Requests</p></router-link></li>
            </ul>
        </li>         
        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fa fa-cogs"></i><p>Settings<i class="fas fa-angle-left right"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><router-link to="/archives/settings" class="nav-link"><i class="nav-icon far fa-circle"></i><p>Settings</p></router-link></li>
            </ul>
        </li>
    </ul>
</nav>