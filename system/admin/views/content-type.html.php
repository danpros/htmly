<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<h2><?php echo i18n('Add_content');?></h2>

<p><i class="fa fa-newspaper-o mr-1"></i><a href="<?php echo site_url();?>add/content?type=post"><?php echo i18n('Regular_post')?></a><br><?php echo i18n('Regular_post_comment')?>.</p>
<p><i class="fa fa-picture-o mr-1"></i><a href="<?php echo site_url();?>add/content?type=image"><?php echo i18n('Image_post')?></a><br><?php echo i18n('Image_post_comment')?>.</p>
<p><i class="fa fa-video-camera mr-1"></i><a href="<?php echo site_url();?>add/content?type=video"><?php echo i18n('Video_post')?></a><br><?php echo i18n('Video_post_comment')?>.</p>
<p><i class="fa fa-music mr-1"></i><a href="<?php echo site_url();?>add/content?type=audio"><?php echo i18n('Audio_post')?></a><br><?php echo i18n('Audio_post_comment')?>.</p>
<p><i class="fa fa-external-link mr-1"></i><a href="<?php echo site_url();?>add/content?type=link"><?php echo i18n('Link_post')?></a><br><?php echo i18n('Link_post_comment')?>.</p>
<p><i class="fa fa-quote-right mr-1"></i><a href="<?php echo site_url();?>add/content?type=quote"><?php echo i18n('Quote_post')?></a><br><?php echo i18n('Quote_post_comment')?>.</p>
<p><i class="fa fa-file-text-o mr-1"></i><a href="<?php echo site_url();?>add/page"><?php echo i18n('Static_page')?></a><br><?php echo i18n('Static_page_comment')?>.</p>