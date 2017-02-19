<div class="createPost">
	<h2>Add Post</h2>

	<ul class="addPost">
		<li <?php if (isset($type) && $type == 'is_post'){ echo ' class="active"';}?>><a href="<?php echo site_url(); ?>add/content?type=post"><i class="icon-doc-text"></i>Text</a></li>
		<li <?php if (isset($type) && $type == 'is_image'){ echo ' class="active"';}?>><a href="<?php echo site_url(); ?>add/content?type=image"><i class="icon-camera"></i>Image</a></li>
		<li <?php if (isset($type) && $type == 'is_video'){ echo ' class="active"';}?>><a href="<?php echo site_url(); ?>add/content?type=video"><i class="icon-videocam"></i>Video</a></li>
		<li <?php if (isset($type) && $type == 'is_audio'){ echo ' class="active"';}?>><a href="<?php echo site_url(); ?>add/content?type=audio"><i class="icon-music"></i>Audio</a></li>
		<li <?php if (isset($type) && $type == 'is_link'){ echo ' class="active"';}?>><a href="<?php echo site_url(); ?>add/content?type=link"><i class="icon-link"></i>Link</a></li>
		<li <?php if (isset($type) && $type == 'is_quote'){ echo ' class="active"';}?>><a href="<?php echo site_url(); ?>add/content?type=quote"><i class="icon-chat-empty"></i>Quote</a></li>
	</ul>
</div>