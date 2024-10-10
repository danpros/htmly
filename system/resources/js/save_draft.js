const response = document.getElementById("response");

function updateData() {
  var title = $("#pTitle").val();
  var url = $("#pURL").val();
  var content = $("#wmd-input").val();
  var description = $("#pMeta").val();
  var tag = $("#pTag").val();
  var category = $("#pCategory").val();
  var posttype = $("#pType").val();
  var pimage = $("#pImage").val();
  var paudio = $("#pAudio").val();
  var pvideo = $("#pVideo").val();
  var pquote = $("#pQuote").val();
  var plink = $("#pLink").val();
  var pDate = $("#pDate").val();
  var pTime = $("#pTime").val();
  var oldfile = $("#oldfile").val();
  var dateTime = pDate + " " + pTime;
  var autoSave = 'autoSave';

  // Prepare data to send to PHP
  var data = {
    title: title,
    url: url,
    content: content,
    description: description,
    tag: tag,
    category: category,
    posttype: posttype,
    pimage: pimage,
    paudio: paudio,
    pvideo: pvideo,
    pquote: pquote,
    plink: plink,
    dateTime: dateTime,
    autoSave: autoSave,
    addEdit: addEdit,
    oldfile: oldfile,
	parent_page: parent_page
  };
  
  $.ajax({
    url: base_path + 'admin/autosave',
    type: "POST",
    data: data,
    success: function(response) {
      $("#response").html(response.message);
      $("#oldfile").val(response.file);
      $("#response").fadeIn(600, function() {
        $("#response").css("display", "block");
      });
      setTimeout(function() {
        $("#response").fadeOut(600, function() {
          $("#response").css("display", "none");
        });
      }, 3000);
    }
  });
}

$(document).ready(function() {
  setInterval(updateData, saveInterval);
});
