<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item"><router-link to="/emr/front_office/dashboard" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></router-link></li>
        <li class="nav-item"><router-link to="/emr/front_office/patients" class="nav-link"><i class="nav-icon fas fa-user-injured"></i><p>Patients</p></router-link></li>  
        <li class="nav-item"><router-link to="/emr/front_office/appointments" class="nav-link"><i class="nav-icon fas fa-calendar-check"></i><p>Appointments</p></router-link></li>
        <li class="nav-item"><router-link to="/emr/front_office/visits" class="nav-link"><i class="nav-icon fas fa-calendar"></i><p>Visits</p></router-link></li>
        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-list"></i><p>Queues<i class="fas fa-angle-left right"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><router-link to="/emr/front_office/queues/mine" class="nav-link"><i class="far fa-circle nav-icon"></i><p>My Queue</p></router-link></li>
                <li class="nav-item"><router-link to="/emr/front_office/queues/vitals" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Nurse Queue</p></router-link></li>
                <li class="nav-item"><router-link to="/emr/front_office/queues/doctor" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Doctor Queue</p></router-link></li>
                <li class="nav-item"><router-link to="/emr/front_office/queues/laboratory" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Laboratory Queue</p></router-link></li>
                <li class="nav-item"><router-link to="/emr/front_office/queues/radiology" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Radiology Queue</p></router-link></li>
                <li class="nav-item"><router-link to="/emr/front_office/queues/admission" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Admission Queue</p></router-link></li>
            </ul>
        </li>
        <li class="nav-item"><router-link to="/emr/front_office/antenatals" class="nav-link"><i class="nav-icon fas fa-female"></i><p>Antenatal</p></router-link></li>
        <li class="nav-item"><router-link to="/emr/front_office/packages" class="nav-link"><i class="nav-icon fas fa-gifts"></i><p>Packages</p></router-link></li>  
        <li class="nav-item"><router-link to="/emr/front_office/consent_forms" class="nav-link"><i class="fas fa-file-pdf nav-icon"></i><p>Consent Forms</p></router-link></li>
        <li class="nav-item"><router-link to="/emr/front_office/visits/new_bill" class="nav-link"><i class="nav-icon fas fa-file-invoice"></i><p>New Bill</p></router-link></li>   
    </ul>
</nav><?php /**PATH C:\wamp64\www\laravel10\ajeville\resources\views/partials/lte/asides/front_office.blade.php ENDPATH**/ ?>