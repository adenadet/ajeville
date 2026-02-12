<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">  
        <li class="nav-item"><a href="/emr/anesthesist/dashboard" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>
        <li class="nav-item"><router-link to="/emr/anesthesist/cases" class="nav-link"><i class="nav-icon far fa-clipboard"></i><p>Cases</p></router-link></li>
        <li class="nav-item"><router-link to="/emr/anesthesist/reports" class="nav-link"><i class="nav-icon far fa-copy"></i><p>Reports</p></router-link></li>
        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fa fa-cogs"></i><p>Settings<i class="fas fa-angle-left right"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><router-link to="/emr/anesthesist/settings/drugs" class="nav-link"><i class="nav-icon far fa-circle"></i><p>Drugs</p></router-link></li>
            </ul>
        </li>
    </ul>
</nav>
