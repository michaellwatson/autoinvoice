
      <?php if($add_jobs){ ?> 
        <div class="row" style="padding:15px;">
          <a href="<?php echo base_url('job/add');?>" class="btn btn-primary">Add Job</a>
        </div>
      <?php } ?>
      <div id="jobs-list"></div>


<script language="javascript">
var pageSize = 25;
var searchUrl = '';
var count = 0;

  var storeTable = Ext.create('Ext.data.JsonStore', {
    count: 0,
    listeners: {
      load: function () {
        console.log(this);
      }
    },
    sorters: [{
      property: 'date_of_instruction',
      direction: 'DESC'
    }],
    sortRoot: 'date_of_instruction',
    sortOnLoad: false,
    remoteSort: true,
    pageSize: pageSize,
    fields: ['taken_by_name', 'date_of_instruction', 'method_of_instruction', 'client', 'nature_of_instruction', 'fee_amount', 'fee_notes', 'standard_items', 'updated_at', 'created_at', 'flag'],
    proxy: {
      type: 'ajax',
      reader: {
        type: 'json',
        root: 'store',
        totalProperty: 'totalProperty'
      }
    },
  });

function doSearch(){
  
    var dateFrom     = Ext.getCmp('dateFrom').getValue();
    var dateTo       = Ext.getCmp('dateTo').getValue(); 
    console.log(dateFrom);
    console.log(dateTo);

    var method_search     = Ext.getCmp('method_search').getValue();
    var nature_search   = Ext.getCmp('nature_search').getValue();
    var standard_search    = Ext.getCmp('standard_search').getValue();
    var client_search  = Ext.getCmp('client_search').getValue();  
    var flag_search     = Ext.getCmp('flag_search').getValue();
    var assigned_to     = Ext.getCmp('assigned_to').getValue();
    var dateFromF = '';
    var dateToF = '';

    if(dateFrom!==null){
      var dateFrom_c = new Date(dateFrom);
      console.log(dateFrom_c);
      //('0' + (dateFrom_c.getMonth() + 1)).slice(-2)
      dateFromF = dateFrom_c.getFullYear() + "-" + ('0' + (dateFrom_c.getMonth() + 1)).slice(-2) + "-" + ('0' + (dateFrom_c.getDate() + 1)).slice(-2);
    }
    if(dateTo!==null){
      var dateTo_c = new Date(dateTo);
      console.log(dateTo_c);
      dateToF = dateTo_c.getFullYear() + "-" + ('0' + (dateTo_c.getMonth() + 1)).slice(-2) + "-" + ('0' + (dateTo_c.getDate() + 1)).slice(-2);            
    }

    searchUrl = '?dateFrom='+dateFromF+'&dateTo='+dateToF+'&method_search='+method_search+'&nature_search='+nature_search+'&standard_search='+standard_search+'&client_search='+client_search+'&flag_search='+flag_search+'&assigned_to='+assigned_to;

    console.log(searchUrl);

    storeTable.currentPage = '1';
    storeTable.loadData([],false);
    storeTable.getProxy().url = base_url + 'job/results/' + searchUrl;

    storeTable.load();
                                
    var panel = Ext.getCmp('gridPanelHourly');
    panel.show();
    panel.doLayout(); 
    Ext.getCmp('pagingToolBar').moveFirst();  
              
}

Ext.onReady(function () {

  // setup the state provider, all state information will be saved to a cookie
  //Ext.state.Manager.setProvider(Ext.create('Ext.state.CookieProvider'));
  if (Ext.supports.LocalStorage) {
    //alert('Localstorage Store');
    Ext.state.Manager.setProvider(
      Ext.create('Ext.state.LocalStorageProvider')
    );
  } else {
    //Ext.state.Manager.setProvider(
    //  Ext.create('Ext.state.CookieProvider')
    //);
    var thirtyDays = new Date(new Date().getTime() + (1000 * 60 * 60 * 24 * 30));
    Ext.state.Manager.setProvider(new Ext.state.CookieProvider({
      expires: thirtyDays
    }));
  }

  var globals = {
    currentEditId: '',
    totalPages: '',
    loaded: false,
    fromRecord: '',
    toRecord: '',
    storeLoaded: false,
    visibleColumns: '',
    indexID: '',
    color: ''
  }
  Ext.namespace("Ext.ux");

  Ext.Loader.setConfig({
    enabled: true,
    paths: {
      Ext: '.',
      'Ext.ux': '../public/extjs/ux'
    }
  });

  Ext.require([
    'Ext.grid.*',
    'Ext.data.*',
    'Ext.ux.grid.Printer',
    'Ext.ux.RowExpander'
  ]);

  Ext.ux.comboBoxRendererWarrant = function (combo) {
    return function (value) {
      return value;
    }
  }
  Ext.ux.comboBoxRenderer = function (combo) {
    return function (value) {
      var idx = combo.store.find(combo.valueField, value);
      var rec = combo.store.getAt(idx);
      return (rec == null ? '' : rec.get(combo.displayField));
    };
  }
  Ext.ux.comboBoxRendererCustom = function(combo) {
    return function(value) {
    var idx = combo.store.find(combo.valueField, value);
    var rec = combo.store.getAt(idx);
    switch(rec.get(combo.valueField)){
      case 1:
        return '<img src="'+url+'assets/images/icons/flag_red.png">';
      break;
      case 2:
        return '<img src="'+url+'assets/images/icons/flag_yellow.png">';
      break;
      case 3:
        return '<img src="'+url+'assets/images/icons/flag_green.png">';
      break;
      case 4:
        return '<img src="'+url+'assets/images/icons/flag_blue.png">';
      break;
      case 5:
        return '<img src="'+url+'assets/images/icons/flag_orange.png">';
      break;
      case 6:
        return '<img src="'+url+'assets/images/icons/flag_pink.png">';
      break;
      case 7:
        return '<img src="'+url+'assets/images/icons/flag_purple.png">';
      break;
      case -1:
        return '<img src="'+url+'assets/images/icons/flag_white.png">';
      break;
    };
    };
  }

  var flagStatus = Ext.create('Ext.data.Store', {
    fields: ['name', 'value', 'icon'],
    data:[  
      {'name': 'No Flag', 'value': 0 ,'icon': 'cancel.png'},
      {'name': 'Red Flag', 'value': 1 ,'icon': 'flag_red.png'},
      {'name': 'Yellow Flag', 'value': 2 ,'icon': 'flag_yellow.png'},
      {'name': 'Green Flag', 'value': 3 ,'icon': 'flag_green.png'},
      {'name': 'Blue Flag', 'value': 4 ,'icon': 'flag_blue.png'},
      {'name': 'Orange Flag', 'value': 5 ,'icon': 'flag_orange.png'},
      {'name': 'Pink Flag', 'value': 6 ,'icon': 'flag_pink.png'},
      {'name': 'Purple Flag', 'value': 7 ,'icon': 'flag_purple.png'},
      {'name': 'No Color', 'value': '-1' ,'icon': 'flag_white.png'}
    ]
  });

  var user_store = Ext.create('Ext.data.Store', {
    fields: ['name', 'value'],
    data:[  
        <?php foreach($users as $u){ ?>
          {'name': '<?php echo $u->us_firstName.' '.$u->us_surname;?>', 'value': '<?php echo $u->us_id;?>' },
        <?php } ?>
        ]
  });


  var method_store = Ext.create('Ext.data.Store', {
    fields: ['name', 'value'],
    data:[  
        <?php foreach($method as $m){ ?>
          {'name': '<?php echo $m->name;?>', 'value': '<?php echo $m->id;?>' },
        <?php } ?>
        ]
  });


  var flagsCombo = new Ext.form.ComboBox({
    store: flagStatus,
    displayField: 'name',
    valueField: 'value',
    editable: false,
    listConfig: {
            getInnerTpl: function() {
              // here you place the images in your combo
              var tpl = '<div>'+
              '<img src="'+url+'/assets/images/icons/{icon}" align="left">&nbsp;&nbsp;'+
              '</div>';
              return tpl;
            }
          },
    listeners: {
      // public event change - when selection1 dropdown is changed
      change: function (field, newValue, oldValue) {
        //alert('hit');
        gridPanelHourlyVar.setLoading(true);

        $.ajax({
          type: "POST",
          url: base_url+'job/update_flag',
          data: {
            'id': globals.currentEditId,
            'ci_csrf_token': '',
            'val': newValue
          },
          dataType: 'json',
          success: function (data) {
            if (data.status == '1') {
              //alert('hit');
              var grid = Ext.getCmp('gridPanelHourly');
              var currentFlag = '';
              var flagClass = '';
              //alert(newValue);
              switch(newValue){
                 case 1:
                  flagClass = 'redFlag-row';
                 break;
                 case 2:
                  flagClass =  'yellowFlag-row';
                 break;
                 case 3:
                  flagClass =  'greenFlag-row';
                 break;
                 case 4:
                  flagClass =  'blueFlag-row';
                 break;
                 
                case 5:
                  flagClass =  'orangeFlag-row';
                 break;
                case 6:
                  flagClass =  'pinkFlag-row';
                 break;
                case 7:
                  flagClass =  'purpleFlag-row';
                 break;
                }
                //alert(flagClass);
                gridPanelHourlyVar.store.getAt(globals.indexID).set('flag',newValue);
                grid.getView().addRowCls(globals.indexID, flagClass);
              
            } else {
              Ext.Msg.alert('Error', data.msg);
            }
            gridPanelHourlyVar.setLoading(false);
          }
        });
      }
    }
  });


  storeTable.currentPage = '1';
  storeTable.loadData([], false);
  storeTable.getProxy().url = base_url + 'job/results/' + searchUrl;
  console.log(storeTable.getProxy().url);

  storeTable.load();
  console.log(storeTable.count);


  var gridPanelHourlyVar = Ext.create('Ext.grid.Panel', {
    id: 'gridPanelHourly',
    width: '100%',
    height: $(document).height(),
    hidden: true,
    title: 'Jobs',
    store: storeTable,
    stateful: true,
    XStateful: true,
    selModel: new Ext.selection.RowModel({
      mode: "MULTI"
    }),
    stateId: 'stateGridHouses',
    dockedItems: [{
      xtype : "toolbar",
      dock : "top",
      id: "hideAbleToolbar",
      items: [
      {
        xtype: 'combobox',
        id: 'assigned_to',
        emptyText: 'Assigned To',
        forceSelection: true,

        fieldLabel: 'Assigned',
        labelWidth:70,
        width:250,

        displayField: 'name',
        valueField: 'value',
        editable: false,
        store: {
          fields: ['name', 'value'],
          data: [
            {'name': 'Please Select', 'value': '' },
            <?php foreach($users as $u){ ?>
              {'name': '<?php echo $u->us_firstName.' '.$u->us_surname;?>', 'value': '<?php echo $u->us_id;?>' },
            <?php } ?>
          ]
        },
        listeners: {
          focus: function (combo) {
            combo.setValue('');
          },
          change: function (combo) {
            //alert('hit');
            if (globals.storeLoaded == true) {
              //if (Ext.getCmp('autoSearch').getValue() == true) {
                doSearch();
              //}
            }
          },
          afterRender: function (combo) {
            combo.setValue('');
          }
        }
      },
      { 
        xtype: 'datefield',
        id: 'dateFrom',
        fieldLabel: 'Date From',
        labelWidth:70,
        width:240,
        name: 'date',
        // The value matches the format; will be parsed and displayed using that format.
        format: 'd/m/Y',
        altFormats: 'd/m/Y',
        submitFormat: 'd/m/Y',
        value: '',
        listeners: {
          focus: function (combo) {
            combo.setValue('');
          },
          change: function (combo) {
            //alert('hit');
            if (globals.storeLoaded == true) {
              //if (Ext.getCmp('autoSearch').getValue() == true) {
                doSearch();
              //}
            }
          },
          afterRender: function (combo) {
            combo.setValue('');
          }
        }
      },
      { 
        xtype: 'datefield',
        id: 'dateTo',
        fieldLabel: 'Date To',
        labelWidth:70,
        width:240,
        name: 'date',
        // The value matches the format; will be parsed and displayed using that format.
        format: 'd/m/Y',
        altFormats: 'd/m/Y',
        submitFormat: 'd/m/Y',
        value: '',
        listeners: {
          focus: function (combo) {
            combo.setValue('');
          },
          change: function (combo) {
            //alert('hit');
            if (globals.storeLoaded == true) {
                //if (Ext.getCmp('autoSearch').getValue() == true) {
                doSearch();
                //}
            }
          },
          afterRender: function (combo) {
            combo.setValue('');
          }
        }
      },
      {
        xtype: 'combobox',
        id: 'method_search',
        emptyText: 'Method',
        forceSelection: true,

        fieldLabel: 'Method',
        labelWidth:50,
        width:250,

        displayField: 'name',
        valueField: 'value',
        editable: false,
        store: {
          fields: ['name', 'value'],
          data: [
            {'name': 'Please Select', 'value': '' },
            <?php foreach($method as $m){ ?>
              {'name': '<?php echo $m->name;?>', 'value': '<?php echo $m->id;?>' },
            <?php } ?>
          ]
        },
        listeners: {
          focus: function (combo) {
            combo.setValue('');
          },
          change: function (combo) {
            //alert('hit');
            if (globals.storeLoaded == true) {
              //if (Ext.getCmp('autoSearch').getValue() == true) {
                doSearch();
              //}
            }
          },
          afterRender: function (combo) {
            combo.setValue('');
          }
        }
      },
      {
        xtype: 'combobox',
        id: 'nature_search',
        emptyText: 'Nature',
        forceSelection: true,

        fieldLabel: 'Nature',
        labelWidth:50,
        width:250,

        displayField: 'name',
        valueField: 'value',
        editable: false,
        store: {
          fields: ['name', 'value'],
          data: [
            {'name': 'Please Select', 'value': '' },
            <?php foreach($nature as $n){ ?>
              {'name': '<?php echo $n->name;?>', 'value': '<?php echo $n->id;?>' },
            <?php } ?>
          ]
        },
        listeners: {
          focus: function (combo) {
            combo.setValue('');
          },
          change: function (combo) {
            //alert('hit');
            if (globals.storeLoaded == true) {
              //if (Ext.getCmp('autoSearch').getValue() == true) {
                doSearch();
              //}
            }
          },
          afterRender: function (combo) {
            combo.setValue('');
          }
        }
      },
      {
        xtype: 'combobox',
        id: 'standard_search',
        emptyText: 'Standard',
        forceSelection: true,

        fieldLabel: 'Standard',
        labelWidth:60,
        width:250,

        displayField: 'name',
        valueField: 'value',
        editable: false,
        store: {
          fields: ['name', 'value'],
          data: [
            {'name': 'Please Select', 'value': '' },
            <?php foreach($standard as $s){ ?>
              {'name': '<?php echo $s->name;?>', 'value': '<?php echo $s->id;?>' },
            <?php } ?>
          ]
        },
        listeners: {
          focus: function (combo) {
            combo.setValue('');
          },
          change: function (combo) {

            if (globals.storeLoaded == true) {
              //if (Ext.getCmp('autoSearch').getValue() == true) {
                doSearch();
              //}
            }
          },
          afterRender: function (combo) {
            combo.setValue('');
          }
        }
      },
      {
          xtype: 'combobox',
          id: 'client_search',
          emptyText: 'Search Clients',
          width: 150, 
          hideTrigger: true, 
          minChars: 1, 
          triggerAction: 'query', 
          typeAhead: true, 
          store: { 
            fields: ['text'], 
              proxy: { 
                type: 'ajax', 
                  url: base_url+'client/clientLike' 
                  }  
            },
          listeners: {
            focus: function(combo) {
            combo.setValue('');
            },
            change: function(combo) {
              //alert('hit');
            if(globals.storeLoaded==true){
              //if(Ext.getCmp('autoSearch').getValue()==true){
                doSearch();
              //}
            }
            },
            afterRender:function(combo) {
            combo.setValue('');
            }
          }
      },
      {
          xtype: 'combobox',
          id: 'flag_search',
          emptyText: 'Flag',
          forceSelection: true,
          width: 100, 
          displayField: 'name',
          valueField: 'value',
          editable: false,
          store: flagStatus,
          listeners: {
            focus: function(combo) {
            combo.setValue('');
            },
            change: function(combo) {
              //alert('hit');
            if(globals.storeLoaded==true){
              //if(Ext.getCmp('autoSearch').getValue()==true){
                doSearch();
              //}
            }
            },
            afterRender:function(combo) {
            combo.setValue(0);
            }
          },
          listConfig: {
            getInnerTpl: function() {
              // here you place the images in your combo
              var tpl = '<div>'+
              '<img src="'+base_url+'assets/images/icons/{icon}" align="left">&nbsp;&nbsp;'+
              '</div>';
              return tpl;
            }
          }
      },
      {
        iconCls: 'excelWhite',
          handler : function(){

          visibleColumns = Array();
          textColumns = Array();
          widthColumn = Array();
          //console.log('##');
          gridPanelHourlyVar.columns.forEach(function(entry) {
            console.log(entry);
            //console.log(entry.dataIndex);
            //console.log(entry.hidden);
            if(entry.hidden==false){
              visibleColumns.push(entry.dataIndex);
              textColumns.push(entry.text);
              //widthColumn.push(entry.width);  
            }
            switch(entry.initialConfig.dataIndex){
              case "date_added":
                searchUrl = searchUrl+='&sort=%5B%7B%22property%22%3A%22date_added%22%2C%22direction%22%3A%22'+entry.sortState+'%22%7D%5D';
              break;  
            }
          });
          //console.log(visibleColumns);
          
          storeTable.pageSize = globals.totalPages;
          //var containerWidth = $('#headercontainer-1030').width();
          //console.log(containerWidth);
          //now that the grid can cover the screen 
          //we need to find out proportianal values
          var bigWidth = $('#main-div').width();
          //alert(bigWidth);
          var containerWidth = 1170;
          if(bigWidth>containerWidth){
            var ratio = containerWidth/bigWidth;
          }else{
            var ratio = 1;
          }
          //alert(ratio);
          var checkPercent = 0;
          
          //$('#headercontainer-1030-innerCt').find('.x-column-header-inner').each(function() {
          $("[id^=headercontainer-][id$=-innerCt]").find('.x-column-header-inner').each(function() {
            if($(this).width()>0){
              console.log('@@');
              console.log($(this));
              console.log(this);
              console.log($(this).width());
              var percent = (($(this).width()+20)/containerWidth)*100;
              percent = Math.ceil(percent);
              console.log(percent);
              
              widthColumn.push((percent*ratio));
              checkPercent+=percent;
              console.log();
            }
          });
          console.log(Math.ceil(checkPercent));
          //console.log(base_url+'job/results/'+searchUrl+'&type=xls&statsOnly=1&start=0&limit='+globals.totalPages+'&columns='+visibleColumns+'&text='+textColumns+'&width='+widthColumn);
          if(searchUrl==''){
            window.location.href = base_url+'job/results/?type=xls&statsOnly=1&start=0&limit='+globals.totalPages+'&columns='+visibleColumns+'&text='+textColumns+'&width='+widthColumn; 
          }else{
            window.location.href = base_url+'job/results/'+searchUrl+'&type=xls&statsOnly=1&start=0&limit='+globals.totalPages+'&columns='+visibleColumns+'&text='+textColumns+'&width='+widthColumn; 
          }
                
              }
          },

      ]
    }],
    viewConfig: {
      getRowClass: function (record) {
        //have to have more that one
        //class if required
        //alert(record.get('temp_flat_color'));
        var newClass = '';

          switch (record.get('flag')) {
            case 1:
              newClass = 'redFlag-row';
            break;
            case 2:
              newClass = 'yellowFlag-row';
            break;
            case 3:
              newClass = 'greenFlag-row';
            break;
            case 4:
              newClass = 'blueFlag-row';
            break;
            case 5:
              newClass = 'orangeFlag-row';
            break;
            case 6:
              newClass = 'pinkFlag-row';
            break;
            case 7:
              newClass = 'purpleFlag-row';
            break;
          }

        return newClass;
      }

    },
    listeners: {
      itemdblclick: function (dv, record, item, index, e) {
        //alert('working');
        var id = gridPanelHourlyVar.store.getAt(index).data.id;
        console.log(id);
        window.location.href = base_url+"job/add/" + id;
      },
      itemclick: function (dv, record, item, index, e) {
        globals.currentEditId = gridPanelHourlyVar.store.getAt(index).data.id;
        globals.indexID = index;
        globals.color = gridPanelHourlyVar.store.getAt(index).data.temp_flat_color;
      }
    },
    plugins: [
      Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 1
      })
    ],
    columns: [{
        text: 'id',
        dataIndex: 'id',
        flex: 1,
        hidden: true,
        hideable: false
      },
      {
        text: 'Taken by',
        dataIndex: 'taken_by_name',
        flex: 1,
      },
      {
        text: 'Date of instruction',
        dataIndex: 'date_of_instruction',
        flex: 1,
      },
      {
        text: 'Method of instruction',
        dataIndex: 'method_of_instruction',
        flex: 1,
      },
      {
        text: 'Client',
        dataIndex: 'client',
        flex: 1,
      },
      {
        text: 'Nature of instruction',
        dataIndex: 'nature_of_instruction',
        flex: 1,
      },
      {
        text: 'Fee amount',
        dataIndex: 'fee_amount',
        flex: 1,
      },
      {
        text: 'Fee notes',
        dataIndex: 'fee_notes',
        flex: 1,
      },
      {
        text: 'Standard items',
        dataIndex: 'standard_items',
        flex: 1,
      },
      { 
        text: 'Flag', dataIndex: 'flag', flex: 1, hidden: false, hideable: true,
        editor:flagsCombo, 
        renderer: Ext.ux.comboBoxRendererCustom(flagsCombo),
          listeners: {
          select: function(combo, recs, opts){
            //alert('hit');
            combo.fireEvent('blur'); 
          }
        },
      },
    ],
    bbar: Ext.create('Ext.PagingToolbar', {
        store: storeTable,
        pageSize: pageSize,
        id: 'pagingToolBar',
        displayInfo: true,
        displayMsg: 'Displaying jobs {0} - {1} of {2}',
        emptyMsg: "No Jobs to display",
        listeners: {
          change : function(thisd, params){   
            console.log(params);    
            globals.totalPages = params.total;
            globals.fromRecord = params.fromRecord;
            globals.toRecord = params.toRecord;

            activePage = params.currentPage;
            /*
            $.ajax({
              type: "POST",
              url: 'http://88.150.132.66/V2/index.php/home/saveCurrentPage',
              data: {'currentPage': activePage,'ci_csrf_token':''},
              dataType:'json',
              success: function(data){
                if(data.status=='1'){
                  //var grid = Ext.getCmp('gridPanelHourly');
                }else{
                  Ext.Msg.alert('Error', data.msg);
                }
              }
            });
            */
          }
        }
    }),
    renderTo: 'jobs-list'
  });

  var panel = Ext.getCmp('gridPanelHourly');
  panel.show();
  panel.doLayout();
  var count = 0;
  storeTable.on({ 'load': function (store, records, successful){
    if(count==0){
      storeTable.loadData(store.getProxy().getReader().rawData.store,false);
      storeTable.load();
              
      var panel = Ext.getCmp('gridPanelHourly');
      panel.show();
      panel.doLayout(); 
      count++;
    }
    globals.storeLoaded = true;
    }
  });


  //clear stuff on load
  Ext.getCmp('method_search').setValue('');



});
</script>