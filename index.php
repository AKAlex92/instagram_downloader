<?php
session_start();
if(isset($_POST) && $_POST['url'] != "")
{
	$link = trim($_POST['url']);
	require_once 'insta.php';
	$insta = new Insta($link);
	$insta->start();
	$return = $insta->get_errors_info_warnings();
	$_SESSION['return'] = $return;
	if(count($return['errors']) > 0)
	{
		header("Location: index.php");
		die();
	}
}
$show_error = false;
if(isset($_SESSION['return']['errors']) && count($_SESSION['return']['errors']) > 0)
{
	$show_error = true;
	$errors_msg = implode("<br>", $_SESSION['return']['errors']);
	unset($_SESSION['return']['errors']);
}
?>
<!DOCTYPE html>
<!--[if lt IE 7]>      <html class="no-js lt-ie9 lt-ie8 lt-ie7"> <![endif]-->
<!--[if IE 7]>         <html class="no-js lt-ie9 lt-ie8"> <![endif]-->
<!--[if IE 8]>         <html class="no-js lt-ie9"> <![endif]-->
<!--[if gt IE 8]><!--> <html class="no-js"> <!--<![endif]-->
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<title>Instagram Download Picture Utility</title>
		<meta name="description" content="">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<link rel="stylesheet" href="">
		<link rel="stylesheet" href="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/css/bootstrap.min.css" integrity="sha384-Gn5384xqQ1aoWXA+058RXPxPg6fy4IWvTNh0E263XmFcJlSAwiGgFAW/dAiS6JXm" crossorigin="anonymous">
		
	</head>
	<body>
		<main role="main" class="container">
			<div class="starter-template">
				<h1>Instagram Download Picture Utility</h1>
				<p class="lead">This utility is useful to download instagram picture from a shared link.
				<br>
				Enter a valid instagram url link with the picture you wish to download and see the magic happens!</p>
				<form method="POST" action="index.php">
					<div class="form-group">
						<label for="url">Instagram Link:</label>
						<input type="text" name="url" id="url" class="form-control" placeholder="Instagram Link" value="https://www.instagram.com/p/B0nqmo_oatb/?igshid=13dek5xvzvlix">
						<small class="form-text text-muted">Example: https://www.instagram.com/p/Bz_RdN5iuwk/?utm_source=ig_web_button_share_sheet</small>
					</div>
					<input type="submit" class="btn btn-primary" value="Download">
				</form>
				<br>
				<div id="alert_error" class="alert alert-danger d-none" role="alert"></div>
			</div>
		</main>
		<!-- /.container -->
		<script src="" async defer></script>
		<script  src="https://code.jquery.com/jquery-3.4.1.min.js" integrity="sha256-CSXorXvZcTkaix6Yvo6HppcZGetbYMGWSFlBw8HfCJo=" crossorigin="anonymous"></script>
		<script src="https://maxcdn.bootstrapcdn.com/bootstrap/4.0.0/js/bootstrap.min.js" integrity="sha384-JZR6Spejh4U02d8jOt6vLEHfe/JQGiRRSQQxSfFWpi1MquVdAyjUar5+76PVCmYl" crossorigin="anonymous"></script>
		<?php
		if($show_error)
		{
			// script for showing and hide alert
		?>
			<script>
			$(document).ready(function () {
				$("#alert_error").show();
				$("#alert_error").removeClass('d-none');
				$("#alert_error").html('<?php echo $errors_msg; ?>');
				$("#alert_error").fadeTo(5000, 500).slideUp(500, function(){
					$("#alert_error").slideUp(500);
					$("#alert_error").html('');
				});
				
			});
			</script>
		<?php
		}
		?>
	</body>
</html>
