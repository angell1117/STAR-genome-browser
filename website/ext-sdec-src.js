var base_url= "http://tabit.ucsd.edu/";
Math.gcd = function(a, b) {
    var a = a ? parseInt(a) : 1;
    var b = b ? parseInt(b) : 1;
    var n = a > b ? a: b;
    var d = a > b ? b: a;
    var r = 0;
    while (true) {
        r = n % d;
        if (r == 0) break;
        n = d;
        d = r
    }
    return d
};
Math.simplify = function(n, d) {
    var n = n ? parseInt(n) : 1;
    var d = d ? parseInt(d) : 1;
    var gcd = Math.gcd(n, d);
    return [n / gcd, d / gcd]
};
rColor = function(s) {
    if(s.indexOf("#") == 0) s = s.substring(1);
    var sR = s.substring(0,2);
    var sG = s.substring(2,4);
    var sB = s.substring(4);
    sR = 255 - parseInt(sR, 16);
    sG = 255 - parseInt(sG, 16);
    sB = 255 - parseInt(sB, 16);
    var nR = sR.toString(16);
    var nG = sG.toString(16);
    var nB = sB.toString(16);
    if(nR.length == 1) nR = '0' + nR;
    if(nG.length == 1) nG = '0' + nG;
    if(nB.length == 1) nB = '0' + nB;
    return "#" + nR + nG + nB
};
function find(arr, param, value) {
  var hit = null;
  Ext.each(arr,
    function(item) {
      if (item[param] && item[param] == value) {
        hit = item;
        return false
      }
  });
  return hit
};
function getSourceData(row) {
  var obj = {};
  if(!row.center) return obj;

  obj['Access permission'] = row.access || 'private';
  obj['Institute'] = row.center || '';
  obj['Experimental type'] = row.data_type || '';
  obj['Mark name'] = row.mark || '';
  obj['Organism'] = row.organism || '';
  obj['Track name'] = row.track_name || '';
  obj['Data URL'] = row.track_url || '';
  obj['Cell/Tissue'] = row.type || '';
  obj['Upload date'] = row.uploaddate || '';
  obj['Data source'] = row.url_self || '';
  obj['Track owner'] = row.user_id || '';
  obj['Track type'] = row.track_type || 'ReadsTrack';
  if(row.track_type == 'ModelsTrack') obj['Track type'] = 'Gene model';
  else if(row.track_type == 'ReadsTrack') obj['Track type'] = 'Short reads';
  else if(row.track_type == 'MethTrack') obj['Track type'] = 'CG/CHG/CHH';
  else if(row.track_type == 'HiCTrack') obj['Track type'] = 'HiC data';
  else if(row.track_type == 'IntensityTrack') obj['Track type'] = 'Intensity';
  return obj
};
Array.prototype.remove = function(item) {
    for (var i = 0,
    len = this.length; i < len; i++) {
        if (this[i] == item) {
            this.splice(i, 1);
            return true
        }
    }
    return false
};
Array.prototype.insert = function(index, item) {
    var index = parseInt(index) || null;
    if (index == null || index >= this.length || index < 0) {
        this.push(item);
        return
    }
    this.splice(index, 0, item)
};
Array.prototype.search = function(item) {
    for (var i = 0,
    len = this.length; i < len; i++) {
        if (this[i] == item) {
            return i
        }
    }
    return - 1
};
if (!Ext) {
    var html = "<div style='margin:auto; width:600px; border:solid black 1px; padding:15px; margin-top:100px; font-family:arial; font-size:13px;'>";
    html += "<h1>Error: Ext not found</h1><br />";
    html += "<p>This application could not find the ExtJS Javascript libraries and consequently, cannot run.</p>";
    html += "<ul style='padding:10px; list-style:circle; font-size:12px;'>";
    html += "<li>Check that ExtJS libraries are included in the document &lt;head&gt; section</li>";
    html += "<li>Check that ExtJS libraries are included before this error checking routine</li>";
    html += "<li>Check that ExtJS libraries are being referenced from a correct URL</li>";
    html += "<li>Check that your internet connection is active</li>";
    html += "</ul>";
    html += "<p>Please notify your website administrator of this problem so that it may be fixed.</p>";
    html += "<p>ExtJS is available from <a href='http://www.extjs.com'>http://www.extjs.com</a></p>";
    html += "<p><a href='http://www.extjs.com/download'><img src='img/extjs.png' alt='Get ExtJS 2' /></a>";
    html += "</div>";
    window.onload = function() {
        document.body.innerHTML = html
    }
} else {
    Ext.Ajax.timeout = 600000;
    window.total_uploaded = 0;
    Ext.QuickTips.init();
}
var WebApp = (function() {
    function checkBrowser() {
        if (Ext.isGecko) {
            return true
        } else if (Ext.isIE) {
         var canvas = document.createElement("canvas");
           if(canvas.getContext) Ext.isIE9 = true;
     else Ext.isIE8 = true;
           return true
        } else if (Ext.isChrome) {
            return true
        } else if (Ext.isOpera) {
            return true
        } else if (Ext.isSafari) {
            return true
        } else {
            return false
        }
    };
    return {
        checkBrowser: checkBrowser
    }
})();

var AnnoJ = (function() {
    var GUI = {};
    function init() {
        if (!WebApp.checkBrowser()) {
            return false
        }
        try {
            GUI = buildGUI()
        } catch(e) {
            Ext.MessageBox.hide();
            return false
        };
    };
  var recordType = Ext.data.Record.create([
    { name: 'track_name'},
    { name: 'track_id'},
    { name: 'track_type'},
    { name: 'organism'},
    { name: 'mark'},
    { name: 'data_type'},
    { name: 'type'},
    { name: 'center'},
    { name: 'track_user'},
    { name: 'track_url'},
    { name: 'user_id'},
    { name: 'url_self'},
    { name: 'access'},
    { name: 'uploaddate'},
    { name: 'organism'}
  ]);
  var settings = {
    conf_id: '',
    name: 'new_configuration',
    description: ''
  }
  var myFormPanel = function(config) {
    myFormPanel.superclass.constructor.call(this, config);
      this.addEvents({
          'datauploaded': true
      })
  };
  Ext.extend(myFormPanel, Ext.form.FormPanel);
  var uploadForm = new myFormPanel({
    frame:true,
    title:"New Tracks",
    autoScroll: true,
    bodyStyle:"padding:5px 5px 0",
    items:[ {
        xtype:'fieldset',
        title:'Track Meta',
        defaultType: 'textfield',
        collapsible: true,
        items: [{     
          fieldLabel: 'Access permission',     
          labelWidth: 5,
          xtype:'radiogroup',    
          columns : 1,
          items:[
            new Ext.form.Radio({       
              checked: true,
              name : "access",       
              inputValue : "private",    
              boxLabel : "Private(Only you)"      
            }),
            new Ext.form.Radio({       
              name : "access",       
              inputValue : "group", 
              boxLabel : "Group(Group members)"   
            }),     
            new Ext.form.Radio({       
              name : "access",       
              inputValue : "public",    
              boxLabel : "Public(All users)"      
            })
          ]},
          {
            xtype:'radiogroup',    
            fieldLabel: 'Track type',     
            columns : 1,    
            items:[ 
              new Ext.form.Radio({ 
                checked: true,
                name : "tracktype",      
                inputValue : "ReadsTrack",       
                boxLabel : "Short reads(Eland, Bowtie, BED, etc.)"
              }), 
              new Ext.form.Radio({       
                name : "tracktype",    
                inputValue : "MethTrack",       
                boxLabel : "CG/CHG/CHH methylation sites"
              }), 
              new Ext.form.Radio({       
                name : "tracktype",       
                inputValue : "IntensityTrack",    
                boxLabel : "Intensity values(WIG, BED, etc.)"      
              })
            ]},
          {
            xtype:'radiogroup',    
            fieldLabel: 'Organism',
            id:'x-organism',
            columns : 1,    
            items:[ 
              new Ext.form.Radio({ 
                checked: true,
                name : "organism",      
                inputValue : "Homo sapiens",       
                boxLabel : "Homo sapiens"
              }), 
              new Ext.form.Radio({       
                name : "organism",    
                inputValue : "Mus musculu",       
                boxLabel : "Mus musculu"
              }), 
              new Ext.form.Radio({       
                 name : "organism",       
                 inputValue : "Arabidopsis thaliana",    
                 boxLabel : "Arabidopsis thaliana"      
              })
          ]},
          {
            fieldLabel:"Institute",
            xtype:"textfield",
            name:"institute",
            id:"institute",
            value: "e.g UCSD",
            width:200
          }
          ]},
          {
            xtype:'radiogroup',    
            fieldLabel: 'Data source',
            id:'x-datasource',
            columns : 2,
            items:[ 
              new Ext.form.Radio({ 
                checked: true,
                name : "datasource",      
                inputValue : "uploadn",       
                boxLabel : "Data track URL",
                listeners : {
                  check : function(checkbox, checked) {
                    if (checked) {
                      if(swfu) swfu.cancelQueue();
                      if(swfu1) swfu1.cancelQueue();
                      uploadForm.findById('x-btnSubmit').setDisabled(false);
                      uploadForm.findById('x-trackname').setDisabled(false);
                      uploadForm.findById('x-trackurl').setDisabled(false);
                      uploadForm.findById('selfdefine').setDisabled(true);
                      uploadForm.findById('x-format').setDisabled(true);
                                                            
                      uploadForm.findById('x-group-upload').setDisabled(true);
                      uploadForm.findById('x-group-upload').setVisible(false);
                      uploadForm.findById('x-group-url').setDisabled(false);
                      uploadForm.findById('x-group-url').setVisible(true)
                    }
                 }}
            }), 
            new Ext.form.Radio({       
              name : "datasource",       
              inputValue : "uploady",    
              boxLabel : "Upload data files", 
              listeners : {
                check : function(checkbox, checked) {
                  if (checked) {
                  if(!swfu) swfu = new SWFUpload(x_settings);
                  if(!swfu1) swfu1 = new SWFUpload(x_settings1);
                  uploadForm.findById('x-trackname').setDisabled(true);
                  uploadForm.findById('x-trackurl').setDisabled(true);
                  uploadForm.findById('x-format').setDisabled(false);

                  uploadForm.findById('x-group-upload').setDisabled(false);
                  uploadForm.findById('x-group-upload').setVisible(true);
                  uploadForm.findById('x-group-url').setDisabled(true);
                  uploadForm.findById('x-group-url').setVisible(false);
                  if(window.total_uploaded == 0)
                  uploadForm.findById('x-btnSubmit').setDisabled(true)
               }
            }}
           })
          ]},
        {
        xtype:'fieldset',
        title:'Remote track URLs ',
        id: 'x-group-url',
        collapsible: true,
        items:[
          {
            id: 'x-label-name',
            html: '<div>Track name (terminated by newline)</div>'
          },
          {
            xtype:'textarea', 
            width: 250,   
            id:'x-trackname',
            allowBlank: false,
            emptyText: 'e.g. trackname1\n  trackname2\n  trackname3'
          },
          {
            id: 'x-label-url',
            html: '<div>Track URL (terminated by newline)</div>'
          },
          {
            xtype:'textarea', 
            width: 250,   
            id:'x-trackurl',
            allowBlank: false,
            emptyText: 'e.g. http://urdomain/urtrk1.php\n  http://urdomain/urtrk2.php\n  http://urdomain/urtrk3.php'
          }
       ]},
       {
        xtype:'fieldset',
        title:'Upload data files ',
        id: 'x-group-upload',
        collapsible: true,
        items:[
          {
            xtype: 'combo',
            fieldLabel: 'Data format',
            typeAhead: true,
            triggerAction: 'all',
            width: 100,
            grow: true,
            growMin: 10,
            growMax: 100,
            id: 'x-format',
            emptyText: '--select--',
            forceSelection: true,
            mode: 'local',
            displayField: 'id',
            store: new Ext.data.SimpleStore({
              fields: ['id'],
              data: [['BED'],['WIG'],['Eland'],['Bowtie'],['Mapview'],['SAM'],['GFF'],['Self-define']]
            }),
            listeners : {
            select : function(e) {
            if(e.getValue() == 'Self-define')
              uploadForm.findById('selfdefine').setDisabled(false);
            else uploadForm.findById('selfdefine').setDisabled(true);
            }
           }
         },
         {
           fieldLabel:"Or self-define",
           xtype:"textfield",
           name:"selfdefine",
           id:"selfdefine",
           disabled: true,
           maskRe: /[\-0-9:]/,
           regex: /^[\-0-9]+:[\-0-9]+:[\-0-9]+:[\-0-9]+:[\-0-9]+:[\-0-9]+$/,
           value: "1:2:3:4:5:6",
           width:100
         },
         {
           id: 'x-selfdefine',
           html:"<div>Self-defined format(chromosome:start:end:strand:value:sequence, separated by ':') allows users to specify how to extract information. Each field is a column index in the data file. For example: 1:2:3:4:6:-1 means chromosome, start position, end position, strand and value will be extracted from column 1, 2, 3, 4 and 6, respectively. There's no sequence information.For quick start, please download and try BED format data: <a href=\"http://tabit.ucsd.edu/sdec/manual/h3k4me1-demo.bed.tar.gz\">h3k4me1-demo.bed.tar.gz</a></div>"
         }, 
         {
           id: 'x-uploadswf',
           html:"<div> <div id=\"spanButtonPlaceHolder\"></div> <input id=\"btnCancel\" type=\"button\" value=\"Cancel Queue 1\" onclick=\"swfu.cancelQueue();\" disabled=\"disabled\" style=\"margin-left: 2px; font-size: 8pt; height: 22px;\" /> </div> <div> <div id=\"spanButtonPlaceHolder1\"></div> <input id=\"btnCancel1\" type=\"button\" value=\"Cancel Queue 2\" onclick=\"swfu1.cancelQueue();\" disabled=\"disabled\" style=\"margin-left: 2px; font-size: 8pt; height: 22px;\" /> <div id=\"divStatus\">0 Files Uploaded</div> </div> <div class=\"fieldset flash\" id=\"fsUploadProgress\"> </div>"
         }, 
         {
           id: 'x-message',
           html:"<div></div>"
         } 
        ]},
          {
            xtype:'button',
            style : 'margin-top: 30px',
            id: "x-btnSubmit", 
            text: "Submit Traks", 
            handler:function(){ 
              Ext.MessageBox.confirm('Confirm', 'Submit the tracks?', function(id){
                if(id == 'no') return;
                else{
                var para = uploadForm.getForm().getValues(true);
                Ext.Ajax.request({
                  url: base_url + 'sdec/ext-subtracks.php',
                  method: 'POST',
                  params: para,
                  failure: function(response, options) {
                    Ext.Msg.alert('Error','Communication error!')
                  },
                  success: function(response, options) {
                    if(!response) { 
                      Ext.Msg.alert('Error','Server error: no response!');
                      return
                    }
                    try {
                      response = Ext.util.JSON.decode(response.responseText)
                    } catch(ex) {
                      Ext.Msg.alert('Error','Illegal JSON string!');
                      return
                  }
                  if(response.success == false){
                    Ext.Msg.alert('Error',response.responseText);
                    return
                  }
                  TrackList.ds.baseParams = {
                    showmy: 'My tracks'
                  };
                  TrackList.ds.load({start:0, limit:40})
                  var gsm = TrackList.grid.getSelectionModel();
                  gsm.selectLastRow(true)
                  var r = TrackList.grid.getView().getRow(0);
                  if(r) r.style.backgroundColor = "#99cc99";
                  window.total_uploaded = 0;
                  uploadForm.findById('x-btnSubmit').setDisabled(true);
                  Ext.Msg.alert('Success', response.result.responseText);
                  return
                }
                });
               }
            })
          }}
        ]
      });
  uploadForm.on('datauploaded',
  function(e) {
    var para = uploadForm.getForm().getValues(true) + '&inquiry=inquiry';

    Ext.Ajax.request({
      url: base_url + 'sdec/ext-brwtrk.php',
      method: 'POST',
      params: para,
      failure: function(response, options) {
        Ext.Msg.alert('Error','Communication error!')
      },
      success: function(response, options) {
        if(!response) { 
          Ext.Msg.alert('Error','Server error: no response!');
          return
        }
        try {
          response = Ext.util.JSON.decode(response.responseText)
        } catch(ex) {
          Ext.Msg.alert('Error','Illegal JSON string!');
          return
        }
        if(response.success == false){
          Ext.Msg.alert('Error',response.responseText);
          return
        }

        var info = uploadForm.findById('x-message');
        info.body.dom.innerHTML = response.responseText;
        return
      }
    });
  });
 
  var TrackOption = (function() {
    var sm=new Ext.grid.CheckboxSelectionModel();
    var cm = new Ext.grid.ColumnModel([
      sm,
      {header:'Groups',dataIndex:'category', sortable: false},
      {header:'Name',dataIndex:'name', sortable: false}
    ]);
    var ds = new Ext.data.GroupingStore({
      url: base_url + 'sdec/ext-brwtrk.php',
      baseParams: {
           showoptions: 'options'
      },
      reader: new Ext.data.JsonReader({
        root: 'rows',
        totalProperty: 'count'
      },
      [
      { name: 'name'},
      { name: 'category'}
      ]),
      groupField: 'category'
    });
    var searchButton = new Ext.Button({
      iconCls: 'silk_server_go',
      text: 'Search', 
      scale: 'medium',
      cls:'x-btn',
      tooltip: 'Search tracks by selected options',
      handler: function() {
        var sm = grid.getSelectionModel();
        var rows = sm.getSelections();
        var cell_type = '';
        var center = '';
        var data_type = '';
        var mark = '';
        var organism = '';
        for(var i = 0; i < rows.length; i++){
          var cls = rows[i].data.category;
          var name = "'" + rows[i].data.name + "'";
          if(rows[i].data.name == '') continue;
            if(cls == 'cell' && cell_type == '') cell_type += name;
            if(cls == 'center' && center == '') center += name;
            if(cls == 'experiment' && data_type == '') data_type += name;
            if(cls == 'mark' && mark == '') mark += name;
            if(cls == 'organism' && organism == '') organism += name
            if(cls == 'cell' && cell_type != '') cell_type += ',' + name;
            if(cls == 'center' && center != '') center += ',' + name;
            if(cls == 'experiment' && data_type != '') data_type += ',' + name;
            if(cls == 'mark' && mark != '') mark += ',' + name;
            if(cls == 'organism' && organism != '') organism += ',' + name
        }
        TrackList.ds.baseParams = {
          searchoption: 'option tracks',
          type: cell_type,
          center: center,
          data_type: data_type,
          mark: mark,
          organism: organism
        };
        TrackList.ds.load({start:0, limit:40})
      }
    });
    var toolbar = new Ext.Toolbar({
      items: ['Select options', searchButton]
    });
    var grid = new Ext.grid.GridPanel({
      store: ds,
      cm:cm,
      sm:sm,
      tbar: toolbar,
      viewConfig:{
        forceFit:true
      },
      view:new Ext.grid.GroupingView()
    });               
    ds.load();
    return {
      grid: grid
    }
  })();
  var ConfList = (function() {
    var sm = new Ext.grid.CheckboxSelectionModel();
    var cm = new Ext.grid.ColumnModel([
      new Ext.grid.RowNumberer(),
      sm,
      {header:'View configs',dataIndex:'conf_name', sortable: true},
      {header:'Build date',dataIndex:'build_date', sortable: true},
      {header:'View/Edit',dataIndex:'conf_id', renderer:
      function (value, meta, record) {
        var formatStr = "<INPUT type='button' value='View', cls: 'x-btn-icon', onclick='javscript:return false;' class='view_conf'><INPUT type='button' value='Edit' onclick='javscript:return false;' class='edit_conf'>"; 
        return "<div class='controlBtn'>" + formatStr + "</div>";
      }.createDelegate(this)},
      {header:'Last view',dataIndex:'lastview_date', sortable: true},
      {header:'Times',dataIndex:'view_count', sortable: true},
      {header:'Description',dataIndex:'conf_desc', sortable: true}
    ]);
    var ds = new Ext.data.GroupingStore({
      url: base_url + 'sdec/ext-brwconf.php',
      baseParams: {
        showall: 'all configurations'
      },
      reader: new Ext.data.JsonReader({
        root: 'rows',
        idProperty: 'conf_id',
        totalProperty: 'count'
      },
      [{ name: 'conf_name'},
       { name: 'build_date'},
       { name: 'conf_id'},
       { name: 'conf_desc'},
       { name: 'lastview_date'},
       { name: 'view_count'}
      ]),
      sortInfo:{field:'lastview_date',direction:"DESC"},
      remoteSort: true
    });
    
    ds.on('load', function (records, successful, operation, eOpts) {
      var total = ds.getTotalCount();
      grid.toolbars[1].displayMsg = 'Total <span style="color:#2211ee">'+total+'</span> configurations';
    });
    var Toolbar = (function() {
      var removeItems = new Ext.Button({
        iconCls: 'silk_delete',
        text: 'Remove selected', 
        scale: 'medium',
        cls:'x-btn',
        tooltip: 'Remove selected configurations',
        handler: function() {
          var sm = grid.getSelectionModel();
          var rows = sm.getSelections();
          var confids = '';
          for(var i = 0; i < rows.length; i++){
            if(confids == '') confids += rows[i].data.conf_id;
            else confids += ',' + rows[i].data.conf_id;
          }

          Ext.MessageBox.confirm('Confirm', 'Remove selected ' + rows.length + ' configurations?', function(id){
          if(id == 'no') return;
          else{
          var para = {};
          para.confids = confids;
          para.action = 'remove';

          Ext.Ajax.request({
            url: base_url + 'sdec/ext-aj_conf.php',
            method: 'POST',
            params: para,
            failure: function(response, options) {
              Ext.Msg.alert('Error','Communication error!')
            },
            success: function(response, options) {
              if(!response) { 
                Ext.Msg.alert('Error','Server error: no response!');
                return
              }
              try {
                response = Ext.util.JSON.decode(response.responseText)
              } catch(ex) {
                Ext.Msg.alert('Error','Illegal JSON string!');
                return
              }
              if(response.success == false){
                Ext.Msg.alert('Error',response.responseText);
             return
              }

              ConfList.ds.load();
              ConfList.grid.getView().refresh();
              Ext.Msg.alert('Success',response.responseText);

              return
            }
          });
          }
        })
        }
      });
      var searchBox = new Ext.form.TextField({
        width: 200,
        selectOnFocus: true
      });
      searchBox.setValue('');
      searchBox.on('specialKey',
      function(config, event) {
        if (event.getKey() == event.ENTER) {
          var words = searchBox.getValue();
          ds.baseParams = {
            action: 'search',
            keyword: words
          };
          ds.load({start:0, limit:40});
        }
      });
    
      return {
        searchBox: searchBox,
        removeItems: removeItems
      }
    })();
    var toolbar = new Ext.Toolbar({
      items: [Toolbar.removeItems, '-','Search', Toolbar.searchBox]
    });
    var grid = new Ext.grid.GridPanel({
      store: ds,
      cm:cm,
      sm:sm,
      viewConfig:{
        forceFit:true,
        enableRowBody:true
      },
      enableDragDrop: true,
      ddGroup : 'mygrid-dd-option',
      ddText : 'Drag to Track Pool to view',
      tbar: toolbar,
      bbar:new Ext.PagingToolbar({
        pageSize:40,
        store: ds,
        displayInfo:true,
        displayMsg:'Browsing configurations',
        emptyMsg: 'No available configurations'
      })         
    });               
    ds.load();
    grid.on('cellclick', function (grid, rowIndex, columnIndex, e) {
      var btn = e.getTarget('.controlBtn');
      if (btn) {
        var t = e.getTarget();
        var record = grid.getStore().getAt(rowIndex);
        var control = t.className;
        switch (control) {
          case 'view_conf':
            var para = {};
            para.id = record.id;
            para.action = 'genhtml';
			console.log(para.id);

            Ext.Ajax.request({
              url: base_url + 'sdec/ext-aj_conf.php',
              method: 'POST',
              params: para,
              failure: function(response, options) {
                Ext.Msg.alert('Error','Communication error!')
              },
              success: function(response, options) {
                if(!response) { 
                Ext.Msg.alert('Error','Server error: no response!');
                return
                }
                try {
                response = Ext.util.JSON.decode(response.responseText)
                } catch(ex) {
                Ext.Msg.alert('Error','Illegal JSON string!');
                return
                }
                if(response.success == false){
                Ext.Msg.alert('Error',response.responseText);
                                       return
                }

                window.location.href = response.responseText;
                                return
              }
            });
            break;
          case 'edit_conf':
            var para = {};
            para.id = record.id;
            para.action = 'load';

            Ext.Ajax.request({
              url: base_url + 'sdec/ext-aj_conf.php',
              method: 'POST',
              params: para,
              failure: function(response, options) {
                Ext.Msg.alert('Error','Communication error!')
              },
              success: function(response, options) {
                if(!response) { 
                Ext.Msg.alert('Error','Server error: no response!');
                return
                }
                try {
                response = Ext.util.JSON.decode(response.responseText)
                } catch(ex) {
                Ext.Msg.alert('Error','Illegal JSON string!');
                return
                }
                if(response.success == false){
                Ext.Msg.alert('Error',response.responseText);
                                    return
                }
                var gsm = TrackPool.grid.getSelectionModel();
                TrackPool.setName(response.conf_name);
                TrackPool.ds.removeAll();
                for(i = 0; i < response.rows.length; i++){
                var row = response.rows[i];
                if(TrackPool.ds.getById(row.track_id)) continue;

                var record = new recordType(row);
                TrackPool.ds.add(record, row.track_id);
                gsm.selectLastRow(true)
                }
                settings.conf_id = response.conf_id;
                TrackPool.setDesc(TrackPool.ds.getCount() + ' Tracks');
                                return
              }
            });
            break;
        }
      }
    },this); 
    return {
      grid: grid,
      ds: ds
    }
  })();
  var TrackList = (function() {
    var sm=new Ext.grid.CheckboxSelectionModel();
    var cm = new Ext.grid.ColumnModel([
      new Ext.grid.RowNumberer(),
      sm,
      {header:'Track name',dataIndex:'track_name', sortable: true, renderer:
        function (value, meta, record) {
          if(record.data.url_self == '' || record.data.url_self == null) 
            return "<div>"+record.data.track_name+"</div>";
          var formatStr = "<a href=" + record.data.url_self + " target=_blank>" + record.data.track_name + "</a>"; 
          return formatStr
        }.createDelegate(this)},
      {header:'Track type',dataIndex:'track_type', sortable: true},
      {header:'Organism',dataIndex:'organism', sortable: true},
      {header:'Mark',dataIndex:'mark', sortable: true},
      {header:'Data type',dataIndex:'data_type', sortable: true},
      {header:'Cell/Tissue',dataIndex:'type', sortable: true},
      {header:'Institute',dataIndex:'center', sortable: true},
      {header:'Access',dataIndex:'access', sortable: true},
      {header:'Upload date',dataIndex:'uploaddate', sortable: true}
    ]);
    var ds = new Ext.data.GroupingStore({
      url: base_url + 'sdec/ext-brwtrk.php',
      baseParams: {
         showall: 'all tracks'
      },
      reader: new Ext.data.JsonReader({
         root: 'rows',
         idProperty: 'track_id',
         totalProperty: 'count'
      },
      recordType),
      groupField: 'organism',
      sortInfo:{field:'uploaddate',direction:"DESC"},
      remoteSort: true
      });
      ds.on('load', function (records, successful, operation, eOpts) {
      var total = ds.getTotalCount();
      grid.toolbars[1].displayMsg = 'Total <span style="color:#2211ee">'+total+'</span> tracks';
    });
    var Toolbar = (function() {
      var allButton = new Ext.Button({
        text: '<u>All tracks</u>', 
        tooltip: 'All tracks',
        scale: 'medium',
        cls:'x-btn',
        handler: function() {
          ds.baseParams = {
            showall: 'all tracks'
          };
          ds.load({start:0, limit:40})
        }
      });
      var sdecButton = new Ext.Button({
        text: '<u>SDEC tracks</u>', 
        tooltip: 'SDEC tracks',
        scale: 'medium',
        cls:'x-btn',
        handler: function() {
          ds.baseParams = {
            showsdec: 'SDEC tracks'
          };
          ds.load({start:0, limit:40})
        }
      });
      var myButton = new Ext.Button({
        text: '<u>My tracks</u>', 
        tooltip: 'My tracks',
        scale: 'medium',
        cls:'x-btn',
        handler: function() {
          ds.baseParams = {
            showmy: 'My tracks'
          };
          ds.load({start:0, limit:40})
        }
      });
      var addButton = new Ext.Button({
        iconCls: 'silk_server_go',
        scale: 'medium',
        cls:'x-btn',
        text: 'Add to track pool/Editor', 
        tooltip: 'Add tracks to track pool',
        handler: function() {
          var sm = grid.getSelectionModel();
          var rows = sm.getSelections();
          var index = getGUI().TrackOrganizer.getActiveTab();
          if(sm.hasSelection() && index){
            if(index.title.indexOf('Track') >= 0){
              var gsm = TrackPool.grid.getSelectionModel();
              for(i = 0; i < rows.length; i++){
                var rowData = rows[i];
                if(TrackPool.ds.getById(rowData.id)) continue;
                TrackPool.ds.add(rowData);
                gsm.selectLastRow(true)
              }
              TrackPool.setDesc(TrackPool.ds.getCount() + ' Tracks')
            }
            if(index.title.indexOf('Editor') >= 0){
              TrackEditor.setID(rows[0].id);
              var obj = getSourceData(rows[0].data);
              TrackEditor.editor.setSource(obj);
            }
          }
        }
      });
      var removeButton = new Ext.Button({
        iconCls: 'silk_delete',
        text: 'Remove selected', 
        scale: 'medium',
        cls:'x-btn',
        tooltip: 'Remove selected tracks',
        handler: function() {
          var sm = grid.getSelectionModel();
          var rows = sm.getSelections();
          var trkids = '';
          var count = 0;
          for(var i = 0; i < rows.length; i++){
            if(rows[i].data.user_id != global_username) continue;
            count++;
            if(trkids == '') trkids += rows[i].data.track_id;
            else trkids += ',' + rows[i].data.track_id;
          }

          if(count == 0) { 
            Ext.Msg.alert('Error','No owner tracks selected!');
            return
          }
          Ext.MessageBox.confirm('Confirm', 'Remove selected(only owner) ' + count + ' tracks?', function(id){
          if(id == 'no') return;
          else{
          var para = {};
          para.trkids = trkids;
          para.action = 'removetrack';

          Ext.Ajax.request({
            url: base_url + 'sdec/ext-brwtrk.php',
            method: 'POST',
            params: para,
            failure: function(response, options) {
              Ext.Msg.alert('Error','Communication error!')
            },
            success: function(response, options) {
              if(!response) { 
                Ext.Msg.alert('Error','Server error: no response!');
                return
              }
              try {
                response = Ext.util.JSON.decode(response.responseText)
              } catch(ex) {
                Ext.Msg.alert('Error','Illegal JSON string!');
                return
              }
              if(response.success == false){
                Ext.Msg.alert('Error',response.responseText);
                                   return
              }

              TrackList.ds.load();
              TrackList.grid.getView().refresh();
              ConfList.ds.load();
              ConfList.grid.getView().refresh();

              for(var i = 0; i < rows.length; i++){
                if(rows[i].data.user_id != global_username) continue;
                var id = rows[i].data.track_id;
                var row = TrackPool.ds.getById(id);
                if(row) TrackPool.ds.remove(row);
                row = TrackList.ds.getById(id);
                if(row) TrackList.ds.remove(row);
              }
              Ext.Msg.alert('Success',response.responseText);
              return
            }
          });
          }
        })
        }
      });
      var msg = new Ext.Toolbar.TextItem('Search by keywords');
      var searchbox = new Ext.form.TextField({
        width: 200,
        selectOnFocus: true
      });
      searchbox.setValue('');
      searchbox.on('specialKey',
        function(config, event) {
          if (event.getKey() == event.ENTER) {
            var words = searchbox.getValue();
            if(words == '') return;
            ds.baseParams = {
              keyword: words
            };
            ds.load({start:0, limit:40})
          }
      });
    
      return {
        allButton: allButton,
        sdecButton: sdecButton,
        myButton: myButton,
        addButton: addButton,
        removeButton: removeButton,
        searchbox: searchbox,
        msg: msg
      }
    })();
    var toolbar = new Ext.Toolbar({
      items: [Toolbar.allButton, '-', Toolbar.sdecButton, '-', Toolbar.myButton,'-', Toolbar.msg, Toolbar.searchbox]
    });
    var grid=new Ext.grid.GridPanel({
      store: ds,
      cm:cm,
      sm:sm,
      viewConfig:{
        forceFit:true
      },
      enableDragDrop: true,
      ddGroup : 'mygrid-dd-track',
      ddText : 'Drag to track pool/Editor',
      view:new Ext.grid.GroupingView(),
      tbar: toolbar,
      bbar:new Ext.PagingToolbar({
        pageSize:40,
        store: ds,
        displayInfo:true,
        displayMsg:'Browsing tracks',
        emptyMsg: 'No track information',
        items: ['-',Toolbar.addButton,'-',Toolbar.removeButton]
      })                   
    });               
    ds.load();
    return {
      grid: grid,
      ds: ds
    }
  })();
  var TrackPool = (function() {
    var sm=new Ext.grid.CheckboxSelectionModel();
    var cm = new Ext.grid.ColumnModel([
      new Ext.grid.RowNumberer(),
      sm,
      {header:'Track name',dataIndex:'track_name', sortable: true},
      {header:'Track type',dataIndex:'track_type', sortable: true},
      {header:'Organism',dataIndex:'organism', sortable: true},
      {header:'Mark',dataIndex:'mark', sortable: true},
      {header:'Data',dataIndex:'data_type', sortable: true},
      {header:'Cell/Tissue',dataIndex:'type', sortable: true},
      {header:'Institute',dataIndex:'center', sortable: true},
      {header:'Access',dataIndex:'access', sortable: true},
      {header:'Upload date',dataIndex:'uploaddate', sortable: true}
    ]);
    var ds = new Ext.data.GroupingStore({
        url: base_url + 'sdec/ext-brwtrk.php',
        baseParams: {
           annotation: 'Annotations'
        },
        reader: new Ext.data.JsonReader({
           root: 'rows',
           idProperty: 'track_id',
           totalProperty: 'count'
        }, recordType)
    });
    
    var nameBox = new Ext.form.TextField({
      value: settings.name,
      width: 150,
      selectOnFocus: true
    });
    nameBox.on('specialKey',
    function(config, event) {
        settings.name = nameBox.getValue()
    });
    var descBox = new Ext.form.TextField({
      value: settings.description,
      width: 200,
      selectOnFocus: true
    });
    descBox.on('specialKey',
    function(config, event) {
        settings.description = descBox.getValue()
    });
    var setDesc = function(title){
      settings.description = title;
      descBox.setValue(title)
    };
    var setName = function(title){
      settings.name = title;
      nameBox.setValue(title)
    };
    var newButton = new Ext.Button({
      text: 'New', 
      tooltip: 'Create new configuration',
      scale: 'medium',
      cls:'x-btn',
      handler: function() {
        var height = 80;
        var trkids = '';
        var trkhts = '';
        if(descBox.getValue() == '')
          setDesc(ds.getCount() + ' Tracks');
        for(var i = 0; i < grid.store.data.getCount(); i++){
          var track_id = grid.store.data.items[i].data.track_id;
          var track_type = grid.store.data.items[i].data.track_type;
          if(track_type == 'ModelsTrack') height = 100;
          else height = 80;
          if(i == 0){
            trkids += track_id;
            trkhts += height
          }
          else{
            trkids += ',' + track_id;
            trkhts += ',' + height
          }
        }
        
        var para = {};
        para.conf_id = settings.conf_id;
        para.conf_name = nameBox.getValue();
        para.conf_desc = settings.description;
        para.trkids = trkids;
        para.trkhts = trkhts;
        para.action = 'create';

        Ext.MessageBox.confirm('Confirm', 'Create configurations(name:' + para.conf_name + ')?', function(id){
        if(id == 'no') return;
        else{

        Ext.Ajax.request({
          url: base_url + 'sdec/ext-brwtrk.php',
          method: 'POST',
          params: para,
          failure: function(response, options) {
            Ext.Msg.alert('Error','Communication error!')
          },
          success: function(response, options) {
            if(!response) { 
              Ext.Msg.alert('Error','Server error: no response!');
              return
            }
            try {
              response = Ext.util.JSON.decode(response.responseText)
            } catch(ex) {
              Ext.Msg.alert('Error','Illegal JSON string!');
              return
            }
            if(response.success == false){
              Ext.Msg.alert('Error',response.responseText);
                               return
            }

            ConfList.ds.load();
            ConfList.grid.getView().refresh();

            Ext.Msg.alert('Success',para.conf_name + ' saved!');
            var r = ConfList.grid.getView().getRow(0);
            if(r) r.style.backgroundColor = "#99cc99";
                           return
          }
        });
        }
      });
      }
    });
    var updateButton = new Ext.Button({
      text: 'Update', 
      tooltip: 'Update to last selected configuration',
      scale: 'medium',
      cls:'x-btn',
      handler: function() {
        var height = 80;
        var trkids = '';
        var trkhts = '';
        if(descBox.getValue() == '')
          setDesc(ds.getCount() + ' Tracks');

        for(var i = 0; i < grid.store.data.getCount(); i++){
          var track_id = grid.store.data.items[i].data.track_id;
          var track_type = grid.store.data.items[i].data.track_type;
          if(track_type == 'ModelsTrack') height = 100;
          else height = 80;
          if(i == 0){
            trkids += track_id;
            trkhts += height
          }
          else{
            trkids += ',' + track_id;
            trkhts += ',' + height
          }
        }
        
        var para = {};
        para.conf_id = settings.conf_id;
        para.conf_name = settings.name;
        para.conf_desc = settings.description;
        para.trkids = trkids;
        para.trkhts = trkhts;
        para.action = 'update';

        if(!para.conf_id || para.conf_id == ''){
          Ext.Msg.alert('Warning','No selected configuration to update!');
          return
        }

        Ext.MessageBox.confirm('Confirm', 'Update configuration to last selected(name:' + para.conf_name + ')?', function(id){
        if(id == 'no') return;
        else{

        Ext.Ajax.request({
          url: base_url + 'sdec/ext-brwtrk.php',
          method: 'POST',
          params: para,
          failure: function(response, options) {
            Ext.Msg.alert('Error','Communication error!')
          },
          success: function(response, options) {
            if(!response) { 
              Ext.Msg.alert('Error','Server error: no response!');
              return
            }
            try {
              response = Ext.util.JSON.decode(response.responseText)
            } catch(ex) {
              Ext.Msg.alert('Error','Illegal JSON string!');
              return
            }
            if(response.success == false){
              Ext.Msg.alert('Error',response.responseText);
                               return
            }

            ConfList.ds.load();
            ConfList.grid.getView().refresh();
            var index = ConfList.grid.store.findBy(
            function(record, id){
              if(id == settings.conf_id) return true
            });

            Ext.Msg.alert('Success',' Successfully updated!');
            var r = ConfList.grid.getView().getRow(index);
            if(r) r.style.backgroundColor = "#99cc99";
                           return
          }
        });
        }
      });
      }
    });
    var removeButton = new Ext.Button({
      iconCls: 'silk_delete',
      text: 'Remove', 
      tooltip: 'Remove selected tracks',
      scale: 'medium',
      cls:'x-btn',
      handler: function() {
        var sm = grid.getSelectionModel();
        var rows = sm.getSelections();
        if(sm.hasSelection()){
          for(i = 0; i < rows.length; i++){
            var rowData = rows[i];
            grid.store.remove(rowData);
          }
        }
        setDesc(ds.getCount() + ' Tracks');
        grid.getView().refresh()
      }
    });
    var resetButton = new Ext.Button({
      iconCls: 'silk_delete',
      text: 'Reset', 
      scale: 'medium',
      cls:'x-btn',
      tooltip: 'Reset track pool',
      handler: function() {
        ds.load();
        grid.getView().refresh();
        setDesc(ds.getCount() + ' Tracks');
        settings.conf_id = ''
      }
    });
    var toolbar = new Ext.Toolbar({
      items: [removeButton, '-', resetButton,'-', 'Name', nameBox, 'Description', descBox,'-', newButton, '-', updateButton]
    });
    var grid = new Ext.grid.GridPanel({
      store: ds,
      cm:cm,
      sm:sm,
      viewConfig:{
        forceFit:true
      },
      enableDragDrop: true,
      ddGroup : 'mygrid-dd',
      ddText : 'Drag to reorder',
      tbar: toolbar
    });               
    ds.load();
    return {
      grid: grid,
      ds: ds,
      setName: setName,
      setDesc: setDesc
    }
  })();
  var TrackEditor = (function() {
    var track_id = null;
    function setID(id) {
      track_id = id
    };
    function getID() {
      return track_id
    };
    var Toolbar = (function() {
      var saveButton = new Ext.Button({
        iconCls: 'silk_delete',
        text: 'Save track', 
        scale: 'medium',
        cls:'x-btn',
        tooltip: 'Save modified track information',
        handler: function() {
          var row ={};
          row.access = editor.store.getById('Access permission').get('value');
          row.center = editor.store.getById('Institute').get('value');
          row.data_type = editor.store.getById('Experimental type').get('value');
          row.mark = editor.store.getById('Mark name').get('value');
          row.organism = editor.store.getById('Organism').get('value');
          row.track_name = editor.store.getById('Track name').get('value');
          row.track_url = editor.store.getById('Data URL').get('value');
          row.type = editor.store.getById('Cell/Tissue').get('value');
          row.uploaddate = editor.store.getById('Upload date').get('value');
          row.url_self = editor.store.getById('Data source').get('value');
          row.user_id = editor.store.getById('Track owner').get('value');
          row.track_type = 'ReadsTrack';
          var val = editor.store.getById('Track type').get('value');
          if(val == 'Gene model') row.track_type = 'ModelsTrack';
          else if(val == 'Short reads') row.track_type = 'ReadsTrack';
          else if(val == 'CG/CHG/CHH') row.track_type = 'MethTrack';
          else if(val == 'HiC data') row.track_type = 'HiCTrack';
          else if(val == 'Intensity') row.track_type = 'IntensityTrack';
        
          if(row.track_name == '' || row.track_url == ''){
            Ext.Msg.alert('Error','Track name, Data URL is empty!');
            return
          }

          var old = TrackList.grid.store.getById(track_id);
          if(old && old.data.user_id != global_username){    
            Ext.Msg.alert('Error','Only owner can update track!');
            return
          }

          if(old) Ext.apply(old.data, row); 
          
          row.id = track_id;
          row.action = 'save';

          Ext.Ajax.request({
            url: base_url + 'sdec/ext-editrk.php',
            method: 'POST',
            params: row,
            failure: function(response, options) {
              Ext.Msg.alert('Error','Communication error!')
            },
            success: function(response, options) {
              if(!response) { 
                Ext.Msg.alert('Error','Server error: no response!');
                return
              }
              try {
                response = Ext.util.JSON.decode(response.responseText)
              } catch(ex) {
                Ext.Msg.alert('Error','Illegal JSON string!');
                return
              }
              if(response.success == false){
                Ext.Msg.alert('Error',response.responseText);
                                return
              }
              Ext.Msg.alert('Success','Track updated!');
                            return
            }
          });

          var index = TrackList.grid.store.findBy(
            function(record, id){
              if(id == track_id) return true
          });

          var r = TrackList.grid.getView().getRow(index);
          if(r) r.style.backgroundColor = "#99cc99";
        }
      });
    
      return {
        saveButton: saveButton
      }
    })();
    var toolbar = new Ext.Toolbar({
      items: [Toolbar.saveButton]
    });
    var comboTracktype = new Ext.form.ComboBox({
      fieldLabel: 'Track type',
      name: 'Track type',
      allowBlank: false,
      store: ['Gene model', 'Short reads', 'CG/CHG/CHH', 'HiC data','Intensity'],
      typeAhead: true,
      mode: 'local',
      editable: false,
      triggerAction: 'all',
      emptyText:'--Select--',
      selectOnFocus: true
    });
    var comboAccess = new Ext.form.ComboBox({
      fieldLabel: 'Access permission',
      name: 'Access permission',
      allowBlank: false,
      store: ['private', 'group', 'public'],
      typeAhead: true,
      mode: 'local',
      editable: false,
      triggerAction: 'all',
      emptyText:'--Select--',
      selectOnFocus: true
    });

    var editor = new Ext.grid.PropertyGrid({
      autoHeight: true,
      listeners:{
        render: function(grid){
          grid.getColumnModel().setColumnWidth(0, 100);
        }
      },
      customEditors: {
        'Track type': new Ext.grid.GridEditor(comboTracktype),
        'Access permission': new Ext.grid.GridEditor(comboAccess)
      },
      tbar: toolbar
    });
    editor.on("beforeedit",function(e){
      if(e.record.data.name == 'Upload date' || e.record.data.name == 'Track owner'){
        e.cancel = true;
        return false
      }
    });
    return {
      setID: setID,
      getID: getID,
      editor: editor
    }
  })();
    function buildGUI() {
       var TrackOrganizer = new Ext.TabPanel({
          iconCls: 'silk_wrench',
          defaults:{autoScroll:false},
          aciveTab: 0,
          margins: '0 0 0 0',
          items: [
           {title: 'Track Pool', layout:'fit', items: TrackPool.grid},
           {title: 'Editor', layout:'fit', autoScroll:true, items: TrackEditor.editor}
          ]
       });

    var Container = new Ext.Panel({
       title: '',
       region: 'center',
       iconCls: 'silk_wrench',
       defaults:{autoScroll:false},
       collapsible: true,
       split: true,
       margins: '0 0 0 0',
       layoutConfig: {
        animate: true
       },
       items: [{title: '', layout:'form', items:TrackList.grid}, TrackOrganizer]
    });
  
    var items;
    if(global_username.indexOf('guest') >= 0){ 
      items =  ['<font style="font-size: 16px;">Welcome, Guest!</font>', '-', '<a style="visited {text-decoration: none}; link {text-decoration: none}; visited {text-decoration: none}; active {text-decoration: none}; hover {text-decoration: underline; color: red;}", href=\"mailto:star@wanglab.ucsd.edu\" target=_self><font style="font-size: 16px;">Contact</font></a>', '-', '<a href=\"logout.php\" target=_self><font style="font-size: 16px;">Login</font></a>']
    }
    else 
        if(global_username.indexOf('encode') >= 0){ 
      items = ['<font style="font-size: 16px;">Welcome,'+global_username+'</font>!','-', '<a href=\"../mENCODE/brwtrk.php\" target=_self class=\"link\"><font style="font-size: 16px;">Portal</font></a>', '-', '<a href=\"profile.php\" target=_self class=\"link\"><font style="font-size: 16px;">My Profile</font></a>', '-', '<a href=\"mailto:star@wanglab.ucsd.edu\" target=_self><font style="font-size: 16px;">Contact</font></a>', '-', '<a href=\"logout.php\" target=_self><font style="font-size: 16px;">Logout</font></a>']

        }
    else{
      items = ['<font style="font-size: 16px;">Welcome,'+global_username+'</font>!', '-', '<a href=\"profile.php\" target=_self class=\"link\"><font style="font-size: 16px;">My Profile</font></a>', '-', '<a href=\"mailto:star@wanglab.ucsd.edu\" target=_self><font style="font-size: 16px;">Contact</font></a>', '-', '<a href=\"logout.php\" target=_self><font style="font-size: 16px;">Logout</font></a>']}
    var toolbar = new Ext.Toolbar({ items: items });
 
    var Welcome = new Ext.Panel({
       region: 'north',
       iconCls: 'silk_wrench',
       margins: '0 0 0 0',
       tbar: toolbar
     });

    var Nav = new Ext.TabPanel({
      region: 'south',
      iconCls: 'silk_wrench',
      defaults:{autoScroll:false},
      aciveTab: 0,
      margins: '0 0 0 0',
      items: [
        {title: 'Tracks', layout:'fit', items: TrackOption.grid},
        {title: 'Configs', layout:'fit', items: ConfList.grid},
        {title: 'Upload',  layout:'fit', items: uploadForm},
        {title: 'Documentation', layout:'fit',autoScroll:true,  autoLoad:{url:'manual/tutorial.html',scripts:true}}
      ]
    });

    var West = new Ext.Panel({
       title: '',
       region: 'west',
       iconCls: 'silk_wrench',
       width: screen.width/3 + 50,
       defaults:{autoScroll:false},
       collapsible: true,
       split: true,
       margins: '0 0 0 0',
       layoutConfig: {
         animate: true
       },
       items: [Welcome, Nav]
    });

    var Viewport = new Ext.Viewport({
        layout: 'border',
        items: [West, Container]
    });
 
    Nav.on('tabchange',
    function(newcard, oldcard, eopts) {
      if(swfu) swfu.cancelQueue();
      if(swfu1) swfu1.cancelQueue();
      newcard.getUpdater().refresh();
      West.setWidth(screen.width/3 + 50);
      Container.setVisible(true)
      if(newcard.activeTab.title == 'Documentation'){
        West.setWidth(screen.width);
        Container.setVisible(false)
      }
      if(newcard.activeTab.title == 'Upload'){
        if(!swfu){
          uploadForm.findById('x-group-upload').setDisabled(true);
          uploadForm.findById('x-group-upload').setVisible(false);
          uploadForm.findById('x-group-url').setDisabled(false);
          uploadForm.findById('x-group-url').setVisible(true)
        }
      }
    });
    TrackOrganizer.on('tabchange',
    function(newcard, oldcard, eopts) {
      newcard.getUpdater().refresh()
    });
 
    Nav.setActiveTab(1);
    TrackOrganizer.setActiveTab(0);

    var height = document.body.clientHeight;
    TrackList.grid.setHeight(height/2);
    TrackOrganizer.setHeight(height/2 - 25);
    TrackPool.grid.setHeight(height/2 - 25);
    Nav.setHeight(height - Welcome.getHeight() - 25);
    window.onresize = function(){
      var height = document.body.clientHeight;
      TrackList.grid.setHeight(height/2);
      TrackOrganizer.setHeight(height/2 - 25);
      TrackPool.grid.setHeight(height/2 - 25);
      Nav.setHeight(height - Welcome.getHeight() - 25)
    };
    var ddrow = new Ext.dd.DropTarget(TrackPool.grid.getView().mainBody, {
      ddGroup: 'mygrid-dd',
      copy: false,
      notifyDrop : function(dd, e, data){
        var sm = TrackPool.grid.getSelectionModel();
        var rows = data.selections;
        var index = dd.getDragData(e).rowIndex;
        if(typeof(index) == "undefined") { return; }
        if(sm.hasSelection()){
          for(i = 0; i < rows.length; i++){
            var rowData = rows[i];
            if(!this.copy) TrackPool.ds.remove(rowData);
            TrackPool.ds.insert(index, rowData);
          }
          sm.selectRecords(rows);
        }
        TrackPool.grid.getView().refresh()
      }
    });
    var editorTarget = new Ext.dd.DropTarget(TrackOrganizer.getEl(), {
      ddGroup: 'mygrid-dd-track',
      copy: false,
      notifyDrop : function(dd, e, data){
        var sm = TrackList.grid.getSelectionModel();
        var rows = data.selections;
        var index = TrackOrganizer.getActiveTab();
        if(sm.hasSelection() && index){
          if(index.title.indexOf('Track') >= 0){
            var gsm = TrackPool.grid.getSelectionModel();
            for(i = 0; i < rows.length; i++){
              var rowData = rows[i];
              if(TrackPool.ds.getById(rowData.id)) continue;
              TrackPool.ds.add(rowData);
              gsm.selectLastRow(true)
            }
            TrackPool.setDesc(TrackPool.ds.getCount() + ' Tracks')
          }
          if(index.title.indexOf('Editor') >= 0){
            TrackEditor.setID(rows[0].id);
            var obj = getSourceData(rows[0].data);
            TrackEditor.editor.setSource(obj);
          }
        }
      }
    });
    var confTarget = new Ext.dd.DropTarget(TrackPool.grid.getEl(), {
      ddGroup: 'mygrid-dd-option',
      copy: false,
      notifyDrop : function(dd, e, data){
        var rows = data.selections;
        if(rows && rows.length > 0){
          var para = {};
          para.id = rows[0].id;
          para.action = 'load';

          Ext.Ajax.request({
            url: base_url + 'sdec/ext-aj_conf.php',
            method: 'POST',
            params: para,
            failure: function(response, options) {
              Ext.Msg.alert('Error','Communication error!')
            },
            success: function(response, options) {
              if(!response) { 
                Ext.Msg.alert('Error','Server error: no response!');
                return
              }
              try {
                response = Ext.util.JSON.decode(response.responseText)
              } catch(ex) {
                Ext.Msg.alert('Error','Illegal JSON string!');
                return
              }
              if(response.success == false){
                Ext.Msg.alert('Error',response.responseText);
                                return
              }
              var gsm = TrackPool.grid.getSelectionModel();
              TrackPool.setName(response.conf_name);
              for(i = 0; i < response.rows.length; i++){
                var row = response.rows[i];
                if(TrackPool.ds.getById(row.track_id)) continue;

                var record = new recordType(row);
                TrackPool.ds.add(record, row.track_id);
                gsm.selectLastRow(true)
              }
              settings.conf_id = response.conf_id;
              TrackPool.setDesc(TrackPool.ds.getCount() + ' Tracks');
              return
            }
          });
        }
      }
    });

    return {
      Viewport: Viewport,
      TrackOrganizer: TrackOrganizer,
      Container: Container
    }
    };
    function getGUI() {
        return GUI
    };
    function getUploadForm() {
        return uploadForm
    };
    return {
        init: init,
        getUploadForm: getUploadForm,
        getGUI: getGUI
    }
})();
var fn;
if (Ext) {
  Ext.onReady(AnnoJ.init);
}
var Mouse = function() {
    var self = this;
    this.addEvents({
        'dragStarted': true,
        'dragged': true,
        'dragEnded': true,
        'dragCancelled': true,
        'pressed': true,
        'released': true,
        'moved': true
    });
    var mouse = {
        x: 0,
        y: 0,
        down: false,
        drag: false,
        downX: 0,
        downY: 0,
        target: null
    };
    Ext.EventManager.addListener(window, 'scroll',
    function(event) {
        mouse.drag = false;
        mouse.down = false
    });
    Ext.EventManager.addListener(window, 'keydown',
    function() {
        if (mouse.drag) {
            mouse.drag = false;
            self.fireEvent('dragCancelled', mouse)
        }
    });
    this.getMouse = function() {
        return mouse
    }
};
Ext.extend(Mouse, Ext.util.Observable);
