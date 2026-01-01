

<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <title>Saint Nicholas Hospital Learning Management System | Log in</title>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <link rel="stylesheet" href="{{asset('plugins/fontawesome-free/css/all.min.css')}}">
    <link rel="stylesheet" href="https://code.ionicframework.com/ionicons/2.0.1/css/ionicons.min.css')}}">
    <link rel="stylesheet" href="{{asset('plugins/icheck-bootstrap/icheck-bootstrap.min.css')}}">
    <link rel="stylesheet" href="{{asset('dist/css/adminlte.min.css')}}">
    <link href="https://fonts.googleapis.com/css?family=Source+Sans+Pro:300,400,400i,700" rel="stylesheet">
</head>
<body class="hold-transition login-page" style="background:url({{asset('/img/background/SNHFront.jpg')}}); height: 500px; background-position: center; background-repeat: no-repeat; background-size: cover;" >
<div class="login-box" style="background-color: rgba(200, 200, 200, 0.5);">
    <div class="login-logo">
        <a href="/">
            <img src="{{asset('img/background/SNH_logo.png')}}" height="60" width="auto" />
        </a>
    </div>
    <div class="card">
        <div class="card-body login-card-body">
            <p class="login-box-msg"><strong>For additional security kindly enter your First Name and Passport Number</strong></p>
            <form action="" method="get">
                <div class="input-group mb-3">
                    <input type="text" class="form-control @error('f') is-invalid @enderror" id="f" name="f" placeholder="First Name" value="{{old('f')}}" required>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-user"></span></div>
                    </div>
                    @error('f')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror 
                </div>
                <div class="input-group mb-3">
                    <input id="p" type="text" class="form-control @error('p') is-invalid @enderror" name="p" placeholder="Passport Number" required>
                    <div class="input-group-append">
                        <div class="input-group-text"><span class="fas fa-file"></span></div>
                    </div>
                    @error('p')
                    <span class="invalid-feedback" role="alert"><strong>{{ $message }}</strong></span>
                    @enderror
                </div>
                <div class="row">
                    <div class="col-5">
                    </div>
                    <div class="col-7">
                        <button type="submit" class="btn btn-primary btn-block">Get Certificate</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="{{asset('plugins/jquery/jquery.min.js')}}"></script>
<script src="{{asset('plugins/bootstrap/js/bootstrap.bundle.min.js')}}"></script>
<script src="{{asset('dist/js/adminlte.min.js')}}"></script>
</body>
</html>