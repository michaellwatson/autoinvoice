jQuery(window).load(function() {    

    $(document).on("click", ".crop_button", function (e) {

        e.preventDefault();
        let image = $(this).attr('data-image');
        let imgId = $(this).attr('data-imgId');
        let img = $(this).attr('data-img');

        let w = $(this).attr('data-w');
        let h = $(this).attr('data-h');

        let field_id = $(this).attr('data-field-id');
        let column = $(this).attr('data-column');

        //alert(imgId);
        //alert(img);
        //alert(column);

        $('#img').val(img);
        $('#imgId').val(imgId);

        $('#w').val(w);
        $('#h').val(h);

        $('#field_id').val(field_id);
        $('#column').val(column);

        //alert(image);
        $('.crop_modal').modal('show');
		jcrop_api.setImage(image); 

    });

});