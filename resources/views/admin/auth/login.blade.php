<!DOCTYPE html>
<html lang="en">
<head>
	<title>Admin Panel Login</title>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1">
<!--===============================================================================================-->	
	<!-- Favicon -->
        <link rel="apple-touch-icon" sizes="180x180" href="{{asset('public/apple-touch-icon.png')}}">
        <link rel="icon" type="image/png" sizes="32x32" href="{{asset('public/favicon-32x32.png')}}">
        <link rel="icon" type="image/png" sizes="16x16" href="{{asset('public/favicon-16x16.png')}}">
        <link rel="manifest" href="{{asset('public/site.webmanifest')}}">
        <link rel="mask-icon" href="{{asset('public/safari-pinned-tab.svg')}}" color="#556b2f">
        <meta name="msapplication-TileColor" content="#ffffff">
        <meta name="theme-color" content="#ffffff">
    <!-- Favicon-->
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{asset('public/LoginPage/vendor/bootstrap/css/bootstrap.min.css')}}">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{asset('public/LoginPage/fonts/font-awesome-4.7.0/css/font-awesome.min.css')}}">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{asset('public/LoginPage/fonts/Linearicons-Free-v1.0.0/icon-font.min.css')}}">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{asset('public/LoginPage/vendor/animate/animate.css')}}">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="{{asset('public/LoginPage/vendor/css-hamburgers/hamburgers.min.css')}}">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{asset('public/LoginPage/vendor/animsition/css/animsition.min.css')}}">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{asset('public/LoginPage/vendor/select2/select2.min.css')}}">
<!--===============================================================================================-->	
	<link rel="stylesheet" type="text/css" href="{{asset('public/LoginPage/vendor/daterangepicker/daterangepicker.css')}}">
<!--===============================================================================================-->
	<link rel="stylesheet" type="text/css" href="{{asset('public/LoginPage/css/util.css')}}">
	<link rel="stylesheet" type="text/css" href="{{asset('public/LoginPage/css/main.css')}}">
<!--===============================================================================================-->

<style>
    .forget{
        color: #9fa19f;
        text-transform: uppercase;
    }
    .forget:hover{
        color: #000;
        text-transform: uppercase;
        text-decoration: underline;
    }
</style>
</head>
<body>
	
	<div class="limiter">
		<div class="container-login100" style="background-image: url('{{asset('public/LoginPage/images/Frame 2.jpg')}}');">
			<div class="wrap-login100 p-t-30 p-b-50">
				<span class="login100-form-title p-b-41">
					<img src="{{asset('public/images/logowhite.png')}}" width="100px">
				</span>
				<form class="login100-form validate-form p-b-33 p-t-5" method="POST" action="{{ route('admin.storelogin') }}">
                    @csrf
					<div class="wrap-input100 validate-input" data-validate = "Enter username">
						<input class="input100" type="text" name="email" placeholder="Email">
						<span class="focus-input100" data-placeholder="&#xe82a;"></span>
					</div>

					<div class="wrap-input100 validate-input" data-validate="Enter password">
						<input class="input100" type="password" name="password" placeholder="Password">
						<span class="focus-input100" data-placeholder="&#xe80f;"></span>
					</div>
					<div class="container-login100-form-btn m-t-32">
						<a href="{{route('admin.reset-form')}}" class="forget">Forget Password</a>
					</div>

					<div class="container-login100-form-btn m-t-32">
						<button class="login100-form-btn">
							Login
						</button>
					</div>
                    <div class="text-center mb-3">
                        <script type="text/javascript">
                        window.setTimeout(
                            "document.getElementById('popmessage').style.display='none';",
                            6000);
                        </script>
                        @if(session()->has('error'))
                        <div id="popmessage" class="text-danger form-control-feedback" style="color:red;">
                            {{ session()->get('error') }}<br />
                        </div>
                        @endif
                        @if(session()->has('success'))
                        <div id="popmessage" class="text-success form-control-feedback">
                            {{ session()->get('success') }}
                        </div>
                        @endif
                    </div>

				</form>
			</div>
		</div>
	</div>
	

	<div id="dropDownSelect1"></div>
	
<!--===============================================================================================-->
	<script src="{{asset('public/LoginPage/vendor/jquery/jquery-3.2.1.min.js')}}"></script>
<!--===============================================================================================-->
	<script src="{{asset('public/LoginPage/vendor/animsition/js/animsition.min.js')}}"></script>
<!--===============================================================================================-->
	<script src="{{asset('public/LoginPage/vendor/bootstrap/js/popper.js')}}"></script>
	<script src="{{asset('public/LoginPage/vendor/bootstrap/js/bootstrap.min.js')}}"></script>
<!--===============================================================================================-->
	<script src="{{asset('public/LoginPage/vendor/select2/select2.min.js')}}"></script>
<!--===============================================================================================-->
	<script src="{{asset('public/LoginPage/vendor/daterangepicker/moment.min.js')}}"></script>
	<script src="{{asset('public/LoginPage/vendor/daterangepicker/daterangepicker.js')}}"></script>
<!--===============================================================================================-->
	<script src="{{asset('public/LoginPage/vendor/countdowntime/countdowntime.js')}}"></script>
<!--===============================================================================================-->
	<script src="{{asset('public/LoginPage/js/main.js')}}"></script>

</body>
</html>