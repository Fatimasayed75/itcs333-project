<?php
session_start();

$signInErrMsg = $registerErrMsg = "";
$isSignUpError = false;

if (isset($_SESSION['signin-error'])) {
	$signInErrMsg = $_SESSION['signin-error'];
	unset($_SESSION['signin-error']);
}

if (isset($_SESSION['register-error'])) {
	$registerErrMsg = $_SESSION['register-error'];
	$isSignUpError = true;
	unset($_SESSION['register-error']);
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
	<!-- Apply the "right-panel-active" class conditionally based on $isSignUpError -->
	<div class="container <?php echo $isSignUpError ? 'right-panel-active' : ''; ?>" id="container">
		<!-- SIGN UP FORM -->
		<div class="form-container sign-up-container">
			<form action="../../../backend/server/handleRegister.php" method="post">
				<h1>Create Account</h1>
				<div id="names-div">
					<input type="text" placeholder="First Name" name="firstName" required
						value="<?php echo isset($_SESSION['old-input-reg']['fname']) ? htmlspecialchars($_SESSION['old-input-reg']['fname']) : ''; ?>" />
					<input type="text" placeholder="Last Name" name="lastName" required
						value="<?php echo isset($_SESSION['old-input-reg']['lname']) ? htmlspecialchars($_SESSION['old-input-reg']['lname']) : ''; ?>" />
				</div>
				<input type="email" placeholder="Email" name="email" required
					value="<?php echo isset($_SESSION['old-input-reg']['email']) ? htmlspecialchars($_SESSION['old-input-reg']['email']) : ''; ?>" />
				<input type="password" placeholder="Password" name="password" required />

				<!-- Display register error message only if it exists -->
				<?php if (!empty($registerErrMsg)): ?>
					<p class="register-error error"><?php echo $registerErrMsg; ?></p>
				<?php endif; ?>

				<button type="submit" name="sign-up-btn">Sign Up</button>
				<a href="#" id="haveAccount">Already have an account?</a>
			</form>
		</div>

		<!-- SIGN IN FORM -->
		<div class="form-container sign-in-container">
			<form action="../../../backend/server/handleLogin.php" method="post">
				<h1>Sign in</h1>
				<input type="email" placeholder="Email" name="email" required
					value="<?php echo isset($_SESSION['old-input-signin']['email']) ? htmlspecialchars($_SESSION['old-input-signin']['email']) : ''; ?>">
				<input type="password" placeholder="Password" name="password" required />

				<!-- Display sign-in error message only if it exists -->
				<?php if (!empty($signInErrMsg)): ?>
					<p class="signin-error error"><?php echo $signInErrMsg; ?></p>
				<?php endif; ?>

				<button type="submit" name="sign-in-btn">Sign In</button>
				<div id="signInLinks">
					<a href="#" id="signUpLink">Sign up?</a>
					<a href="../../../backend/server/handleGuests.php" id="enterGuest">Enter as a guest?</a>
				</div>

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

<?php
// Clear the session data after displaying the form
unset($_SESSION['old-input-reg']);
unset($_SESSION['old-input-signin']);
?>