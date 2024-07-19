if (typeof makesortable !== 'function') {
    function makesortable(adv_column) {
        $("#sortableDocuments_" + adv_column).sortable({
            items: "li",
            cursor: 'move',
            opacity: 0.6,
            update: function () {
                var order = serializeList($("#sortableDocuments_" + adv_column));

                $.ajax({
                    type: "POST",
                    dataType: "json",
                    url: "/Post/orderDocuments", // Adjust the URL as needed
                    data: {'order': order },
                    success: function (response) {
                        deleteButton(adv_column);
                        save_button();
                    }
                });
            }
        });
    }
}

$(document).on('click', '.btn_delete_document',function(e) {
    e.preventDefault();
    if(confirm_delete()){
        var dataid = $(this).attr('data-id');

        //alert(dataid);
        $.ajax({
            type: "POST",
            url: base_url + 'Post/deleteDocuments',
            data: {'id':dataid},
            dataType:'json',
            success: function(data)
            {
                $('#document_'+dataid).remove();
            }
        });
    }
});

if (typeof serializeList !== 'function') {
    function serializeList(container)
    {
      var str = ''
      var n = 0
      var els = container.find('li')
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
        str+=x[1]+',';
      }
      return str
    }
}