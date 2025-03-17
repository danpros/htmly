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
  <br><br>
  <!-- Form Input Section -->
  <div>
    <label for="type">Field Type</label>
    <select id="type">
      <option value="text">Text</option>
      <option value="textarea">Textarea</option>
      <option value="checkbox">Checkbox</option>
      <option value="select">Select</option>
    </select>
    <input type="text" id="name" placeholder="Name (ID)">
    <input type="text" id="label" placeholder="Label">
    <input type="text" id="value" placeholder="Value (optional)">
	
    <button id="add-field" class="btn btn-primary">Add Field</button>

    <div id="options-container" style="display: none;">
      <strong>Options</strong>
      <div id="option-list"></div>
      <button id="add-option" class="btn btn-primary">Add Option</button>
    </div>
  </div>
  
<br><br>
<!-- Form submit Section -->
<button class="btn btn-primary" id="saveButton"><?php echo i18n('save');?></button>
<br><br>
<!-- JSON Output Section -->
<details>
<summary>JSON Output</summary>
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