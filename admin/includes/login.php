<?php
	include '../includes/session.php';
		
	if(!empty($_REQUEST['user']) && !empty($_REQUEST['password'])) {
		
		$user = $_REQUEST['user'];
		$pass = $_REQUEST['password'];
		
		$user_file = '../../admin/users/' . $user . '.txt';
		$user_pass = @file_get_contents($user_file);
		
		if(file_exists($user_file)) {
			if($pass === $user_pass) {
				$_SESSION['user'] = $user;
				header('location: ../index.php');
			}
			else {
				echo <<<EOF
				<!DOCTYPE html>
				<html>
				<head>
					<title>Admin Panel</title>
					<link rel="stylesheet" type="text/css" href="../resources/style.css" />
				</head>
				<body>
				<div class="wrapper-outer">
					<div class="wrapper-inner">
						<p>Password and username not match!</p>
						<p>Login Form</p>
						<form method="POST" action="login.php">
							User:<br>
							<input type="text" name="user"/><br><br>
							Pass:<br>
							<input type="password" name="password"/><br><br>
							<input type="submit" name="submit" value="Login"/>
						</form>
					</div>
				</div>
				</body>
				</html>
EOF;
			}
		}
		else {
			echo <<<EOF
			<!DOCTYPE html>
			<html>
			<head>
				<title>Admin Panel</title>
				<link rel="stylesheet" type="text/css" href="../resources/style.css" />
			</head>
			<body>
			<div class="wrapper-outer">
				<div class="wrapper-inner">
					<p>Please create username.txt inside "admin/users" folder and put your password inside it.</p>
					<p>Login Form</p>
					<form method="POST" action="login.php">
						User:<br>
						<input type="text" name="user"/><br><br>
						Pass:<br>
						<input type="password" name="password"/><br><br>
						<input type="submit" name="submit" value="Login"/>
					</form>
				</div>
			</div>
			</body>
			</html>
EOF;
		}
	}
	else {
		header('location: ../index.php');
	}
?>