(function () {

	imageEditor = document.getElementById("wmd-input-image");
	linkEditor	= document.getElementById("wmd-input-link");
	quoteEditor = document.getElementById("quoteEditor");
	videoEditor = document.getElementById("videoEditor");
	audioEditor = document.getElementById("audioEditor");
	
		
	if(imageEditor)
	{
		var converterImage = new Markdown.Converter();
		Markdown.Extra.init(converterImage);
		var options = { strings: { "toolbar":  ["image", "undo", "redo"] } };
		var editorImage = new Markdown.Editor(converterImage, "-image", options);
		
		var converter = new Markdown.Converter();
		Markdown.Extra.init(converter);
		var options = { strings: { "toolbar":  ["bold", "italic", "link", "undo", "redo", "olist", "ulist"] } };
		var editor = new Markdown.Editor(converter, false, options);
		addImageDialog(editorImage);
	}
	else if(linkEditor)
	{
		var converterLink = new Markdown.Converter();
		Markdown.Extra.init(converterLink);
		var options = { strings: { "toolbar":  ["link"] } };
		var editorLink = new Markdown.Editor(converterLink, "-link", options);

		var converter = new Markdown.Converter();
		Markdown.Extra.init(converter);
		var options = { strings: { "toolbar": ["bold", "italic", "undo", "redo", "olist", "ulist"] } };
		var editor = new Markdown.Editor(converter, false, options);
	}
	else if(quoteEditor || videoEditor || audioEditor)
	{
		var converterFeature = new Markdown.Converter();
		Markdown.Extra.init(converterFeature);
		var options = { strings: { "toolbar":  [] } };
		var editorFeature = new Markdown.Editor(converterFeature, "-feature",options);
	
		var converter = new Markdown.Converter();
		Markdown.Extra.init(converter);
		var options = { strings: { "toolbar": ["bold", "italic", "undo", "redo", "olist", "ulist", "link"] } };
		var editor = new Markdown.Editor(converter, false, options);		
	}
	else
	{
		var converter = new Markdown.Converter();
		Markdown.Extra.init(converter);
		var editor = new Markdown.Editor(converter);
		addImageDialog(editor);
	}
	
	function addImageDialog(editor)
	{
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
	}

	if(imageEditor){ editorImage.run(); }
	if(linkEditor){ editorLink.run(); }
	if(quoteEditor || videoEditor || audioEditor){ editorFeature.run() ;}
	
    editor.run();
	
})();