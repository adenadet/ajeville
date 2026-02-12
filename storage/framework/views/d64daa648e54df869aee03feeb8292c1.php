<nav class="mt-2">
    <ul class="nav nav-pills nav-sidebar flex-column" data-widget="treeview" role="menu" data-accordion="false">  
        <li class="nav-item"><a href="/emr/admission/dashboard" class="nav-link"><i class="nav-icon fas fa-tachometer-alt"></i><p>Dashboard</p></a></li>
        <li class="nav-item"><router-link to="/emr/admission/requests" class="nav-link"><i class="nav-icon far fa-copy"></i><p>Requests</p></router-link></li>
        <li class="nav-item"><a href="#" class="nav-link"><i class="nav-icon fa fa-cogs"></i><p>Settings<i class="fas fa-angle-left right"></i></p></a>
            <ul class="nav nav-treeview">
                <li class="nav-item"><router-link to="/emr/admission/categories" class="nav-link"><i class="nav-icon far fa-circle"></i><p>Categories</p></router-link></li>
                <li class="nav-item"><router-link to="/emr/admission/room_types" class="nav-link"><i class="nav-icon far fa-clipboard"></i><p>Room Types</p></router-link></li>
                <li class="nav-item"><router-link to="/emr/admission/services" class="nav-link"><i class="nav-icon fa fa-concierge-bell"></i><p>Services</p></router-link></li>
                <li class="nav-item"><router-link to="/emr/admission/wards" class="nav-link"><i class="nav-icon far fa-copy"></i><p>Wards</p></router-link></li>
            </ul>
        </li>
    </ul>
</nav><?php /**PATH C:\wamp64\www\laravel10\ajeville\resources\views/partials/lte/asides/admission.blade.php ENDPATH**/ ?>