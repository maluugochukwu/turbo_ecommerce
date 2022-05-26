<?php
session_start();
if(isset($_SESSION['merchant_sess_id']))
{
    header('Location: home.php');
}
require_once('vendor/autoload.php');
$dotenv = Dotenv\Dotenv::createImmutable("./");
$dotenv->load();
$baseurl = "";
?>
<!DOCTYPE html>
<html lang="en">


<!-- Mirrored from appstack.bootlab.io/pages-sign-in.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 26 Jul 2019 15:57:14 GMT -->
<!-- Added by HTTrack --><meta http-equiv="content-type" content="text/html;charset=UTF-8" /><!-- /Added by HTTrack -->
<head>
	<meta charset="utf-8">
	<meta http-equiv="X-UA-Compatible" content="IE=edge">
	<meta name="viewport" content="width=device-width, initial-scale=1, shrink-to-fit=no">
	<meta name="description" content="Admin Portal">
	<meta name="author" content="Access Solutions Ltd">

<!--	<title>Vuvaa Shop</title>-->
	<title><?php echo $_ENV['APPLICATION_NAME']; ?></title>
	<link rel="stylesheet" href="<?php echo $baseurl; ?>css/parsley.css">
	<link rel="preconnect" href="http://fonts.gstatic.com/" crossorigin>
	<link rel="icon" href="<?php echo $baseurl; ?>img/favicon.ico" sizes="32x32" />
	<!-- PICK ONE OF THE STYLES BELOW -->
	<link href="<?php echo $baseurl; ?>css/classic.css" rel="stylesheet">
	<!-- <link href="css/corporate.css" rel="stylesheet"> -->
	<!-- <link href="css/modern.css" rel="stylesheet"> -->

	<!-- BEGIN SETTINGS -->
	<!-- You can remove this after picking a style -->
	<style>
		body {
			opacity: 0;
		}
	</style>
	<script src="<?php echo $baseurl; ?>js/settings.js"></script>
	<!-- END SETTINGS -->
<!-- Global site tag (gtag.js) - Google Analytics -->
</head>

<body style="background: #639de2">
	<main class="main d-flex w-100">
		<div class="container d-flex flex-column">
			<div class="row">
				<div class="col-sm-12 col-md-8 col-lg-6 mx-auto d-table h-100 mt-5">
					<div class="d-table-cell align-middle">

						<div class="text-center mt-4">
							<h1 class="h2" style="color:#000">Welcome to <?php echo $_ENV['APPLICATION_NAME']; ?></h1>
							<p class="lead" style="color:#fff">
								Sign in to your account
							</p>
						</div>

						<div class="card">
							<div class="card-body">
								<div class="m-sm-4">
									<div class="text-center">
										<img src="<?php echo $baseurl; ?>img/572.png" alt="Chris Wood" class="img-fluid" width="132" height="132" />
									</div>
									<form id="form1" onsubmit="return false">
										<input type="hidden" name="op" value="Users.login">
										<div class="form-group">
											<label>Username</label>
											<input class="form-control form-control-lg" type="text"  name="username" required placeholder="Enter your username" />
										</div>
										<div class="form-group">
											<label>Password</label>
											<input class="form-control form-control-lg"  type="password" name="password" required placeholder="Enter your password" />
											<small>
             <a href="forgot_password.php">Forgot password?</a> 
          </small>
										</div>
										<div>

											<!-- <div class="custom-control custom-checkbox align-items-center">
												<input type="checkbox" class="custom-control-input" value="remember-me" name="remember-me" checked>
												<label class="custom-control-label text-small">Remember me next time</label>
											</div> -->
										</div>
										<div id="server_mssg"></div>
										<div class="text-center mt-3">
											<button onclick="sendLogin('form1')" id="button" class="btn btn-lg btn-info btn-block">Sign in</button>
										</div>
									</form>
								</div>
							</div>
						</div>

					</div>
				</div>
			</div>
		</div>
	</main>

	<!-- <script src="js/app.js"></script> -->
	<script src="<?php echo $baseurl; ?>js/jquery.min.js"></script>
	<script src="<?php echo $baseurl; ?>js/jquery.blockUI.js"></script>
	<script src="<?php echo $baseurl; ?>js/parsely.js"></script>
	
	<script src="<?php echo $baseurl; ?>js/sweet_alerts.js"></script>
	<script src="<?php echo $baseurl; ?>js/main.js"></script>
	<script>
		function sendLogin(id)
		{
			var forms = $('#'+id);
			forms.parsley().validate();
			if(forms.parsley().isValid())
			{
                $.blockUI();
				var data = $("#"+id).serialize();
				$.post("utilities_default.php",data,function(res)
				{
                    $.unblockUI();
					var response = JSON.parse(res);
					if(response.response_code == 0)
					{
                        $("#button").attr("disabled",true);
                        
                        $("#server_mssg").html(`<i class="fa fa-user-check" style="color:green"></i> `+response.response_message);
						setTimeout(() => {
							window.location = 'home.php#page';
						}, 2000);
					}
					else
					{
						$("#server_mssg").html(`<i class="fa fa-user-times" style="color:red"></i> `+response.response_message);
					}
				});
			}
		}
	</script>
</body>


<!-- Mirrored from appstack.bootlab.io/pages-sign-in.html by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 26 Jul 2019 15:57:14 GMT -->
</html>