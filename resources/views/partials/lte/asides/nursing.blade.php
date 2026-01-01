
<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">    
        <li class="nav-item"><router-link to="/emr/nursing/dashboard" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></router-link></li>
        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-list"></i><p>Queue<i class="fas fa-angle-left right"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><router-link to="/emr/nursing/queue/vitals" class="nav-link"><i class="nav-icon fa fa-file"></i><p>Vital Queue</p></router-link></li>
                <li class="nav-item"><router-link to="/emr/nursing/queue/admission" class="nav-link"><i class="nav-icon fa fa-procedures"></i><p>Admission Queue</p></router-link></li>
            </ul>
        </li><li class="nav-item"><router-link to="/emr/nursing/tasks" class="nav-link"><i class="nav-icon fas fa-bell"></i><p>Notifications</p></router-link></li>
        <li class="nav-item"><router-link to="/emr/nursing/daily_tasks" class="nav-link"><i class="nav-icon fa fa-clipboard"></i><p>My Daily Tasks</p></router-link></li>
        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-cogs"></i><p>Assessment Settings<i class="fas fa-angle-left right"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><router-link to="/emr/nursing/assessments" class="nav-link"><i class="nav-icon fa fa-file"></i><p>All Assessment </p></router-link></li>
                <li class="nav-item"><router-link to="/emr/nursing/assessments/types" class="nav-link"><i class="nav-icon fa fa-book"></i><p>Assessment Types</p></router-link></li>
            </ul>
        </li>
    </ul>
</nav>
