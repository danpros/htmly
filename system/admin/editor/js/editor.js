(function () {

    var converter = new Markdown.Converter();
    Markdown.Extra.init(converter);
    var editor = new Markdown.Editor(converter);

    var $dialog = $('#insertImageDialog').dialog({ 
        autoOpen: false,
        closeOnEscape: false,
        open: function(event, ui) { $(".ui-dialog-titlebar-close").hide(); }
    });

    var $url = $('input[type=text]', $dialog);
    var $file = $('input[type=file]', $dialog);

    editor.hooks.set('insertImageDialog', function(callback) {

        var dialogClose = function() {
            $url.val('');
            $file.val('');
            $dialog.dialog('close');
        };

        $dialog.dialog({
            buttons :  { 
                "Insert" : {
                    text: "Insert",
                    id: "insert",
                    click: function(){
                        callback($url.val().length > 0 ? $url.val(): null);
                        dialogClose();
                    }   
                },
                "Cancel" : {
                    text: "Cancel",
                    id: "cancel",
                    click: function(){
                        dialogClose();
                        callback(null);
                    }   
                }       
            } 
        });

        var uploadComplete = function(response) {
            if (response.error == '0') {
                $url.val(base_path + response.path);
                $("#insert").trigger('click');
            } else {
                alert(response.error);
                $file.val('');
            }
        };

        $file.ajaxfileupload({
            'action': base_path + 'upload.php',
            'onComplete': uploadComplete,
        });

        $dialog.dialog('open');

        return true; // tell the editor that we'll take care of getting the image url
    });

    editor.run();

})();