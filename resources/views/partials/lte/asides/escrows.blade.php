<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">  
        <li class="nav-item"><router-link to="/escrows/dashboard" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></router-link></li>
        <li class="nav-item"><router-link to="/escrows/transactions" class="nav-link"><i class="nav-icon fas fa-file-invoice"></i><p>Transactions</p></router-link></li>
        <li class="nav-item"><router-link to="/escrows/partners" class="nav-link"><i class="nav-icon fas fa-users"></i><p>Partners</p></router-link></li>
        <li class="nav-item"><router-link to="/escrows/products" class="nav-link"><i class="nav-icon fas fa-icons"></i><p>Products</p></router-link></li>
        <li class="nav-item"><router-link to="/escrows/disputes" class="nav-link"><i class="nav-icon fas fa-people-arrows"></i><p>Disputes</p></router-link></li>       
        <li class="nav-item"><router-link to="#" class="nav-link"><i class="nav-icon fa fa-cogs"></i><p>Settings<i class="fas fa-angle-left right"></i></p></router-link>
            <ul class="nav nav-treeview">
                <li class="nav-item"><router-link to="/escrows/admin/approval_matrices" class="nav-link"><i class="nav-icon far fa-circle"></i><p>Approval Matrices</p></router-link></li>
            </ul>
        </li>
    </ul>
</nav>
