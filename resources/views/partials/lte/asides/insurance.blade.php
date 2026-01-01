<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item"><router-link to="/emr/insurance/dashboard" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></router-link></li>
        <li class="nav-item">
            <a href="#" class="nav-link">
                <i class="nav-icon fas fa-list"></i><p>Queue<i class="fas fa-angle-left right"></i>
                </p>
            </a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><router-link to="/emr/insurance/queue/authorizations" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Awaiting Auth. Code</p></router-link></li>
                <li class="nav-item"><router-link to="/emr/insurance/queue/uncovered" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Uncovered Transactions</p></router-link></li>
                <li class="nav-item"><router-link to="/emr/insurance/queue/co-paid" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Co-pay Transactions</p></router-link></li>
            </ul>
        </li>
        <li class="nav-item"><router-link to="/emr/insurance/claims" class="nav-link"><i class="nav-icon fas fa-desktop"></i><p>Clams Form</p></router-link></li>
        <li class="nav-item"><router-link to="/emr/insurance/patients/search" class="nav-link"><i class="nav-icon fas fa-search"></i><p>Search for Patient</p></router-link></li>  
        <li class="nav-item"><router-link to="/emr/insurance/providers" class="nav-link"><i class="nav-icon fas fa-university"></i><p>Providers</p></router-link></li>  
        <li class="nav-item"><router-link to="/emr/insurance/uncovered" class="nav-link"><i class="nav-icon fas fa-receipt"></i><p>Uncovered Transactions</p></router-link></li>   
    </ul>
</nav>