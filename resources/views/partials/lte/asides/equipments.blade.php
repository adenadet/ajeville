<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item"><router-link to="/equipments/dashboard" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></router-link></li>
        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fa fa-laptop-house"></i><p>Asset Management<i class="fas fa-angle-left right"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><router-link to="/equipments/assets/items" class="nav-link"><i class="nav-icon fas fa-table"></i><p>Items</p></router-link></li>
                <li class="nav-item"><router-link to="/equipments/assets/transfers" class="nav-link"><i class="nav-icon fa fa-indent"></i><p>Transfer Reports</p></router-link></li>
                <li class="nav-item"><router-link to="/equipments/assets/reports" class="nav-link"><i class="nav-icon fas fa-file-alt"></i><p>Reports</p></router-link></li>
            </ul>
        </li>
        <li class="nav-item"><router-link to="/equipments/maintenance" class="nav-link"><i class="nav-icon fas fa-tools mr-1"></i><p>Maintenance <i class="fas fa-angle-left right"></i></p></router-link>
            <ul class="nav nav-treeview">
                <li class="nav-item"><router-link to="/equipments/maintenance/pending" class="nav-link"><i class="nav-icon fas fa-tags"></i><p>All</p></router-link></li>
                <!--li class="nav-item"><router-link to="/equipments/maintenance/overdue" class="nav-link"><i class="nav-icon fas fa-tags"></i><p>Overdue Invoices</p></router-link></li-->
                <li class="nav-item"><router-link to="/equipments/maintenance/completed" class="nav-link"><i class="nav-icon fas fa-tags mr-1"></i><p>History/Reports</p></router-link></li>
            </ul>
        </li>
        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fa fa-calendar-alt"></i><p> Preventive Scheduling<i class="fas fa-angle-left right"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><router-link to="/equipments/preventive/calendar" class="nav-link"><i class="nav-icon fas fa-calendar"></i><p>Scheduled</p></router-link></li>
                <li class="nav-item"><router-link to="/equipments/preventive/reports" class="nav-link"><i class="nav-icon fas fa-copy"></i><p>Reports</p></router-link></li>
            </ul>
        </li>
        <li class="nav-item"><router-link to="/equipments/settings" class="nav-link"><i class="nav-icon fa fa-cogs"></i><p>Settings</p></router-link></li> 
    </ul>
</nav>