<?php if(!login()) {?>
	<?php login_message(null);?>
	<h1>Login</h1>
	<form method="POST" action="login">
		User:<br>
		<input type="text" name="user"/><br><br>
		Pass:<br>
		<input type="password" name="password"/><br><br>
		<input type="submit" name="submit" value="Login"/>
	</form>
<?php } else {header('location: admin');} ?>