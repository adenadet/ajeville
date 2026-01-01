<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">
        <meta name="csrf-token" content="{{ csrf_token() }}">

        <title>{{ config('app.name', 'Laravel') }}</title>
        <link rel="icon" type="image/png" href="{{asset(config('app.logo'))}}">
        <link rel="dns-prefetch" href="//fonts.gstatic.com">
        <link href="https://fonts.googleapis.com/css?family=Nunito" rel="stylesheet">
        <link rel="stylesheet" href="{{asset('lte/plugins/fontawesome-free/css/all.min.css')}}">
        <link rel="stylesheet" href="{{asset('lte/dist/css/adminlte.min.css')}}">
        <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
        @vite('resources/css/app.css')
    </head>
    <body>
        <div id="corner">
            @include('partials.lte.top')
            @include('partials.lte.left')
            <div class="content-wrapper">
                <div class="content-header">
                    <div class="container-fluid">
                        <div class="row mb-2">
                            <div class="col-sm-6">
                                <h1 class="m-0 text-dark">{{$page_title}}</h1>
                            </div>
                            <div class="col-sm-6">
                                <ol class="breadcrumb float-sm-right">
                                <li class="breadcrumb-item"><a href="/">Home</a></li>
                                <li class="breadcrumb-item active">{{$page_title}}</li>
                                </ol>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="content">
                    <div class="row">
                    @if (in_array($page_title,  ['Notice Board', 'E-Services | Administrator', 'E-Services | Front Admin', 'E-Services | Front Office', 'E-Services | Medical Officer', 'E-Services | Radiologist', 'Staff of the Month']))
                        @if( $page_title == 'Notice Board')
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-header bg-navy"><h3 class="card-title">Sub Menus</h3></div>
                                <div class="card-body p-0">
                                    <ul class="nav nav-pills flex-column">
                                        <li class="nav-item"><router-link to="/notices" class="nav-link"><i class="fa fa-file"></i> All Noticies</router-link></li>
                                        @if(Auth::user()->hasRole('Policy Admin') || Auth::user()->hasRole('Super Admin') || Auth::user()->can('policy_administer')) 
                                        <li class="nav-item">
                                            <router-link to="/notices/admin" class="nav-link"><i class="fas fa-cog"></i> Administrator</router-link>
                                        </li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @elseif( $page_title == 'Staff of the Month')
                        <div class="col-md-3">
                            <div class="card">
                                <div class="card-header bg-navy"><h3 class="card-title">Sub Menus</h3></div>
                                <div class="card-body p-0">
                                    <ul class="nav nav-pills flex-column">
                                        <li class="nav-item active"><router-link to="/staff_month/winners" class="nav-link"><i class="fa fa-book"></i> All Winners</router-link></li>
                                        <li class="nav-item"><router-link to="/staff_month/vote" class="nav-link"><i class="fas fa-vote-yea"></i> Vote for this Month</router-link></li>
                                        @if(Auth::user()->hasRole('Head of Department') || Auth::user()->hasRole('Super Admin') || Auth::user()->hasRole('Chief Consultant') || Auth::user()->hasRole('Head Nurse') || Auth::user()->hasRole('Practice Manager'))
                                        <li class="nav-item"><router-link to="/staff_month/nominate" class="nav-link"><i class="fas fa-user-check"></i> Nominate</router-link></li>
                                        @endif
                                        @if(Auth::user()->hasRole('Human Resource') || Auth::user()->hasRole('Super Admin'))
                                        <li class="nav-item"><router-link to="/staff_month/admin" class="nav-link"><i class="fas fa-poll"></i> Admin</router-link></li>
                                        @endif
                                    </ul>
                                </div>
                            </div>
                        </div>
                        @elseif (($page_title == 'E-Services | Administrator') || ($page_title == 'E-Services | Front Admin') || ($page_title == 'E-Services | Front Office') || ($page_title == 'E-Services | Medical Officer') || ($page_title == 'E-Services | Radiologist'))
                        <div class="col-md-3">
                            @if($page_title == 'E-Services | Front Office') @include('partials.eservices.front')
                            @elseif($page_title == 'E-Services | Medical Officer') @include('partials.eservices.doctor')
                            @elseif($page_title == 'E-Services | Radiologist') @include('partials.eservices.radiologist')
                            @elseif($page_title == 'E-Services | Front Admin') @include('partials.eservices.front_admin')
                            @elseif($page_title == 'E-Services | Administrator') @include('partials.eservices.admin')
                            @endif
                        </div>
                        @endif
                        <div class="col-md-9 mt-0">
                            <router-view></router-view>
                        </div>
                    @elseif ($page_title == 'Policy | Reader')
                    <div class="col-md-12 mt-0">
                        <div class="card">
                            <div class="card-header"><h3 class="card-title">{{$policy->name}}</h3></div>
                            <div class="card-body">
                                <iframe src="{{ asset($policy->file) }}" class="col-12" style="min-height: 1000px"></iframe>
                            </div>
                        </div>
                    </div>
                    @else
                    <div class="col-md-12">
                        <router-view></router-view>
                    </div>
                    @endif
                    </div>
                </div>
            </div>
            @include('partials.lte.footer')
        </div>
        @vite('resources/js/app.js')
        <script src="{{asset('lte/plugins/jquery/jquery.min.js')}}"></script>
        <script src="{{asset('lte/plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
        <script src="{{asset('lte/dist/js/adminlte.min.js')}}"></script>
    </body>
</html>