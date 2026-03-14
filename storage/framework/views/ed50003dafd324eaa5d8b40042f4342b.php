<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">
        <li class="nav-item"><router-link to="/emr/laboratory/dashboard" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></router-link></li>
        <li class="nav-item"><router-link to="/emr/laboratory/queues" class="nav-link"><i class="nav-icon fa fa-list"></i><p>Queue</p></router-link></li>        
        <!--li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-calendar"></i><p>Uncollected Queue<i class="fas fa-angle-left right"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><router-link to="/emr/laboratory/queues/all" class="nav-link"><i class="nav-icon far fa-circle"></i><p>All</p></router-link></li>      
                <li class="nav-item"><router-link to="/emr/laboratory/queues/paid" class="nav-link"><i class="nav-icon far fa-circle"></i><p>Paid </p></router-link></li>      
                <li class="nav-item"><router-link to="/emr/laboratory/queues/pending" class="nav-link"><i class="nav-icon far fa-circle"></i><p>Pending </p></router-link></li>      
                <li class="nav-item"><router-link to="/emr/laboratory/queues/hmo" class="nav-link"><i class="nav-icon far fa-circle"></i><p>Awaiting Insurance</p></router-link></li>
            </ul>
        </li-->
        <li class="nav-item"><router-link to="/emr/laboratory/insurance" class="nav-link"><i class="nav-icon fas fa-users"></i><p>Insurance Desk</p></router-link></li>  
        <li class="nav-item"><router-link to="/emr/laboratory/point_of_sale" class="nav-link"><i class="nav-icon fas fa-cash-register"></i><p>Create Request</p></router-link></li>  
        <li class="nav-item"><router-link to="/emr/laboratory/specimens" class="nav-link"><i class="nav-icon fas fa-vials"></i><p>Specimen Management</p></router-link></li>  
        <!--li class="nav-item"><router-link to="/emr/laboratory/linked_images" class="nav-link"><i class="nav-icon fas fa-search"></i><p>See Image</p></router-link></li>  
        <li class="nav-item"><router-link to="/emr/laboratory/completed" class="nav-link"><i class="nav-icon fas fa-search"></i><p>See Report</p></router-link></li-->  
        <li class="nav-item"><router-link to="/emr/laboratory/referred_out" class="nav-link"><i class="nav-icon fas fa-outdent"></i><p>Referred Out</p></router-link></li>  
        <li class="nav-item"><router-link to="/emr/laboratory/referred_in" class="nav-link"><i class="nav-icon fas fa-indent"></i><p>Referred In</p></router-link></li>  
        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-cogs"></i><p>Settings<i class="fas fa-angle-left right"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><router-link to="/emr/laboratory/settings/analytes" class="nav-link"><i class="nav-icon fas fa-vials"></i><p>Analytes</p></router-link></li>      
                <li class="nav-item"><router-link to="/emr/laboratory/settings/bottles" class="nav-link"><i class="nav-icon fas fa-flask"></i><p>Bottles</p></router-link></li>      
                <li class="nav-item"><router-link to="/emr/laboratory/settings/services" class="nav-link"><i class="nav-icon fas fa-microscope"></i><p>Services</p></router-link></li>      
                <li class="nav-item"><router-link to="/emr/laboratory/settings/specimen_types" class="nav-link"><i class="nav-icon fas fa-vials"></i><p>Specimen Types</p></router-link></li>      
                <li class="nav-item"><router-link to="/emr/laboratory/settings/panel_investigations" class="nav-link"><i class="nav-icon fas fa-object-group"></i><p>Panel Investigations</p></router-link></li>  
                <li class="nav-item"><router-link to="/emr/laboratory/settings/result_templates" class="nav-link"><i class="nav-icon fas fa-file-pdf"></i><p>Result Templates</p></router-link></li>
            </ul>
        </li> 
    </ul>
</nav><?php /**PATH C:\wamp64\www\laravel10\ajeville\resources\views/partials/lte/asides/laboratory.blade.php ENDPATH**/ ?>