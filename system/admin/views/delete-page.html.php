<?php echo '<p>Are you sure want to delete <strong>' . $p->title . '</strong>?</p>';?>
<form method="POST">
	<input type="hidden" name="file" value="<?php echo $p->file ?>"/><br>
	<input type="submit" name="submit" value="Delete"/>
	<span><a href="<?php echo site_url() . 'admin' ?>">Cancel</a></span>
</form>