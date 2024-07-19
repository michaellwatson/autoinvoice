if($('#client-div').length>0){


	var storeTable = 
	Ext.create('Ext.data.JsonStore', {
		pageSize: 25,
    	fields:['c_id', 'clientName', 'companyName', 'companyAddress', 'companyAddress2', 'postcode', 'telephoneNumber', 'email'],
		proxy: {
			type: 'ajax',
			reader: {
				type: 'json',
				root: 'store',
				totalProperty: 'totalProperty'
			}
    	},
    	autoLoad : true,
     		/*data:{'items':[{ 
			'id': '',  
			'name':'',
			'house_number':'',
			'street_name':'',
			'town':'',
			'postcode':'',
			'pt_name':'',
			'pc_name':'',
			'companyName':'',
			'tagname':'',
		}]}*/
	}); 

var pluginExpanded = true;
var gridPanelHourlyVar = Ext.create('Ext.grid.Panel', {
	id:'gridPanelHourly',
	hidden:true,
    title: 'Manage Clients',
    store: storeTable,
	listeners : {
		itemdblclick: function(dv, record, item, index, e) {
			//alert('working');
			var c_id = gridPanelHourlyVar.store.getAt(index).data.c_id;
			console.log(c_id);
			window.location.href = base_url+"client/add/"+c_id;
		}
	},

    columns: [
        { text: 'id', dataIndex: 'c_id', flex: 1, hidden: true, hideable: false },
		{ text: 'Client Name', dataIndex: 'clientName', flex: 1 },
		{ text: 'Company Name', dataIndex: 'companyName', flex: 1 },
		{ text: 'Company Address', dataIndex: 'companyAddress', flex: 1 },
		{ text: 'Company Address 2', dataIndex: 'companyAddress2', flex: 1 },
		{ text: 'Postcode ', dataIndex: 'postcode', flex: 1 },
		{ text: 'Email', dataIndex: 'email', flex: 1 },
		{
                xtype: 'actioncolumn',
				text:'Delete',
                width: 60,
                items: [{
                    icon: base_url+'assets/images/icons/cancel.png',
                    handler: function(grid, rowIndex, colindex) {
						var id = grid.store.getAt(rowIndex).data.c_id;
						Ext.MessageBox.confirm('Delete', 'Are you sure ?', function(btn){
							if(btn === 'yes'){
								//some code
								
								$.ajax({
								   type: "POST",
								   url: base_url+'client/delete',
								   data: {'client_id': id},
								   dataType:'json',
								   success: function(data)
								   {
									   if(data.status=='1'){
										   var grid = Ext.getCmp('gridPanelHourly');
											var selection = grid.getView().getSelectionModel().getSelection()[0];
											console.log(selection);
											if (selection) {
												storeTable.remove(selection);
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
            store: storeTable,
            displayInfo: true,
            displayMsg: 'Displaying houses {0} - {1} of {2}',
            emptyMsg: "No houses to display",
        }),
    renderTo: 'client-div'
});

					storeTable.loadData([],false);
					storeTable.getProxy().url = base_url+'client/results';
					storeTable.load();
					var panel = Ext.getCmp('gridPanelHourly');
					panel.show();
					panel.doLayout(); 

}