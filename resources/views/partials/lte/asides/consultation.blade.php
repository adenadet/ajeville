<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">  
        <li class="nav-item"><a href="/emr/consultations/dashboard" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>
        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fa fa-list"></i><p>Queues<i class="fas fa-angle-left right"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><router-link to="/emr/consultations/my_queue" class="nav-link"><i class="nav-icon fas fa-circle"></i><p>My Queue </p></router-link></li>  
                <li class="nav-item"><router-link to="/emr/consultations/department_queue" class="nav-link"><i class="nav-icon fas fa-circle"></i><p>Department Queue </p></router-link></li>  
                <li class="nav-item"><router-link to="/emr/consultations/doctor_queue" class="nav-link"><i class="nav-icon fas fa-circle"></i><p>Doctors' Queue </p></router-link></li>  
            </ul>
        </li>   
        <li class="nav-item"><router-link to="/emr/consultations/my_previous_consultations" class="nav-link"><i class="nav-icon fas fa-copy"></i><p>Previous Consultations</p></router-link></li>
        <li class="nav-item"><router-link to="/emr/consultations/request_templates" class="nav-link"><i class="nav-icon fas fa-tasks"></i><p>Request Templates</p></router-link></li>
                 
        @if(Auth::user()->hasRole('HR Admin') || Auth::user()->hasRole('Super Admin'))
        @endif
    </ul>
</nav>
