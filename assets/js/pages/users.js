if($('#users-list').length>0){

var pageSize = 25;
var searchUrl = '';
var count = 0;
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

  var storeTable = Ext.create('Ext.data.JsonStore', {
    count: 0,
    listeners: {
      load: function () {
        console.log(this);
      }
    },
    sorters: [{
      property: 'us_lastlogin',
      direction: 'DESC'
    }],
    sortRoot: 'us_lastlogin',
    sortOnLoad: false,
    remoteSort: true,
    pageSize: pageSize,
    fields: ['us_id', 'us_firstName', 'us_surname', 'us_company', 'us_joined', 'us_lastlogin', 'us_verified', 'us_blocked', 'role_name'],
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
  storeTable.loadData([], false);
  storeTable.getProxy().url = base_url + 'user/results/' + searchUrl;
  console.log(storeTable.getProxy().url);

  storeTable.load();
  console.log(storeTable.count);


  var gridPanelHourlyVar = Ext.create('Ext.grid.Panel', {
    id: 'gridPanelHourly',
    width: '100%',
    height: $(document).height(),
    hidden: true,
    title: 'Users',
    store: storeTable,
    stateful: true,
    XStateful: true,
    selModel: new Ext.selection.RowModel({
      mode: "MULTI"
    }),
    stateId: 'stateGridUsers',
    viewConfig: {
      getRowClass: function (record) {
      }
    },
    listeners: {
      itemdblclick: function (dv, record, item, index, e) {
        //alert('working');
        var id = gridPanelHourlyVar.store.getAt(index).data.us_id;
        console.log(id);
        window.location.href = base_url+"user/add/" + id;
      },
      
      itemclick: function (dv, record, item, index, e) {
        console.log(index);
        globals.currentEditId = gridPanelHourlyVar.store.getAt(index).data.us_id;
        globals.indexID = index;
      }
    
    },
    plugins: [
      Ext.create('Ext.grid.plugin.CellEditing', {
        clicksToEdit: 1
      })
    ],
    columns: [{
        text: 'id',
        dataIndex: 'us_id',
        flex: 1,
        //hidden: true,
        //hideable: false
      },
      {
        text: 'First Name',
        dataIndex: 'us_firstName',
        flex: 1,
      },
      {
        text: 'Surname',
        dataIndex: 'us_surname',
        flex: 1,
      },
      {
        text: 'Company',
        dataIndex: 'us_company',
        flex: 1,
      },
      {
        text: 'Joined',
        dataIndex: 'us_joined',
        flex: 1,
      },
      {
        text: 'Last Login',
        dataIndex: 'us_lastlogin',
        flex: 1,
      },
      {
        text: 'Verified',
        dataIndex: 'us_verified',
        flex: 1,
        renderer:function(value, metaData, record, row, col, store, gridView){
          if(value=='0'){
            return 'N';
          }else{
            return 'Y';
          }
        } 
      },
      {
        text: 'Blocked',
        dataIndex: 'us_blocked',
        flex: 1,
        renderer:function(value, metaData, record, row, col, store, gridView){
          if(value=='0'){
            return 'N';
          }else{
            return 'Y';
          }
        } 
      },
      {
        text: 'Role',
        dataIndex: 'role_name',
        flex: 1,
      },
    ],
    renderTo: 'users-list'

  });

  var panel = Ext.getCmp('gridPanelHourly');
  panel.show();
  panel.doLayout();
  //Ext.getCmp('pagingToolBar').moveFirst();
});

}