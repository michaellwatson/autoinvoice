jQuery("#treatment_form").submit(function(e) {
    e.preventDefault();
    //var l = Ladda.create($('#submitButton'));
    var l = Ladda.create(document.querySelector('#submitButton'));
    l.start();

    var save_url = url + "Treatments/save_treatment_data";
    jQuery.ajax({
        type: "POST",
        url: save_url,
        data: jQuery("#treatment_form").serialize(), // serializes the form's elements.
        dataType: 'json',
        success: function(data) {
            if (data.status == 1) {
                jQuery('#msgHereSuccess').show();
                jQuery('#msgHereSuccess').html(data.msg); // show response from the php script.
                jQuery('#msgHereError').hide();
                setTimeout(function() {
                    window.location.reload()
                }, 2000);

            } else {
                jQuery('#msgHereSuccess').hide();
                jQuery('#msgHereError').html(data.msg); // show response from the php script.
                jQuery('#msgHereError').show();
            }
            jQuery('#alertModal').modal('show');
            l.stop();
        }
    });

    return false;
});


function setEdit(id, name) {
    jQuery('#t_id').val(id);
    jQuery('#t_name').val(name);
}

function deleteTreatment(id){
    jQuery.ajax({
        type: "POST",
        dataType: "json",
        url: url + "Treatments/delete_treatment/"+id,
        success: function(response) {
            window.location.reload();
        }
    });
}

function makeSortable() {
    jQuery(".survey_types").sortable({
        items: "tr",
        cursor: 'move',
        opacity: 0.6,
        start: function() {},
        stop: function() {},
        update: function() {
            //alert('hit');

            var order = serializeList(jQuery(".survey_types"));
            console.log(order);

            jQuery.ajax({
                type: "POST",
                dataType: "json",
                url: url + "Treatments/reorder_treatments",
                data: {
                    'order': order
                },
                success: function(response) {}
            });

        }
    });
}

function serializeList(container) {
    var str = ''
    var n = 0
    var els = container.find('tr')
    for (var i = 0; i < els.length; ++i) {
        var el = els[i]
        var p = el.id;
        console.log(p);
        var x = p.split('_');
        /*if (p != -1) {
          if (str != '') str = str + '&'
          str = str + el.id.substring(0, p) + '[]=' + (n + 1)
          ++n
        }*/
        str += x[1] + ',';
    }
    //alert(str);
    return str
}

$(document).ready(function() {
    makeSortable();

    $(document).on('click', '.sort_alpha', function(){

        jQuery.ajax({
            type: "POST",
            dataType: "json",
            url: url + "Treatments/reorder_treatments_alpha",
            success: function(response) {
                window.location.reload();
            }
        });
    
    });

});