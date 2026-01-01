<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item"><router-link to="/emr/radiology/dashboard" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></router-link></li>
        <li class="nav-item"><router-link to="/emr/radiology/queues" class="nav-link"><i class="nav-icon fa fa-list"></i><p>Queue</p></router-link></li>
        <li class="nav-item"><router-link to="/emr/radiology/insurance" class="nav-link"><i class="nav-icon fas fa-users"></i><p>HMO Desk</p></router-link></li>  
        <li class="nav-item"><router-link to="/emr/radiology/point_of_sale" class="nav-link"><i class="nav-icon fas fa-cash-register"></i><p>Create Request</p></router-link></li>  
        <li class="nav-item"><router-link to="/emr/radiology/patient_search" class="nav-link"><i class="nav-icon fas fa-search"></i><p>Search for Patient</p></router-link></li>  
        <li class="nav-item"><router-link to="/emr/radiology/linked_images" class="nav-link"><i class="nav-icon fas fa-search"></i><p>See Image</p></router-link></li>  
        <li class="nav-item"><router-link to="/emr/radiology/completed" class="nav-link"><i class="nav-icon fas fa-search"></i><p>See Report</p></router-link></li>  
        <li class="nav-item"><router-link to="/emr/radiology/referred_out" class="nav-link"><i class="nav-icon fas fa-outdent"></i><p>Referred Out</p></router-link></li>  
        <li class="nav-item"><router-link to="/emr/radiology/referred_in" class="nav-link"><i class="nav-icon fas fa-indent"></i><p>Referred In</p></router-link></li>  
        <li class="nav-item"><router-link to="/emr/radiology/store_management" class="nav-link"><i class="nav-icon fas fa-boxes"></i><p>Store Manager</p></router-link></li>  
        <li class="nav-item"><router-link to="/emr/radiology/settings" class="nav-link"><i class="nav-icon fas fa-cogs"></i><p>Settings<i class="fas fa-angle-left right"></i></p></router-link>
            <ul class="nav nav-treeview">
                <li class="nav-item"><router-link to="/emr/radiology/settings/services" class="nav-link"><i class="nav-icon fas fa-vials"></i><p>Services</p></router-link></li>      
                <li class="nav-item"><router-link to="/emr/radiology/settings/bottles" class="nav-link"><i class="nav-icon fas fa-flask"></i><p>Bottles</p></router-link></li>      
                <li class="nav-item"><router-link to="/emr/radiology/settings/panel_investigations" class="nav-link"><i class="nav-icon fas fa-object-group"></i><p>Panel Investigations</p></router-link></li>  
                <li class="nav-item"><router-link to="/emr/radiology/settings/result_templates" class="nav-link"><i class="nav-icon fas fa-file-pdf"></i><p>Result Templates</p></router-link></li>
            </ul>
        </li> 
    </ul>
</nav>