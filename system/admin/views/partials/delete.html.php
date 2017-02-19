<div class="creatorMenu">
	<h2>Delete</h2>
	<p>Are you sure want to delete <strong><?php echo $p->title; ?></strong>?</p>
	<form method="POST">
		<input type="hidden" name="file" value="<?php echo $p->file ?>"/><br>
		<input type="hidden" name="csrf_token" value="<?php echo get_csrf() ?>">
		<input type="submit" name="submit" value="Delete"/>
		<span><a href="<?php echo $back ?>">Cancel</a></span>
	</form>
</div>