<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">    
        <li class="nav-item"><router-link to="/pharmacy/dashboard" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></router-link></li>
        <li class="nav-item"><router-link to="/pharmacy/pos" class="nav-link"><i class="nav-icon fa fa-cash-register"></i><p>POS</p></router-link></li>
        <li class="nav-item"><router-link to="/pharmacy/prescriptions" class="nav-link"><i class="nav-icon fas fa-list"></i><p>Prescriptions</p></router-link></li>
        <li class="nav-item"><router-link to="/pharmacy/fulfillments" class="nav-link"><i class="nav-icon fa fa-tasks"></i><p>Dispense Prescription</p></router-link></li>
        <li class="nav-item"><router-link to="/pharmacy/assessments/assigned" class="nav-link"><i class="nav-icon fas fa-file"></i><p>Assigned Assessment</p></router-link></li>
        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fas fa-cogs"></i><p>Settings<i class="fas fa-angle-left right"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><router-link to="/pharmacy/settings/drugs" class="nav-link"><i class="nav-icon fas fa-pills"></i><p>Drugs</p></router-link></li>
                <li class="nav-item"><router-link to="/pharmacy/settings/drug_items" class="nav-link"><i class="nav-icon fas fa-list"></i><p>Specific Drugs</p></router-link></li>
                <li class="nav-item"><router-link to="/pharmacy/settings/drug_forms" class="nav-link"><i class="nav-icon fa fa-shapes"></i><p>Drug Forms</p></router-link></li>
                <!--li class="nav-item"><router-link to="/pharmacy/batch_assign" class="nav-link"><i class="nav-icon fa fa-calendar"></i><p>Daily Shift Assign</p></router-link></li>
                <li class="nav-item"><router-link to="/pharmacy/shift_types" class="nav-link"><i class="nav-icon fa fa-calendar"></i><p>Shift Types</p></router-link></li-->
            </ul>
        </li>
    </ul>
</nav>