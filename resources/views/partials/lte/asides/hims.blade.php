<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item"><router-link to="/hims/dashboard" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></router-link></li>
        <li class="nav-item"><router-link to="/hims/patients" class="nav-link"><i class="nav-icon fas fa-users"></i><p>Patients</p></router-link></li>  
        <li class="nav-item"><router-link to="/hims/patients/search" class="nav-link"><i class="nav-icon fas fa-search"></i><p>Search for Patient</p></router-link></li>  
        <li class="nav-item"><router-link to="/hims/patients/new" class="nav-link"><i class="nav-icon fas fa-user-plus"></i><p>New Patient</p></router-link></li>  
        <li class="nav-item"><router-link to="/hims/visits" class="nav-link"><i class="nav-icon fas fa-calendar"></i><p>Visits</p></router-link></li>
        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-list"></i><p>Queues<i class="fas fa-angle-left right"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><router-link to="/hims/queues/mine" class="nav-link"><i class="far fa-circle nav-icon"></i><p>My Queue</p></router-link></li>
                <li class="nav-item"><router-link to="/hims/queues/vitals" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Nurse Queue</p></router-link></li>
                <li class="nav-item"><router-link to="/hims/queues/doctor" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Doctor Queue</p></router-link></li>
                <li class="nav-item"><router-link to="/hims/queues/laboratory" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Laboratory Queue</p></router-link></li>
                <li class="nav-item"><router-link to="/hims/queues/radiology" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Radiology Queue</p></router-link></li>
                <li class="nav-item"><router-link to="/hims/queues/admission" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Admission Queue</p></router-link></li>
            </ul>
        </li>
        <li class="nav-item"><router-link to="/hims/antenatal" class="nav-link"><i class="nav-icon fas fa-female"></i><p>Antenatal</p></router-link></li>
        <li class="nav-item"><router-link to="/hims/packages" class="nav-link"><i class="nav-icon fas fa-gifts"></i><p>Packages</p></router-link></li>  
        <li class="nav-item"><router-link to="/hims/triages" class="nav-link"><i class="nav-icon fas fa-search"></i><p>Triages</p></router-link></li>
        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-file-pdf"></i><p>Consent Forms<i class="fas fa-angle-left right"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><router-link to="/visits/consent_forms/new" class="nav-link"><i class="far fa-circle nav-icon"></i><p>New Consent</p></router-link></li>
                <li class="nav-item"><router-link to="/visits/consent_forms/all" class="nav-link"><i class="far fa-circle nav-icon"></i><p>Signed Consent Form</p></router-link></li>
            </ul>
        </li>
        <li class="nav-item"><router-link to="/hims/visits/new_bill" class="nav-link"><i class="nav-icon fas fa-file-invoice"></i><p>New Bill</p></router-link></li>   
    </ul>
</nav>