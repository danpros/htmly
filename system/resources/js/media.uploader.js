(function () {

    //======Media Uploader=====

    var dialogClose = function() {
        $('#insertMediaDialog').modal('hide');
        $('#insertImageDialogURL').val('');
        $('#insertImageDialogFile').val('');
        $('#insertMediaDialogURL').val('');
        $('#insertMediaDialogFile').val('');
        $('#gallery-1').html(initial_image);
        $('#gallery-2').html(initial_image);
    };
    $('#insertMediaDialogInsert').click( function() {
        $('.media-uploader').val('');
        $('.imgPrev > img').remove();
        $('.media-uploader').val( $('#insertMediaDialogURL').val().length > 0 ? $('#insertMediaDialogURL').val() : null );
        $('.imgPrev').prepend($('<img>',{id:'imgFile',src: $('#insertMediaDialogURL').val()}));
        dialogClose();
    });
    $('#insertMediaDialogClose').click( function() {
        dialogClose();
    });
    $('#insertMediaDialogCancel').click( function() {
        dialogClose();
    });
    $('#insertMediaDialogFile').on('input', function(){
        var file = $("#insertMediaDialogFile").prop("files");
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
                    $('.media-uploader').val('');
                    $('.imgPrev > img').remove();
                    $('.media-uploader').val(base_path + response.path);
                    $('.imgPrev').prepend($('<img>',{id:'imgFile',src: base_path + response.path}));
                    dialogClose();
                }
                else
                {
                    if (response.error !== '') alert(response.error);
                    else alert("An unknown error has occurred");
                    console.error("Bad Response");
                    console.error(response);
                    $('#insertMediaDialogFile').val('');
                }
            },
            failure: function (response) {
                if (response.error !== '') alert(response.error);
                else alert("An unknown error has occurred");
                console.error("Unable to Upload");
                console.error(response);
                $('#insertMediaDialogFile').val('');
            }
        });//ajax
    });//oninput
    
    $('#insertButton').click(function() {
        $('#insertMediaDialog').modal('show');
    });

})();