{
    text: '<?php echo $f['adv_text'];?>',
    xtype: 'checkcolumn',
    dataIndex: '<?php echo 'ad_'.$f['adv_column'];?>',
    flex: 1,
    listeners: {
        checkchange: function(column, recordIndex, checked) {
            console.log(column);
            console.log(column.id);
            console.log(recordIndex);

            var bool = 0;
            console.log($('#' + table_name + '-body').find('tbody tr').eq(recordIndex));
            var $cell = $('#' + table_name + '-body').find('tbody tr').eq(recordIndex).find('.x-grid-cell-' + column.id).find('div');
            $cell.hide();
            var imageName = 'img-' + recordIndex + '-' + column.id;
            var $img = $('#' + table_name + '-body').find('tbody tr').eq(recordIndex).find('.x-grid-cell-' + column.id).append('<div id="' + imageName + '" class="x-grid-cell-inner" style="text-align: center; display: block;"><img src="' + base_url + '/assets/img/ajax-loader.gif"></div>');
            if (checked == true) {
                bool = 1;
            } else {
                bool = 0;
            }
            console.log(gridPanelHourlyVar.store.getAt(recordIndex).data);
            var id = gridPanelHourlyVar.store.getAt(recordIndex).data.ad_id;
            $.ajax({
                type: "POST",
                url: base_url + 'checkboxbool/Checkboxbool/update',
                data: {
                    'entry_id': id,
                    'ci_csrf_token': '',
                    'field': '<?php echo 'ad_'.$f['adv_column'];?>',
                    'value': bool
                },
                dataType: 'json',
                success: function(data) {
                    if (data.status == '1') {

                    } else {
                        Ext.Msg.alert('Error', data.msg);
                    }
                    $('#' + imageName).remove();
                    $cell.show();
                }

            });

        }
    }
},