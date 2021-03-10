<h3><?php echo i18n('Updated_to');?><h3>
<h2>[<?php echo $info['tag_name']; ?>] <?php echo $info['name']; ?></h2>
<p><?php echo \Michelf\MarkdownExtra::defaultTransform($info['body']); ?></p>
<?php require_once "system/upgrade/run.php"; ?>
<?php 
if (file_exists('install.php')) {
    unlink('install.php');
}
?>