<?php if (isset($error)) { ?>
	<div class="error-message"><?php echo $error?></div>
 <?php } ?>
<?php if(!login()) {?>
	<h1>Login</h1>
	<form method="POST" action="login">
		User <span class="required">*</span> <br>
		<input type="text" class="<?php if (isset($username)) { if (empty($username)) { echo 'error';}} ?>" name="user"/><br><br>
		Password <span class="required">*</span> <br>
		<input type="password" class="<?php if (isset($password)) { if (empty($password)) { echo 'error';}} ?>" name="password"/><br><br>
		<input type="submit" name="submit" value="Login"/>
	</form>
<?php } else {header('location: admin');} ?>