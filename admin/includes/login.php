<?php

	if(isset($_POST['submit'])) {

		$user = $_POST['user'];
		$user_file = 'users/' . $user . '.txt';
		$pass = $_POST['password'];
		$user_pass = @file_get_contents($user_file);
		
		if(file_exists($user_file)) {
			if($pass === $user_pass) {
				$_SESSION['user'] = $user;
				header('location: index.php');
			}
			else {
				echo 'Username and password not match!';
			}
		}
		else {
			echo 'Please create username.txt inside "admin/users" folder and put your password inside it.';
		}
	
	} 
?>
<p>Login Form</p>
<form method="POST">
	User:<br>
	<input type="text" name="user"/><br><br>
	Pass:<br>
	<input type="password" name="password"/><br><br>
	<input type="submit" name="submit" value="Login"/>
</form>