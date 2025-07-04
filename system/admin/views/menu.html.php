<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<?php
$menu = '';
$filename = "content/data/menu.json";
if (file_exists($filename)) {
    $json = json_decode(file_get_contents('content/data/menu.json', true));
    $menus = json_decode($json);
    if (!empty($menus)) {
        $menu = parseMenus($menus);        
    }
}

function parseMenus($menus) {
    $ol = '<ol class="dd-list">';
    foreach ($menus as $menu) {
        $ol .= parseMenu($menu); 
    }
    $ol .= '</ol>';
    return $ol;
}

function parseMenu($menu) {
    $target = !empty($menu->target) ? 'data-target="'.$menu->target.'"' : null;
    $li = '<li class="dd-item" data-class="'. $menu->class .'" data-id="'. $menu->id .'" data-name="'.$menu->name.'" '.$target.' data-slug="'.htmlspecialchars($menu->slug, FILTER_SANITIZE_URL).'">';
    $li .= '<div class="dd-handle">'.$menu->name.'</div>';
    $li .= '<span class="button-delete btn btn-danger btn-xs" style="margin-right:0.5rem" data-owner-id="'.$menu->id.'">'.i18n('Delete').'</span>';
    $li .= '<span class="button-edit btn btn-primary btn-xs" data-owner-id="'.$menu->id.'">'.i18n('Edit').'</span>';
    if (isset($menu->children)) { 
        $li .= parseMenus($menu->children);
    }
    $li .= '</li>';
    return $li;
}

?>

<div class="row">
    <div class="col-md-6">
        <div class="dd nestable"><?php if (!empty($menu)) {echo  '<h2>Drag & Drop</h2><br>'. $menu;} else {echo '<span>'.i18n('At_the_moment_you_are_using_auto_generated_menu').'</span><ol class="dd-list"></ol>';}?></div>
    </div>

    <div class="col-md-6">
        <form id="menu-add">
            <h4><?php echo i18n('Add_menu');?></h4>
            <div class="form-group">
            <label for="addInputName"><?php echo i18n('Name');?></label>
            <input type="text" class="form-control" id="addInputName" placeholder="<?php echo i18n('Link_name')?>" required>
            </div>
            <div class="form-group">
            <label for="addInputSlug"><?php echo i18n('Slug');?></label>
            <input type="text" class="form-control" id="addInputSlug" placeholder="<?php echo i18n('item_slug');?>" required>
            </div>
            <div class="form-group">
            <label for="addInputTarget">Target (<?php echo i18n('optional');?>)</label>
            <input type="text" class="form-control" id="addInputTarget" placeholder="_blank, _self, _parent etc.">
            </div>
            <div class="form-group">
            <label for="addInputClass"><?php echo i18n('CSS_Class_Optional');?></label>
            <input type="text" class="form-control" id="addInputClass" placeholder="<?php echo i18n('item_class');?>">
            </div>
            <button class="btn btn-primary btn-sm" id="addButton"><?php echo i18n('Add_link');?></button>
        </form>
        <form id="menu-editor" style="display: none;">
            <h4>Editing <span id="currentEditName"></span></h4>
            <div class="form-group">
            <label for="editInputName"><?php echo i18n('Name')?></label>
            <input type="text" class="form-control" id="editInputName" placeholder="<?php echo i18n('Link_name')?>" required>
            </div>
            <div class="form-group">
            <label for="editInputSlug"><?php echo i18n('Slug');?></label>
            <input type="text" class="form-control" id="editInputSlug" placeholder="<?php echo i18n('item_slug');?>">
            </div>
            <div class="form-group">
            <label for="editInputTarget">Target (<?php echo i18n('optional');?>)</label>
            <input type="text" class="form-control" id="editInputTarget" placeholder="_blank, _self, _parent etc.">
            </div>
            <div class="form-group">
            <label for="editInputClass"><?php echo i18n('CSS_Class_Optional');?></label>
            <input type="text" class="form-control" id="editInputClass" placeholder="<?php echo i18n('item_class');?>">
            </div>
            <button class="btn btn-primary btn-sm" id="editButton"><?php echo i18n('Save_Edit');?></button>
        </form>
    </div>
</div>

<div class="row">
    <div class="output-container">
        <div class="col">
            <button class="btn btn-primary" id="saveButton"><?php echo i18n('Save_Menu');?></button>
            <form class="form" style="display:none;">
                <textarea class="form-control" id="json-output" rows="5"></textarea>
            </form>
        </div>
    </div>    
</div>
<script src="<?php echo site_url() ?>system/resources/js/jquery.nestable.js"></script>
<script src="<?php echo site_url() ?>system/resources/js/jquery.nestable++.js"></script>
<script>
  $('.dd.nestable').nestable({
    maxDepth: 2
  })
  .on('change', updateOutput);
  

  $('#addMenu').click(function() {
    $('#menu-add').fadeIn();
  });
    
  $("#saveButton").click(function(){
    updateOutput($('.dd.nestable').data('output', $('#json-output')));
    var js = $('#json-output').val();
    $.ajax({
      type: 'POST',
      url: '<?php echo site_url();?>admin/menu',
      dataType: 'json',
      data: {'json': js},
      success: function (response) {
         alert(response.message);
         location.reload();
      },
    });    
  });
</script>
<style>
/**
*  Nestable css
*/
.dd {
  position: relative;
  display: block;
  margin: 0;
  padding: 0;
  max-width: 600px;
  list-style: none;
  font-size: 13px;
  line-height: 20px;
}

.dd-list {
  display: block;
  position: relative;
  margin: 0;
  padding: 0;
  list-style: none;
}

.dd-list .dd-list {
  padding-left: 30px;
}

.dd-collapsed .dd-list {
  display: none;
}

.dd-item,
.dd-empty,
.dd-placeholder {
  display: block;
  position: relative;
  margin: 0;
  padding: 0;
  min-height: 20px;
  font-size: 13px;
  line-height: 20px;
}

.dd-handle {
  display: block;
  margin: 5px 0;
  padding: 5px 10px;
  color: #333;
  text-decoration: none;
  font-weight: bold;
  border: 1px solid #ccc;
  background: #fafafa;
  background: -webkit-linear-gradient(top, #fafafa 0%, #eee 100%);
  background: -moz-linear-gradient(top, #fafafa 0%, #eee 100%);
  background: linear-gradient(top, #fafafa 0%, #eee 100%);
  -webkit-border-radius: 3px;
  border-radius: 3px;
  box-sizing: border-box;
  -moz-box-sizing: border-box;
  cursor: move;
}

.dd-handle:hover {
  color: #2ea8e5;
  background: #fff;
}

.dd-item > button {
  display: block;
  position: relative;
  cursor: pointer;
  float: left;
  width: 25px;
  height: 20px;
  margin: 5px 0;
  padding: 0;
  text-indent: 100%;
  white-space: nowrap;
  overflow: hidden;
  border: 0;
  background: transparent;
  font-size: 12px;
  line-height: 1;
  text-align: center;
  font-weight: bold;
}

.dd-item > button:before {
  content: '+';
  display: block;
  position: absolute;
  width: 100%;
  text-align: center;
  text-indent: 0;
}

.dd-item > button[data-action="collapse"]:before {
  content: '-';
}

.dd-placeholder,
.dd-empty {
  margin: 5px 0;
  padding: 0;
  min-height: 30px;
  background: #f2fbff;
  border: 1px dashed #b6bcbf;
  box-sizing: border-box;
  -moz-box-sizing: border-box;
}

.dd-empty {
  border: 1px dashed #bbb;
  min-height: 100px;
  background-color: #e5e5e5;
  background-image: -webkit-linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff),
    -webkit-linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff);
  background-image: -moz-linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff),
    -moz-linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff);
  background-image: linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff),
    linear-gradient(45deg, #fff 25%, transparent 25%, transparent 75%, #fff 75%, #fff);
  background-size: 60px 60px;
  background-position: 0 0, 30px 30px;
}

.dd-dragel {
  position: absolute;
  pointer-events: none;
  z-index: 9999;
}

.dd-dragel > .dd-item .dd-handle {
  margin-top: 0;
}

.dd-dragel .dd-handle {
  -webkit-box-shadow: 2px 4px 6px 0 rgba(0, 0, 0, .1);
  box-shadow: 2px 4px 6px 0 rgba(0, 0, 0, .1);
}

/**
* Nestable Extras
*/
.nestable-lists {
  display: block;
  clear: both;
  padding: 30px 0;
  width: 100%;
  border: 0;
  border-top: 2px solid #ddd;
  border-bottom: 2px solid #ddd;
}

#nestable-menu {
  padding: 0;
  margin: 20px 0;
}

#nestable-output,
#nestable2-output {
  width: 100%;
  height: 7em;
  font-size: 0.75em;
  line-height: 1.333333em;
  font-family: Consolas, monospace;
  padding: 5px;
  box-sizing: border-box;
  -moz-box-sizing: border-box;
}

#nestable2 .dd-handle {
  color: #fff;
  border: 1px solid #999;
  background: #bbb;
  background: -webkit-linear-gradient(top, #bbb 0%, #999 100%);
  background: -moz-linear-gradient(top, #bbb 0%, #999 100%);
  background: linear-gradient(top, #bbb 0%, #999 100%);
}

#nestable2 .dd-handle:hover {
  background: #bbb;
}

#nestable2 .dd-item > button:before {
  color: #fff;
}

.dd {
  //  float: left;
  //  width: 48 %;
  width: 80%;
}

.dd + .dd {
  margin-left: 2%;
}

.dd-hover > .dd-handle {
  background: #2ea8e5 !important;
}

/**
* Nestable Draggable Handles
*/
.dd3-content {
  display: block;
  height: 30px;
  margin: 5px 0;
  padding: 5px 10px 5px 40px;
  color: #333;
  text-decoration: none;
  font-weight: bold;
  border: 1px solid #ccc;
  background: #fafafa;
  background: -webkit-linear-gradient(top, #fafafa 0%, #eee 100%);
  background: -moz-linear-gradient(top, #fafafa 0%, #eee 100%);
  background: linear-gradient(top, #fafafa 0%, #eee 100%);
  -webkit-border-radius: 3px;
  border-radius: 3px;
  box-sizing: border-box;
  -moz-box-sizing: border-box;
}

.dd3-content:hover {
  color: #2ea8e5;
  background: #fff;
}

.dd-dragel > .dd3-item > .dd3-content {
  margin: 0;
}

.dd3-item > button {
  margin-left: 30px;
}

.dd3-handle {
  position: absolute;
  margin: 0;
  left: 0;
  top: 0;
  cursor: pointer;
  width: 30px;
  text-indent: 100%;
  white-space: nowrap;
  overflow: hidden;
  border: 1px solid #aaa;
  background: #ddd;
  background: -webkit-linear-gradient(top, #ddd 0%, #bbb 100%);
  background: -moz-linear-gradient(top, #ddd 0%, #bbb 100%);
  background: linear-gradient(top, #ddd 0%, #bbb 100%);
  border-top-right-radius: 0;
  border-bottom-right-radius: 0;
}

.dd3-handle:before {
  content: '≡';
  display: block;
  position: absolute;
  left: 0;
  top: 3px;
  width: 100%;
  text-align: center;
  text-indent: 0;
  color: #fff;
  font-size: 20px;
  font-weight: normal;
}

.dd3-handle:hover {
  background: #ddd;
}


/*
* Nestable++
*/
.button-delete {
  position: absolute;
  top: 4px;
  right: 40px;
}

.button-edit {
  position: absolute;
  top: 4px;
  right: 5px;
}

#saveButton {
  padding-right: 30px;
  padding-left: 30px;
}

.output-container {
  margin-top: 20px;
}

#json-output {
  margin-top: 20px;
}
</style>