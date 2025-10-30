<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php 
global $config_file; 
$this_config = parse_ini_file($config_file, true);
?>    
<div class="row">
<?php foreach (glob('themes/*/layout.html.php') as $folder) : ?>
<?php $exp = explode('/',pathinfo($folder)['dirname']); $theme = $exp['1']; $theme_path = pathinfo($folder)['dirname'];?>
<?php $image = ($m = glob($theme_path . '/screenshot.*')) ? site_url() . $m[0] : site_url() . 'system/resources/images/default-screenshot.jpg';?>
    <div class="col-lg-4 col-md-6">
        <div class="card <?php echo $this_config['views.root'] === $theme_path ? 'card-primary': '';?>">
            <div class="card-header"><img class="card-img-top" height="200px" style="object-fit: cover;" src="<?php echo $image;?>"/></div>
            <div class="card-body">
                <h3><?php echo $theme;?></h3>
                <?php if ($this_config['views.root'] === $theme_path ):?>
                    <button class="btn btn-secondary disabled"><?php echo i18n('enable');?></button>
                <?php else:?>
                    <button class="btn btn-primary enable-button" data-value="<?php echo $theme_path;?>"><?php echo i18n('enable');?></button>
                <?php endif;?>
            
                <?php if ($this_config['views.root'] === $theme_path  && file_exists($theme_path . '/theme.json')) :?>
                    <a class="btn btn-primary" href="<?php echo site_url() . 'admin/themes/' . $theme;?>"><?php echo i18n('settings');?></a>
                <?php endif;?>
            </div>
        </div>
    </div>
<?php endforeach; ?>
</div>
<script>
$('.enable-button').on("click", function(e) {
    var data = $(e.target).attr('data-value');
    $.ajax({
      type: 'POST',
      url: '<?php echo site_url();?>admin/themes',
      dataType: 'json',
      data: {'json': data},
      success: function (response) {
         alert(response.message);
         location.reload();
      },
    }); 
});
</script>
