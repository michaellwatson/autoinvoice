          {
              xtype: 'combobox',
              id: '<?php echo $f->adv_column;?>',
              emptyText: 'Search <?php echo $f->adv_text?>',
              width: 300, 
              hideTrigger: true, 
              minChars: 1, 
              triggerAction: 'query', 
              typeAhead: true, 
              valueField: 'text',
              displayField: 'text',
              store: { 
                autoLoad: true,
                fields: [{name: 'text'}, {name: 'ID'}], 
                  proxy: { 
                    type: 'ajax', 
                      url: url+'autocomplete/Autocomplete/search?field=<?php echo $f->adv_id?>&linkedtable=<?php echo $f->adv_linkedtableid?>&type=search_field' 
                      }  
                },
              listeners: {
                focus: function(combo) {
                combo.setValue('');
                },
                change: function(combo) {
                  //alert('hit');
                //if(globals.storeLoaded==true){
                    doSearch();
                //}
                },
                afterRender:function(combo) {
                combo.setValue('<?php if(isset($_SESSION['search_fields'][$formID][$f->adv_column])){ echo $_SESSION['search_fields'][$formID][$f->adv_column]; };?>');
                }
              }
          },