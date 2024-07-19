{
    xtype: 'checkbox',
    id: '<?php echo $f['adv_column'];?>',
    fieldLabel: '<?php echo $f['adv_text'];?>',
    forceSelection: true,
    width: 150, 
    labelWidth: 120,  
    listeners: {
    change: function() {

        doSearch();

    },
    afterRender:function() {
        this.setValue('<?php if(isset($_SESSION['search_fields'][$formID][$f->adv_column])){ echo $_SESSION['search_fields'][$formID][$f->adv_column]; };?>');
    }
  }
},