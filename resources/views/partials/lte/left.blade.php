<aside class="main-sidebar sidebar-primary bg-white">
    <a href="/home" class="brand-link bg-navy text-white">
        <img src="{{asset(config('app.logo'))}}" alt="{{config('app.name')}}" class="brand-image img-circle elevation-3 bg-white" style="opacity: 1">
        <span class="brand-text font-weight-light text-white">{{config('app.name_short')}}</span>
    </a>
    <div class="sidebar mb-5">
        @if($page_title == 'Approvals') @include('partials.lte.asides.approvals')
        @elseif($page_title == 'Archives') @include('partials.lte.asides.archives')
        @elseif($page_title == 'Chats') @include('partials.lte.asides.chat')
        @elseif($page_title == 'Consultation') @include('partials.lte.asides.consultation')
        @elseif($page_title == 'Cooperative') @include('partials.lte.asides.cooperative')
        @elseif($page_title == 'Cooperative Admin') @include('partials.lte.asides.cooperative_admin')
        @elseif($page_title == 'Customer Relations') @include('partials.lte.asides.crm')
        @elseif($page_title == 'Dashboard') @include('partials.lte.asides.dashboard')
        @elseif($page_title == 'Equipments') @include('partials.lte.asides.equipments')
        @elseif($page_title == 'Escrow Admin') @include('partials.lte.asides.escrow_admin')
        @elseif($page_title == 'Escrows') @include('partials.lte.asides.escrows')
        @elseif($page_title == 'Facility Management')@include('partials.lte.asides.facility')
        @elseif($page_title == 'Finance')@include('partials.lte.asides.finance')
        @elseif($page_title == 'Front Office')@include('partials.lte.asides.front_office')
        @elseif($page_title == 'Human Resources Admin')@include('partials.lte.asides.hr_admin')
        @elseif($page_title == 'Human Resources Management')@include('partials.lte.asides.hr')
        @elseif($page_title == 'Managed Care')@include('partials.lte.asides.insurance')
        @elseif($page_title == 'Inventory')@include('partials.lte.asides.inventory')
        @elseif($page_title == 'Laboratory')@include('partials.lte.asides.laboratory')
        @elseif($page_title == 'Learn Admin')@include('partials.lte.asides.learn_admin')
        @elseif($page_title == 'Learn Student' || $page_title == 'Learn Tutor')@include('partials.lte.asides.learn')
        @elseif($page_title == 'Laboratory')@include('partials.lte.asides.laboratory')
        @elseif($page_title == 'Loans')@include('partials.lte.asides.loans')
        @elseif($page_title == 'Loans Admin')@include('partials.lte.asides.loans_staff')
        @elseif($page_title == 'Nursing Care')@include('partials.lte.asides.nursing')
        @elseif($page_title == 'Operations')@include('partials.lte.asides.operations')
        @elseif($page_title == 'Policies')@include('partials.lte.asides.policies')
        @elseif($page_title == 'Policy | Reader')@include('partials.lte.asides.policies')
        @elseif($page_title == 'Procurement')@include('partials.lte.asides.procurement')
        @elseif($page_title == 'Radiology')@include('partials.lte.asides.radiology')
        @elseif($page_title == 'Sales Orders')@include('partials.lte.asides.sales')
        @else @include('partials.lte.asides.dashboard')
        @endif
    </div>
</aside>