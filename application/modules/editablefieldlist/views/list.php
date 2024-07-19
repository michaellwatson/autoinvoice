{
    text: '<?php echo $f['adv_text'];?>',
    dataIndex: '<?php echo 'ad_'.$f['adv_column'];?>',
    flex: 1,
    editor: {
        xtype: 'textfield',
        allowBlank: false,
        listeners: {
            change: function(field, newVal, oldVal) {
                //alert(field.value);
                gridPanelHourlyVar.setLoading(true);
                var id = gridPanelHourlyVar.store.getAt(globals.indexID).data.ad_id;
                $.ajax({
                    type: "POST",
                    url: base_url + 'editablefieldlist/Editablefieldlist/update',
                    data: {
                        'entry_id': id,
                        'ci_csrf_token': '',
                        'field': '<?php echo 'ad_'.$f['adv_column'];?>',
                        'value': field.value
                    },
                    dataType: 'json',
                    success: function(data) {
                        if (data.status == '1') {

                        } else {
                            Ext.Msg.alert('Error', data.msg);
                        }
                        gridPanelHourlyVar.setLoading(false);
                    }
                });
            }
        },
    }
},