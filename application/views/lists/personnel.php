<?php if($edit_client){ ?> 
    <div class="row" style="padding:15px;">
        <a href="<?php echo base_url('personnel/add');?>" class="btn btn-primary">Add Personnel</a>
    </div>
<?php } ?>
<div id="personnel-div" style="width:100%;"></div>
      
<script language="javascript">

	var personnelTable = 
	Ext.create('Ext.data.JsonStore', {
		pageSize: 25,
    	fields:['id', 'firstname', 'lastname', 'email', 'phone',  'telephoneNumber', 'email', 'companyName'],
		proxy: {
			type: 'ajax',
			reader: {
				type: 'json',
				root: 'store',
				totalProperty: 'totalProperty'
			}
    	},
    	autoLoad : true,
	}); 

var pluginExpanded = true;
var gridPanelHourlyVar = Ext.create('Ext.grid.Panel', {
	id:'gridPanelHourly',
	hidden:true,
    title: 'Manage Personnel',
    store: personnelTable,
	listeners : {
		itemdblclick: function(dv, record, item, index, e) {
			//alert('working');
			var id = gridPanelHourlyVar.store.getAt(index).data.id;
			console.log(id);
			window.location.href = "<?php echo base_url('personnel/add');?>/"+id;
		}
	},

    columns: [
        { text: 'id', dataIndex: 'id', flex: 1, hidden: true, hideable: false },
		{ text: 'First name', dataIndex: 'firstname', flex: 1 },
		{ text: 'Last name', dataIndex: 'lastname', flex: 1 },
		{ text: 'Email', dataIndex: 'email', flex: 1 },
		{ text: 'Phone', dataIndex: 'phone', flex: 1 },
		{ text: 'Company', dataIndex: 'companyName', flex: 1 },
		{
                xtype: 'actioncolumn',
				text:'Delete',
                width: 60,
                items: [{
                    icon: '<?php echo base_url('assets/images/icons/cancel.png');?>',
                    handler: function(grid, rowIndex, colindex) {
						var id = grid.store.getAt(rowIndex).data.id;
						Ext.MessageBox.confirm('Delete', 'Are you sure ?', function(btn){
							if(btn === 'yes'){
								//some code
								
								$.ajax({
								   type: "POST",
								   url: '<?php echo base_url('personnel/delete');?>',
								   data: {'id': id},
								   dataType:'json',
								   success: function(data)
								   {
									   if(data.status=='1'){
										   var grid = Ext.getCmp('gridPanelHourly');
											var selection = grid.getView().getSelectionModel().getSelection()[0];
											console.log(selection);
											if (selection) {
												personnelTable.remove(selection);
											}
									   }else{
										   Ext.Msg.alert('Error', data.msg);
									   }
								   }
								});
							}else{
								//some code
							}
 						});
                    }
                }]
            }

    ],
    height: $(document).height(),
	width:'100%',
	// paging bar on the bottom
        bbar: Ext.create('Ext.PagingToolbar', {
            store: personnelTable,
            displayInfo: true,
            displayMsg: 'Displaying houses {0} - {1} of {2}',
            emptyMsg: "No Personnel to display",
        }),
    renderTo: 'personnel-div'
});

<?php 
	$url = '';
	if(isset($client_id)){
		$url = base_url('personnel/results/'.$client_id);
	}else{
		$url = base_url('personnel/results/');
	}
?>
personnelTable.loadData([],false);
personnelTable.getProxy().url = '<?php echo $url; ?>';
personnelTable.load();
var panel = Ext.getCmp('gridPanelHourly');
panel.show();
panel.doLayout(); 


</script>
