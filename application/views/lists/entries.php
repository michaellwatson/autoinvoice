<?php 
$path = dirname(dirname(__FILE__)).'/../';
$CI =& get_instance();
$CI->load->model('Advertcategorymodel');

?>  <div class="row p-3">
      <div class="d-inline p-2">
        <h1><?php echo $table_name;?> List View</h1>
      </div>
      <?php if($create && isset($name)){ ?> 
        <div class="d-inline p-2">
          <div class="row" style="padding:15px;padding-top:6px;">
            <a href="<?php echo base_url('Post/create_form/1?formID='. $formID);?>" class="btn btn-primary">Add <?php echo $name;?></a>
          </div>
        </div>
      <?php } ?>
      <?php 
        if((int)$user['us_role']===1){
          if(isset($import) && isset($name)){
          ?>
          <div class="d-inline p-2">
            <div class="row" style="padding:15px;padding-top:6px;">
              <a href="<?php echo base_url('import/documents/'. $formID);?>" class="btn btn-primary">Import into <?php echo $name;?></a>
            </div>
          </div>
          <?php 
        }
      } ?>
    </div>
    <?php //echo $_SESSION[$default_table_name]['search_url'];?>
    <div id="entries-list"></div>

<input type="hidden" value="<?php echo $formID;?>" name="formID" id="formID">

<script language="javascript">

    //sort ASC/DESC
  var sortOrders = Ext.create('Ext.data.Store', {
      fields: ['name', 'value'],
      data: [
          {
            'name': 'Not Set',
            'value': 0
          },
          {
            'name': 'Newest',
            'value': 'ASC'
          },
          {
            'name': 'Oldest',
            'value': 'DESC'
          },
      ]
  });

  var storeTable = Ext.create('Ext.data.JsonStore', {
  	id: 'storeTable',
    count: 0,
    listeners: {
      load: function () {
        //console.log(this);
        //alert('hit');
      }
    },
    sorters: [{
      property: 'ad_id',
      direction: 'DESC'
    }],
    sortRoot: 'ad_id',
    sortOnLoad: false,
    remoteSort: true,
    pageSize: pageSize,
    fields: [<?php echo $strcolumns;?>],
    proxy: {
      type: 'ajax',
      reader: {
        type: 'json',
        root: 'store',
        totalProperty: 'totalProperty'
      }
    },
  });

  storeTable.currentPage = '1';
  //storeTable.loadData([], false);
  //storeTable.getProxy().url = base_url + 'entries/results?formID=' + $('#formID').val();
  console.log(storeTable.getProxy().url);

  //storeTable.load();
  console.log(storeTable.count);
  //sort ASC/DESC end
  //

  <?php $tool_count = 0;?>
  var toolbars<?php echo $tool_count;?> = {
  xtype : 'toolbar',
  id: 'topToolbar',
  dock: 'top',
  enableOverflow: true,
  items: [
    <?php 
    $search_count = 0;
    foreach($search_fields as $f){
      $search_count++;
      //echo $search_count;
      $module_loaded = false;
      if(is_dir($path.'modules/'.$f['fi_type'])){
        //method exists would be more elegant
        try{
          $field_name = Modules::run(strtolower($f['fi_type']).'/'.ucfirst($f['fi_type']).'/get', 'field_name');
          if($field_name == $f['fi_type']){
            
            echo Modules::run(strtolower($f['fi_type']).'/'.ucfirst($f['fi_type']).'/search_field', $f);

            $module_loaded = true;
          }
        } catch(Exception $e){
          $module_loaded = false;
        }
      }

      if($module_loaded == false){

        if($f->adv_field_type == 3){
          ?>
          {
            xtype: 'combobox',
            id: '<?php echo $f->adv_column;?>',
            emptyText: 'Search <?php echo $f->adv_text?>',
            width: 300,
            hideTrigger: true,
            minChars: 1,
            triggerAction: 'query',
            typeAhead: true,
            store: {
              fields: ['text'],
              proxy: {
                type: 'ajax',
                url: url+'Entries/query/<?php echo $f->adv_column?>'
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
              	try{
                	combo.setValue('<?php if(isset($_SESSION['search_fields'][$formID][$f->adv_column])){ echo $_SESSION['search_fields'][$formID][$f->adv_column]; };?>');
            	}catch{

              	}
            }
            }
          },
          <?php 
        } else if($f->adv_field_type == 5){
          ?>
          {
            xtype: 'combobox',
            id: '<?php echo $f->adv_column;?>',
            emptyText: 'Search <?php echo $f->adv_text?>',
            width: 300,
            hideTrigger: true,
            minChars: 1,
            triggerAction: 'query',
            typeAhead: true,
            store: {
              fields: ['text'],
              proxy: {
                type: 'ajax',
                url: url+'Entries/query/<?php echo $f->adv_column?>'
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
                //combo.setValue('');
                combo.setValue('<?php if(isset($_SESSION['search_fields'][$formID][$f->adv_column])){ echo $_SESSION['search_fields'][$formID][$f->adv_column]; };?>');
              }
            }
          },
          <?php 

        }
      }
          if($search_count >= 5){
            $search_count = 0;
            $tool_count++;
            ?>
            ]
          }
          var toolbars<?php echo $tool_count;?> = {
            xtype : 'toolbar',
            id: 'topToolbar<?php echo $tool_count;?>',
            enableOverflow: true,
            dock: 'top',
            items: [
            <?php
          }
    } 
    ?>   

    {
      xtype: 'combobox',
      id: 'sort_order',
      fieldLabel: 'Sort Order',
      forceSelection: true,
      labelWidth: 90,
      width: 250,
      displayField: 'name',
      valueField: 'value',
      editable: false,
      store: sortOrders,

      listeners: {
        change: function(combo) {
          //alert('hit');
          if(globals.storeLoaded == true){
            doSearch();
            //alert('hit');
          }
        },
        afterRender:function(combo) {
          <?php if(isset($_SESSION[$default_table_name]) && isset($_SESSION[$default_table_name]['sort_order'])){?>
          combo.setValue('<?php echo $_SESSION[$default_table_name]['sort_order'];?>');
          <?php } ?>
        }
      }
    },

  ]

};

var pageSize = 25;
var searchUrl = '';
var count = 0;
var globals = {
    currentEditId:'',
    totalPages:'',
    loaded:false,
    fromRecord:'',
    toRecord:'',
    storeLoaded:false,
    visibleColumns:'',  
    indexID:''  ,
    color:''  
}
var table_name = '<?php echo $table_name;?>';
var formID = '<?php echo $formID;?>';
var sortOrder = 'DESC';

function doSearch(storeTable){
  
  try {
    <?php foreach($search_fields as $f){ ?>
      var <?php echo $f->adv_column;?>        = Ext.getCmp('<?php echo $f->adv_column;?>').getValue();
    <?php } ?>
    var hasNonNullValue = false;
    <?php foreach($search_fields as $f){ ?>
      if (<?php echo $f->adv_column;?> !== null && <?php echo $f->adv_column;?> !== '') {
        hasNonNullValue = true;
        //console.log(hasNonNullValue+'###');
      }
    <?php } ?>

    sortOrder = Ext.getCmp('sort_order').getValue();

    searchUrl = '?<?php foreach($search_fields as $f){ echo $f->adv_column; ?>='+<?php echo $f->adv_column;?>+'&<?php } ?>';
    var storeTable = Ext.getStore('storeTable');
    storeTable.currentPage = '1';
    storeTable.loadData([],false);
    storeTable.getProxy().url = base_url + 'entries/results'+searchUrl+'formID=' + $('#formID').val() +'&sort_order='+sortOrder;
    //storeTable.load();
    if(hasNonNullValue){
      storeTable.load({
        callback: function(records, operation, success) {
          if (success) {
            // Refresh the paging toolbar
            //alert('hit');
            //console.log('pagingToolBar');
            Ext.getCmp('pagingToolBar').doRefresh();
          }
        }
      });
     // console.log(storeTable.count);
      
      
      var panel = Ext.getCmp(table_name);
      panel.show();
      panel.doLayout(); 
      Ext.getCmp('pagingToolBar').moveFirst();  
    }
  } catch(err) {
    //console.log(err);
  }
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
    Ext.state.Manager.setProvider(
      Ext.create('Ext.state.CookieProvider')
    );
    var thirtyDays = new Date(new Date().getTime() + (1000 * 60 * 60 * 24 * 30));
    Ext.state.Manager.setProvider(new Ext.state.CookieProvider({
      expires: thirtyDays
    }));
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
    //'Ext.ux.grid.Printer',
    //'Ext.ux.RowExpander'
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
          url: base_url+'Entries/update_flag',
          data: {
            'id': globals.currentEditId,
            'ci_csrf_token': '',
            'val': newValue
          },
          dataType: 'json',
          success: function (data) {
            if (data.status == '1') {
              //alert('hit');
              var grid = Ext.getCmp(table_name);
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

  var gridPanelHourlyVar = Ext.create('Ext.grid.Panel', {
    id: table_name,
    width: '100%',
    height: $( window ).height()-200,
    hidden: true,
    title: '<?php echo $table_name; ?>',
    store: storeTable,
    stateId: table_name,
    stateful: true,
    XStateful: true,
    stateEvents: ['columnresize', 'columnmove', 'show', 'hide'],
    selModel: new Ext.selection.RowModel({
      mode: "MULTI"
    }),
    stateId: 'state_'+table_name,
    tools: [{
            type: 'maximize',
            tooltip: 'Full Screen',
            handler: function(event, toolEl, panel) {
                var win = new Ext.Window({
                    title: table_name,
                    autoScroll: false,                
                    closeAction: 'hide',
                    constrainHeader: true,
                    resizable: false,
                    floatable: false,
                    layout: 'fit',
                    constrain: true,
                    items: [gridPanelHourlyVar],
                    listeners: {
                        show: function(window, eOpts) {
                            gridPanelHourlyVar.tools.maximize.hide();
                        },
                        beforehide: function(window, eOpts) {
                            //console.log("beforehide");
                            window.items.first().tools.maximize.show();
                            window.items.first().el.appendTo(Ext.get(gridPanelHourlyVar.renderTo));
                            //window.items.first().el.setHeight(500);
                            //window.items.first().el.repaint();
                        },
                        hide: function(window, eOpts) {
                            //console.log("hide");
                            location.reload();
                        }
                    }
                });
                win.show().maximize();
            }
        }],
    viewConfig: {
      getRowClass: function (record) {
        //console.log(record.get('ad_flag'));
        var newClass = '';

          switch (parseInt(record.get('ad_flag'))) {
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
        var id = gridPanelHourlyVar.store.getAt(index).data.ad_id;
        ///alert(id);
        $.ajax({
            url: base_url + '/Recordaccess/checklock/' + id, // Replace with your actual URL
            type: 'POST',
            dataType: 'json',
            data: {formID: $('#formID').val()},
                        success: function(response) {
                if (response.status === 'locked') {
                    //console.log('Record is locked by another user');
                    // Do something if the record is locked by another user
                    Ext.Msg.alert('Error', response.message);

                    setTimeout(function(){
                      Ext.Msg.alert('Would you like to take over?', '<div style="width:100%;text-align:center;">Please ensure the other user has left the record, otherwise data loss may occur.<br><br><button class="btn btn-danger take_over ladda-button" data-formid="'+$('#formID').val()+'" data-id="'+id+'" data-style="expand-right">Take over</button></div>');
                    }, 2000);

                } else if (response.status === 'unlocked') {
                    //console.log('Record is unlocked');
                    // Do something if the record is unlocked
                    window.location.href = base_url+"Post/create_form/1/"+id+"?formID="+$('#formID').val();
                } else {
                    //console.log('Error: ' + response.message);
                    // Do something if an error occurred
                }
            },
            error: function() {
                //console.log('Error occurred');
            }
        });
        
      },
      itemclick: function (dv, record, item, index, e) {
        globals.currentEditId = gridPanelHourlyVar.store.getAt(index).data.ad_id;
        globals.indexID = index;
        globals.color = gridPanelHourlyVar.store.getAt(index).data.temp_flat_color;

        var id = gridPanelHourlyVar.store.getAt(index).data.ad_id;
        doubletap(id);
      }
    },
    plugins: [
      Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 1
      })
    ],
    columns: [{
        text: 'id',
        dataIndex: 'ad_id',
        flex: 1,
        hidden: true,
        hideable: false
      },
      <?php 
        //$i = 5;
        $c = 0;
        foreach($fields as $f){
          //if($c<=$i){


          

      $module_loaded = false;
      //echo $path.'modules/'.$f['fi_type'];
      if(is_dir($path.'modules/'.$f['fi_type'])){
        //method exists would be more elegant
        try{
          $field_name = Modules::run(strtolower($f['fi_type']).'/'.ucfirst($f['fi_type']).'/get', 'field_name');
          if($field_name == $f['fi_type']){
            $data_new = array();
            $data_new['f'] = $f;
            $result = Modules::run(strtolower($f['fi_type']).'/'.ucfirst($f['fi_type']).'/list', $data_new);
            echo $result;
            $module_loaded = true;
          }
        }catch(Exception $e){
          $module_loaded = false;
        }
      }
      

        if($module_loaded===false){
          switch($f['fi_type']){
            case 'dropdown':

            $ds = $CI->Advertcategorymodel->getData($f['adv_datasourceId']);
            ?>
            {
              xtype: 'gridcolumn',
              text: '<?php echo $f['adv_text'];?>',
              dataIndex: '<?php echo 'ad_'.$f['adv_column'];?>',
              flex: 1,
              editor: {
                  xtype: 'combobox',
                  allowBlank: false,
                  displayField: 'text',
                  valueField: 'id',
                  queryMode: 'local',
                  store: new Ext.data.SimpleStore({
                    fields: [ "id", "text" ],
                    data: [
                      <?php foreach($ds as $d){ ?>
                        [ <?php echo $d['ds_id'];?>, "<?php echo $d['ds_value'];?>" ],
                      <?php } ?>
                    ]
                  }),
                  listeners: {
                    change: function(field, newVal, oldVal) {
                      
                      gridPanelHourlyVar.setLoading(true);
                      var id = gridPanelHourlyVar.store.getAt(globals.indexID).data.ad_id;
                      $.ajax({
                          type: "POST",
                          url: base_url + 'Entries/update_dropdown/',
                          data: {
                              'entry_id': id,
                              'ci_csrf_token': '',
                              'field': '<?php echo 'ad_'.$f['adv_column'];?>',
                              'value': field.value
                          },
                          dataType: 'json',
                          success: function(data) {
                              if (data.status == '1') {
                                doSearch();
                              } else {
                                  Ext.Msg.alert('Error', data.msg);
                              }
                              gridPanelHourlyVar.setLoading(false);
                          }
                      });
                    }
                  }
              },
              renderer:function(val,meta,record){
                return record.get('<?php echo 'ad_'.$f['adv_column'];?>');
              },
            },
            <?php 
            break;
            default:
            ?>
            {
              text: '<?php echo $f['adv_text'];?>',
              dataIndex: '<?php echo 'ad_'.$f['adv_column'];?>',
              flex: 1,
            },
            <?php 
            break;
          }
        //$c++;
        }
      } ?>
      { 
        text: 'Flag', dataIndex: 'ad_flag', flex: 1, hidden: false, hideable: true,
        editor:flagsCombo, 
        renderer: Ext.ux.comboBoxRendererCustom(flagsCombo),
          listeners: {
            select: function(combo, recs, opts){
              //alert('hit');
              combo.fireEvent('blur'); 
            }
          },
      },
      <?php if($this->Usermodel->has_permission('delete', $formID)){ ?>
      {
          xtype: 'actioncolumn',
          text:'Delete',
                width: 60,
                items: [{
                    icon: base_url+'assets/images/icons/cancel.png',
                    handler: function(grid, rowIndex, colindex) {

                      var id = grid.store.getAt(rowIndex).data.ad_id;

                      Ext.MessageBox.confirm('Delete', 'Are you sure ?', function(btn){
                        if(btn === 'yes'){
                            //some code
                    
                            $.ajax({
                               type: "POST",
                               url: base_url+'Entries/delete?formID='+$('#formID').val(),
                               data: {'id': id},
                               dataType:'json',
                               success: function(data)
                               {
                                 if(data.status=='1'){
                                   var grid = Ext.getCmp(table_name);
                                  var selection = grid.getView().getSelectionModel().getSelection()[0];
                                  //console.log(selection);
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
        },
      <?php } ?>
      <?php if(isset($pdf)){ ?>
      {
        xtype: 'actioncolumn',
        text:'PDF',
                width: 60,
                items: [{
                    icon: base_url+'assets/images/icons/pdf_icon.png',
                    handler: function(grid, rowIndex, colindex) {
                      var id = grid.store.getAt(rowIndex).data.ad_id;
                      window.location.href = base_url+'Post/gen_pdf/1/'+id+'/pdf';
                    }
                }]
      },
      <?php } ?>
    ],
    dockedItems: [<?php $k = 0; while($k <= $tool_count){?>toolbars<?php echo $k;?>,<?php $k++;}?>],
    bbar: Ext.create('Ext.PagingToolbar', {
      store: storeTable,
      pageSize: pageSize,
      id: 'pagingToolBar',
            displayInfo: true,
            displayMsg: 'Displaying entries {0} - {1} of {2}',
            emptyMsg: "No entries to display",
      listeners: {
                  change : function(thisd, params){   
                  
                  //console.log(params);
                  //console.log('###');
                  if (typeof params !== 'undefined' && typeof params.total !== 'undefined'){
                  
                    globals.totalPages = params.total;
                    globals.fromRecord = params.fromRecord;
                    globals.toRecord = params.toRecord;
                    //console.log(globals);

                    activePage = params.currentPage;
                    
                    $.ajax({
                      type: "POST",
                      url: '<?php echo base_url('Entries/saveCurrentPage');?>',
                      data: {'currentPage': activePage, 'formID': formID, 'from': globals.fromRecord, 'to': globals.toRecord},
                      dataType:'json',
                      success: function(data){
                        if(data.status=='1'){
                          //var grid = Ext.getCmp('gridPanelHourly');
                        }else{
                          Ext.Msg.alert('Error', data.msg);
                        }
                      }
                    });
                  }
                  
        }
      }
    }),
    renderTo: 'entries-list'

  });

storeTable.currentPage = <?php echo $currentPage?>;

storeTable.loadData([],false);
var proxy = storeTable.getProxy()
<?php 

/*if(isset($_SESSION[$default_table_name]['search_url'])){?>
  proxy.url = '<?php echo $_SESSION[$default_table_name]['search_url'];?>';
  proxy.page = <?php echo $currentPage?>;
  storeTable.loadPage(<?php echo $currentPage?>);
<?php }else{ */?>
  proxy.url = '<?php echo base_url('Entries/results')?>'+searchUrl;
  proxy.page = <?php echo $currentPage?>;
  storeTable.loadPage(<?php echo $currentPage?>);
<?php //} ?>


/*
storeTable.load({
  callback: function(){
    //gridPanelHourlyVar.columns[0].setVisible(false);
    //gridPanelHourlyVar.columns[0].hide();
  }
});
*/

storeTable.on({ 'load': function (store, records, successful){

    if(count==0){
      //console.log(store.getProxy().getReader().rawData);
      //storeTable.loadData(store.getProxy().getReader().rawData.store,false);
      //storeTable.load();
      
      var panel = Ext.getCmp(table_name);
      panel.show();
      panel.doLayout(); 
      count++;
    }
    //allList();
    globals.storeLoaded = true;
    //storeTable.pageSize = globals.totalPages;
  }
})
//Ext.getCmp('pagingToolBar').refresh();  
  var panel = Ext.getCmp(table_name);
  panel.show();
  panel.doLayout();
  //Ext.getCmp('pagingToolBar').moveFirst();
});
var clickTimer = null;

function doubletap(id) {
    if (clickTimer == null) {
      clickTimer = setTimeout(function () {
        clickTimer = null;
        //console.log("single");

      }, 500);
    } else {
      
      clearTimeout(clickTimer);
      clickTimer = null;
      //window.location.href = base_url+"Post/create_form/1/"+id+"?formID="+$('#formID').val();
      /*
      $.ajax({
            url: '/Recordaccess/checklock/' + id, // Replace with your actual URL
            type: 'POST',
            dataType: 'json',
            data: {formID: $('#formID').val()},
            success: function(response) {
                if (response.status === 'locked') {
                    console.log('Record is locked by another user');
                    // Do something if the record is locked by another user
                    Ext.Msg.alert('Error', response.message);
                } else if (response.status === 'unlocked') {
                    console.log('Record is unlocked');
                    // Do something if the record is unlocked
                    //window.location.href = base_url+"Post/create_form/1/"+id+"?formID="+$('#formID').val();
                    //
                    //window.location.href = base_url+"Post/create_form/1/"+id+"?formID="+$('#formID').val();
                } else {
                    console.log('Error: ' + response.message);
                    // Do something if an error occurred
                }
            },
            error: function() {
                console.log('Error occurred');
            }
        });
        */

    }
}



$(document).on("click", ".take_over", function(e) {

  let formid = $(this).attr('data-formid');
  let id = $(this).attr('data-id');

  //alert(formid);
  //alert(id);

  var l = Ladda.create(document.querySelector('.take_over'));
  l.start();

  $.ajax({
    url: '/Recordaccess/delete/' + id, // Replace with your actual URL
    type: 'POST',
    dataType: 'json',
    data: {formID: formid},
    success: function(response) {

      if (response.status === 'success') {
        
        //console.log('Record deleted successfully');
        // Do something if the record is deleted successfully
        l.stop();
        window.location.href = base_url+"Post/create_form/1/"+id+"?formID="+$('#formID').val();
      
      } else {
        
        console.log('Error: ' + response.message);
        // Do something if an error occurred
      
      }
    },
    error: function() {
      console.log('Error occurred');
    }
  });

});
</script>