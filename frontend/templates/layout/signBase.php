<?php
session_start();

$errMsg = "";
if (isset($_SESSION['error'])) {
	$errMsg = $_SESSION['error'];
	// Unset the message after displaying it
	unset($_SESSION['error']);
}

?>

<!DOCTYPE html>
<html lang="en">

<head>
	<meta charset="UTF-8">
	<meta name="viewport" content="width=device-width, initial-scale=1.0">
	<title>IT College Room Booking System</title>
	<link rel="stylesheet" href="../../css/login.css">
	<link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/4.7.0/css/font-awesome.min.css">
	<script src="../../js/login.js" defer></script>
</head>

<body>
	<div class="container" id="container">
		<!-- SIGN UP FORM -->
		<div class="form-container sign-up-container">
			<form action="../../../backend/index.php" method="post">
				<h1>Create Account</h1>
				<!-- <div class="social-container">
					<a href="#" class="social"><i class="fa fa-facebook"></i></a>
					<a href="#" class="social"><i class="fa fa-google"></i></a>
					<a href="#" class="social"><i class="fa fa-linkedin"></i></a>
				</div> -->
				<span>or use your email for registration</span>
				<div id="names-div">
					<input type="text" placeholder="First Name" name="firstName" />
					<input type="text" placeholder="Last Name" name="lastName" />
				</div>
				<input type="email" placeholder="Email" name="email" />
				<input type="password" placeholder="Password" name="password" />
				<button type="submit" name="sign-up-btn">Sign Up</button>
				<p id="haveAccoount">Already have an account?</p>
			</form>
		</div>
		<!-- SIGN IN FORM -->
		<div class="form-container sign-in-container">
			<form action="../../../backend/index.php" method="post">

				<h1>Sign in</h1>
				<!-- <div class="social-container">
					<a href="#" class="social"><i class="fa fa-facebook"></i></a>
					<a href="#" class="social"><i class="fa fa-google"></i></a>
					<a href="#" class="social"><i class="fa fa-linkedin"></i></a>
				</div> -->
				<span>or use your account</span>
				<input type="email" placeholder="Email" name="email" />
				<input type="password" placeholder="Password" name="password" />

				<?php if ($errMsg): ?>
					<p class="error"><?php echo $errMsg; ?></p>
				<?php endif; ?>
				
				<button type="submit" name="sign-in-btn">Sign In</button>
				<a href="base.php" id="enterGuest">Enter as a guest?</a>
			</form>
		</div>
		<div class="overlay-container">
			<div class="overlay">
				<div class="overlay-panel overlay-left">
					<h1>Welcome Back!</h1>
					<p>To keep connected with us please login with your personal info</p>
					<button class="ghost" id="signIn">Sign In</button>
				</div>
				<div class="overlay-panel overlay-right">
					<h1>Hello, Friend!</h1>
					<p>Enter your personal details and start journey with us</p>
					<button class="ghost" id="signUp">Sign Up</button>
				</div>
			</div>
		</div>
	</div>
</body>

</html>