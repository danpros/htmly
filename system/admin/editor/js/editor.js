(function () {
    var converter = new Markdown.Converter();
    Markdown.Extra.init(converter);
    var editor = new Markdown.Editor(converter);

    // Run once on DOM ready
    $('#insertImageDialog').on('hidden.bs.modal', function () {
      $('.wmd-prompt-background, .wmd-prompt-dialog').remove();
    });

    //======Image Uploader=====
    var callbackFunc;
    var dialogClose = function() {
        $('#insertImageDialog').modal('hide');
        $('#insertImageDialogURL').val('');
        $('#insertImageDialogFile').val('');
        $('#insertMediaDialogURL').val('');
        $('#insertMediaDialogFile').val('');
        $('#gallery-1').html(initial_image);
        $('#gallery-2').html(initial_image);
        // Also remove the PageDown/WMD overlay, if present
        $('.wmd-prompt-background').remove();
        $('.wmd-prompt-dialog').remove(); // WMD often spawns a paired dialog
    };
    $('#insertImageDialogInsert').click( function() {
        callbackFunc( $('#insertImageDialogURL').val().length > 0 ? $('#insertImageDialogURL').val() : null );
        dialogClose();
    });
    $('#insertImageDialogClose').click( function() {
        callbackFunc(null);
        dialogClose();
    });
    $('#insertImageDialogCancel').click( function() {
        callbackFunc(null);
        dialogClose();
    });
    $('#insertImageDialogFile').on('input', function(){
        var file = $("#insertImageDialogFile").prop("files");
        var formData = new FormData();
        formData.append('file', file[0], file[0].name);
        // Set up the request.
        $.ajax({
            type: "POST",
            url: base_path + 'upload.php',
            data: formData,
            processData: false,
            contentType: false,
            success: function (response) {
                if (response.error == '0')
                {
                    callbackFunc(base_path + response.path);
                    dialogClose();
                }
                else
                {
                    if (response.error !== '') alert(response.error);
                    else alert("An unknown error has occurred");
                    console.error("Bad Response");
                    console.error(response);
                    $('#insertImageDialogFile').val('');
                }
            },
            failure: function (response) {
                if (response.error !== '') alert(response.error);
                else alert("An unknown error has occurred");
                console.error("Unable to Upload");
                console.error(response);
                $('#insertImageDialogFile').val('');
            }
        });//ajax
    });//oninput
    editor.hooks.set('insertImageDialog', function(callback) {
        $('#insertImageDialog').modal('show');
        callbackFunc = callback;

        return true; // tell the editor that we'll take care of getting the image url
    });
    //=====end image uploader=====
    editor.run();

    //=====drag and drop uploading=====
    function initDropZone(dropZoneId, fileInputId) {
      const dropZone = document.getElementById(dropZoneId);
      const fileInput = document.getElementById(fileInputId);
      if (!dropZone || !fileInput) return; // fail gracefully

      // Clicking the drop zone triggers the file picker
      dropZone.addEventListener('click', (e) => {
        e.preventDefault(); // prevent button form submit
        fileInput.click();
      });

      // Highlight on dragover (styles in css file)
      dropZone.addEventListener('dragover', (e) => {
        e.preventDefault();
        dropZone.classList.add('dragover');
      });

      // Remove highlight on dragleave
      dropZone.addEventListener('dragleave', () => {
        dropZone.classList.remove('dragover');
      });

      // Handle drop
      dropZone.addEventListener('drop', (e) => {
        e.preventDefault();
        dropZone.classList.remove('dragover');

        const files = e.dataTransfer.files;
        if (files.length > 0) {
          fileInput.files = files;

          // Fire both 'change' and 'input' to simulate a real user selection
          ['change', 'input'].forEach(eventType => {
            const event = new Event(eventType, { bubbles: true });
            fileInput.dispatchEvent(event);
          });
        }
      });
    }

    // Initialize dropZones
    initDropZone('dropZoneIMDF', 'insertMediaDialogFile');  // Featured image
    initDropZone('dropZoneIIDF', 'insertImageDialogFile');  // Content image
    //=====end drag and drop uploading=====

})();