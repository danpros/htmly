<?php if (!defined('HTMLY')) die('HTMLy'); ?>
<h2 class="post-index"><?php echo i18n('custom_fields');?>: Page</h2>
<br>
<?php 
$field = array();
$field_file= 'content/data/field/page.json';
if (file_exists($field_file)) {
    $field = file_get_contents($field_file, true);
}
?>
<!-- Preview Section -->
<div id="form-preview"></div>  
<br>
<!-- Form Input Section -->
<div class="form-group" id="form-input">
    <span class="d-block mt-1" id="input-status"></span>
    <br>
    <div class="row">
        <div class="col">
            <label for="type">Form type</label>
            <select id="type" class="form-control">
                <option value="text">Text</option>
                <option value="textarea">Textarea</option>
                <option value="checkbox">Checkbox</option>
                <option value="select">Select</option>
            </select>
        </div>
        <div class="col">
            <label for="label">Label</label>
            <input type="text" class="form-control" id="label" placeholder="Label (required)">
        </div>
        <div class="col">
            <label for="name">Name</label>
            <input type="text" class="form-control" id="name" placeholder="Name (required)">
        </div>
        <div class="col">
            <label for="value">Value</label>
            <input type="text" class="form-control" id="value" placeholder="Value (optional)">
        </div>
        <div class="col">
            <label for="info">Info</label>
            <input type="text" class="form-control" id="info" placeholder="Field Info (optional)">
        </div>
        <div class="col">
            <label for="add-field">Operations</label>
            <button id="add-field" class="btn btn-primary">Add Field</button>
        </div>
    </div>
    
    <span class="d-block mt-1"><small><em>No spaces for <code>Name</code> input. Underscores and dashes allowed</em></small></span>

    <div id="options-container" style="display: none;">
        <div class="row">
            <div class="col-sm-6">
                <h5 class="mt-2">Options</h5>
                <div id="option-list"></div>
                <button id="add-option" class="btn btn-primary btn-xs mt-1">Add Option</button>
                <span class="d-block mt-1"><small><em>No spaces for select and option <code>Value</code> input. Underscores and dashes allowed</em></small></span>
            </div>

        </div>
    </div>    
    
</div>

<!-- Form submit Section -->
<button class="btn btn-primary" id="saveButton"><?php echo i18n('save');?></button>

<br><br>

<!-- JSON Output Section -->
<details>
    <summary style="padding:10px; margin-bottom:10px; <?php echo ((config('admin.theme') === 'light' || is_null(config('admin.theme'))) ? "background-color: #E4EBF1;" : "background-color: rgba(255,255,255,.1);");?>">JSON Output</summary>
    <textarea id="json-output" name="page-field" style="field-sizing: content;" rows="20" class="form-control text" readonly></textarea> 
</details>

<br><br>
<script>
  const fields = <?php print_r($field);?>;
  $("#saveButton").click(function(){
    var data = $('#json-output').val();
    $.ajax({
      type: 'POST',
      url: '<?php echo site_url();?>admin/field/page',
      dataType: 'json',
      data: {'json': data},
      success: function (response) {
         alert(response.message);
         location.reload();
      },
    });    
  });
</script>
<script src="<?php echo site_url() ?>system/resources/js/form.builder.js"></script>