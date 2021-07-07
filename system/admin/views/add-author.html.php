<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<link rel="stylesheet" type="text/css" href="<?php echo site_url() ?>system/admin/editor/css/editor.css"/>
<script src="<?php echo site_url() ?>system/resources/js/jquery.min.js"></script>
<script src="<?php echo site_url() ?>system/resources/js/jquery-ui.min.js"></script>
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/Markdown.Converter.js"></script>
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/Markdown.Sanitizer.js"></script>
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/Markdown.Editor.js"></script>
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/Markdown.Extra.js"></script>
<link rel="stylesheet" href="<?php echo site_url() ?>system/resources/css/jquery-ui.css">

<?php if (isset($error)) { ?>
    <div class="error-message"><?php echo $error ?></div>
<?php } ?>

<div class="row">
	<div class="wmd-panel" style="width:100%;">
		<form method="POST">
			<div class="row">
				<div class="col-sm-6">
					<label for="aTitle"><?php echo i18n('Title');?> <span class="required">*</span></label>
					<input type="text" class="form-control <?php if (isset($aTitle)) {if (empty($aTitle)) {echo 'is-invalid';}} ?>" id="aTitle" name="title" value="<?php if (isset($aTitle)) {echo $aTitle;} ?>"/>
					<br>
				</div>
				<div class="col-sm-6">
					<label for="aUsername"><?php echo i18n('Username');?> <span class="required">*</span></label>
					<input type="text" class="form-control text text-lowercase <?php if (isset($aUsername)) {if (empty($aUsername)) {echo 'is-invalid';}} ?>" id="aUsername" name="username" value="<?php if (isset($aUsername)) {echo $aUsername;} ?>" placeholder="<?php if (isset($aUsername)) {echo $aUsername;} ?>"/>
					<br>
				</div>
			</div>
            <div class="row">
				<div class="col-sm-6">
					<label for="aPassword"><?php echo i18n('Password');?> <span class="required">*</span></label>
					<input type="password" class="form-control text <?php if (isset($aPassword)) {if (empty($aPassword)) {echo 'is-invalid';}} ?>" id="aPassword" name="password" value="<?php if (isset($aPassword)) {echo $aPassword;} ?>"/>
					<br>
				</div>
				<div class="col-sm-6">
					<label for="aPassConfirm"><?php echo i18n('Password_confirm');?> <span class="required">*</span></label>
					<input type="password" class="form-control text <?php if (isset($aPassConfirm)) {if (empty($aPassConfirm)) {echo 'is-invalid';}} ?>" id="aPassConfirm" name="passconfirm" value="<?php if (isset($aPassConfirm)) {echo $aPassConfirm;} ?>" placeholder="<?php if (isset($aPassConfirm)) {echo $aPassConfirm;} ?>"/>
					<br>
				</div>
			</div>
			
			<div class="row">
				<div class="col-sm-6">
					<label for="wmd-input"><?php echo i18n('Content');?></label>
					<div id="wmd-button-bar" class="wmd-button-bar"></div>
					<textarea id="wmd-input" class="form-control wmd-input" name="content" cols="20" rows="10"><?php if (isset($aContent)) {echo $aContent;} ?></textarea>
					<br>
					<input type="hidden" name="csrf_token" value="<?php echo get_csrf() ?>">
					<input type="submit" name="submit" class="btn btn-primary submit" value="<?php echo i18n('Add_author');?>"/>
				</div>
				<div class="col-sm-6">
					<label><?php echo i18n('Preview');?></label>
					<br>
					<div id="wmd-preview" class="wmd-panel wmd-preview" style="width:100%;overflow:auto;"></div>
				</div>
			</div>
		</form>
	</div>
	
	<style>
	.wmd-prompt-background {z-index:10!important;}
	#wmd-preview img {max-width:100%;}
	</style>
	<div class="modal fade" id="insertImageDialog" tabindex="-1" role="dialog" aria-labelledby="insertImageDialogTitle" aria-hidden="true">
		<div class="modal-dialog modal-dialog-centered" role="document">
			<div class="modal-content">
				<div class="modal-header">
					<h5 class="modal-title" id="insertImageDialogTitle"><?php echo i18n('Insert_Image');?></h5>
					<button type="button" class="close" id="insertImageDialogClose" data-dismiss="modal" aria-label="Close">
					<span aria-hidden="true">&times;</span>
					</button>
				</div>
				<div class="modal-body">
					<div class="form-group">
						<label for="insertImageDialogURL">URL</label>
						<input type="text" class="form-control" id="insertImageDialogURL" size="48" placeholder="<?php echo i18n('Enter_image_URL');?>" />
					</div>
					<hr>
					<div class="form-group">
						<label for="insertImageDialogFile"><?php echo i18n('Upload');?></label>
						<input type="file" class="form-control-file" name="file" id="insertImageDialogFile" accept="image/png,image/jpeg,image/gif" />
					</div>
				</div>
				<div class="modal-footer">
					<button type="button" class="btn btn-primary" id="insertImageDialogInsert"><?php echo i18n('Insert_Image');?></button>	
					<button type="button" class="btn btn-secondary"  id="insertImageDialogCancel" data-dismiss="modal"><?php echo i18n('Cancel');?></button>
				</div>
			</div>
		</div>
	</div>
</div>
<!-- Declare the base path. Important -->
<script type="text/javascript">var base_path = '<?php echo site_url() ?>';</script>
<script type="text/javascript" src="<?php echo site_url() ?>system/admin/editor/js/editor.js"></script>