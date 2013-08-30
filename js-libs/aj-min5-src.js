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
findConf = function(id) {
    var track = find(AnnoJ.config.tracks,'id', id);
    if(!track) track = AnnoJ.config.infoTrack;
    return track
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
var isInfo = function(id) {
    if(id.indexOf('trackxxxx-') != -1) 
      return true;
    else return false
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
    function alert(message, type) {
        switch (type) {
        case 'ERROR':
            break;
        case 'WARNING':
            break;
        default:
            type = 'INFO'
        };
        Ext.Msg.show({
            title: type,
            msg: message,
            buttons: Ext.Msg.OK,
            icon: Ext.MessageBox[type]
        })
    };
    function exception(e, message) {
        if (!Ext.isIE && console && console.log) console.log(e);
        if (!message) var message = 'An exception was encountered.';
        error(message + '<br /><br />Details:<br />File: ' + e.fileName + '<br />Line: ' + e.lineNumber + '<br />Info: ' + e.message)
    };
    function error(message) {
        alert(message, 'ERROR')
    };
    function warning(message) {
        alert(message, 'WARNING')
    };
    function notice(message) {
        alert(message, 'INFO')
    };
    return {
        checkBrowser: checkBrowser,
        alert: alert,
        exception: exception,
        error: error,
        warning: warning,
        notice: notice
    }
})();
var BaseJS = (function() {
    var emptyFunction = function() {};
    var defaultSyndication = {
        institution: {
            name: '',
            url: '',
            logo: ''
        },
        engineer: {
            name: '',
            email: ''
        },
        service: {
            title: '',
            version: '',
            description: '',
            request: {
                type: '',
                format: '',
                schema: ''
            },
            response: {
                type: '',
                format: '',
                schema: ''
            }
        }
    };
    var defaultRequest = {
        url: '',
        data: null,
        method: 'GET',
        success: emptyFunction,
        failure: emptyFunction,
        requestJSON: true,
        receiveJSON: true
    };
    function syndicate(params) {
        Ext.applyIf(params || {},
        defaultRequest);
        request({
            url: params.url || '',
            data: {
                action: 'syndicate'
            },
            requestJSON: false,
            receiveJSON: true,
            success: function(response) {
                Ext.applyIf(response.data || {},
                defaultSyndication);
                params.success(response.data)
            },
            failure: params.failure || emptyFunction
        })
    };
    function syndicationToHTML(syndication) {
        var s = {};
        Ext.apply(s, syndication || {},
        defaultSyndication);
        var html = "<div style='padding:2px;'>";
        html += "<div><a href='" + s.institution.url + "'><img src='" + s.institution.logo + "' alt='Data provider institutional logo' /></a></div>";
        html += "<div><b>Provider: </b><a href='" + s.institution.url + "'>" + s.institution.name + "</a></div>";
        html += "<div><b>Contact: </b><a href='mailto:" + s.engineer.email + "'>" + s.engineer.name + "</a></div>";
        html += "<hr />";
        html += "<div><b>" + s.service.title + "</b></div>";
        html += "<div>" + s.service.description + "</div>";
        return html + "</div>"
    };
    function objectToHTML(obj) {
        var html = '<ul>';
        for (var param in obj) {
            if (!obj.hasOwnProperty(param)) continue;
            if (typeof(obj[param]) == 'string') {
                html += '<li><b>' + param + ':</b> ' + obj[param] + '</li>'
            } else {
                html += objectToHTML(obj[param])
            }
        }
        return html + '</ul>'
    };
    function request(params) {
        Ext.applyIf(params || {},
        defaultRequest);
        if (!params.url) return;
        if (params.method != 'GET' && params.method != 'POST') return;
        if (params.requestJSON) {
            params.data = {
                request: Ext.util.JSON.encode(params.data)
            }
        }
        Ext.Ajax.request({
            url: params.url,
            method: params.method,
            params: params.data,
            failure: function(response, options) {
                params.failure('Communication error: ' + response.responseText)
            },
            success: function(response, options) {
                if (!response) {
                    params.failure('Server error: no response');
                    return
                }
                if (params.receiveJSON) {
                    try {
                        response = Ext.util.JSON.decode(response.responseText)
                    } catch(ex) {
                        params.failure('Illegal JSON string: ' + response.responseText);
                        return
                    }
                    if (response.success == false) {
                        params.failure('Server error: ' + (response.message || 'unspecified server error'));
                        return
                    }
                    params.success(response)
                } else {
                    params.success(response.responseText)
                }
            }
        })
    };
    return {
        syndicate: syndicate,
        toHTML: syndicationToHTML,
        request: request
    }
})();
var tokens = 0;
var token_ready = true;
var cursor = {
    id: '',
    type: '',
    offsetTop: '',
    offsetHeight: ''
};
var InfoRequest = {
    ready: false,
    position: 0,
    bases: 10,
    pixels: 1,
    corr: []
};
var AnnoJ = (function() {
    var defaultConfig = {
        tracks: [],
        active: [],
        genome: '',
        bookmarks: '',
        styles: [],
        location: {
            assembly: '3',
            position: 15678,
            bases: 400,
            pixels: 2
        },
        admin: {
            name: '',
            email: '',
            notes: ''
        },
        cls: 'tracks',
        citation: '',		
    };

    var config = defaultConfig;
    var GUI = {};
    var isReady = false;
    function init() {
        Ext.MessageBox.progress('Building');
        Ext.MessageBox.updateProgress(0.05, '', 'Checking browser...');
        if (!WebApp.checkBrowser()) {
            Ext.MessageBox.hide();
            return false
        }
        Ext.MessageBox.updateProgress(0.10, '', 'Applying configuration...');


		
		
        if (!AnnoJ.config) {
            Ext.MessageBox.hide();
            WebApp.error('Failed to load AnnoJ.config object.');
            return false
        }
		var defaultConfig = {
        tracks: [],
        active: [],
        genome: '',
        bookmarks: '',
        styles: [],
        location: {
            assembly: '3',
            position: 15678,
            bases: 400,
            pixels: 2
        },
        admin: {
            name: '',
            email: '',
            notes: ''
        },
        cls: 'tracks',
        citation: '',	
		test:'testttt',
		settings:{
			scale:0,
			}
		};
		
		Ext.apply(config, AnnoJ.config|| {},defaultConfig);
        if(!AnnoJ.config.settings) {AnnoJ.config.settings = {}};

        Ext.each(AnnoJ.config.tracks,
        function(track) {
        if(!track.scale) track.scale = 1;
        if(!track.color || typeof(track.color) == 'string') track.color = {};
        if(track.type == 'HiCTrack'){
            if(!track.assembly) track.assembly = '1';
            if(!track.style) track.style = 0;
            if(!track.unity) track.unity = 4;
            if(!track.offsety) track.offsety = 0
        }
        });
        if(!AnnoJ.config.maxlist) AnnoJ.config.maxlist = new Array();
        if(!AnnoJ.config.infoTrack) AnnoJ.config.infoTrack = {};
		

        Ext.MessageBox.updateProgress(0.15, '', 'Building GUI...');
        try {
            GUI = buildGUI()
        } catch(e) {
            Ext.MessageBox.hide();
            WebApp.exception(e, 'An exception occured when initializing graphical user interface.');
            return false
        };
        GUI.notice('Browser check passed', true);
        GUI.notice('Configuration loaded');
        GUI.notice('GUI constructed');
        Ext.MessageBox.updateProgress(0.3, '', 'Loading stylesheets...');
        GUI.notice('Stylesheets loaded');
        Ext.MessageBox.updateProgress(0.4, '', 'Syndicating genome...');
        GUI.NavBar.syndicate({
            url: config.genome,
            success: function(response) {
                GUI.notice('Genome syndicated');
                GUI.NavBar.setLocation(config.location);
                Ext.MessageBox.updateProgress(0.5, '', 'Building tracks...');
                buildTracks();
                GUI.notice('Tracks instantiated');
                Ext.MessageBox.updateProgress(1.0, '', 'Finished.');
                Ext.MessageBox.hide();
                AnnoJ.isReady = true
            },
            failure: function(string) {
                Ext.MessageBox.hide();
                error('Unable to load genomic metadata from address: ' + config.genome);
                Ext.MessageBox.alert('Error', 'Unable to load genomic metadata from address: ' + config.genome)
            }
        })
    };
    function InitTracks(id) {
        var track = GUI.Tracks.tracks.find('id', id);
        if (track) {
            GUI.TrackSelector.activate(track);
            GUI.Tracks.tracks.open(track);
            var ratio = tokens / AnnoJ.config.active.length;
            var percent = ratio * 100;
            Ext.MessageBox.updateProgress(0.5 + ratio / 2.0, '', 'Loading tracks........' + percent.toFixed(0) + '%');
        }
        tokens++;
        token_ready = true
    };
    function buildTracks() {
        Ext.each(config.tracks,
        function(trackConfig, index) {
            try {
                var track = new AnnoJ[trackConfig.type](trackConfig)
            } catch(e) {
                config.tracks[index] = null;
                WebApp.error(e);
                if(!Ext.isIE) console.log(e);
                return
            };
            GUI.Tracks.tracks.manage(track);
            GUI.TrackSelector.manage(track);
            GUI.TrackSelector.expand();
            GUI.TrackSelector.active.expand();
            GUI.TrackSelector.inactive.expand()
        });
        Ext.each(GUI.Tracks.tracks.tracks,
        function(track) {
            track.on('describe',
            function(syndication) {
                GUI.InfoBox.echo(BaseJS.toHTML(syndication));
            })
        });
        Ext.each(config.active,
        function(id) {
            var track = GUI.Tracks.tracks.find('id', id);
            if (track) {
                GUI.TrackSelector.activate(track);
                GUI.Tracks.tracks.open(track)
            }
        })
    };
    function buildGUI() {
        var Messenger = new AnnoJ.Messenger();
        var TrackSelector = new AnnoJ.TrackSelector({
            structure: config.structure,
            activeTracks: config.active
        });
        var Bookmarker = new AnnoJ.Bookmarker({
            datasource: config.bookmarks || config.genome
        });
        var StyleSelector = new AnnoJ.StyleSelector({
            styles: config.styles
        });
        var InfoBox = new AnnoJ.InfoBox();
        var AboutBox = new AnnoJ.AboutBox({
            admin: config.admin
        });
        var NavBar = new AnnoJ.Navigator();
        var InfoBar = new AnnoJ.InfoToolBar();
        var Tracks = new AnnoJ.Tracks({
            tbar: NavBar.ext,
            tracks: config.tracks,
            activeTracks: config.active
        });
        var TracksInfo = new AnnoJ.TracksInfo({
            region: 'south',
            height: screen.height/2,
            tbar: InfoBar.ext,
            tracks: [],
            activeTracks: []
        });

        if (config.citation) {
            AboutBox.addCitation(config.citation)
        }
        var Accordion = new Ext.Panel({
            title: 'Configuration',
            region: 'north',
            layout: 'accordion',
            iconCls: 'silk_wrench',
            collapsible: true,
            split: true,
            minSize: 160,
            width: 250,
            height: screen.height/2,
            maxSize: 400,
            margins: '0 0 0 0',
            layoutConfig: {
                animate: true
            },
            items: [AboutBox, TrackSelector, InfoBox, Messenger, StyleSelector, Bookmarker]
        });
        var Container = new Ext.Panel({
            title: 'Information Box',
            region: 'west',
            iconCls: 'silk_wrench',
            collapsible: true,
            split: true,
            minSize: 160,
            width: 250,
            maxSize: 400,
            margins: '0 0 0 0',
            layoutConfig: {
                animate: true
            },
            items: [Accordion, TracksInfo]
        });
        var Viewport = new Ext.Viewport({
            layout: 'border',
            items: [Container, Tracks]
        });
        NavBar.on('describe',
        function(syndication) {
            InfoBox.echo(BaseJS.toHTML(syndication));
            InfoBox.expand()
        });
        NavBar.on('browse', Tracks.tracks.setLocation);
        NavBar.on('dragModeSet', Tracks.setDragMode);
        Tracks.on('dragModeSet', NavBar.setDragMode);
        TrackSelector.on('openTrack', Tracks.tracks.open);
        TrackSelector.on('moveTrack', Tracks.tracks.reorder);
        TrackSelector.on('closeTrack', Tracks.tracks.myclose);
        Bookmarker.disable();
        Bookmarker.hide();
        StyleSelector.disable();
        StyleSelector.hide();
        function alert(message, type, important) {
            Messenger[type](message);
            if (important) {
                Messenger.expand();
                Accordion.expand()
            }
        };
        function error(message, important) {
            alert(message, 'error', important)
        };
        function warning(message, important) {
            alert(message, 'warning', important)
        };
        function notice(message, important) {
            alert(message, 'notice', important)
        };
        return {
            Messenger: Messenger,
            TrackSelector: TrackSelector,
            StyleSelector: StyleSelector,
            InfoBox: InfoBox,
            AboutBox: AboutBox,
            NavBar: NavBar,
            Tracks: Tracks,
            TracksInfo: TracksInfo,
            Accordion: Accordion,
            Viewport: Viewport,
            Container: Container,
            alert: alert,
            error: error,
            warning: warning,
            notice: notice
        }
    };
    function getLocation() {
        return GUI.NavBar.getLocation()
    };
    function setLocation(location) {
        return GUI.NavBar.setLocation(location)
    };
    function alert(message, type) {
        GUI.Messenger.alert(message, type)
    };
    function error(message) {
        GUI.Messenger.error(message)
    };
    function warning(message) {
        GUI.Messenger.warning(message)
    };
    function notice(message) {
        GUI.Messenger.notice(message)
    };
    function pixels2bases(pixels) {
        return GUI.NavBar.pixels2bases(pixels)
    };
    function bases2pixels(bases) {
        return GUI.NavBar.bases2pixels(bases)
    };
    function xpos2gpos(xpos) {
        return GUI.NavBar.xpos2gpos(xpos)
    };
    function gpos2xpos(gpos) {
        return GUI.NavBar.gpos2xpos(gpos)
    };
    function getGUI() {
        return GUI
    };
    return {
        ready: true,
        init: init,
        InitTracks: InitTracks,
        alert: alert,
        isReady: isReady,
        error: error,
        warning: warning,
        notice: notice,
        getLocation: getLocation,
        setLocation: setLocation,
        pixels2bases: pixels2bases,
        bases2pixels: bases2pixels,
        xpos2gpos: xpos2gpos,
        gpos2xpos: gpos2xpos,
        getGUI: getGUI,
        Plugins: {},
        Helpers: {}
    }
})();
var fn;
if (Ext) {
    Ext.onReady(AnnoJ.init);
    //fn = window.setInterval(checkReady, 40);
}
function checkReady()
{
   if(AnnoJ && AnnoJ.isReady)
   {
    var GUI = AnnoJ.getGUI();
    if(tokens >= AnnoJ.config.active.length){
        window.clearInterval(fn);
        Ext.MessageBox.hide()
    }
    if(token_ready && tokens < AnnoJ.config.active.length){
       var id = AnnoJ.config.active[tokens];
       token_ready = false;
       var tmp = AnnoJ.InitTracks.createCallback(id);
        Ext.onReady(tmp,this,{delay:10})
    }
   }
}
AnnoJ.TrackSelector = (function() {
    var root = new Ext.tree.TreeNode({
        text: 'Tracks',
        allowDrag: false,
        allowDrop: false
    });
    var active = new Ext.tree.TreeNode({
        text: 'Active Tracks',
        allowDrag: false,
        allowDrop: true,
        leaf: false,
        expandable: true,
        expanded: true
    });
    var inactive = new Ext.tree.TreeNode({
        text: 'Inactive Tracks',
        allowDrag: false,
        allowDrop: true,
        leaf: false,
        expandable: true,
        expanded: true
    });
    root.appendChild(active);
    root.appendChild(inactive);
    function manage(track) {
        if (!track instanceof AnnoJ.BaseTrack) return;
        var parent = importPath(track.config.path);
        var node = new Ext.tree.TreeNode({
            id: 'tree_' + track.config.id,
            text: track.config.name,
            iconCls: track.config.iconCls,
            allowDrag: true,
            allowDrop: false,
            leaf: true
        });
        node.originalParent = parent;
        node.track = track;
        track.node = node;
        parent.appendChild(node);
        track.on('close', inactivate)
    };
    function unmanage(track) {
        if (!track instanceof AnnoJ.Track) return;
        if (!track.node) return;
        track.node.remove();
        node.track = null;
        delete track.node;
        track.un('close', inactivate)
    };
    function inactivate(track) {
        if(track.config.id.indexOf('new-') >= 0){
               var child = active.findChild('text', track.config.name);
            active.removeChild(child)
        }
        else track.node.originalParent.appendChild(track.node)
    };
    function inactivateAll() {
        Ext.each(active.childNodes,
        function(child) {
            inactivate(child.track)
        })
    };
    function activate(track) {
        active.appendChild(track.node)
    };
    function insertBefore(track,before) {
        var child = active.findChild('text', before);
        active.insertBefore(track.node, child)
    };
    function importPath(path) {
        var dirs = path.split('/');
        var parent = inactive;
        Ext.each(dirs,
        function(dir) {
            var child = parent.findChild('text', dir);
            if (!child) {
                child = new Ext.tree.TreeNode({
                    text: dir,
                    allowDrag: false,
                    allowDrop: true,
                    leaf: false
                })
            }
            parent.appendChild(child);
            parent = child
        });
        return parent
    };
    function getActive() {
        var list = [];
        Ext.each(active.childNodes,
        function(child) {
            list.push(child.track)
        });
        return list
    };
    function getActiveIDs() {
        var list = [];
        Ext.each(active.childNodes,
        function(child) {
            list.push(child.track.config.id)
        });
        return list
    };
    return function(userConfig) {
        var defaultConfig = {
            title: 'Track Selection',
            iconCls: 'silk_package',
            border: false,
            autoScroll: true,
            ddScroll: true,
            enableDD: true,
            rootVisible: false,
            singleExpand: false,
            structure: [],
            activeTracks: []
        };
        var config = defaultConfig;
        Ext.apply(config, userConfig || {},
        defaultConfig);
        AnnoJ.TrackSelector.superclass.constructor.call(this, config);
        this.addEvents({
            'openTrack': true,
            'closeTrack': true,
            'moveTrack': true
        });
        this.setRootNode(root);
        this.on('movenode',
        function(tree, node, oldParent, newParent, index) {
            if (oldParent == active) {
                if (newParent == active) {
                    this.fireEvent('moveTrack', node.track, node.nextSibling ? node.nextSibling.track: null)
                } else {
                    node.originalParent.appendChild(node);
                    this.fireEvent('closeTrack', node.track)
                }
            } else {
                if (newParent == active) {
                    this.fireEvent('openTrack', node.track, node.nextSibling ? node.nextSibling.track: null)
                } else if (node.originalParent && newParent != node.originalParent) {
                    node.originalParent.appendChild(node)
                } else {
                    return
                }
            }
        });
        this.manage = manage;
        this.activate = activate;
        this.inactivate = inactivate;
        this.insertBefore = insertBefore;
        this.active = active;
        this.inactive = inactive;
        this.getActiveIDs = getActiveIDs;
        this.inactivateAll = inactivateAll
    }
})();
Ext.extend(AnnoJ.TrackSelector, Ext.tree.TreePanel);
AnnoJ.Navigator = function() {
    var self = this;
    this.addEvents({
        'browse': true,
        'describe': true,
        'dragModeSet': true
    });
    var Syndicator = (function() {
        var syndication = {};
        function syndicate(params) {
            if (!params.url) {
                if (params.failure) params.failure('Unable to syndicate as no URL was provided');
                return
            }
            BaseJS.syndicate({
                url: params.url,
                success: function(response) {
                    syndication = response;
                    Controls.setTitle(syndication.service.title);
                    if (syndication.genome.assemblies) {
                        Controls.bindAssemblies(syndication.genome.assemblies, AnnoJ.config.location.assembly);
                        AnnoJ.config.assemblies = syndication.genome.assemblies
                    }
                    if (params.success) {
                        params.success(response)
                    }
                },
                failure: function(string) {
                    if (params.failure) params.failure(string)
                }
            })
        };
        function get() {
            return syndication
        };
        return {
            get: get,
            syndicate: syndicate
        }
    })();
    var Navigator = (function() {
        var location = {
            assembly: '',
            position: 0,
            bases: 10,
            pixels: 1
        };
        var Assembly = (function() {
            var defaultConfig = {
                selected: '',
                options: [],
                verbose: false
            };
            var config = defaultConfig;
            function init(userConfig) {
                Ext.apply(config, userConfig, defaultConfig);
                set(config.selected || userConfig.options[0].id || '')
            };
            function set(value) {
                if (!value) return false;
                var valid = false;
                Ext.each(config.options,
                function(option) {
                    if (option.id == value) {
                        valid = true;
                        config.selected = value;
                        AnnoJ.config.location.assembly = value;
                        location.assembly = value;
                        Position.init({
                            min: 1,
                            max: option.size,
                            value: Position.get()
                        });
                        return false
                    }
                });
                if (config.verbose && !valid) AnnoJ.warning('Illegal assembly value selected (' + value + ')');
                return valid
            };
            function get() {
                return config.selected
            };
            return {
                init: init,
                get: get,
                set: set
            }
        })();
        var Position = (function() {
            var defaultConfig = {
                min: 0,
                max: 0,
                position: 0,
                verbose: false
            };
            var config = defaultConfig;
            var atMin = false;
            var atMax = false;
            function init(userConfig) {
                Ext.apply(config, userConfig, defaultConfig);
                set(config.position)
            };
            function get() {
                return config.position
            };
            function set(gpos) {
                var gpos = parseInt(gpos || 1);
                atMin = false;
                atMax = false;
                if (gpos <= config.min) {
                    gpos = config.min;
                    atMin = true;
                    if (config.verbose) AnnoJ.notice('At minimum position of assembly ' + Assembly.get())
                }
                if (gpos >= config.max) {
                    gpos = config.max;
                    atMax = true;
                    if (config.verbose) AnnoJ.notice('At maximum position of assembly ' + Assembly.get())
                }
                config.position = gpos;
                location.position = gpos
            };
            return {
                init: init,
                get: get,
                set: set,
                atMax: function() {
                    return atMax
                },
                atMin: function() {
                    return atMin
                }
            }
        })();
        var Zoom = (function() {
            var defaultConfig = {
                max: 200000,
                min: 0.05,
                bases: 10,
                pixels: 1,
                verbose: false
            };
            var config = defaultConfig;
            var atMin = false;
            var atMax = false;
            function init(userConfig) {
                Ext.apply(config, userConfig, defaultConfig);
                set(config.bases, config.pixels)
            };
            function scale(multiplier) {
                if (!multiplier) return;
                var bases = multiplier > 0 ? config.bases * multiplier: config.bases;
                var pixels = multiplier > 0 ? config.pixels: config.pixels * -multiplier;
                set(bases, pixels)
            };
            function step(closer) {
                var b = config.bases;
                var p = config.pixels;
                if (b > p) {
                    var f = Math.pow(10, Math.round((b + '').length) - 1);
                    var d = b / f;
                    if (closer) {
                        b = (d == 1) ? b - f / 10 : b - f
                    } else {
                        b = b + f
                    }
                } else if (b < p) {
                    var f = Math.pow(10, Math.round((p + '').length) - 1);
                    var d = p / f;
                    if (closer) {
                        p = p + f
                    } else {
                        p = (d == 1) ? p - f / 10 : p - f
                    }
                } else {
                    closer ? p++:b++
                }
                set(b, p)
            };
            function get() {
                return {
                    bases: config.bases,
                    pixels: config.pixels
                }
            };
            function set(bases, pixels) {
                if (!bases || !parseInt(bases)) bases = 1;
                if (!pixels || !parseInt(pixels)) pixels = 1;
                bases = parseInt(bases);
                pixels = parseInt(pixels);
                atMin = false;
                atMax = false;
                var ratio = bases / pixels;
                if (ratio >= config.max) {
                    bases = config.max;
                    pixels = 1;
                    if (bases < 1) {
                        pixels = Math.round(1 / bases);
                        bases = 1
                    }
                    atMax = true
                } else if (ratio <= config.min) {
                    bases = 1;
                    pixels = Math.round(1 / config.min);
                    if (pixels == 0) {
                        bases = Math.round(config.min);
                        pixels = 1
                    }
                    atMin = true
                } else {
                    if (bases > pixels) {
                        bases = Math.round(bases / pixels);
                        pixels = 1;
                        var f = Math.pow(10, Math.round((bases + '').length) - 1);
                        bases = f * Math.round(bases / f)
                    } else {
                        pixels = Math.round(pixels / bases);
                        bases = 1;
                        var f = Math.pow(10, Math.round((pixels + '').length) - 1);
                        pixels = f * Math.round(pixels / f)
                    }
                }
                config.bases = bases;
                config.pixels = pixels;
                location.bases = bases;
                location.pixels = pixels
            };
            return {
                init: init,
                scale: scale,
                step: step,
                set: set,
                atMax: function() {
                    return atMax
                },
                atMin: function() {
                    return atMin
                }
            }
        })();
        function getLocation() {
            return location
        };
        function setLocation(view) {
            var view = Ext.apply({},
            view || {},
            location);
            Assembly.set(view.assembly);
            Position.set(view.position);
            Zoom.set(view.bases, view.pixels);
            Controls.refreshControls();
            return location
        };
        function step(closer) {
            Zoom.step(closer)
        };
        function scale(multiplier) {
            if (!multiplier || !parseFloat(multiplier)) return;
            Zoom.scale(multiplier)
        };
        function bump(bases) {
            if (!bases || !parseInt(bases)) return;
            Position.set(location.position + parseInt(bases))
        };
        function pixels2bases(pixels) {
            if (!pixels || !parseInt(pixels)) return 0;
            return Math.round(parseInt(pixels) * location.bases / location.pixels)
        };
        function bases2pixels(bases) {
            if (!bases || !parseInt(bases)) return 0;
            return Math.round(parseInt(bases) * location.pixels / location.bases)
        };
        function xpos2gpos(xpos) {
            var edges = getEdges();
            return pixels2bases(xpos) + edges.g1
        };
        function gpos2xpos(gpos) {
            var edges = getEdges();
            return bases2pixels(gpos) - edges.x1
        };
        function getEdges() {
            var halfX = Math.round(Toolbar.getBox().width / 2);
            var halfG = pixels2bases(halfX);
            var locG = location.position;
            var locX = bases2pixels(locG);
            return {
                g1: locG - halfG,
                g2: locG + halfG,
                x1: locX - halfX,
                x2: locX + halfX
            }
        };
        return {
            Assembly: Assembly,
            Position: Position,
            Zoom: Zoom,
            getLocation: getLocation,
            setLocation: setLocation,
            scale: scale,
            bump: bump,
            step: step,
            pixels2bases: pixels2bases,
            bases2pixels: bases2pixels,
            getEdges: getEdges,
            xpos2gpos: xpos2gpos,
            gpos2xpos: gpos2xpos
        }
    })();
    var Controls = (function() {
        var info = new Ext.Button({
            iconCls: 'silk_information',
            tooltip: 'Show information about the track',
            handler: function() {
                self.fireEvent('describe', Syndicator.get())
            }
        });
        var title = new Ext.Toolbar.TextItem('Awaiting syndication...');
        var filler = new Ext.Toolbar.Fill();

        var defaultSettings = {
        baseline: 0,
        display: 0,
        scale: 0,
        multi: 1,
        hic_d: 0,
        yaxis: 10
        };
        Ext.applyIf(AnnoJ.config.settings, defaultSettings);

        var Baseline = new Ext.form.TextField({
            width: 30,
            value: AnnoJ.config.settings.baseline,
            maskRe: /[0-9]/,
            regex: /^[0-9]+$/,
            selectOnFocus: true
        });
        Baseline.on('specialKey',
            function(config, event) {
                var val = Baseline.getValue();
                if(val == "") Baseline.setValue(AnnoJ.config.settings.baseline);
                AnnoJ.config.settings.baseline = Baseline.getValue()
        });

        var checked1 = true;
        var checked2 = false;
        if(AnnoJ.config.settings.display == 1) checked1 = false;
        var showMode = new Ext.CycleButton({
            showText: true,
            prependText: 'Display as: ',
            tooltip: 'toggle between histogram and heatmap',
            items: [{
                text: 'Histogram',
                checked: checked1
            },
            {
                text: 'Heatmap',
                checked: !checked1
            }],
            changeHandler: function(btn, item) {
                if(item.text == "Heatmap") AnnoJ.config.settings.display = 1;
                else AnnoJ.config.settings.display = 0;

                var Tracks = AnnoJ.getGUI().Tracks;
                if(Tracks){
                    for(var i in Tracks.tracks.tracks){
                        var track = Tracks.tracks.tracks[i];
                        if(AnnoJ.config.settings.display == 1){
                            if(track.setHeatmapHeight) track.setHeatmapHeight(20);
                            if(track.Toolbar) track.Toolbar.hide()
                        }
                        if(AnnoJ.config.settings.display == 0){
                            var height = findConf(track.config.id).height;
                            track.setHeight(height);
                            if(track.Toolbar) track.Toolbar.show()
                        }
                    }
                  }
            }
        });
		
        
        if(AnnoJ.config.settings.scale == 0) {
			checked1 = true;
			checked2 = false;		
        }
		if(AnnoJ.config.settings.scale == 1) {
            checked1 = false;
            checked2 = true
        }
        if(AnnoJ.config.settings.scale == 2) {
            checked1 = false
            checked2 = false
        }
        var scaleMode = new Ext.CycleButton({
            showText: true,
            prependText: 'Scale:',
            tooltip: 'Scaling method for multiple tracks',
            items: [{
                text: 'Fixed(Genome)',
                checked: !checked1 && !checked2
                },
                {
                    text: 'Individual(Screen)',
                    checked: checked1
                },
                {
                    text: 'Uniform(Screen)',
                    checked: checked2
                }],
                changeHandler: function(btn, item) {
					
                    if(item.text == "Uniform(Screen)") AnnoJ.config.settings.scale = 1;
                    else if(item.text == "Fixed(Genome)") AnnoJ.config.settings.scale = 2;
                    else AnnoJ.config.settings.scale = 0;
					
                }
        });
        var scaleBox = new Ext.form.TextField({
            width: 30,
            value: 1,
            maskRe: /[0-9\.]/,
            regex: /^[0-9\.]+$/,
            selectOnFocus: true
        });
        scaleBox.on('specialKey',
        function(config, event) {
          if (event.getKey() == event.ENTER) {
            var f = scaleBox.getValue();
            if(f == "") f = 1;
            scaleBox.setValue(f);
            var Tracks = AnnoJ.getGUI().Tracks;
            if(Tracks){
               for(var i = 0; i <  Tracks.tracks.tracks.length; i++){
                  var track = Tracks.tracks.tracks[i];
                  if(Tracks.tracks.isActive(track)) track.Toolbar.setScale(f);
               }
            }
            AnnoJ.config.settings.multi = 1
         }
        });
        var GlobalscaleBox = new Ext.form.TextField({
            width: 30,
            value: AnnoJ.config.settings.yaxis,
            maskRe: /[0-9\.]/,
            regex: /^[0-9\.]+$/,
            selectOnFocus: true
        });
        GlobalscaleBox.on('specialKey',
        function(config, event) {
          if (event.getKey() == event.ENTER) {
            var scaler = GlobalscaleBox.getValue();
            if(scaler == "") GlobalscaleBox.setValue(AnnoJ.config.settings.yaxis);
            AnnoJ.config.settings.yaxis = GlobalscaleBox.getValue()
          }
        });

        var dragMode = new Ext.CycleButton({
            showText: true,
            prependText: 'Drag mode: ',
            tooltip: 'Action to be performed when you click and drag in a track',
            items: [{
                text: 'browse',
                iconCls: 'silk_cursor',
                checked: true
            },
            {
                text: 'zoom',
                iconCls: 'silk_magnifier'
            },
            {
                text: 'scale',
                iconCls: 'silk_arrow_inout'
            },
            {
                text: 'resize',
                iconCls: 'silk_shape_handles'
            }],
            changeHandler: function(btn, item) {
                self.fireEvent('dragModeSet', item.text)
            }
        });
        Ext.EventManager.addListener(window, 'keyup',
        function(event) {
            if (event.getTarget().tagName == 'INPUT') return;
            if (event.getKey() != 16) return;
            dragMode.toggleSelected()
        });
        function setDragMode(mode) {
            dragMode.suspendEvents();
            var num = 0;
            var max = dragMode.items.length;
            while (dragMode.getActiveItem().text != mode) {
                dragMode.toggleSelected();
                if (++num > max) break
            }
            dragMode.resumeEvents()
        };
        function setSelected(option, mode) {
            option.suspendEvents();
            var num = 0;
            var max = option.items.length;
            while (option.getActiveItem().text != mode) {
                option.toggleSelected();
                if (++num > max) break
            }
            option.resumeEvents()
        };
        var ratio = new Ext.form.TextField({
            width: 60,
            maskRe: /[0-9:]/,
            regex: /^[0-9]+:[0-9]+$/,
            selectOnFocus: true
        });
        ratio.on('blur',
        function(config, event) {
            var value = this.getValue() || '10:1';
            var bases = parseInt(value.split(':')[0]);
            var pixels = parseInt(value.split(':')[1]);
            var location = Navigator.setLocation({
                bases: bases,
                pixels: pixels
            });
            refreshControls();
            self.fireEvent('browse', Navigator.getLocation())
        });
        ratio.on('specialKey',
        function(config, event) {
            var value = this.getValue() || '10:1';
            var bases = parseInt(value.split(':')[0]);
            var pixels = parseInt(value.split(':')[1]);
            AnnoJ.config.location.bases = bases;
            AnnoJ.config.location.pixels = pixels;
            if (event.getKey() == event.ENTER) {
                //this.fireEvent('blur');
                var location = Navigator.setLocation({
                    bases: bases,
                    pixels: pixels
                });
                refreshControls();
                self.fireEvent('browse', Navigator.getLocation())
            }
        });
        var further = new Ext.Button({
            iconCls: 'silk_zoom_out',
            tooltip: 'Zoom out by a fixed increment',
            handler: function() {
                if (this.disabled) return;
                Navigator.step(false);
                refreshControls();
                self.fireEvent('browse', Navigator.getLocation())
            }
        });
        var closer = new Ext.Button({
            iconCls: 'silk_zoom_in',
            tooltip: 'Zoom in by a fixed increment',
            handler: function() {
                if (this.disabled) return;
                Navigator.step(true);
                refreshControls();
                self.fireEvent('browse', Navigator.getLocation())
            }
        });
        var assembly = new Ext.form.ComboBox({
            typeAhead: true,
            triggerAction: 'all',
            width: 40,
            grow: true,
            growMin: 40,
            growMax: 100,
            forceSelection: true,
            mode: 'local',
            displayField: 'id'
        });
        assembly.on('select',
        function(e) {
        AnnoJ.config.location.assembly = e.getValue()
        });
        var jumpLeft = new Ext.form.NumberField({
            width: 70,
        readOnly: true,
            allowNegative: false,
            allowDecimals: false,
            grow: true,
            growMin: 50,
            growMax: 100
        });
        var jumpRight = new Ext.form.NumberField({
            width: 70,
            readOnly: true,
            allowNegative: false,
            allowDecimals: false,
            grow: true,
            growMin: 50,
            growMax: 100
        });
        var jump = new Ext.form.NumberField({
            width: 70,
            allowNegative: false,
            allowDecimals: false,
            grow: true,
            growMin: 50,
            growMax: 100
        });
        jump.on('specialKey',
        function(config, event) {
        AnnoJ.config.location.assembly = assembly.getValue();
        AnnoJ.config.location.position = parseInt(this.getValue());
        if (event.getKey() == event.ENTER) {
                Navigator.setLocation({
                    assembly: assembly.getValue(),
                    position: parseInt(this.getValue())
                });
                refreshControls();
                self.fireEvent('browse', Navigator.getLocation())
        }
        });
        var go = new Ext.Button({
            iconCls: 'silk_server_go',
            text: 'Go',
            tooltip: 'Browse to the specified position',
            handler: function() {
                Navigator.setLocation({
                    assembly: assembly.getValue(),
                    position: parseInt(jump.getValue())
                });
                refreshControls();
                self.fireEvent('browse', Navigator.getLocation())
            }
        });
        var prev = new Ext.Button({
            iconCls: 'silk_arrow_left',
            tooltip: 'Jump one screen to the left',
            handler: function() {
                Navigator.bump( - Navigator.pixels2bases(Toolbar.getSize().width));
                refreshControls();
                self.fireEvent('browse', Navigator.getLocation())
            }
        });
        var next = new Ext.Button({
            iconCls: 'silk_arrow_right',
            tooltip: 'Jump one screen to the right',
            handler: function() {
                Navigator.bump(Navigator.pixels2bases(Toolbar.getSize().width));
                refreshControls();
                self.fireEvent('browse', Navigator.getLocation())
            }
        });
        function bindAssemblies(options, selected) {
            if (!options || options.length == 0) return;
            var temp = [];
            Ext.each(options,
            function(item) {
                temp.push([item.id])
            });
            if (!selected) selected = temp[0];
            var store = new Ext.data.SimpleStore({
                fields: ['id'],
                data: temp
            });
            assembly.bindStore(store);
            assembly.setValue(selected);
            Navigator.Assembly.init({
                options: options,
                selected: selected
            })
        };
        function refreshControls() {
            var view = Navigator.getLocation();
            assembly.setValue(AnnoJ.config.location.assembly);
            closer.enable();
            further.enable();
            if (Navigator.Zoom.atMin()) closer.disable();
            if (Navigator.Zoom.atMax()) further.disable();
            ratio.setValue(view.bases + ':' + view.pixels);
            prev.enable();
            next.enable();
            if (Navigator.Position.atMin()) prev.disable();
            if (Navigator.Position.atMax()) next.disable();
            jump.setValue(view.position);
            var edges = Navigator.getEdges();
            jumpLeft.setValue(edges.g1);
            jumpRight.setValue(edges.g2)
        };
        function loadSettings(settings) {
            var Tracks = AnnoJ.getGUI().Tracks;
            for(var i = 0; i < AnnoJ.config.active.length; i++){
                var id = AnnoJ.config.active[i];
                for(var j =  settings.active.length-1; j >= 0; j--){
                    if(id == settings.active[j]) break
                }
                if(j < 0) {
                    var track = Tracks.tracks.find('id', id);
                    if (track) Tracks.tracks.close(track)
                }
            } 
            for(var i = 0; i < settings.active.length; i++) {
            var id = settings.active[i];
                for(var j =  AnnoJ.config.active.length-1; j >= 0; j--) {
                if(id == AnnoJ.config.active[j]) break
            }
            if(j < 0) {
                var track = Tracks.tracks.find('id', id);
                if (track) {
                    var first = null;
                if(i > 0){
                    var id = settings.active[i-1];
                    first = Tracks.tracks.find('id', id);
                }
                   Tracks.tracks.manage(track);
                   Tracks.tracks.insert(track, first);
                   track.setLocation(AnnoJ.config.location)
                   //AnnoJ.getGUI().TrackSelector.activate(track);
                   //Tracks.tracks.open(track)
                }
            }
        }

        Ext.apply(AnnoJ.config, settings);
        Baseline.setValue(AnnoJ.config.settings.baseline);
        if(AnnoJ.config.settings.dislay == 0) setSelected(showMode, 'Histogram');
        else setSelected(showMode, 'Heatmap');
        if(AnnoJ.config.settings.scale == 0) setSelected(scaleMode, 'Individual(Screen)');
        else if(AnnoJ.config.settings.scale == 1) setSelected(scaleMode, 'Uniform(Screen)');
        else setSelected(scaleMode, 'Fixed(Screen)');
        scaleBox.setValue(1);
        GlobalscaleBox.setValue(AnnoJ.config.settings.yaxis);
        ratio.setValue(AnnoJ.config.location.bases + ':' + AnnoJ.config.location.pixels);
        assembly.setValue(AnnoJ.config.location.assembly);
        if(Tracks){
        for(var i = 0; i <  Tracks.tracks.tracks.length; i++){
            var track = Tracks.tracks.tracks[i];
            var conf = find(AnnoJ.config.tracks,'id',track.config.id);
            if(!conf){
                track.Toolbar.setScale(conf.scale, true);
                //track.setHeight(AnnoJ.config.tracks[j].height)
            }
        }
        }
           
        Navigator.setLocation(AnnoJ.config.location);
        self.fireEvent('browse', Navigator.getLocation())
        };
        function setTitle(txt) {
            title.el.dom.innerHTML = txt
        };
        return {
            info: info,
            title: title,
            filler: filler,
            dragMode: dragMode,
            scaleMode: scaleMode,
            showMode: showMode,
            scaleBox: scaleBox,
            Baseline: Baseline,
            GlobalscaleBox: GlobalscaleBox,
            ratio: ratio,
            further: further,
            closer: closer,
            assembly: assembly,
            jump: jump,
            jumpLeft: jumpLeft,
            jumpRight: jumpRight,
            go: go,
            prev: prev,
            next: next,
            bindAssemblies: bindAssemblies,
            refreshControls: refreshControls,
            loadSettings: loadSettings,
            setTitle: setTitle,
            setSelected: setSelected,
            setDragMode: setDragMode
        }
    })();
    var Toolbar = new Ext.Toolbar({
        cls: 'AJ_Navbar',
        items: [Controls.info, Controls.title, '->', 'Baseline', Controls.Baseline, Controls.showMode,'-', Controls.scaleMode, 'Multi', Controls.scaleBox, 'Y-axis', Controls.GlobalscaleBox,'-', Controls.dragMode, ' ', Controls.ratio, Controls.further, Controls.closer, ' ', Controls.assembly, Controls.jump, Controls.go, Controls.prev, Controls.next]
    });
    Toolbar.on('render',
    function() {
        this.un('render');
        Controls.refreshControls()
    });
    
    var ehandler =  function(method){
        if(!AnnoJ.config.trks) return;

        var trackConfig = {};
        trackConfig.urls = '';

        var first = null;
        var newid = '';
        var newname = '';
        for(var i = 0; i < AnnoJ.config.trks.length; i++){
          var id = AnnoJ.config.trks[i];
          var track = AnnoJ.getGUI().Tracks.tracks.find('id', id);
          if(track){
            trackConfig.urls += track.config.data + ',';
            newid += '-' + track.config.id;
            newname += '-' + track.config.name;
            if(!first ) first = track
          }
        }
        var loc = AnnoJ.getLocation();
        trackConfig.data = '/proxy/http://tabit.ucsd.edu/fetchers/analysis.php';
        trackConfig.name = method + newname;
        trackConfig.type = 'ReadsTrack';
        trackConfig.path = 'analysis';
        trackConfig.action = method;
        trackConfig.color = {};
        trackConfig.scale = 1;
        trackConfig.assembly = AnnoJ.config.location.assembly;
        trackConfig.showControls = true;
        if(method == 'Correlation' || method == 'Intensity'){
          trackConfig.id = 'trackyyyy-0';
          trackConfig.height = 160;
          var trk = AnnoJ.getGUI().TracksInfo.tracks.tracks[0];
          if(trk){
             trk.close();
             trk = null;
             AnnoJ.getGUI().TracksInfo.tracks.tracks[0] = null;
          }
        }
        else if(method == 'Peakcall'){
          trackConfig.height = 80;
          trackConfig.id = 'new-' + method + newid;
          if(first.config.type != 'ReadsTrack'){
             Ext.MessageBox.alert('Warning', trackConfig.name + ' is not ReadsTrack, can not launch!');
             return
          }
          BaseJS.request({
            url: trackConfig.data,
            method: 'POST',
            requestJSON: false,
            data: {
               action: 'range',
               assembly: trackConfig.assembly,
               left: loc.left,
               right: loc.right,
               bases: loc.bases,
               pixels: loc.pixels,
               action2: method,
               urls: trackConfig.urls,
               tracktype: trackConfig.type,
               table: trackConfig.name
            },
            success: function(response) {
               trackConfig.data = response.data
               trackConfig.type = 'IntensityTrack';
               var trk = AnnoJ.getGUI().Tracks.tracks.find('id', trackConfig.id);
               if(trk){
                   Ext.MessageBox.alert('Warning', trackConfig.name + ' already exists!');
                   return
               }
               try {
                 var trk = new AnnoJ[trackConfig.type](trackConfig);
               } 
               catch(e) 
               {
                 WebApp.error(e);
                 if(!Ext.isIE) console.log(e);
                 return 
               };
                 var j = AnnoJ.config.active.indexOf(first.config.id);
                 if(j >= 0) {
                    AnnoJ.config.active.splice(j, 0, trackConfig.id);
                    AnnoJ.config.tracks.splice(j, 0, trackConfig)
                 }
                AnnoJ.getGUI().Tracks.tracks.manage(trk);
                AnnoJ.getGUI().Tracks.tracks.insert(trk, first);
                AnnoJ.getGUI().TrackSelector.manage(trk);
                AnnoJ.getGUI().TrackSelector.insertBefore(trk, first.config.name);
                AnnoJ.getGUI().Tracks.tracks.refresh();
                trk.setLocation(loc)
            },
            failure: function(message) {
                 Ext.MessageBox.alert('Warning', 'Failed to call peaks!');
                 return;
            }
          })
        }
        else{
            trackConfig.height = 80;
            trackConfig.id = 'new-' + method + newid;
            var trk = AnnoJ.getGUI().Tracks.tracks.find('id', trackConfig.id);
            if(trk){
                Ext.MessageBox.alert('Warning', trackConfig.name + ' already exists!');
                return false
            }
        }
        if(method == 'Peakcall') return;

        try {
            var trk = new AnnoJ[trackConfig.type](trackConfig);
        } 
        catch(e) 
        {
           WebApp.error(e);
           if(!Ext.isIE) console.log(e);
           return false
        };
        if(method == 'Correlation' || method == 'Intensity'){
            Ext.apply(AnnoJ.config.infoTrack, trackConfig);
            AnnoJ.getGUI().TracksInfo.tracks.tracks[0] = trk;
            AnnoJ.getGUI().TracksInfo.tracks.open(trk);
            if(trk) trk.setLocation(loc);
        }
        else{
            var j = AnnoJ.config.active.indexOf(first.config.id);
            if(j >= 0) {
                AnnoJ.config.active.splice(j, 0, trackConfig.id);
                AnnoJ.config.tracks.splice(j, 0, trackConfig)
            }
            AnnoJ.getGUI().Tracks.tracks.manage(trk);
            AnnoJ.getGUI().Tracks.tracks.insert(trk, first);
            AnnoJ.getGUI().TrackSelector.manage(trk);
            AnnoJ.getGUI().TrackSelector.insertBefore(trk, first.config.name);
            AnnoJ.getGUI().Tracks.tracks.refresh();
            trk.setLocation(loc)
        }
    };
    var savehandler =  function(slot){
        var loc = AnnoJ.getLocation();
        AnnoJ.config.location.position = loc.position;
        var gurl = '/proxy/http://tabit.ucsd.edu/fetchers/save.php';
            BaseJS.request({
                url: gurl,
                method: 'POST',
                requestJSON: false,
                data: {
                    action: 'save',
                    slot: slot,
                    name: document.URL,
                    tracks: Ext.encode(AnnoJ.config.tracks),
                    active: Ext.encode(AnnoJ.config.active),
                    genome: Ext.encode(AnnoJ.config.genome),
                    bookmarks: Ext.encode(AnnoJ.config.bookmarks),
                    stylesheets: Ext.encode(AnnoJ.config.stylesheets),
                    location: Ext.encode(AnnoJ.config.location),
                    settings: Ext.encode(AnnoJ.config.settings),
                    admin: Ext.encode(AnnoJ.config.admin)
                },
                success: function(response) {
                   Ext.Msg.alert('Settings',response.message)
                },
                failure: function(message) {
                    AnnoJ.error(message)
                }
            })
        return
    };
    var snapshot =  function(action, slot){
        var gurl = '/proxy/http://tabit.ucsd.edu/fetchers/save.php';
        BaseJS.request({
            url: gurl,
            method: 'POST',
            requestJSON: false,
            data: {
                action: action,
                name: document.URL,
                slot: slot
            },
            success: function(response) {
                var settings = eval(response.message);
                if(action == 'load') Controls.loadSettings(settings)
                if(action == 'parameter'){
                    for(var i = 0; i < settings.length; i++){
                        if(settings[i] == '') continue;
                        var rs = [new Ext.data.Record({id:settings[i]})];
                            if(store) store.insert(store.getCount(),rs)
                    }
                }
            },
            failure: function(message) {
                AnnoJ.error(message)
            }
        })
        return
    };
    var saveMarker = new Ext.Button({
        iconCls: 'silk_server_go',
        text: 'Snapshot',
        tooltip: 'Tag current view settings',
        handler: function() {
        Ext.MessageBox.prompt("Mark view","Please input name:",function(bu,txt){
        if(bu == 'ok'){
            if((store && store.getCount() > 50) || txt == '' || txt == 'default') return;
            if(txt == 'snapshots') txt = txt + '-1';
            for(var i = store.getCount()-1; i >= 0; i--){
            if(store.getAt(i).data.id == txt) break
            }
            savehandler(txt);
            if(i < 0){
                var rs = [new Ext.data.Record({id:txt})];
                if(store) store.insert(store.getCount(),rs)
            }
        }
        }, null, false, AnnoJ.config.markname);
    }
     });
    var runSave = new Ext.Button({
        iconCls: 'silk_server_go',
        text: 'Save',
        tooltip: 'Save to default settings',
        handler: function() {
            savehandler('default')
    }
     });
    var sItem = 'default';
    var runLoad = new Ext.Button({
        iconCls: '',
        text: 'Load',
        tooltip: 'Load snapshot',
        handler: function() {
        if(sItem == '') return;
        snapshot('load', sItem)
    }
     });
    var runRemove = new Ext.Button({
        iconCls: '',
        text: 'Remove',
        tooltip: 'Remove this snapshot',
        handler: function() {
        if(sItem == 'default') return;
        Ext.MessageBox.confirm('Confirm', 'Remove ' + sItem + '?', function(id){
        if(id == 'no') return;
        else{
            for(var i = 0; i < store.getCount(); i++){
            if(store.getAt(i).data.id == sItem) break
            }
                    store.removeAt(i);
                 loader.setValue(['default']);
                snapshot('remove', sItem);
            sItem = 'default'
        }
        }); 
    }
     });
     var loader = new Ext.form.ComboBox({
        typeAhead: true,
        triggerAction: 'all',
        width: 60,
        grow: true,
        growMin: 40,
        growMax: 100,
        forceSelection: true,
        mode: 'local',
        displayField: 'id'
     });
     loader.on('select',
     function(e) {
    sItem = e.getValue()
     });
     var store = new Ext.data.SimpleStore({
        fields: ['id'],
        data: [['default']]
     });
     loader.bindStore(store);
     loader.setValue(['default']);
     snapshot('parameter');

    var runMerge = new Ext.Button({
        iconCls: 'silk_server_go',
        text: 'Merge',
        tooltip: 'Merge tracks',
        handler: function() {
            ehandler("Merge")
    }
     });
    var runIntersection = new Ext.Button({
        iconCls: 'silk_server_go',
        text: 'Intersect',
        tooltip: 'Get intersection',
        handler: function() {
            ehandler("Intersection")
    }
     });
    var runSummation = new Ext.Button({
        iconCls: 'silk_server_go',
        text: 'Sum',
        tooltip: 'Sum up the tracks',
        handler: function() {
            ehandler("Summation")
    }
     });
    var runSubtract = new Ext.Button({
        iconCls: 'silk_server_go',
        text: 'Subtract',
        tooltip: 'Subtract tracks',
        handler: function() {
            ehandler("Subtract")
    }
     });
    var runIntensity = new Ext.Button({
        iconCls: 'silk_server_go',
        text: 'Intensity',
        tooltip: 'get Intensity',
        handler: function() {
            ehandler("Intensity")
    }
     });
    var runCorrelation = new Ext.Button({
        iconCls: 'silk_server_go',
        text: 'Correlation',
        tooltip: 'Run pearson correlation',
        handler: function() {
            ehandler("Correlation")
    }
     });
    var runPeakcall = new Ext.Button({
        iconCls: 'silk_server_go',
        text: 'Peak calling',
        tooltip: 'Peak calling',
        handler: function() {
            ehandler("Peakcall")
    }
     });
    var Tbar = new Ext.Toolbar({
    xtype: 'container',
    layout: 'anchor',
    defaults: {anchor: '0'},
    defaultType: 'toolbar',
    items: [{
          items: Toolbar
        }, 
        {
            items: [Controls.jumpLeft, '-', runSave, saveMarker, '-', loader, runLoad, runRemove, '-', 'Analysis:', runMerge, runIntersection, runSummation, runSubtract, runIntensity, runCorrelation, runPeakcall, '->', Controls.jumpRight]
        }]
    });

    this.ext = Tbar;
    this.getLocation = Navigator.getLocation;
    this.setLocation = Navigator.setLocation;
    this.pixels2bases = Navigator.pixels2bases;
    this.bases2pixels = Navigator.bases2pixels;
    this.xpos2gpos = Navigator.xpos2gpos;
    this.gpos2xpos = Navigator.gpos2xpos;
    this.syndicate = Syndicator.syndicate;
    this.setTitle = Controls.setTitle;
    this.setDragMode = Controls.setDragMode
};
Ext.extend(AnnoJ.Navigator, Ext.util.Observable);
AnnoJ.InfoToolBar = function() {
    var self = this;
    var Controls = (function() {
        var zoomMode = new Ext.CycleButton({
            showText: true,
        prependText: 'Zoom:',
            items: [
        { text: '1000:1' },
            { text: '100:1' },
            { text: '10:1', checked: true },
            { text: '1:1' },
            { text: '1:5' },
            { text: '1:10' }
        ],
            changeHandler: function(btn, item) {
        var value = item.text;
                InfoRequest.bases = parseInt(value.split(':')[0]);
                InfoRequest.pixels = parseInt(value.split(':')[1])
            }
        });

    var checked1 = true;
    if(AnnoJ.config.settings.hic_d == 1) checked1 = false;
        var HicOri = new Ext.CycleButton({
            showText: true,
        prependText: 'HiC Axis:',
            items: [
            { text: 'chr1(horizontal)', checked: checked1 },
            { text: 'chr2(vertical)', checked: !checked1 }
        ],
            changeHandler: function(btn, item) {
        if(item.text == 'chr1(horizontal)') AnnoJ.config.settings.hic_d = 0;
        if(item.text == 'chr2(vertical)') AnnoJ.config.settings.hic_d = 1
            }
        });

        return {
            zoomMode: zoomMode,
            HicOri: HicOri
        }
    })();
    var Toolbar = new Ext.Toolbar({
        cls: 'AJ_Navbar',
        items: [Controls.zoomMode, Controls.HicOri]
    });
    this.ext = Toolbar;
};
Ext.extend(AnnoJ.InfoToolBar, Ext.util.Observable);
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
var Mouse = new Mouse();
AnnoJ.Bookmarker = (function() {
    var server = '';
    var bookmarks = [];
    var input = new Ext.form.TextField({
        allowBlank: false,
        width: 135
    });
    var button = new Ext.Button({
        text: 'Save',
        iconCls: 'silk_disk',
        tooltip: 'Save a bookmark of the current loation',
        handler: function() {
            if (!input.isValid()) {
                WebApp.warning('Please enter a bookmark name');
                return
            }
            add(input.getValue())
        }
    });
    var body = new Ext.Element(document.createElement('DIV'));
    body.addClass('AJ_bookmarks');
    var toolbar = new Ext.Toolbar({
        items: [input, button]
    });
    function load() {
        if (!server) return;
        BaseJS.request({
            url: server,
            method: 'POST',
            request: {
                action: 'load'
            },
            success: function(response) {
                bookmarks = response;
                redraw()
            },
            failure: function(response) {
                AnnoJ.error(response)
            }
        })
    };
    function save() {
        if (!server) return;
        BaseJS.request({
            url: server,
            method: 'POST',
            request: {
                action: 'save',
                bookmarks: bookmarks
            },
            success: function(response) {
                AnnoJ.notice(response, false)
            },
            failure: function(response) {
                AnnoJ.error(response)
            }
        })
    };
    function redraw() {
        components.body.update('');
        Ext.each(bookmarks, render)
    };
    function render(bookmark) {
        var row = new Ext.Element(document.createElement('DIV'));
        var del = new Ext.Element(document.createElement('DIV'));
        var nam = new Ext.Element(document.createElement('DIV'));
        row.addClass('AJ_bookmark');
        del.addClass('AJ_bookmark_delete');
        nam.addClass('AJ_bookmark_select');
        row.bookmark = bookmark;
        row.appendChild(del);
        row.appendChild(nam);
        row.appendTo(body);
        var loc = bookmark.location;
        nam.update(bookmark.name + ' (' + loc.assembly + ':' + loc.position + ' @ ' + loc.bases + ':' + loc.pixels + ')');
        del.on('click',
        function(event) {
            event.stopEvent();
            remove(row.bookmark);
            row.remove()
        });
        nam.on('click',
        function(event) {
            event.stopEvent();
            if(!Ext.isIE) console.log(row.bookmark.location);
            AnnoJ.setLocation(row.bookmark.location)
        })
    };
    function add(name) {
        var loc = AnnoJ.getLocation();
        var bookmark = {
            name: name,
            location: {
                assembly: loc.assembly,
                position: loc.position,
                bases: loc.bases,
                pixels: loc.pixels
            }
        };
        bookmarks.push(bookmark);
        render(bookmark)
    };
    function remove(bookmark) {
        var clean = [];
        Ext.each(bookmarks,
        function(item) {
            if (item != bookmarks) {
                clean.push(item)
            }
        });
        delete bookmark;
        bookmarks = clean;
        save()
    };
    return function(url) {
        AnnoJ.Bookmarker.superclass.constructor.call(this, {
            title: 'Bookmarks',
            border: false,
            iconCls: 'silk_book_open',
            autoScroll: true,
            contentEl: body,
            tbar: toolbar
        });
        server = url || '';
        this.load = load
    }
})();
Ext.extend(AnnoJ.Bookmarker, Ext.Panel);
AnnoJ.Messenger = (function() {
    var body = new Ext.Element(document.createElement('DIV'));
    body.addClass('AJ_system_messages');
    function clear() {
        body.update('')
    };
    function alert(message, type, important) {
        if (!type || (type != 'error' && type != 'warning' && type != 'notice')) type = 'notice';
        body.update("<div class='AJ_system_" + type + "'>" + message + "</div>" + body.dom.innerHTML)
    };
    function error(message) {
        if (!Ext.isIE && console) console.trace();
        alert(message, 'error', true)
    };
    function warning(message) {
        if (!Ext.isIE && console) console.trace();
        alert(message, 'warning', true)
    };
    function notice(message, important) {
        alert(message, 'notice', important || false)
    };
    return function() {
        AnnoJ.Messenger.superclass.constructor.call(this, {
            title: 'System Messages',
            iconCls: 'silk_terminal',
            autoScroll: true,
            border: false,
            contentEl: body
        });
        this.clear = clear;
        this.alert = alert;
        this.error = error;
        this.warning = warning;
        this.notice = notice
    }
})();
Ext.extend(AnnoJ.Messenger, Ext.Panel);
AnnoJ.InfoBox = function() {
    var self = this;
    var body = new Ext.Element(document.createElement('DIV'));
    body.addClass('AJ_infobox');
    var innerHTML = {
        annoj: '',
        citation: '',
        message: ''
    };
    AnnoJ.InfoBox.superclass.constructor.call(this, {
        title: 'Information',
        iconCls: 'silk_information',
        border: false,
        contentEl: body,
        autoScroll: true
    });
    this.echo = function(msg) {
        self.expand();
        if (typeof(msg) == 'object') {
            var html = '<table>';
            for (var name in msg) {
                html += "<tr><td><b>" + name + "</b></td><td>" + msg[name] + "</td></tr>"
            }
            html += "</table>";
            body.update(html);
            return
        }
        body.update(msg)
    }
};
Ext.extend(AnnoJ.InfoBox, Ext.Panel);
AnnoJ.AboutBox = (function() {
    var info = {
        logo: "<a href='http://www.annoj.org'><img src='http://neomorph.salk.edu/epigenome/img/Anno-J.jpg' alt='Anno-J logo' /></a>",
        version: 'Beta 1.5',
        engineer: 'Julian Tonti-Filippini, Tao Wang',
        //contact: 'tontij01(at)student.uwa.edu.au',
        //copyright: '&copy; 2008 Julian Tonti-Filippini',
        copyright: '&copy; Julian Tonti-Filippini, Tao Wang',
        website: "<a href='http://www.annoj.org'>http://www.annoj.org</a>",
        tutorial: "<a target='new' href='http://neomorph.salk.edu/index.html'>SALK example</a>",
        //website: "",
        //tutorial: "",
        license: "<a target='new' rel='license' href='http://creativecommons.org/licenses/by-nc-sa/3.0/'><img alt='Creative Commons License' style='border-width:0' src='http://i.creativecommons.org/l/by-nc-sa/3.0/88x31.png' /></a>"
    };
    var body = new Ext.Element(document.createElement('DIV'));
    var html = "<div style='padding-bottom:10px;'>" + info.logo + "</div>" + "<table style='font-size:10px';>" + "<tr><td><div><b>Version: </b></td><td>" + info.version + "</div></td></tr>" + "<tr><td><div><b>Engineer: </b></td><td>" + info.engineer + "</div></td></tr>" + "<tr><td><div><b>Contact: </b></td><td>" + info.contact + "</div></td></tr>" + "<tr><td><div><b>Copyright: </b></td><td>" + info.copyright + "</div></td></tr>" + "<tr><td><div><b>Website: </b></td><td>" + info.website + "</div></td></tr>" + "<tr><td><div><b>License: </b></td><td>" + info.license + "</div></td></tr>" + "<tr><td><div><b>Tutorial: </b></td><td>" + info.tutorial + "</div></td></tr>" + "</table>";
    body.addClass('AJ_aboutbox');
    body.update(html);
    function addCitation(c) {
        body.update(c + html)
    };
    return function() {
        AnnoJ.AboutBox.superclass.constructor.call(this, {
            title: 'Citation',
            iconCls: 'silk_user_comment',
            border: false,
            contentEl: body,
            autoScroll: true
        });
        this.info = info;
        this.addCitation = addCitation
    }
})();
Ext.extend(AnnoJ.AboutBox, Ext.Panel);
AnnoJ.StyleSelector = (function() {
    var stylesheets = [];
    var body = new Ext.Element(document.createElement('TABLE'));
    body.addClass('AJ_style_selector');
    function manage(css) {
        Ext.apply({},
        css || {},
        {
            id: '',
            href: '',
            active: false,
            name: 'Unknown'
        });
        if (!css.id || !css.href) return;
        var row = new Ext.Element(document.createElement('TR'));
        row.appendTo(body);
        var td1 = new Ext.Element(document.createElement('TD'));
        var td2 = new Ext.Element(document.createElement('TD'));
        row.appendChild(td1);
        row.appendChild(td2);
        td2.update(css.name);
        var cb = new Ext.form.Checkbox({
            checked: css.active,
            renderTo: td1
        });
        cb.css = css;
        cb.on('check',
        function(me, checked) {
            checked ? enable(me.css) : disable(me.css)
        });
        stylesheets.push(css);
        if (css.active) {
            css.active = false;
            enable(css)
        }
    };
    function enable(css) {
        if (css.active) return;
        var node = document.createElement('LINK');
        node.setAttribute('id', css.id);
        node.setAttribute('type', 'text/css');
        node.setAttribute('rel', 'stylesheet');
        node.setAttribute('href', css.href);
        var head = document.getElementsByTagName('HEAD')[0];
        head.appendChild(node);
        css.active = true
    };
    function disable(css) {
        if (!css.active) return;
        var el = document.getElementById(css.id);
        if (el) el.parentNode.removeChild(el);
        css.active = false
    };
    return function() {
        AnnoJ.StyleSelector.superclass.constructor.call(this, {
            title: 'Styles',
            iconCls: 'silk_css',
            autoScroll: true,
            border: false,
            contentEl: body
        });
        this.manage = manage
    }
})();
Ext.extend(AnnoJ.StyleSelector, Ext.Panel);
AnnoJ.Tracks = function(userConfig) {
    var self = this;
    var body = new Ext.Element(document.createElement('DIV'));
    body.addClass('AJ_tracks');
    var defaultConfig = {
        title: 'Tracks',
        region: 'center',
        iconCls: 'silk_bricks',
        deferredRender: true,
        contentEl: body,
        autoScroll: false,
        margin: '0 0 0 0'
    };
    var config = defaultConfig;
    Ext.apply(config, userConfig || {},
    defaultConfig);
    AnnoJ.Tracks.superclass.constructor.call(this, config);
    self.addEvents({
        'dragStarted': true,
        'dragCancelled': true,
        'dragEnded': true,
        'dragged': true,
        'dragModeSet': true
    });
    var mouse = {
        x: 0,
        y: 0,
        down: false,
        drag: false,
        downX: 0,
        downY: 0
    };
    body.on('scroll',
    function(event) {
        mouse.drag = false;
        mouse.down = false
    });
    body.on('mousedown',
    function(event) {
        if (event.button != 0) return;
        if (event.target.tagName == 'INPUT') return;
        event.stopEvent();
        mouse.drag = false;
        mouse.down = true;
        mouse.downX = mouse.x;
        mouse.downY = mouse.y
    });
    body.on('mousemove',
    function(event) {
        mouse.x = event.getPageX() - this.getX();
        mouse.y = event.getPageY() - this.getY();
        if (!mouse.down) return;
        if (!mouse.drag) {
            mouse.drag = true;
            self.fireEvent('dragStarted', {
                x: mouse.x,
                y: mouse.y
            });
            return
        }
        self.fireEvent('dragged', {
    
            x1: mouse.downX,
            y1: mouse.downY,
            x2: mouse.x,
            y2: mouse.y
        })
    });
    body.on('mouseup',
    function(event) {
        if (event.button != 0) return;
        if (!mouse.down) return;
        mouse.down = false;
        if (mouse.drag) {
            mouse.drag = false;
            self.fireEvent('dragEnded', {
        x: mouse.x,
        y: mouse.y})
        } else {
            self.fireEvent('released', mouse)
        }
    });
    Ext.EventManager.addListener(window, 'keydown',
    function() {
        if (mouse.drag) {
            mouse.drag = false;
            mouse.down = false;
            self.fireEvent('dragCancelled')
        }
    });
    var dragMode = 'browse';
    this.setDragMode = function(mode, broadcast) {
        if (mouse.drag) {
            mouse.drag = false;
            self.fireEvent('dragCancelled')
        }
        if (mode == dragMode) return;
        switch (mode) {
        case 'browse':
            dragMode = mode;
            break;
        case 'zoom':
            dragMode = mode;
            break;
        case 'scale':
            dragMode = mode;
            break;
        case 'resize':
            dragMode = mode;
            break;
        default:
            dragMode = 'browse'
        }
        if (broadcast) {
            self.fireEvent('dragModeSet', dragMode)
        }
    };
    this.getDragMode = function() {
        return dragMode
    };
    Ext.EventManager.addListener(window, 'keyup',
    function(event) {
        if (event.getTarget().tagName == 'INPUT') return;
        switch (event.getKey()) {
        case 66:
            self.setDragMode('browse', true);
            break;
        case 82:
            self.setDragMode('resize', true);
            break;
        case 83:
            self.setDragMode('scale', true);
            break;
        case 90:
            self.setDragMode('zoom', true);
            break
        }
    });
    this.MouseLabel = (function() {
        var ext = Ext.get(document.createElement('DIV'));
        ext.addClass('AJ_mouse_label');
        ext.appendTo(body);
        show(ext);
        body.on('mousemove',
        function(event) {
            event.stopEvent();
            var offset = AnnoJ.bases2pixels(getEdges().g1);
            if (mouse.drag) {
                if (dragMode == 'zoom') {
                    show();
                    showText('<div>' + AnnoJ.pixels2bases(mouse.x + offset) + '</div>&darr;')
                } else if (dragMode == 'scale') {
                    hide()
                } else {
                    show()
                }
            } else {
                show();
        var pp = cursor.offsetTop - cursor.offsetHeight / 2;
        var max;
        if(AnnoJ.config.settings.scale == 0) max = AnnoJ.config.maxlist[cursor.id];
        else if(AnnoJ.config.settings.scale == 2) max = AnnoJ.config.settings.yaxis;
        else max = AnnoJ.config.max;

        var conf = findConf(cursor.id);
        var scroll_width = (body.dom.offsetWidth - body.dom.clientWidth)/2;
        InfoRequest.position = AnnoJ.pixels2bases(mouse.x + offset + scroll_width);
        if(cursor.type == 'HiCTrack'){
           var trk = AnnoJ.getGUI().Tracks.tracks.find('id', cursor.id);
           var data = trk.getData();
           if(data['resolution'] && data['resolution'][2]){
               var bin = data['resolution'][2];
                       var x = AnnoJ.pixels2bases(offset + mouse.x + scroll_width);
               var idx = Math.floor(x / bin);
            if(conf.style == 1){
                var y =  Math.floor(bin / conf.unity *(cursor.offsetHeight - cursor.offsetTop)) + trk.config.indexy * bin;
                var idy = trk.config.indexy + Math.floor((cursor.offsetHeight - cursor.offsetTop) / conf.unity);
                if(data[idx] && data[idx][idy]){
                    var val = data[idx][idy];
                      showText('<div>' + x + ',' + y + ',' + val + '</div>&darr;')
                }
                else showText('<div>' + x + ',' + y + '</div>&darr;')
            }
            if(conf.style == 0){
                var x1 = x - AnnoJ.pixels2bases(cursor.offsetHeight-cursor.offsetTop);
                var x2 = x + AnnoJ.pixels2bases(cursor.offsetHeight-cursor.offsetTop);
                var i1 = Math.floor(x1 / bin);
                var i2 = Math.floor(x2 / bin);
                if(data[i1] && data[i1][i2]){
                    var val = data[i1][i2];
                      showText('<div>' + x + ',' + x1 + ',' + x2 + ',' + val + '</div>&darr;')
                }
                else showText('<div>' + x + ',' + x1 + ',' + x2 + '</div>&darr;')
            }
          }
          else{
            var x = AnnoJ.pixels2bases(offset + mouse.x + scroll_width);
            var x1 = x - AnnoJ.pixels2bases(cursor.offsetHeight-cursor.offsetTop);
            var x2 = x + AnnoJ.pixels2bases(cursor.offsetHeight-cursor.offsetTop);
            showText('<div>' + x + ',' + x1 + ',' + x2 + '</div>&darr;')
          }
        }
        else{
          if(!max || !conf.scale || AnnoJ.config.settings.display == 1){
            showText('<div>' + AnnoJ.pixels2bases(mouse.x + offset + scroll_width) + '</div>&darr;');
          }
          else {
            max /= conf.scale;
            var ppos = Math.round(-pp * max * 20 / cursor.offsetHeight)/10;
            showText('<div>' + AnnoJ.pixels2bases(mouse.x + offset + scroll_width) + ',' + ppos + '</div>&darr;');
          }
        }
            }
            ext.setLeft(mouse.x - Math.round(ext.getWidth() / 2));
            ext.setTop(mouse.y - ext.getHeight() - 5)
        });
        function getEdges() {
            var half = Math.round(AnnoJ.pixels2bases(body.getWidth()) / 2);
            var view = AnnoJ.getLocation();
            return {
                g1: view.position - half,
                g2: view.position + half
            }
        };
        function showText(text) {
            ext.update(text);
            setDisplayed(true)
        };
        function showCoord() {
            var edges = self.getEdges();
            var offset = AnnoJ.bases2pixels(edges.g1);
            showText('<div>' + AnnoJ.pixels2bases(mouse.x + offset) + '</div>&darr;')
        };
        function show_info() {
            info_ext.setDisplayed(true);
            info_ext.setLeft(mouse.x - Math.round(ext.getWidth() / 2) + 2);
            info_ext.setTop(mouse.y - ext.getHeight() - 5)
        }
        function hide_info() {
            info_ext.setDisplayed(false)
        }
        function show() {
            ext.setDisplayed(true);
            ext.setLeft(mouse.x - Math.round(ext.getWidth() / 2) + 2);
            ext.setTop(mouse.y - ext.getHeight() - 5)
        }
        function hide() {
            ext.setDisplayed(false)
        }
        function setDisplayed(state) {
            state ? show() : hide()
        };
        return {
            showText: showText,
            showCoord: showCoord,
            setDisplayed: setDisplayed,
            show: show,
            hide: hide
        }
    })();
    this.Scaler = (function() {
        var container = Ext.get(document.createElement('DIV'));
        container.setStyle('position', 'absolute');
        container.setStyle('z-index', '9999');
        container.appendTo(body);
        var bg = Ext.get(document.createElement('DIV'));
        var fg = Ext.get(document.createElement('DIV'));
        bg.appendTo(container);
        fg.appendTo(container);
        bg.addClass('AJ_scaler_bg');
        fg.addClass('AJ_scaler_fg');
        bg.setStyle('position', 'absolute');
        fg.setStyle('position', 'absolute');
        fg.setBottom(0);
        fg.setLeft(0);
        bg.setTop(0);
        bg.setLeft(fg.getWidth());
        var track = null;
        var scale = 1;
        var start = 1;
        container.hide();
        function showAt(x, y) {
            track = self.tracks.mouse2track(x, y);
            if (!track || !track.getScale || !track.setScale) {
                track = null;
                return
            }
            var val = track.getScale();
            setScale(val);
            start = scale;
            fg.setLeft(0);
            bg.setLeft(fg.getWidth());
            container.show(true);
            container.setX(x - fg.getWidth() - Math.round(bg.getWidth() / 2));
            //container.setY(y - (1 - scale) * bg.getHeight() - body.getScroll().top)
            container.setY(y - (1 - scale) * bg.getHeight())
        };
        function hide() {
            container.hide(true)
        };
        function update(offset) {
            var shift = offset / bg.getHeight();
            var target = start + shift;
            if (target < 0 || target > bg.getHeight()) return;
            setScale(target)
        };
        function setScale(v) {
            if (!track) return;
            if (v > 1) v = 1;
            if (v < 0) v = 0;
            var rounded = Math.round(20 * v) / 20;
            if (scale == rounded) {
                return
            }
            scale = rounded;
            track.setScale(scale);
            var trackConfig = find(AnnoJ.config.tracks,'id',track.config.id);
            if(!trackConfig) trackConfig = AnnoJ.config.tracks[0];
            trackConfig.scale = scale;
            var px = bg.getHeight() - Math.round(scale * bg.getHeight());
            fg.setTop(px - Math.round(fg.getHeight() / 2))
        };
        function getScale() {
            return scale
        };
        body.on('mousedown',
        function(event) {
            if (event.button != 0) return;
            if (dragMode != 'scale') return;
            if (event.getTarget().tagName == 'INPUT') return;
            showAt(event.getPageX() - Math.round(bg.getWidth() / 2) - 2, event.getPageY());
            self.MouseLabel.hide();
            self.CrossHairs.hide()
        });
        body.on('mouseup',
        function() {
            if (dragMode != 'scale') return;
            hide()
        });
        self.on('dragEnded',
        function() {
            if (dragMode != 'scale') return;
            hide()
        });
        self.on('dragged',
        function() {
            if (dragMode != 'scale') return;
            update(mouse.downY - mouse.y)
        });
        return {}
    })();
    this.Resizer = (function() {
        var box = Ext.get(document.createElement('DIV'));
        box.addClass('AJ_resizer');
        box.setStyle('position', 'absolute');
        box.appendTo(body);
        box.hide();
        var height = 0;
        var track = null;
        function bind(track) {
            box.setTop(track.ext.getY() - body.getY());
            box.setLeft(0);
            box.setWidth(track.ext.getWidth());
            box.setHeight(track.ext.getHeight());
            height = box.getHeight();
            show()
        };
        function show() {
            box.show()
        };
        function hide() {
            track = null;
            box.hide(true)
        };
        body.on('mousedown',
        function(event) {
            if (event.button != 0) return;
            if (dragMode != 'resize') return;
            if (event.getTarget().tagName == 'INPUT') return;
            track = self.tracks.mouse2track(event.getPageX(), event.getPageY());
            if (!track) {
                track = null;
                return
            }
            bind(track)
        });
        body.on('mouseup',
        function(event) {
            if (dragMode != 'resize') return;
            if (event.getTarget().tagName == 'INPUT') return;
            track.setHeight(box.getHeight());
            hide()
        });
        self.on('dragged',
        function() {
            if (dragMode != 'resize' || !track) return;
            var h = height + mouse.y - mouse.downY;
            if (h < track.getMinHeight()) return;
            if (h > track.getMaxHeight()) return;
            box.setHeight(height + mouse.y - mouse.downY);
            track.setHeight(box.getHeight())
        });
        self.on('dragEnded',
        function() {
            if (dragMode != 'resize' || !track) return;
            track.setHeight(box.getHeight());
            hide()
        });
        self.on('dragCancelled',
        function() {
            if (dragMode != 'resize' || !track) return;
            hide()
        });
        return {
            show: show,
            hide: hide
        }
    })();
    this.CrossHairs = (function() {
        var gap = 5;
        var showNS = true;
        var showEW = false;
        var north = Ext.get(document.createElement('DIV'));
        var south = Ext.get(document.createElement('DIV'));
        var east = Ext.get(document.createElement('DIV'));
        var west = Ext.get(document.createElement('DIV'));
        north.addClass('AJ_crosshair');
        south.addClass('AJ_crosshair');
        east.addClass('AJ_crosshair');
        west.addClass('AJ_crosshair');
        north.setStyle({
            position: 'absolute',
            top: 0,
            width: 0,
            height: 0,
            borderLeft: 'dotted red 1px'
        });
        south.setStyle({
            position: 'absolute',
            top: 0,
            width: 0,
            height: '100%',
            borderLeft: 'dotted red 1px'
        });
        east.setStyle({
            position: 'absolute',
            left: 0,
            width: '100%',
            height: 0,
            borderTop: 'dotted red 1px'
        });
        west.setStyle({
            position: 'absolute',
            left: 0,
            width: 0,
            height: 0,
            borderTop: 'dotted red 1px'
        });
        north.appendTo(body);
        south.appendTo(body);
        east.appendTo(body);
        west.appendTo(body);
        toggleNS(showNS);
        toggleEW(showEW);
        function setGap(n) {
            gap = Math.max(parseInt(n) || 0, 0)
        };
        function toggleNS(state) {
            showNS = state ? true: false;
            north.setDisplayed(showNS);
            south.setDisplayed(showNS)
        };
        function toggleEW(state) {
            showEW = state ? true: false;
            east.setDisplayed(showEW);
            west.setDisplayed(showEW)
        };
        function setXY(x, y) {
            var x = Math.max(parseInt(x) || 0, 0);
            var y = Math.max(parseInt(y) || 0, 0);
            if (showNS) {
                north.setLeft(x - 1);
                south.setLeft(x - 1);
                north.setHeight(y - gap);
                south.setTop(y + gap)
            }
            if (showEW) {
                east.setTop(y - 1);
                west.setTop(y - 1);
                east.setLeft(x + gap);
                west.setWidth(x - gap)
            }
        };
        function show() {
            toggleNS(true);
            toggleEW(false)
        };
        function hide() {
            toggleNS(false);
            toggleEW(false)
        };
        body.on('mousemove',
        function(event) {
            if (mouse.drag) {
                if (dragMode == 'zoom' || dragMode == 'scale') {
                    toggleNS(false);
                    toggleEW(false);
                    return
                }
            }
            toggleNS(true);
            setXY(mouse.x, mouse.y)
        });
        return {
            setGap: setGap,
            toggleNS: toggleNS,
            toggleEW: toggleEW,
            setXY: setXY,
            show: show,
            hide: hide
        }
    })();
    this.Region = (function() {
        var ext = Ext.get(document.createElement('DIV'));
        ext.addClass('AJ_region_indicator');
        ext.appendTo(body);
        ext.setDisplayed(false);
        function show() {
            ext.setDisplayed(true)
        };
        function hide() {
            ext.setDisplayed(false)
        };
        function setBox(box) {
            ext.setLeft(box.x1);
            ext.setTop(box.y1);
            ext.setWidth(box.x2 - box.x1);
            ext.setHeight(box.y2 - box.y1)
        };
        function getBox() {
            return {
                x1: ext.getLeft(true),
                x2: ext.getLeft(true) + ext.getWidth(),
                y1: ext.getTop(true),
                y2: ext.getTop(true) + ext.getHeight()
            }
        };
        function mouse2box() {
            var x1 = mouse.downX;
            var x2 = mouse.x;
            var y1 = mouse.downY;
            var y2 = mouse.y;
            if (x1 > x2) {
                var temp = x1;
                x1 = x2;
                x2 = temp
            }
            if (y1 > y2) {
                var temp = y1;
                y1 = y2;
                y2 = temp
            }
            return {
                x1: x1,
                x2: x2,
                y1: y1,
                y2: y2
            }
        };
        self.on('dragStarted',
        function() {
            if (dragMode != 'zoom') return;
            setBox({
                x1: mouse.downX,
                y1: mouse.downY,
                x2: mouse.downX,
                y2: mouse.downY
            });
            show()
        });
        self.on('dragEnded',
        function() {
            var box = getBox();
            if (dragMode == 'browse') {
                hide();
                var loc = AnnoJ.getLocation();
                loc.position -= AnnoJ.pixels2bases(mouse.x - mouse.downX);
                loc = AnnoJ.setLocation(loc);

                self.tracks.each(function(track) {
                    track.moveCanvas(0);
                    track.setLocation(loc)
                })
            }
            if (dragMode == 'zoom') {
                var left = ext.getLeft(true);
                var width = ext.getWidth();
                var loc = AnnoJ.getLocation();
                hide();
                if (width < 10) return;
                loc.position = AnnoJ.xpos2gpos(Math.round((box.x1 + box.x2) / 2));
                loc.bases = AnnoJ.pixels2bases(box.x2 - box.x1);
                loc.pixels = body.getWidth();
                loc = AnnoJ.setLocation(loc);
                self.tracks.setLocation(loc)
            }
        });
        self.on('dragged',
        function() {
            var box = mouse2box();
            if (dragMode == 'browse') {
                ext.setLeft(mouse.x - mouse.downX);
                self.tracks.each(function(track) {
                    track.moveCanvas(mouse.x - mouse.downX)
                });
                return
            }
            if (dragMode == 'zoom') {
                box.y1 = 0;
                box.y2 = body.getHeight();
                setBox(box);
                return
            }
        });
        self.on('dragCancelled',
        function() {
            hide()
        });
        return {
            hide: hide,
            show: show,
            setBox: setBox,
            getBox: getBox
        }
    })();
    this.tracks = (function() {
        var active = [];
        var tracks = [];
        var enabled = [];
        var disabled = [];
        var timer = null;
        var focused = null;
        Ext.EventManager.addListener(body.dom, 'scroll',
        function() {
            clearTimeout(timer);
            timer = setTimeout(refresh, 100)
        });
        Ext.EventManager.addListener(window, 'resize',
        function() {
            clearTimeout(timer);
            timer = setTimeout(refresh, 100)
        });
        function refresh() {
            clearTimeout(timer);
            var view = getLocation();
            disabled = [];
            enabled = [];
            Ext.each(active,
            function(track) {
                if (onscreen(track)) {
                    if (track.Syndicator.isSyndicated()) track.unmask();
                    enabled.push(track)
                } else {
                    track.mask('Track temporarily disabled');
                    disabled.push(track)
                }
                track.setLocation(view)
            })
        };
        function onscreen(track) {
            if (body.getTop() > track.ext.getBottom()) return false;
            if (body.getBottom() < track.ext.getTop()) return false;
            return true
        };
        function manage(track) {
            if (!track instanceof AnnoJ.BaseTrack) return;
            if (isManaged(track)) return;
            tracks.push(track);
            track.on('generic', propagate);
            track.on('close', close);
            track.on('browse', setLocation);
            track.on('error', error)
        };
        function unmanage(track) {
            if (!track instanceof AnnoJ.Track) return;
            close(track);
            tracks.remove(track);
            track.un('generic', propagate);
            track.un('close', close);
            track.un('browse', setLocation);
            track.un('error', error)
        };
        function isManaged(track) {
            return tracks.search(track) != -1
        };
        function mouse2track(x, y) {
            var track = null;
            Ext.each(active,
            function(item) {
                var x1 = item.ext.getX();
                var x2 = x1 + item.ext.getWidth();
                var y1 = item.ext.getY();
                var y2 = y1 + item.ext.getHeight();
                if (x >= x1 && x <= x2 && y >= y1 && y <= y2) {
                    track = item;
                    return false
                }
            });
            return track
        };
        function isActive(track) {
            return active.search(track) != -1
        };
        function open(track, existing) {
            if(!track.ext.isVisible()){
                track.ext.setHeight(track.config.height);
                track.ext.setVisible(true)
            }
            if (!isManaged(track)) return;
            if (isActive(track)) return;
            active.push(track);

            if (existing) {
                track.insertBefore(existing.ext)
            } else {
                track.appendTo(body.dom)
            }
            if (!track.Syndicator.isSyndicated()) {
                track.Syndicator.syndicate({
                    success: function() {
                        track.setLocation(AnnoJ.getLocation())
                    },
                    failure: function() {
                        track.mask('Error: track failed to syndicate');
                        AnnoJ.error("Track '" + track.getID() + "' failed to syndicate");
                        close(track)
                    }
                })
            } else {
                track.unmask();
                track.setLocation(AnnoJ.getLocation())
            }
            refresh()
        };
        function myclose(track) {
            if (!isActive(track)) return;
            active.remove(track);
            enabled.remove(track);
            disabled.remove(track);
            track.hide();
            AnnoJ.config.active.remove(track.config.id);
            refresh()
        };
        function close(track) {
            if (!isActive(track)) return;
            active.remove(track);
            enabled.remove(track);
            disabled.remove(track);
            track.close();
            AnnoJ.config.active.remove(track.config.id);

            if(track.config.id.indexOf('new-') >= 0){
              var trk = window.find(AnnoJ.config.tracks, 'id', track.config.id);
              AnnoJ.config.tracks.remove(trk);
              tracks.remove(track);
              track.un('generic', propagate);
              track.un('close', close);
              track.un('browse', setLocation);
              track.un('error', error)
            }
            refresh()
        };
        function reorder(track, existing) {
            //track.remove();
            if (existing) {
                track.insertBefore(existing.ext)
            } else {
                track.appendTo(body.dom)
            }
            AnnoJ.config.active.remove(track.config.id);
            var index = AnnoJ.config.active.search(existing.config.id);
            AnnoJ.config.active.insert(index, track.config.id);
            refresh()
        };
        function insert(track, existing) {
            if (!isManaged(track)) return;
            if (isActive(track)) return;
            active.push(track);
            if (existing) {
                track.insertBefore(existing.ext)
            } else {
                track.appendTo(body.dom)
            }
        };
        function closeAll() {
            Ext.each(active, close)
        };
        function error(track, message) {
            AnnoJ.error('An error was generated by track: ' + track.name + '.<br />The track has been removed from the display.<br />Error: ' + message);
            close(track)
        };
        function propagate(type, data) {
            Ext.each(enabled,
            function(item) {
                item.receive(type, data)
            })
        };
        function setLocation(view) {
            var view = AnnoJ.setLocation(view);
            Ext.each(enabled,
            function(track) {
                track.setLocation(view)
            })
        };
        function getLocation() {
            return AnnoJ.getLocation()
        };
        function clear() {
            while (tracks.length) unmanage(tracks[0])
        };
        function find(param, value) {
            var hit = null;
            Ext.each(tracks,
            function(track) {
                if (track.config[param] && track.config[param] == value) {
                    hit = track;
                    return false
                }
            });
            return hit
        };
        function getConfigs() {
            var list = [];
            Ext.each(tracks,
            function(track) {
                list.push(track.config)
            });
            return list
        };
        function each(func) {
            Ext.each(active, func)
        };
        return {
            manage: manage,
            unmanage: unmanage,
            refresh: refresh,
            clear: clear,
            setLocation: setLocation,
            getLocation: getLocation,
            open: open,
            close: close,
            myclose: myclose,
            reorder: reorder,
            insert: insert,
            body: body,
            isActive: isActive,
            find: find,
            tracks: tracks,
            getConfigs: getConfigs,
            closeAll: closeAll,
            each: each,
            mouse2track: mouse2track
        }
    })()
};
Ext.extend(AnnoJ.Tracks, Ext.Panel);
AnnoJ.TracksInfo = function(userConfig) {
    var self = this;
    var body = new Ext.Element(document.createElement('DIV'));
    body.addClass('AJ_tracks');
    var defaultConfig = {
        title: 'Additonal Information',
        region: 'south',
        iconCls: 'silk_bricks',
        deferredRender: true,
        contentEl: body,
        autoScroll: false,
        margin: '0 0 0 0'
    };
    var config = defaultConfig;
    Ext.apply(config, userConfig || {},
    defaultConfig);
    AnnoJ.TracksInfo.superclass.constructor.call(this, config);
    this.tracks = (function() {
        var active = [];
        var tracks = [];
        var enabled = [];
        var disabled = [];
        var timer = null;
        var focused = null;
        Ext.EventManager.addListener(body.dom, 'scroll',
        function() {
            clearTimeout(timer);
            timer = setTimeout(refresh, 100)
        });
        Ext.EventManager.addListener(window, 'resize',
        function() {
            clearTimeout(timer);
            timer = setTimeout(refresh, 100)
        });
        function refresh() {
            clearTimeout(timer);
            var view = getLocation();
            disabled = [];
            enabled = [];
            Ext.each(tracks,
            function(track) {
                if (onscreen(track)) {
                    if (track.Syndicator.isSyndicated()) track.unmask();
                    enabled.push(track)
                } else {
                    track.mask('Track temporarily disabled');
                    disabled.push(track)
                }
                track.setLocation(view)
            })
        };
        function onscreen(track) {
            if (body.getTop() > track.ext.getBottom()) return false;
            if (body.getBottom() < track.ext.getTop()) return false;
            return true
        };
        function manage(track) {
            if (!track instanceof AnnoJ.BaseTrack) return;
            if (isManaged(track)) return;
            tracks.push(track);
            track.on('generic', propagate);
            track.on('close', close);
            track.on('browse', setLocation);
            track.on('error', error)
        };
        function unmanage(track) {
            if (!track instanceof AnnoJ.Track) return;
            close(track);
            tracks.remove(track);
            track.un('generic', propagate);
            track.un('close', close);
            track.un('browse', setLocation);
            track.un('error', error)
        };
        function isManaged(track) {
            return tracks.search(track) != -1
        };
        function open(track) {
            track.appendTo(body.dom)
        };
        function genid() {
        var id = 'trackxxxx-' + tracks.length;
        return id
    }
        function setLocation(view) {
            var view = AnnoJ.setLocation(view);
            Ext.each(enabled,
            function(track) {
                track.setLocation(view)
            })
        };
        function getLocation() {
            return AnnoJ.getLocation()
        };
        return {
            manage: manage,
            unmanage: unmanage,
            setLocation: setLocation,
            getLocation: getLocation,
            open: open,
            genid: genid,
            body: body,
            tracks: tracks
        }
    })()
};
Ext.extend(AnnoJ.TracksInfo, Ext.Panel);
AnnoJ.Bugs = (function() {
    var body = Ext.get(document.createElement('DIV'));
    body.addClass('AJ_bugs');
    var buglist = Ext.get(document.createElement('DIV'));
    var report = new Ext.form.TextArea();
    buglist.appendTo(body);
    return function() {
        AnnoJ.AboutBox.superclass.constructor.call(this, {
            title: 'Bugs',
            iconCls: 'silk_bug',
            border: false,
            contentEl: body,
            autoScroll: true
        })
    }
})();
Ext.extend(AnnoJ.Bugs, Ext.Panel);
AnnoJ.Helpers.List = function() {
    var self = this;
    this.first = null;
    this.last = null;
    this.count = 0;
    var Node = function(item) {
        this.next = null;
        this.prev = null;
        this.value = item
    };
    this.insertFirst = function(item) {
        var node = new Node(item);
        if (!self.first) {
            self.first = node;
            self.last = node
        } else {
            node.next = self.first;
            node.prev = null;
            node.next.prev = node;
            self.first = node
        }
        self.count++
    };
    this.insertLast = function(item) {
        var node = new Node(item);
        if (!self.last) {
            self.first = node;
            self.last = node
        } else {
            node.next = null;
            node.prev = self.last;
            node.prev.next = node;
            self.last = node
        }
        self.count++
    };
    this.insertBefore = function(existing, item) {
        if (existing == null) {
            self.insertLast(item);
            return
        }
        if (!existing instanceof Node) {
            return
        }
        if (existing == self.first) {
            self.insertFirst(item);
            return
        }
        var node = new Node(item);
        node.next = existing;
        node.prev = existing.prev;
        node.next.prev = node;
        node.prev.next = node;
        self.count++
    };
    this.insertAfter = function(existing, item) {
        if (existing == null) {
            self.insertFirst(item);
            return
        }
        if (!existing instanceof Node) {
            return
        }
        if (existing == self.last) {
            self.insertLast(item);
            return
        }
        var node = new Node(item);
        node.prev = existing;
        node.next = existing.next;
        node.next.prev = node;
        node.prev.next = node;
        self.count++
    };
    this.remove = function(existing) {
        if (!existing instanceof Node) {
            return
        }
        if (existing == self.first && existing == self.last) {
            self.first = null;
            self.last = null
        } else if (existing == self.first) {
            self.first = existing.next;
            self.first.prev = null
        } else if (existing == self.last) {
            self.last = existing.prev;
            self.last.next = null
        } else {
            existing.next.prev = existing.prev;
            existing.prev.next = existing.next
        }
        existing.prev = null;
        existing.next = null;
        temp = existing.value;
        delete existing;
        self.count--;
        return temp
    };
    this.clear = function() {
        var vals = [];
        while (self.first) {
            vals.push(remove(self.first))
        }
        self.count = 0;
        return vals
    };
    this.apply = function(func) {
        if (func == undefined || !(func instanceof Function)) {
            return
        }
        for (var node = self.first; node; node = node.next) {
            if (!func(node.value)) {
                break
            }
        }
    };
    this.find = function(value) {
        for (var node = self.first; node; node = node.next) {
            if (node.value == value) {
                return node
            }
        }
        return null
    }
};
var PointList = function() {
    var index = {};
    var count = 0;
    var self = this;
    var first = null;
    var last = null;
    var viewL = null;
    var viewR = null;
    var PointNode = function(id, x, item) {
        this.id = id || '';
        this.x = parseInt(x) || 0;
        this.value = item || null;
        this.next = null;
        this.prev = null
    };
    this.getCount = function() {
        return count
    };
    this.getFirst = function() {
        return first
    };
    this.getLast = function() {
        return last
    };
    this.getIndex = function() {
        return index
    };
    this.createNode = function(id, x, item) {
        return new PointNode(id, x, item)
    };
    this.clear = function() {
        while (first) {
            self.remove(first)
        }
    };
    this.prune = function(x1, x2) {
        while (first && first.x < x1) {
            self.remove(first)
        }
        while (last && last.x > x2) {
            self.remove(last)
        }
    };
    this.parse = function(data) {};
    this.subset = function(x1, x2) {
        var data = [];
        var x1 = parseInt(x1) || 0;
        var x2 = parseInt(x2) || 0;
        if (x1 > x2) return data;
        for (var node = first; node; node = node.next) {
            if (node.x < x1) continue;
            if (node.x > x2) break;
            data.push(node.value)
        }
        return data
    };
    this.apply = function(func, x1, x2) {
        if (! (func instanceof Function)) return;
        var x1 = parseInt(x1) || first.x;
        var x2 = parseInt(x2) || last.x;
        if (x1 > x2) return;
        for (var node = first; node; node = node.next) {
            if (node.x < x1) continue;
            while (node) {
                if (node.x > x2) break;
                func(node);
                node = node.next
            }
            break
        }
    };
    this.insert = function(node) {
        if (! (node instanceof PointNode)) return;
        if (index[node.id]) {
            index[node.id].value = node.value;
            return
        }
        index[node.id] = node;
        if (count == 0) {
            first = node;
            last = node;
            count = 1;
            return
        }
        if (node.x <= first.x) {
            node.next = first;
            first.prev = node;
            first = node
        } else if (node.x >= last.x) {
            node.next = null;
            node.prev = last;
            node.prev.next = node;
            last = node
        } else {
            if (Math.abs(node.x - first.x) < Math.abs(node.x - last.x)) {
                for (var existing = first; existing; existing = existing.next) {
                    if (node.x <= existing.x) {
                        node.next = existing;
                        node.prev = existing.prev;
                        node.next.prev = node;
                        node.prev.next = node;
                        break
                    }
                }
            } else {
                for (var existing = last; existing; existing = existing.prev) {
                    if (node.x >= existing.x) {
                        node.next = existing.next;
                        node.prev = existing;
                        node.next.prev = node;
                        node.prev.next = node;
                        break
                    }
                }
            }
        }
        count++
    };
    this.insertPoints = function(array) {
        var len = array.length;
        if (len == 0) return;
        if (count > 0 && Math.abs(array[0].x - first.x) < Math.abs(array[0].x - last.x)) {
            for (var i = len - 1; i >= 0; i--) {
                self.insert(array[i])
            }
        } else {
            for (var i = 0; i < len; i++) {
                self.insert(array[i])
            }
        }
    };
    this.remove = function(node) {
        if (! (node instanceof PointNode)) return;
        if (!index[node.id]) return;
        if (count == 0) return;
        if (count == 1) {
            first = null;
            last = null;
            viewL = null;
            viewR = null
        } else {
            if (node == viewL) {
                viewL = node.next
            }
            if (node == viewR) {
                viewR = node.prev
            }
            if (node == first) {
                first = node.next;
                first.prev = null
            } else if (node == last) {
                last = node.prev;
                last.next = null
            } else {
                node.prev.next = node.next;
                node.next.prev = node.prev
            }
        }
        node.prev = null;
        node.next = null;
        delete index[node.id];
        delete node;
        count--
    };
    this.viewport = (function() {
        function get() {
            return {
                left: viewL,
                right: viewR
            }
        };
        function set(x1, x2) {
            if (count == 0) {
                clear();
                return
            }
            var x1 = parseInt(x1) || 0;
            var x2 = parseInt(x2) || 0;
            if (x1 > x2) return;
            for (var node = first; node; node = node.next) {
                if (node.x < x1) continue;
                viewL = node;
                while (node) {
                    if (node.x > x2) break;
                    viewR = node;
                    node = node.next
                }
                break
            }
        };
        function update(x1, x2) {
            var x1 = parseInt(x1) || 0;
            var x2 = parseInt(x2) || 0;
            if (x1 > x2) return;
            if (!viewL || !viewR) {
                set(x1, x2);
                return
            }
            while (viewL && viewL.x < x1) {
                viewL = viewL.next
            }
            while (viewR && viewR.x > x2) {
                viewR = viewR.prev
            }
            while (viewL && viewL.prev && viewL.prev.x >= x1) {
                viewL = viewL.prev
            }
            while (viewR && viewR.next && viewR.next.x <= x2) {
                viewR = viewR.next
            }
        };
        function clear() {
            viewL = null;
            viewR = null
        };
        function apply(func) {
            if (! (func instanceof Function)) return;
            for (var node = viewL; node; node = node.next) {
                func(node.value);
                if (node == viewR) break
            }
        };
        return {
            get: get,
            set: set,
            clear: clear,
            update: update,
            apply: apply
        }
    })()
};
var RangeList = function() {
    var index = {};
    var count = 0;
    var self = this;
    var firstL = null;
    var firstR = null;
    var lastL = null;
    var lastR = null;
    var viewL = null;
    var viewR = null;
    var RangeNode = function(id, x1, x2, item) {
        this.id = id || '';
        this.x1 = parseInt(x1) || 0;
        this.x2 = parseInt(x2) || 0;
        this.value = item || null;
        this.level = -1;
        this.nextL = null;
        this.nextR = null;
        this.prevL = null;
        this.prevR = null
    };
    this.getCount = function() {
        return count
    };
    this.getFirstL = function() {
        return firstL
    };
    this.getFirstR = function() {
        return firstR
    };
    this.getLastL = function() {
        return lastL
    };
    this.getLastR = function() {
        return lastR
    };
    this.getIndex = function() {
        return index
    };
    this.createNode = function(id, x1, x2, item) {
        return new RangeNode(id, x1, x2, item)
    };
    this.exists = function(id) {
        if (!id) return false;
        return index[id] ? true: false
    };
    this.getNode = function(id) {
        if (!id) return null;
        return index[id] || null
    };
    this.getValue = function(id) {
        if (!id) return null;
        if (!index[id]) return null;
        return index[id].value
    };
    this.clear = function() {
        while (firstL) {
            self.remove(firstL)
        }
    };
    this.prune = function(x1, x2) {
        while (firstR && firstR.x2 < x1) {
            self.remove(firstR)
        }
        while (lastL && lastL.x1 > x2) {
            self.remove(lastL)
        }
    };
    this.parse = function(data) {};
    this.apply = function(func) {
        if (! (func instanceof Function)) return;
        for (var node = firstL; node; node = node.nextL) {
            func(node)
        }
    };
    this.insert = function(node) {
        if (! (node instanceof RangeNode)) return;
        if (index[node.id]) {
            index[node.id].value = node.value;
            return
        }
        index[node.id] = node;
        if (count == 0) {
            firstL = node;
            firstR = node;
            lastL = node;
            lastR = node;
            count = 1;
            return
        }
        if (node.x1 < firstL.x1 || (node.x1 == firstL.x1 && node.x2 <= firstL.x2)) {
            node.nextL = firstL;
            firstL.prevL = node;
            firstL = node
        } else if (node.x1 > lastL.x1 || (node.x1 == lastL.x1 && node.x2 >= lastL.x2)) {
            node.nextL = null;
            node.prevL = lastL;
            node.prevL.nextL = node;
            lastL = node
        } else {
            if (Math.abs(node.x1 - firstL.x1) < Math.abs(node.x1 - lastL.x1)) {
                for (var existing = firstL; existing; existing = existing.nextL) {
                    if (node.x1 < existing.x1 || (node.x1 == existing.x1 && node.x2 <= existing.x2)) {
                        node.nextL = existing;
                        node.prevL = existing.prevL;
                        node.nextL.prevL = node;
                        node.prevL.nextL = node;
                        break
                    }
                }
            } else {
                for (var existing = lastL; existing; existing = existing.prevL) {
                    if (node.x1 > existing.x1 || (node.x1 == existing.x1 && node.x2 >= existing.x2)) {
                        node.nextL = existing.nextL;
                        node.prevL = existing;
                        node.nextL.prevL = node;
                        node.prevL.nextL = node;
                        break
                    }
                }
            }
        }
        if (node.x2 < firstR.x2 || (node.x2 == firstR.x2 && node.x1 <= firstR.x1)) {
            node.nextR = firstR;
            firstR.prevR = node;
            firstR = node
        } else if (node.x2 > lastR.x2 || (node.x2 == lastR.x2 && node.x1 >= lastR.x1)) {
            node.nextR = null;
            node.prevR = lastR;
            node.prevR.nextR = node;
            lastR = node
        } else {
            if (Math.abs(node.x2 - firstR.x2) < Math.abs(node.x2 - lastR.x2)) {
                for (var existing = firstR; existing; existing = existing.nextR) {
                    if (node.x2 < existing.x2 || (node.x2 == existing.x2 && node.x1 <= existing.x1)) {
                        node.nextR = existing;
                        node.prevR = existing.prevR;
                        node.nextR.prevR = node;
                        node.prevR.nextR = node;
                        break
                    }
                }
            } else {
                for (var existing = lastR; existing; existing = existing.prevR) {
                    if (node.x2 > existing.x2 || (node.x2 == existing.x2 && node.x1 >= existing.x1)) {
                        node.nextR = existing.nextR;
                        node.prevR = existing;
                        node.nextR.prevR = node;
                        node.prevR.nextR = node;
                        break
                    }
                }
            }
        }
        count++
    };
    this.remove = function(node) {
        if (! (node instanceof RangeNode)) return;
        if (!index[node.id]) return;
        if (count == 0) return;
        if (node == viewL) viewL = node.nextR;
        if (node == viewR) viewR = node.prevL;
        if (count == 1) {
            firstL = null;
            firstR = null;
            lastL = null;
            lastR = null
        } else {
            if (node == firstL) {
                firstL = node.nextL;
                firstL.prevL = null
            } else if (node == lastL) {
                lastL = node.prevL;
                lastL.nextL = null
            } else {
                node.prevL.nextL = node.nextL;
                node.nextL.prevL = node.prevL
            }
            if (node == firstR) {
                firstR = node.nextR;
                firstR.prevR = null
            } else if (node == lastR) {
                lastR = node.prevR;
                lastR.nextR = null
            } else {
                node.prevR.nextR = node.nextR;
                node.nextR.prevR = node.prevR
            }
        }
        node.prevL = null;
        node.nextL = null;
        node.prevR = null;
        node.nextR = null;
        delete index[node.id];
        delete node;
        count--
    };
    this.levelize = function(func) {
        var max = 0;
        var added = false;
        var inplay = new lightweight_list();
        for (var rangeNode = firstL; rangeNode; rangeNode = rangeNode.nextL) {
            if (func && !func(rangeNode.value)) continue;
            added = false;
            rangeNode.level = 0;
            for (var node = inplay.first; node; node = node.next) {
                if (node.value.x2 <= rangeNode.x1) {
                    inplay.remove(node)
                }
            }
            for (var node = inplay.first; node; node = node.next) {
                if (rangeNode.level < node.value.level) {
                    inplay.insertAfter(node.prev, rangeNode);
                    added = true;
                    break
                }
                rangeNode.level++;
                max = Math.max(max, rangeNode.level)
            }
            if (!added) inplay.insertLast(rangeNode)
        };
        return max
    };
    this.viewport = (function() {
        function get() {
            return {
                viewL: viewL,
                viewR: viewR
            }
        };
        function set(x1, x2) {
            var x1 = parseInt(x1) || 0;
            var x2 = parseInt(x2) || 0;
            if (x1 > x2) return;
            viewL = null;
            viewR = null;
            for (var node = firstR; node; node = node.nextR) {
                if (node.x2 < x1) continue;
                if (node.x1 > x2) break;
                viewL = node;
                viewR = node;
                while (node) {
                    if (node.x1 > x2) break;
                    if (node.x2 < x1){ 
            node = node.nextL; 
            continue;
            }
                    viewR = node;
                    node = node.nextL
                }
                break
            }
        };
        function update(x1, x2) {
            var x1 = parseInt(x1) || 0;
            var x2 = parseInt(x2) || 0;
            if (x1 > x2) return;
            if (!viewL || !viewR) {
                set(x1, x2);
                return
            }
            while (viewL && viewL.x2 < x1) {
                viewL = viewL.nextR
            }
            while (viewR && viewR.x1 > x2) {
                viewR = viewR.prevL
            }
            while (viewL && viewL.prevR && viewL.prevR.x2 >= x1) {
                viewL = viewL.prevR
            }
            while (viewR && viewR.nextL && viewR.nextL.x1 <= x2) {
                viewR = viewR.nextL
            }
        };
        function clear() {
            viewL = null;
            viewR = null
        };
        function apply(func) {
            if (! (func instanceof Function)) return false;
            if (!viewL || !viewR) return;
            for (var node = viewL; node; node = node.nextL) {
                func(node);
                if (node == viewR) break
            }
            return true
        };
        return {
            get: get,
            set: set,
            clear: clear,
            update: update,
            apply: apply
        }
    })()
};
var HistogramData = function() {
    var series = {};
    this.clear = function() {
        for (var s in series) {
            series[s].clear()
        }
    };
    this.prune = function(x1, x2) {
        for (var s in series) {
            series[s].prune(x1, x2)
        }
    };
    this.parse = function(data, above) {
        if (!data) return;
        for (var name in data) {
            if (series[name] == undefined) {
                series[name] = new HistogramList()
            }
            series[name].parse(data[name], above)
        }
    };
    this.getCount = function(data) {
        if (!data) return;
    var count = 0;
        for (var name in data) {
            if (series[name]) count += series[name].getCnt();
        }
    return count
    };
    this.subset2canvas = function(left, right, bases, pixels) {
        var result = {};
        for (var s in series) {
            result[s] = series[s].subset2canvas(left, right, bases, pixels)
        }
        return result
    }
};
var HistogramList = function() {
    HistogramList.superclass.constructor.call(this);
    var self = this;
    var count = 0;
    this.parse = function(data, above) {
        var points = [];
        Ext.each(data,
        function(datum) {
            if (!datum) return;
            if (datum.length == 3) {
                var item = {
                    x: parseInt(datum[0]),
                    w: 1,
                    y: parseFloat(datum[above ? 1 : 2]) || 0
                }
            } else {
                var item = {
                    x: parseInt(datum[0]),
                    w: parseInt(datum[1]) || 0,
                    y: parseFloat(datum[above ? 2 : 3]) || 0
                }
            }
            item.id = item.x;
            if (!item.w || !item.y) return;
            points.push(self.createNode(item.id, item.x, item))
        });
        self.insertPoints(points);
    points = null;
    count = self.getCount()
    };
    this.getCnt = function(){
       return count
    }
    this.subset2canvas = function(x1, x2, bases, pixels) {
        var active = null;
        var subset = [];
        var bases = parseInt(bases) || 0;
        var pixels = parseInt(pixels) || 0;
        if (!bases || !pixels) return subset;
        self.viewport.update(x1, x2);
        self.viewport.apply(function(item) {
            var x = Math.round((item.x - x1) * pixels / bases);
            var y = item.y;
            var w = Math.round(item.w * pixels / bases) || 1;
            if (active == null) {
                active = {
                    x: x,
                    y: y,
                    w: w
                }
            } else if (x == active.x) {
                active.y = Math.max(active.y, y);
                active.w = Math.max(active.w, w)
            } else {
                subset.push(active);
                active = {
                    x: x,
                    y: y,
                    w: w
                }
            }
        });
        if (active) subset.push(active);
        return subset
    }
};
Ext.extend(HistogramList, PointList);
var ReadsList = function() {
    ReadsList.superclass.constructor.call(this);
    var self = this;
    this.parse = function(data, above) {
        if (!data) return;
        var reads = [];
        for (var name in data) {
            if (!data[name]['watson'] || !data[name]['crick']) continue;
            Ext.each(data[name][above ? 'watson': 'crick'],
            function(datum) {
                if (datum.length != 6) return;
                var read = {
                    cls: name,
                    strand: above ? '+': '-',
                    id: datum[0] || '',
                    x: parseInt(datum[1]) || 0,
                    w: parseInt(datum[2]) || 0,
                    places: parseInt(datum[3]) || 0,
                    copies: parseInt(datum[4]) || 0,
                    sequence: datum[5] || ''
                };
        read.sequence += "," + read.copies;
                if (read.id && read.x && read.w && read.places && read.copies) {
                    if (read.places > 1) read.cls += ' multi_mapper';
                    if (read.copies > 1) read.cls += ' multi_copies';
                    var node = self.createNode(read.id, read.x, read.x + read.w - 1, read);
                    self.insert(node)
                }
            })
        }
    };
    this.subset2canvas = function(x1, x2, bases, pixels) {
        var subset = [];
        var bases = parseInt(bases) || 0;
        var pixels = parseInt(pixels) || 0;
        if (!bases || !pixels) return subset;
        self.viewport.update(x1, x2);
        self.viewport.apply(function(node) {
            if (node.x2 < x1) return true;
            subset.push({
                x: Math.round((node.x1 - x1) * pixels / bases),
                w: Math.round((node.value.w) * pixels / bases) || 1,
                cls: node.value.cls,
                sequence: node.value.sequence
            });
            return true
        });
        return subset
    }
};
Ext.extend(ReadsList, RangeList);
var SmallReadsList = function() {
    SmallReadsList.superclass.constructor.call(this);
    var self = this;
    this.parse = function(data, above) {
        if (!data) return;
        var reads = [];
        for (var name in data) {
            if (!data[name]['watson'] || !data[name]['crick']) continue;
            Ext.each(data[name][above ? 'watson': 'crick'],
            function(datum) {
                if (datum.length != 6) return;
                var read = {
                    cls: name,
                    strand: above ? '+': '-',
                    id: datum[0] || '',
                    x: parseInt(datum[1]) || 0,
                    w: parseInt(datum[2]) || 0,
                    places: parseInt(datum[3]) || 0,
                    copies: parseInt(datum[4]) || 0,
                    sequence: datum[5] || ''
                };
                if (read.id && read.x && read.w && read.places && read.copies) {
                    if (read.places > 1) read.cls += ' multi_mapper';
                    if (read.copies > 1) read.cls += ' multi_copies';
                    switch (read.w) {
                    case 20:
                        read.cls += ' sm21mers';
                        break;
                    case 21:
                        read.cls += ' sm22mers';
                        break;
                    case 22:
                        read.cls += ' sm23mers';
                        break;
                    case 23:
                        read.cls += ' sm24mers';
                        break;
                    default:
                        read.cls += ' Others';
                        break
                    }
                    var node = self.createNode(read.id, read.x, read.x + read.w - 1, read);
                    self.insert(node)
                }
            })
        }
    }
};
Ext.extend(SmallReadsList, ReadsList);
var ModelsList = function() {
    ModelsList.superclass.constructor.call(this);
    var self = this;
    this.parse = function(data, above) {
        if (!data || !(data instanceof Array)) return;
        Ext.each(data,
        function(datum) {
            var strand = datum[2];
            if (above && strand != '+') return;
            if (!above && strand != '-') return;
            var item = {
                parent: datum[0],
                id: datum[1],
                strand: datum[2],
                cls: datum[3],
                x: parseInt(datum[4]),
                w: parseInt(datum[5])
            };
            if (!item.parent) {
                if (!self.exists(item.id)) {
                    var node = self.createNode(item.id, item.x, item.x + item.w, item);
                    self.insert(node)
                }
            } else {
                var parent = self.getValue(item.parent);
                if (parent) {
                    if (!parent.children) {
                        parent.children = {}
                    }
                    parent.children[item.id] = item
                }
            }
        })
    };
    this.subset2canvas = function(x1, x2, bases, pixels) {
        var subset = [];
        var bases = parseInt(bases) || 0;
        var pixels = parseInt(pixels) || 0;
        if (!bases || !pixels) return subset;
        self.viewport.update(x1, x2);
        AnnoJ.config.genome_x1 = x1;
        AnnoJ.config.genome_x2 = x2;
        AnnoJ.config.genome_ratio = bases / pixels;
        self.viewport.apply(function(node) {
            if (node.x2 < x1) return true;
            var item = {
                id: node.id,
                cls: node.value.cls,
                x: Math.round((node.value.x - x1) * pixels / bases),
                w: Math.round(node.value.w * pixels / bases) || 1,
                children: []
            };
            if (node.value.children) {
                for (var id in node.value.children) {
                    var child = node.value.children[id];
                    var cw = Math.round(child.w * pixels / bases) || 0;
                    if (cw) {
                        item.children.push({
                            id: child.id,
                            cls: child.cls,
                            x: Math.round((child.x - x1) * pixels / bases),
                            w: cw
                        })
                    }
                }
            }
            subset.push(item);
            return true
        });
        return subset
    }
};
Ext.extend(ModelsList, RangeList);
var PairedReadsList = function() {
    PairedReadsList.superclass.constructor.call(this);
    var self = this;
    this.parse = function(data, above) {
        if (!data) return;
        var reads = [];
        for (var name in data) {
            if (!data[name]['watson'] || !data[name]['crick']) continue;
            Ext.each(data[name][above ? 'watson': 'crick'],
            function(datum) {
                if (datum.length != 7) return;
                var read = {
                    cls: name,
                    strand: above ? '+': '-',
                    id: datum[0] || '',
                    x: parseInt(datum[1]) || 0,
                    w: parseInt(datum[2]) || 0,
                    lenA: parseInt(datum[3]) || 0,
                    lenB: parseInt(datum[4]) || 0,
                    seqA: datum[5],
                    seqB: datum[6]
                };
                if (read.id && read.x && read.w) {
                    var node = self.createNode(read.id, read.x, read.x + read.w - 1, read);
                    self.insert(node)
                }
            })
        }
    };
    this.subset2canvas = function(x1, x2, bases, pixels) {
        var subset = [];
        var bases = parseInt(bases) || 0;
        var pixels = parseInt(pixels) || 0;
        if (!bases || !pixels) return subset;
        self.viewport.update(x1, x2);
        self.viewport.apply(function(node) {
            if (node.x2 < x1) return true;
            subset.push({
                x: Math.round((node.x1 - x1) * pixels / bases),
                w: Math.round((node.value.w) * pixels / bases) || 1,
                e1: Math.round((node.value.lenA) * pixels / bases),
                e2: Math.round((node.value.lenB) * pixels / bases),
                cls: node.value.cls,
                seqA: node.value.seqA,
                seqB: node.value.seqB
            });
            return true
        });
        return subset
    }
};
Ext.extend(PairedReadsList, RangeList);

var BaseCanvas = function(userConfig) {
    var self = this;
    self.config = {};
    var defaultConfig = {};
    Ext.apply(self.config, userConfig || {},
    defaultConfig);
    var container = document.createElement('DIV');
    var canvas = document.createElement("CANVAS");
    if(!canvas.getContext)
    {
        G_vmlCanvasManager.initElement(canvas);
    }
    var brush = canvas.getContext('2d');
    
    var width = 0;
    var height = 0;
    container.style.position = 'relative';
    canvas.style.position = 'relative';
    //canvas.style.backgroundColor = '#332233';
    this.getContainer = function() {
        return container
    };
    this.getCanvas = function() {
        return canvas
    };
    this.getBrush = function() {
        return brush
    };
    this.getWidth = function() {
        return width
    };
    this.getHeight = function() {
        return height
    };
    this.getRegion = function() {
        if (!container || !container.parentNode) return null;
        var pr = Ext.get(container.parentNode).getRegion();
        var cr = Ext.get(container).getRegion();
        var ir = cr.intersect(pr);
        if (!ir) return null;
        if (ir.top < 0) {
            var diff = Math.abs(ir.top);
            ir.top += diff;
            ir.bottom += diff;
            cr.top += diff;
            cr.bottom += diff
        }
        if (ir.left < 0) {
            var diff = Math.abs(ir.left);
            ir.left += diff;
            ir.right += diff;
            cr.left += diff;
            cr.right += diff
        }
        var region = {};
        region.x1 = ir.left - cr.left;
        region.y1 = ir.top - cr.top;
        region.x2 = region.x1 + ir.right - ir.left;
        region.y2 = region.y1 + ir.bottom - ir.top;
        return region
    };
    this.setContainer = function(dom) {
        if (!dom || !dom.appendChild) return;
        if (dom.style.position != 'absolute' && dom.style.position != 'relative') {
            dom.style.position = 'relative'
        }
        if (canvas.parentNode) {
            canvas.parentNode.removeChild(canvas)
        }
        dom.appendChild(canvas);
        container = dom;
        self.clear()
    };
    this.setSize = function(width, height) {
        container.style.width = parseInt(width) || container.offsetWidth;
        container.style.height = parseInt(height) || container.offsetHeight;
        self.refresh()
    };
    this.paint = function() {};
    this.refresh = function() {
        self.clear();
        self.paint()
    };
    this.clear = function() {
        container.removeChild(canvas);
        container.innerHTML = '';
        if(canvas.innerHTML == "" && Ext.isIE){
          canvas = document.createElement("canvas");
          if(!canvas.getContext) G_vmlCanvasManager.initElement(canvas);
        }
        container.appendChild(canvas);
        width = container.offsetWidth;
        height = container.offsetHeight;
        canvas.width = width;

        if ((Ext.isIE8 || Ext.isIE7 || Ext.isOpera)){
           var track_id = container.parentNode.parentNode.parentNode.id;
           var conf = findConf(track_id);
           canvas.height = conf.height / 2; 
           if(conf.single) canvas.height *= 2;
           var track = AnnoJ.getGUI().Tracks.tracks.find('id', track_id);
           if (track && track.config.type == 'HiCTrack' ) canvas.height *= 2
        }
        else  canvas.height = height;

        brush = canvas.getContext('2d');
        brush.clearRect(0, 0, width, height)
    };
    this.paintBox = function(cls, x, y, w, h) {
        if (!check(x, y, w, h)) return;
        var s = self.styles.get(cls);
        if (!s) return;
        var oldTrans = brush.globalAlpha;
        brush.globalAlpha = s.opacity;
        if (s.border.top.width > 0) {
            fillBox(s.border.top.color, x, y, w, s.border.top.width);
            y += s.border.top.width;
            h -= s.border.top.width
        }
        if (s.border.bottom.width > 0) {
            fillBox(s.border.bottom.color, x, y + h - s.border.bottom.width, w, s.border.bottom.width);
            h -= s.border.bottom.width
        }
        if (s.border.left.width > 0) {
            fillBox(s.border.left.color, x, y, s.border.left.width, h);
            x += s.border.left.width;
            w -= s.border.left.width
        }
        if (s.border.right.width > 0) {
            fillBox(s.border.right.color, x + w - s.border.right.width, y, s.border.right.width, h);
            w -= s.border.right.width
        }
        if (s.padding.top) {
            x += s.padding.top;
            h -= s.padding.top
        }
        if (s.padding.bottom) {
            h -= s.padding.bottom
        }
        if (s.padding.left) {
            y += s.padding.left;
            w -= s.padding.left
        }
        if (s.padding.right) {
            w -= s.padding.right
        }
        if (s.fill) {
            fillBox(s.fill, x, y, w, h)
        }
        if (s.image) {
            fillImage(s.image, s.background.repeat, x, y, w, h)
        }
        brush.globalAlpha = oldTrans
    };
    function fillBox(fill, x, y, w, h) {
        var box = check(x, y, w, h);
        if (!box) return;
        brush.fillStyle = fill;
        brush.fillRect(box.x, box.y, box.w, box.h)
    };
    function fillImage(img, repeat, x, y, w, h) {
        if (!img) return;
        if (!img.complete) {
            Ext.EventManager.addListener(img, 'load',
            function() {
                fillImage(img, repeat, x, y, w, h)
            });
            return
        }
        var box = check(x, y, w, h);
        if (!box) return;
        var imgW = img.width;
        var imgH = img.height;
        if (repeat == 'repeat-x') {
            var numx = Math.floor(box.w / imgW);
            var diffx = box.w - (numx * imgW);
            for (var i = 0; i < numx; i++) {
                brush.drawImage(img, box.x + (i * imgW), box.y, imgW, box.h)
            }
            if(diffx > 0 && imgH > 0) brush.drawImage(img, 0, 0, diffx, imgH, box.x + (i * imgW), box.y, diffx, box.h)
        } else if (repeat == 'repeat-y') {
            var numy = Math.floor(box.h / imgH);
            var diffy = box.h - (numh * imgH);
            for (var i = 0; i < numy; i++) {
                brush.drawImage(img, box.x, box.y + (i * imgH), box.w, imgH)
            }
           if(imgW > 0 && diffy >0) brush.drawImage(img, 0, 0, imgW, diffy, box.x, box.y + (i * imgH), box.w, diffy)
        } else {
            brush.drawImage(img, box.x, box.y, box.w, box.h)
        }
    };
    function check(x, y, w, h) {
        var x1 = x;
        var y1 = y;
        var x2 = x + w;
        var y2 = y + h;
        if (x1 < 0) x1 = 0;
        if (y1 < 0) y1 = 0;
        if (x2 >= width) x2 = width - 1;
        if (y2 >= height) y2 = height - 1;
        if (x1 >= x2 || x2 <= 0 || x1 >= width) return null;
        if (y1 >= y2 || y2 <= 0 || y1 >= height) return null;
        return {
            x: x1,
            y: y1,
            w: x2 - x1,
            h: y2 - y1
        }
    };
    this.styles = (function() {
        var styles = {};
        var imgCache = {};
        function set(cls, override) {
            if (!cls || typeof(cls) != 'string') return;
            if (styles[cls] && !override) return;
            styles[cls] = build(cls)
        };
        function get(cls) {
            if (!cls || typeof(cls) != 'string') return null;
            if (!styles[cls]) set(cls);
            return styles[cls] || null
        };
        function remove(cls) {
            if (!cls || typeof(cls) != 'string') return;
            if (styles[cls]) delete styles[cls]
        };
        function clear() {
            delete styles;
            styles = {}
        };
        function build(cls) {
            if (!cls || typeof(cls) != 'string') return;
            if (!container || !container.appendChild) container = document.body;
            var div = Ext.get(document.createElement('DIV'));
            div.addClass(cls);
            div.appendTo(container);
            var css = {
                opacity: div.getStyle('opacity'),
                image: null,
                fill: div.getColor('background-color') || 'white',
                line: div.getColor('color') || 'black',
                w: div.getWidth(),
                h: div.getHeight(),
                x: div.getTop(),
                y: div.getLeft(),
                background: {
                    image: div.getStyle('background-image'),
                    color: div.getColor('background-color'),
                    repeat: div.getStyle('background-repeat'),
                    position: div.getStyle('background-position')
                },
                margin: {
                    top: div.getMargins('t'),
                    bottom: div.getMargins('b'),
                    left: div.getMargins('l'),
                    right: div.getMargins('r')
                },
                border: {
                    top: {
                        width: div.getBorderWidth('t'),
                        color: div.getColor('border-top-color')
                    },
                    bottom: {
                        width: div.getBorderWidth('b'),
                        color: div.getColor('border-bottom-color')
                    },
                    left: {
                        width: div.getBorderWidth('l'),
                        color: div.getColor('border-left-color')
                    },
                    right: {
                        width: div.getBorderWidth('r'),
                        color: div.getColor('border-right-color')
                    }
                },
                padding: {
                    top: div.getPadding('t'),
                    bottom: div.getPadding('b'),
                    left: div.getPadding('l'),
                    right: div.getPadding('r')
                }
            };
            if (css.background.image.substr(0, 4) == 'url(') {
        var start = css.background.image.indexOf("http");
        var end = css.background.image.indexOf(".gif");
                var src = css.background.image.substr(start, end - start + 4);
                var img = new Image();
                img.src = src;
                css.image = img
            } else {
                css.image = null
            }
            div.remove();
            return css
        };
        return {
            set: set,
            get: get,
            remove: remove,
            clear: clear,
            build: build
        }
    })()
};
Ext.extend(BaseCanvas, Ext.util.Observable);
var DataCanvas = function(userConfig) {
    var self = this;
    DataCanvas.superclass.constructor.call(self, userConfig);
    var defaultConfig = {
        scaler: 1.0,
        flippedX: false,
        flippedY: false
    };
    Ext.apply(self.config, userConfig || {},
    defaultConfig);
    this.setScaler = function(value, update) {
        self.config.scaler = parseFloat(value) || 0;
        if (self.config.scaler < 0) self.config.scaler = 0;
        if (update) self.paint()
    };
    this.getScaler = function() {
        var brush = this.getBrush();
        if(brush){
           var id  = brush.canvas.parentNode.parentNode.parentNode.parentNode.id;
           var trackConfig = find(AnnoJ.config.tracks,'id',id);
           if(trackConfig) return trackConfig.scale
           else return self.config.scaler
        }
        return self.config.scaler
    };
    this.flipX = function(update) {
        self.config.flippedX = !self.config.flippedX;
        if (update) this.paint()
    };
    this.flipY = function(update) {
        self.config.flippedY = !self.config.flippedY;
        if (update) this.paint()
    };
    this.isFlippedX = function() {
        return self.config.flippedX
    };
    this.isFlippedY = function() {
        return self.config.flippedY
    };
    this.groups = (function() {
        var list = {};
        function exists(name) {
            return ! (list[name] == undefined)
        };
        function add(name) {
            if (exists(name)) return;
            list[name] = true;
            self.styles.set(name)
        };
        function remove(name) {
            if (!exists(name)) return;
            delete list[name];
            self.styles.remove(name)
        };
        function clear() {
            list = {};
            self.styles.clear()
        };
        function getList() {
            return list
        };
        function toggle(name, state) {
            list[name] = state ? true: false
        };
        function active(name) {
            return list[name] ? true: false
        };
        return {
            exists: exists,
            add: add,
            remove: remove,
            clear: clear,
            getList: getList,
            toggle: toggle,
            active: active
        }
    })()
};
Ext.extend(DataCanvas, BaseCanvas);
var HistogramCanvas = function() {
    HistogramCanvas.superclass.constructor.call(this);
    var self = this;
    var data = {};
    var max = 0;
    this.setData = function(series) {
        max = 0;
        for (var name in series) {
            self.groups.add(name);
            Ext.each(series[name],
            function(datum) {
                max = Math.max(max, datum.y)
            })
        }
        data = series
    };
    this.getMax = function() {
        return max
    };
    this.normalize = function(max) {
        for (var name in data) {
            Ext.each(data[name],
            function(datum) {
                datum.y /= max
            })
        }
    };
    this.paint = function() {
        this.clear();
        var brush = this.getBrush();
        var width = this.getWidth();
        var height = this.getHeight();
        var scaler = this.getScaler();
        var flippedX = this.isFlippedX();
        var flippedY = this.isFlippedY();

    if(Ext.isIE8 || Ext.isIE7) brush.canvas.style.height = height;
    brush.fillStyle = 'rgb(25,25,0)';
    if(AnnoJ.config.settings.display == 0){
            brush.fillRect(1, 0, 1, height);
            brush.fillRect(1, 0, 4, 1);
    }

    var msg,lane,id;
    id  = brush.canvas.parentNode.parentNode.parentNode.parentNode.id;
    brush.font="7pt times";
    brush.fillStyle = 'rgb(25,25,0)';
    lane = brush.canvas.parentNode.className;
    var max = 0;
    for(var i in AnnoJ.config.maxlist){
        if(AnnoJ.config.maxlist[i] && max < AnnoJ.config.maxlist[i]) 
        max = AnnoJ.config.maxlist[i];
    }
    if(AnnoJ.config.settings.scale == 0) max = AnnoJ.config.maxlist[id];
    if(AnnoJ.config.settings.scale == 1) AnnoJ.config.max = max;
    if(AnnoJ.config.settings.scale == 2) max = AnnoJ.config.settings.yaxis;

    if(max == null) msg = "";
    else if(id == 'trackyyyy-0') msg = 1;
    else msg = Math.round(max / scaler*10)/10;

    if(AnnoJ.config.settings.display == 0){
        if(lane.indexOf("AJ_above") != -1) brush.fillText(msg, 2, 10);
        if(lane.indexOf("AJ_below") != -1){
            brush.fillText("0", 2, 10);
            brush.fillText(msg, 2, height-2);
        }
    }
    if (scaler == 0) return;
    var clist = new Array('#FF0000','#00FF00','#0000FF','#DC143C','#FF1493','#800080','#9932CC','#483D8B','#00008B', '#149614','#1E90FF','#5F9EA0','#2F4F4F','#00FF7F','#006400','#7CFC00','#FFD700','#8B0000');
    if(id == 'trackyyyy-0'){
        if(InfoRequest.corr.length == 0) return;
        var h = AnnoJ.config.infoTrack.height / 2;
        var w = Math.ceil(200 / InfoRequest.corr.length);
        for(var i = 0; i < InfoRequest.corr.length; i++){
          var val = InfoRequest.corr[i];
          var label = val.toString();
          var pos = label.indexOf(".");
          if(pos >= 0) label = label.substr(0,pos+3);
          if(lane.indexOf("AJ_above") != -1 && val > 0){
            brush.fillStyle = clist[0];
            brush.fillRect(10 + i*w + 1, h * (1 - val) , w - 2, h*val);
            brush.fillStyle = clist[2];
            brush.fillText(label,10 + i*w, h * (1 - val) - 2);
          }
          if(lane.indexOf("AJ_below") != -1 && val < 0){
            brush.fillStyle = clist[2];
            brush.fillRect(10 + i*w + 1, 0 , w - 2, -h*val);
            brush.fillStyle = clist[0];
            brush.fillText(label,10 + i*w, -h*val + 10);
          }
        }
        return
    }
    for (var cls in data) {
      if (!self.groups.active(cls)) continue;
    
      if(cls >= 0 && cls <= 16){
         brush.fillStyle = clist[cls];
         brush.globalAlpha = 0.7;
      }
      else brush.fillStyle = self.styles.get(cls).fill;
      var track = findConf(id);
      if(track.color[cls]){
         brush.fillStyle = '#' + track.color[cls];
         var cc = rColor(brush.fillStyle);
         if(lane.indexOf("AJ_below") != -1) brush.fillStyle = cc
      }
      var old_x = 0;
      Ext.each(data[cls],
      function(datum) {
         var w = datum.w || 1;
         var h = datum.y;
         h -= AnnoJ.config.settings.baseline;
         if(h <= 0) return;
         h /= max;
         h = Math.round(h * height * scaler);
         if (h == 0) return;
         var x = flippedX ? width - datum.x - w: datum.x;
         var y = flippedY ? 0 : height - h - 1;
         if (x + w < 0 || x > width) return;
         if (h >= height) {
           y = 0;
           h = height - 1
         }
         //AnnoJ.error('haha');
         if(AnnoJ.config.settings.display == 0 || isInfo(id)) 
           brush.fillRect(x, y, w, h);
         else{
            var r = Math.ceil((datum.y - AnnoJ.config.settings.baseline) / max  * 512);
            var g = 255 - r;
            r -= 255;
            if(r > 255) r = 255;
            if(r < 0) r = 0;
            if(g > 255) g = 255;
            if(g < 0) g = 0;
            brush.fillStyle = 'rgb(' + r + ',' + g + ',0)';
            if(flippedY) brush.fillRect(x, 0, w, 10);
            else  brush.fillRect(x, height - 10, w, 10);
            brush.fillStyle = 'rgb(0,255,0)';
            if(flippedY) brush.fillRect(old_x, 0, x - old_x, 10);
            else  brush.fillRect(old_x, height - 10, x - old_x, 10);
            old_x = x + w;
         }
      })
    }
    clist = null;
    //brush.stroke();
    }
};
Ext.extend(HistogramCanvas, DataCanvas);
var HiCInteraction = function() {
    HiCInteraction.superclass.constructor.call(this);
    var self = this;
    var data = {};
    var max = 0;
    var resolution = 10000;
    var map_type = false;
    this.setData = function(series) {
        max = 0;
    if(series['resolution'] && series['resolution'][2]){
        resolution = series['resolution'][2];
            data = series
    }
    else if(series['pair']){
        map_type = true;
            data = series['pair']
    }
    else{
        data = null
    }
    };
    this.getMax = function() {
        return max
    };
    this.normalize = function(max) {
        for (var name in data) {
            Ext.each(data[name],
            function(datum) {
                datum.y /= max
            })
        }
    };
    this.paint = function() {
        this.clear();
    if(!data) return;

        var brush = this.getBrush();
        var width = this.getWidth();
        var height = this.getHeight();
        var scaler = this.getScaler();
        var flippedX = this.isFlippedX();
        var flippedY = this.isFlippedY();

    var id  = brush.canvas.parentNode.parentNode.parentNode.parentNode.id;
    var track = AnnoJ.getGUI().Tracks.tracks.find('id', id);
    var conf = findConf(id);
    var ratio = AnnoJ.pixels2bases(1);
    var h = conf.unity;

        var half = Math.round(AnnoJ.pixels2bases(width) / 2);
        var view = AnnoJ.getLocation();
    var pos = view.position - half;
    var startx = 0;
    if(pos < 0) {
        startx = Math.round(-pos/(2*half) * width);
        pos = 0;
    }

    if(map_type) {
       for (var j = 0; j < data.length; j++) {
          var x = parseInt(data[j][0]) || 0;
          var y = parseInt(data[j][1]) || 0;
          var f = parseFloat(data[j][2]) || 0;

        if(x < view.position - half || x > view.position + half) continue;
        var xpos = (x - view.position + half) / AnnoJ.pixels2bases(1);
        var ypos = (y - conf.offsety) / AnnoJ.pixels2bases(1);

        var color = Math.round(255 - scaler * Math.abs(f)*255);
        if(color < 0) color = 0;
        if(f >= 0) brush.fillStyle = 'rgb(255,' + color + ',' + color + ')';
        else brush.fillStyle = 'rgb(' + color + ',' + color + ',255)';

        brush.beginPath();
        brush.moveTo(xpos, height - ypos - h);
        brush.lineTo(xpos - h, height - ypos);
        brush.lineTo(xpos + h, height - ypos);
        brush.fill()
        }
       return;
    }
    var w = Math.round(resolution / ratio);

    var idx = Math.floor(pos / resolution);
    var first_w = resolution - pos % resolution;
    first_w = Math.round(first_w / resolution * w);

    var posy = conf.offsety;
    var idy = idx;
    if(posy > 0) idy = Math.floor(posy / resolution);
    track.config.indexy = idy;
    var rboundry = Math.ceil((view.position + half) / resolution);

    if(conf.style)
    {
      for (var i in data) {
        if(i == 'resolution') continue;
        var y = parseInt(i) || 0;
        if(y < idy || (y - idy)*h > height) continue;

        for (var j in data[i]) {
          var x = parseInt(j) || 0;
          if(x < idx || x > rboundry) continue;
        var val = data[i][j] || 0;
        if(val <= 0) continue;

        var color = Math.round(255 - scaler * val);
        if(color < 0) color = 0;
        brush.fillStyle = 'rgb(255,' + color + ',' + color + ')';    
            brush.fillRect(startx + first_w + (x - idx - 1)*w, height - (y - idy + 1)*h ,w, h)
        }
      }
    }
    else{
      for (var i in data) {
        if(i == 'resolution') continue;
        var y = parseInt(i) || 0;
        if(y < 2*idx -rboundry || y > 2*rboundry - idx) continue;

        for (var j in data[i]) {
        var x = parseInt(j) || 0;
        if(x < 2*idx - rboundry || x > 2*rboundry - idx) continue;
        var val = data[i][j] || 0;
        if(val <= 0) continue;

        var color = Math.round(255 - scaler * val);
        if(color < 0) color = 0;
        brush.fillStyle = 'rgb(255,' + color + ',' + color + ')';
        var xx = Math.round(((x + y)/2 - idx -1)*w);
        var yy = Math.round(Math.abs(y - x) * w / 2);
        var half = Math.round(w / 2);
        brush.beginPath();
        brush.moveTo(startx + first_w + xx + half, height - yy - h);
        brush.lineTo(startx + first_w + xx + half - h, height - yy);
        brush.lineTo(startx + first_w + xx + half + h, height - yy);
        brush.fill()
        }
        }
       }
    }
};
Ext.extend(HiCInteraction, DataCanvas);
var BoxesCanvas = function(userConfig) {
    var self = this;
    BoxesCanvas.superclass.constructor.call(self, userConfig);
    var defaultConfig = {
        boxHeight: 10,
        boxHeightMax: 24,
        boxHeightMin: 1,
        boxSpace: 1
    };
    Ext.apply(self.config, userConfig, defaultConfig);
    this.paint = function() {
        this.clear();
        var width = this.getWidth();
        var height = this.getHeight();
        var region = this.getRegion();
        var brush = this.getBrush();
        var series = this.series.getAll();
        var flippedX = this.isFlippedX();
        var flippedY = this.isFlippedY();
        var h = self.config.boxHeight * this.getScaler();
        if (h > self.config.boxHeightMax) h = self.config.boxHeightMax;
        if (h < self.config.boxHeightMin) h = self.config.boxHeightMin;
        for (var name in series) {
            var boxes = series[name];
            this.levelize(boxes);
            Ext.each(boxes,
            function(box) {
                if (!self.groups.active(box.cls)) return true;
                var w = box.w;
                var x = flippedX ? width - box.x - w: box.x;
                var y = box.level * (h + self.config.boxSpace);
                y = flippedY ? y: height - y - h;
                if (x + w < region.x1 || x > region.x2) return;
                if (y + h < region.y1 || y > region.y2) return;
                self.paintBox(box.cls, x, y, w, h)
            })
        }
    };
    this.levelize = function(boxes) {
        if (!boxes || !(boxes instanceof Array)) return;
        var inplay = new AnnoJ.Helpers.List();
        var max = 0;
        Ext.each(boxes,
        function(box) {
            self.groups.add(box.cls);
            if (!self.groups.active(box.cls)) return true;
            box.level = 0;
            if (box.x1 == undefined) box.x1 = box.x;
            if (box.x2 == undefined) box.x2 = box.x + box.w;
            var added = false;
            for (var node = inplay.first; node; node = node.next) {
                if (node.value.x2 <= box.x1) {
                    inplay.remove(node)
                }
            }
            for (var node = inplay.first; node; node = node.next) {
                if (box.level < node.value.level) {
                    inplay.insertAfter(node.prev, box);
                    added = true;
                    break
                }
                box.level++;
                max = Math.max(max, box.level)
            }
            if (!added) inplay.insertLast(box)
        });
        return max
    }
};
Ext.extend(BoxesCanvas, DataCanvas);
var MaskCanvas = function(userConfig) {
    var self = this;
    var data = [];
    MaskCanvas.superclass.constructor.call(self, userConfig);
    var defaultConfig = {};
    Ext.apply(self.config, userConfig || {},
    defaultConfig);
    this.setData = function(models) {
        if (! (models instanceof Array)) return;
        data = [];
        Ext.each(models,
        function(model) {
            model.x1 = model.x;
            model.x2 = model.x + model.w
        });
        data = models
    };
    this.paint = function() {
        this.clear();
        if (!data || data.length == 0) return;
        var container = this.getContainer();
        var canvas = this.getCanvas();
        var region = this.getRegion();
        var width = this.getWidth();
        var height = this.getHeight();
        var brush = this.getBrush();
        var flippedX = this.isFlippedX();
        if (region == null) return;
        var y = 0;
        var h = height;
        Ext.each(data,
        function(model) {
            self.groups.add(model.cls);
            if (!self.groups.active(model.cls)) return;
            var w = model.w;
            var x = model.x;
            if (flippedX) x = width - x - w;
            if (x + w < region.x1 || x > region.x2) return;
            self.paintBox(model.cls, x, y, w, h)
        })
    }
};
Ext.extend(MaskCanvas, BoxesCanvas);
var ModelsCanvas = function(userConfig) {
    var self = this;
    var data = [];
    ModelsCanvas.superclass.constructor.call(self, userConfig);
    var defaultConfig = {
        boxHeight: 10,
        boxHeightMax: 24,
        boxHeightMin: 1,
        boxBlingLimit: 5,
        boxSpace: 1,
        labels: true,
        arrows: true,
        strand: '+'
    };
    Ext.apply(self.config, userConfig, defaultConfig);
    self.addEvents({
        'modelSelected': true
    });
    if (self.config.strand == '+' && !self.config.flippedY) self.flipY();
    if (self.config.strand == '-' && self.config.flippedY) self.flipY();
    this.setBoxHeight = function(h) {
        var h = parseInt(h) || 0;
        if (h < self.config.boxHeightMin) h = self.config.boxHeightMin;
        if (h > self.config.boxHeightMax) h = self.config.boxHeightMax;
        self.config.boxHeight = h
    };
    this.setBoxSpace = function(s) {
        var s = parseInt(s) || 0;
        self.config.boxSpace = s < 0 ? 0 : s
    };
    this.setLabels = function(state) {
        self.config.labels = state ? true: false
    };
    this.setArrows = function(state) {
        self.config.arrows = state ? true: false
    };
    this.setStrand = function(s) {
        self.config.strand = (s == '+') ? '+': '-'
    };
    this.setData = function(models) {
        if (! (models instanceof Array)) return;
        data = [];
        Ext.each(models,
        function(model) {
            model.x1 = model.x;
            model.x2 = model.x + model.w;
            self.groups.add(model.cls)
        });
        data = models
    };
    this.paint = function() {
        this.clear();
        var container = this.getContainer();
        var canvas = this.getCanvas();
        var region = this.getRegion();
        var width = this.getWidth();
        var height = this.getHeight();
        var brush = this.getBrush();
        var scaler = this.getScaler();
        var flippedX = this.isFlippedX();
        var flippedY = this.isFlippedY();
        var h = scaler * self.config.boxHeight;
        if (h < self.config.boxHeightMin) h = self.config.boxHeightMin;
        if (h > self.config.boxHeightMax) h = self.config.boxHeightMax;
        var max = this.levelize(data);
        var maxLevel = Math.ceil(region.y2 / (h + self.config.boxSpace));
        var html = '';

        brush.fillStyle = 'rgb(25,25,0)';
        var id  = brush.canvas.parentNode.parentNode.parentNode.parentNode.id;
        var lane = brush.canvas.parentNode.className;
        var name = findConf(id).name;
        var sequence = AnnoJ.config.genome;
        if(lane.indexOf("AJ_above") != -1 && sequence){
            var length = sequence.length;
            var ratio = AnnoJ.config.genome_ratio;
            if(ratio <= 0.125) brush.font="16px Times New Roman";
            else brush.font="12px Times New Roman";
            for (var i = 0; i < length; i++) {
                var letter = sequence.charAt(i+1);
                var letterX = Math.round((i + AnnoJ.config.genome_x - AnnoJ.config.genome_x1 + 1) / ratio);
                if(letter == 'a' || letter == 'A') brush.fillStyle = 'rgb(0,255,0)';
                if(letter == 'c' || letter == 'C') brush.fillStyle = 'rgb(0,0,255)';
                if(letter == 'g' || letter == 'G') brush.fillStyle = 'rgb(0,0,0)';
                if(letter == 't' || letter == 'T') brush.fillStyle = 'rgb(255,0,0)';
                if(letterX > 0) brush.fillText(letter,letterX,30);
            }
        }
        if (!data || data.length == 0) return;

        Ext.each(data,
        function(model) {
            if (!self.groups.active(model.cls)) return;
            if (model.level > maxLevel) return;
            var w = model.w;
            var x = model.x;
            var y = model.level * (h + self.config.boxSpace);
            if (flippedX) x = width - x - w;
            if (flippedY) y = height - y - h - self.config.boxSpace;
            if (x + w < region.x1 || x > region.x2) return;
            if (y + h < region.y1 || y > region.y2) return;
            self.paintBox(model.cls, x, y, w, h);
            Ext.each(model.children,
            function(child) {
                self.paintBox(child.cls, child.x, y, child.w, h)
            });
            if (h >= self.config.boxBlingLimit) {
                if (self.config.arrows) {
                    if (self.config.strand == '+') {
                        self.paintBox('arrowRight', x + w, y, h, h)
                    } else {
                        self.paintBox('arrowLeft', x - h, y, h, h)
                    }
                }
                if (self.config.labels) {
                    html += "<div class='label' style='";
                    html += "position:absolute;";
                    html += "top:" + y + "px;";
                    html += "height:" + h + "px;";
                    html += "font-size:" + h + "px;";
                    html += "cursor:pointer;";
                    if (self.config.strand == '+') {
                        html += "right:" + (width - x) + "px;"
                    } else {
                        html += "left:" + (x + w) + "px;"
                    }
                    html += "'>" + model.id + "</div>"
                }
            }
        });
        var id  = brush.canvas.parentNode.parentNode.parentNode.parentNode.id;
        var div = document.createElement('DIV');
        div.innerHTML = html;
        div.id = 'models_' + id;

        container.appendChild(div);
        var c = Ext.get(container);
        c.removeListener('mouseup', clickModel);
        c.addListener('mouseup', clickModel)
    };
    function clickModel(event, srcEl, obj) {
        var el = Ext.get(srcEl);
        if (el.hasClass('label')) {
            self.fireEvent('modelSelected', el.dom.innerHTML)
        }
    };
    var superLevelize = this.levelize;
    this.levelize = function(data) {
        var container = this.getContainer();
        var h = self.config.boxHeight * this.getScaler();
        var temp = Ext.get(document.createElement('DIV'));
        temp.addClass('label');
        temp.setStyle('position', 'absolute');
        temp.setStyle('visibility', 'hidden');
        temp.setStyle('font-size', h);
        temp.update('0123456789');
        temp.appendTo(container);
        var letterW = temp.getWidth(true) / 10;
        temp.remove();
        Ext.each(data,
        function(model) {
            model.x1 = model.x;
            model.x2 = model.x + model.w;
            if (h >= self.config.boxBlingLimit) {
                if (self.config.arrows) {
                    if (self.config.strand == '+') {
                        model.x2 += h
                    } else {
                        model.x1 -= h
                    }
                }
                if (self.config.labels) {
                    var w = Math.round(letterW * model.id.length);
                    if (self.config.strand == '+') {
                        model.x1 -= w
                    } else {
                        model.x2 += w
                    }
                }
            }
        });
        return superLevelize(data)
    }
};
Ext.extend(ModelsCanvas, BoxesCanvas);
var ReadsCanvas = function(userConfig) {
    var self = this;
    var data = [];
    ReadsCanvas.superclass.constructor.call(self, userConfig);
    var defaultConfig = {
        boxHeight: 8,
        boxHeightMax: 24,
        boxHeightMin: 1,
        boxBlingLimit: 5,
        boxSpace: 1
    };
    Ext.apply(self.config, userConfig, defaultConfig);
    this.setData = function(reads) {
        if (! (reads instanceof Array)) return;
        Ext.each(reads,
        function(read) {
            self.groups.add(read.cls)
        });
        data = reads
    };
    this.toggleSpecial = function(targetCls, state) {
        var list = self.groups.getList();
        for (var cls in list) {
            if (cls.indexOf(targetCls) != -1) {
                self.groups.toggle(cls, state)
            }
        }
    };
    this.paint = function() {
        this.clear();
        var container = this.getContainer();
        var canvas = this.getCanvas();
        var region = this.getRegion();
        var width = this.getWidth();
        var height = this.getHeight();
        var brush = this.getBrush();
        var scaler = this.getScaler();
        var flippedX = this.isFlippedX();
        var flippedY = this.isFlippedY();
        var x = 0;
        var y = 0;
        var w = 0;
        var h = Math.round(self.config.boxHeight * scaler);
        if (h < self.config.boxHeightMin) h = self.config.boxHeightMin;
        if (h > self.config.boxHeightMax) h = self.config.boxHeightMax;
    h += 4;
        var max = this.levelize(data);
        var maxLevel = Math.ceil(region.y2 / (h + self.config.boxSpace));
    var level = 0;
        Ext.each(data,
        function(read) {
            self.groups.add(read.cls);
            if (!self.groups.active(read.cls)) return;
            if (read.level > maxLevel) return;
            if (read.multi && !self.config.showMultis) return;

        var pos = read.sequence.indexOf(",");
        var sequence = read.sequence.substr(0,pos);
        var copies = Math.round(read.sequence.substr(pos+1));
        if(read.level == 0) level = 0;
        else level += copies - 1;

           w = read.w;
           x = flippedX ? width - read.x - read.w: read.x;
           y = (read.level + level)* (h + self.config.boxSpace);
           y = flippedY ? y: height - 1 - y - h;
           if (x + w < region.x1 || x > region.x2) return;
           if (y + h < region.y1 || y > region.y2) return;

       var lane = brush.canvas.parentNode.className;
           var letterW = AnnoJ.bases2pixels(1);
           for (var i = 0; i < copies; i++) {
           if(letterW < 5 || sequence == "") self.paintBox(read.cls, x, y - i*(h + self.config.boxSpace), w, h);
               if(sequence) {
            if(lane.indexOf("AJ_below") != -1){
                var seq = "";
                    var length = sequence.length;
                    for (var j = length-1; j >= 0; j--) {
                            var letter = sequence.charAt(j);
                    seq += letter;
                }
                           letterize(brush, seq, x, y - i*(h + self.config.boxSpace), w, h, container)
            }
            else
                       letterize(brush, sequence, x, y - i*(h + self.config.boxSpace), w, h, container)
               }
       }
        })
    };
    function letterize(brush, sequence, x, y, w, h, container) {
        var clean = "";
        var length = sequence.length;
        var letterW = AnnoJ.bases2pixels(1);
        for (var i = 0; i < length; i++) {
            var letter = sequence.charAt(i);
            switch (letter) {
            case 'A':
                break;
            case 'T':
                break;
            case 'C':
                break;
            case 'G':
                break;
            case 'N':
                break;
            case 'a':
                letter = 'A';
                break;
            case 't':
                letter = 'T';
                break;
            case 'c':
                letter = 'C';
                break;
            case 'g':
                letter = 'G';
                break;
            default:
                letter = 'N'
            }
            clean += letter;
            var letterX = x + (i * letterW);
            //if (letterW < 5 || h < self.config.boxBlingLimit) {
            if (letterW < 5) {
                brush.fillStyle = self.styles.get(letter).fill;
                brush.fillRect(letterX, y, letterW, h)
            } else {
                brush.fillStyle = self.styles.get(letter).fill;
                brush.fillText(letter,letterX, y+h)
                //self.paintBox(letter, letterX, y, letterW, h)
            }
        }
    }
};
Ext.extend(ReadsCanvas, BoxesCanvas);
var PairedReadsCanvas = function(userConfig) {
    var self = this;
    var data = [];
    PairedReadsCanvas.superclass.constructor.call(self, userConfig);
    var defaultConfig = {
        boxHeight: 8,
        boxHeightMax: 24,
        boxHeightMin: 1,
        boxBlingLimit: 5
    };
    Ext.apply(self.config, userConfig, defaultConfig);
    this.setData = function(reads) {
        if (! (reads instanceof Array)) return;
        Ext.each(reads,
        function(read) {
            self.groups.add(read.cls)
        });
        data = reads
    };
    this.toggleSpecial = function(targetCls, state) {
        var list = self.groups.getList();
        for (var cls in list) {
            if (cls.indexOf(targetCls) != -1) {
                self.groups.toggle(cls, state)
            }
        }
    };
    this.paint = function() {
        this.clear();
        var container = this.getContainer();
        var canvas = this.getCanvas();
        var region = this.getRegion();
        var width = this.getWidth();
        var height = this.getHeight();
        var brush = this.getBrush();
        var scaler = this.getScaler();
        var flippedX = this.isFlippedX();
        var flippedY = this.isFlippedY();
        var x = 0;
        var y = 0;
        var w = 0;
        var e1 = 0;
        var e2 = 0;
        var h = Math.round(self.config.boxHeight * scaler);
        if (h < self.config.boxHeightMin) h = self.config.boxHeightMin;
        if (h > self.config.boxHeightMax) h = self.config.boxHeightMax;
        var max = this.levelize(data);
        var maxLevel = Math.ceil(region.y2 / (h + self.config.boxSpace));
        Ext.each(data,
        function(read) {
            self.groups.add(read.cls);
            if (!self.groups.active(read.cls)) return;
            if (read.level > maxLevel) return;
            if (read.multi && !self.config.showMultis) return;
            w = read.w;
            e1 = read.e1;
            e2 = read.e2;
            x = flippedX ? width - read.x - read.w: read.x;
            y = read.level * (h + self.config.boxSpace);
            y = flippedY ? y: height - 1 - y - h;
            if (x + w < region.x1 || x > region.x2) return;
            if (y + h < region.y1 || y > region.y2) return;
            self.paintBox(read.cls, x, y, e1, h);
            self.paintBox(read.cls + '_spacer', x + e1, y, w - (e1 + e2), h);
            self.paintBox(read.cls, x + w - e2, y, e2, h);
            if (read.seqA) {
                letterize(brush, read.seqA, x, y, e1, h, container);
                letterize(brush, read.seqB, x + w - e2, y, e2, h, container)
            }
        })
    };
    function letterize(brush, sequence, x, y, w, h, container) {
        var clean = "";
        var length = sequence.length;
        var letterW = AnnoJ.bases2pixels(1);
        var readLength = length * letterW;
        for (var i = 0; i < length; i++) {
            var letter = sequence.charAt(i);
            switch (letter) {
            case 'A':
                break;
            case 'T':
                break;
            case 'C':
                break;
            case 'G':
                break;
            case 'N':
                break;
            case 'a':
                letter = 'A';
                break;
            case 't':
                letter = 'T';
                break;
            case 'c':
                letter = 'C';
                break;
            case 'g':
                letter = 'G';
                break;
            default:
                letter = 'N'
            }
            clean += letter;
            var letterX = x + (i * letterW);
            if (letterW < 5 || h < self.config.boxBlingLimit) {
                brush.fillStyle = self.styles.get(letter).fill;
                brush.fillRect(letterX, y, letterW, h)
            } else {
                self.paintBox(letter, letterX, y, letterW, h)
            }
        }
    }
};
Ext.extend(PairedReadsCanvas, BoxesCanvas);

AnnoJ.BaseTrack = function(userConfig) {
    var self = this;
    var defaultConfig = {
        id: Ext.id(),
        name: 'Track',
        path: '',
        datasource: '',
        minHeight: 40,
        maxHeight: 1000,
        height: 40,
        resizable: true,
        showControls: false,
        cls: 'AJ_track',
        iconCls: 'silk_bricks',
        enableMenu: true
    };
    this.config = defaultConfig;
    Ext.apply(this.config, userConfig || {},
    defaultConfig);
    this.addEvents({
        'generic': true,
        'close': true,
        'error': true,
        'resize': true
    });
    this.Frame = (function() {
        var d = document.createElement('DIV');
        d.id = self.config.id;
        var ext = Ext.get(d);
        ext.addClass(self.config.cls);
        ext.on('mousemove',
        function(event) {
        cursor.id = self.config.id;
        cursor.type = self.config.type;
        var offsetTop = ext.dom.offsetTop;
        var offsetY = ext.dom.offsetParent.offsetTop;
        var scrollTop = AnnoJ.getGUI().Tracks.tracks.body.dom.scrollTop;
        cursor.offsetTop = event.getPageY() - offsetY + scrollTop - offsetTop; 
        cursor.offsetHeight = ext.dom.offsetHeight; 
            return false
        });
        ext.on('dblclick',
        function(event) {
            event.stopEvent();
            if(self.config.id == 'trackxxxx-0' || self.config.id == 'trackyyyy-0' || self.config.id.indexOf('new-') != -1) return true;
            InfoRequest.ready = true;
               var loc = AnnoJ.getLocation();
               var old_track = AnnoJ.getGUI().TracksInfo.tracks.tracks[0];
            if(old_track){
            old_track.close();
                old_track = null;
                AnnoJ.getGUI().TracksInfo.tracks.tracks[0] = null;
            }
        AnnoJ.config.infoTrack.data = self.config.data;
        AnnoJ.config.infoTrack.name = self.config.name;
        AnnoJ.config.infoTrack.type = self.config.type;
        AnnoJ.config.infoTrack.path = 'analysis';
        AnnoJ.config.infoTrack.action = '';
        AnnoJ.config.infoTrack.color = {};
        AnnoJ.config.infoTrack.scale = 1;
        AnnoJ.config.infoTrack.showControls = true;
        AnnoJ.config.infoTrack.id = 'trackxxxx-0';
        AnnoJ.config.infoTrack.height = 160;
        AnnoJ.config.infoTrack.source = self.config.type;
        AnnoJ.config.infoTrack.assembly = AnnoJ.config.location.assembly;
        if(self.config.type == 'HiCTrack'){
            AnnoJ.config.infoTrack.data = '/proxy/http://tabit.ucsd.edu/fetchers/models/hg18_lishen.php';
            AnnoJ.config.infoTrack.type = 'ModelsTrack';
            if(AnnoJ.config.settings.hic_d) AnnoJ.config.infoTrack.assembly = findConf(self.config.id).assembly;
        }
        try {
            var track = new AnnoJ[AnnoJ.config.infoTrack.type](AnnoJ.config.infoTrack);
        } catch(e) {
            WebApp.error(e);
            if(!Ext.isIE) console.log(e);
            return false
        };
        AnnoJ.getGUI().TracksInfo.tracks.tracks[0] = track;
        AnnoJ.getGUI().TracksInfo.tracks.open(track);
        if(track) track.setLocation(loc);
        return false
    });
        ext.on('contextmenu',
        function(event) {
            event.stopEvent();
            if (self.config.enableMenu) {
                self.ContextMenu.ext.showAt([event.getPageX(), event.getPageY()])
            }
            return false
        });
        ext.setHeight(self.config.height);
        return {
            ext: ext
        }
    })();
    this.Overflow = (function() {
        var ext = Ext.get(document.createElement('DIV'));
        ext.addClass('AJ_overflow');
        return {
            ext: ext
        }
    })();
    this.Canvas = (function() {
        var ext = Ext.get(document.createElement('DIV'));
        ext.addClass('AJ_canvas');
        return {
            ext: ext
        }
    })();
    this.ContextMenu = (function() {
        var ext = new Ext.menu.Menu();
        function enable() {
            self.config.enableMenu = true
        };
        function disable() {
            self.config.enableMenu = false
        };
        function addItems(items) {
            Ext.each(items,
            function(item) {
                ext.add(item)
            })
        };
        return {
            ext: ext,
            enable: enable,
            disable: disable,
            addItems: addItems
        }
    })();
    this.Toolbar = (function() {
        var ext = new Ext.Element(document.createElement('DIV'));
        ext.addClass('AJ_toolbar');
        ext.appendTo(document.body);
        var visible = self.config.showControls;
        var toolbar = new Ext.Toolbar({
        height: 50,
            renderTo: ext
        });
        var closeButton = new Ext.Button({
            iconCls: 'silk_delete',
            tooltip: 'Remove the track',
            permanent: true,
        heatmap_permanent: true,
            handler: function() {
                self.fireEvent('close', self)
            }
        });
        var trackConfig = find(AnnoJ.config.tracks,'id',self.config.id);
        if(!trackConfig) trackConfig = AnnoJ.config.tracks[0];
        var assembly = new Ext.form.ComboBox({
            typeAhead: true,
            triggerAction: 'all',
            width: 40,
            grow: true,
            growMin: 40,
            growMax: 100,
            forceSelection: true,
            mode: 'local',
            displayField: 'id'
        });
        assembly.on('select',
        function(e) {
            trackConfig.assembly = e.getValue()
        });
        function bindAssemblies(options, selected) {
            if (!options || options.length == 0) return;
            var temp = [];
            Ext.each(options,
            function(item) {
                temp.push([item.id])
            });
            if (!selected) selected = temp[0];
            var store = new Ext.data.SimpleStore({
                fields: ['id'],
                data: temp
            });
            assembly.bindStore(store);
            assembly.setValue(selected);
        };
        bindAssemblies(AnnoJ.config.assemblies, trackConfig.assembly);
        var selected = new Ext.form.Checkbox({
            checked: false
        });
        selected.on('check',
        function(me, checked) {
        if(checked){
            if(!AnnoJ.config.trks) AnnoJ.config.trks = new Array();
            var exists = AnnoJ.config.trks.indexOf(self.config.id);
            if(exists < 0) AnnoJ.config.trks.push(self.config.id);
        }
        else{
            var exists = AnnoJ.config.trks.indexOf(self.config.id);
            if(exists >= 0) AnnoJ.config.trks.splice(exists,1);
           }
        });

        var checked1 = true;
        if(trackConfig.style) checked1 = false;
        var displayMode = new Ext.CycleButton({
            showText: true,
            prependText: '',
            tooltip: 'Display mode',
            items: [{
                text: 'Triangle',
                checked: checked1
            },
            {
                text: 'Rectangle',
                checked: !checked1
            }],
            changeHandler: function(btn, item) {
                if(item.text == "Triangle") trackConfig.style = 0;
                if(item.text == "Rectangle") trackConfig.style = 1
            }
        });

        var HicHeight = new Ext.form.TextField({
            width: 20,
            value: trackConfig.unity,
            maskRe: /[0-9]/,
            regex: /^[0-9]+$/,
            selectOnFocus: true
        });
        HicHeight.on('specialKey',
        function(config, event) {
            var val = parseInt(HicHeight.getValue());
            if(val < 0) HicHeight.setValue(1);
            if(val > 100) HicHeight.setValue(100);
            trackConfig.unity = parseInt(HicHeight.getValue()) || 4
        });
        var HicPosition = new Ext.form.TextField({
            width: 40,
            value: trackConfig.offsety,
            maskRe: /[0-9]/,
            regex: /^[0-9]+$/,
            selectOnFocus: true
        });
        HicPosition.on('specialKey',
        function(config, event) {
            var val = parseInt(HicPosition.getValue());
            if(val < 0) HicPosition.setValue(1);
            trackConfig.offsety = parseInt(HicPosition.getValue()) || 0
        });

        var title = new Ext.Toolbar.TextItem(self.config.name);
        title.permanent = true;
        title.heatmap_permanent = true;
        title.addClass('AJ_track_title');

        var trackmenu;
        if(self.config.type != "ModelsTrack"){
            trackmenu = new Ext.menu.Menu({
                items: ['series']
              })
        }
        function addItem(item){
            if(item == 'remove' || item == 'insert' || item == 'search') return;
            if(self.config.type == "ModelsTrack") return;
            if(trackmenu){
                for(var i = 0; i < trackmenu.items.items.length; i++){
                    if(trackmenu.items.items[i].text == item) return;
                }
            }
            trackmenu.add([{
                text: item,
                menu: new Ext.menu.ColorMenu({
                    id: item,
                    handler : function(cm,color){
                        if(typeof color != 'string') return;
                        var conf = find(AnnoJ.config.tracks, 'id', self.config.id);
                        if(conf) conf.color[this.id] = color;
                        self.refreshCanvas();
                    }
                })
            }])
        };

        var val = new Ext.Toolbar.TextItem('scale');
        val.id = 'AJ_val_' + self.config.id;
        var scale_box = new Ext.form.TextField({
            width: 30,
            maskRe: /[0-9\.]/,
            regex: /^[0-9\.]+$/,
            selectOnFocus: true
        });
        scale_box.id = 'AJ_scale_box_' + self.config.id;
        scale_box.setValue(trackConfig.scale);

        function setScale(val, notMul){
            var scaler = parseFloat(scale_box.getValue());
            if(notMul) scaler = 1;
            var value = parseFloat(val) || 1;
            var f = value*scaler;
            scale_box.setValue(f.toString());
            trackConfig.scale = value*scaler;
            self.l_rescale(trackConfig.scale)
        };

        scale_box.on('specialKey',
        function(config, event) {
          if (event.getKey() == event.ENTER) {
             var trackConfig = find(AnnoJ.config.tracks,'id',self.config.id);
             if(!trackConfig) trackConfig = AnnoJ.config.tracks[0];
             var scaler = scale_box.getValue();
             if(scaler == "") scale_box.setValue(1);
             trackConfig.scale = scale_box.getValue();
             self.l_rescale(trackConfig.scale)
          }
        });

        var filler = new Ext.Toolbar.Fill();
        filler.permanent = true;
        var toggleButton = new Ext.Button({
            iconCls: 'silk_application',
            tooltip: 'Toggles toolbar visibility',
            handler: toggle,
            permanent: true
        });
        var head_space = new Ext.Toolbar.Spacer();
        head_space.permanent = true;
        head_space.heatmap_permanent = true;
        var spacer = new Ext.Toolbar.Spacer();
        spacer.permanent = true;
        var colorbutton = new Ext.Toolbar.Button({
            text:'Color',
            menu: trackmenu
          });
        
        var items;
        if(isInfo(self.config.id) || self.config.id == 'trackyyyy-0'){
            items = [title, val, scale_box, spacer];
        }else if(self.config.type == "ModelsTrack"){
            items = [closeButton, title, filler, val, scale_box, toggleButton, spacer];
        }else if(self.config.id.indexOf('new-') != -1){    
            items = [head_space, closeButton, title, filler, val, scale_box, colorbutton, toggleButton, spacer];
        }else if(self.config.type == "HiCTrack"){    
            items = [head_space, displayMode, assembly, 'Y-offset', HicPosition, 'Size', HicHeight, closeButton, title, filler, val, scale_box, colorbutton, toggleButton, spacer];
        }else if(self.config.data.indexOf("tabit.ucsd.edu") < 0){ 
            items = [head_space, closeButton, title, filler, val, scale_box, colorbutton, toggleButton, spacer];
        }else{
            items = [head_space, selected, closeButton, title, filler, val, scale_box, colorbutton, toggleButton, spacer];
        }

        Ext.each(items, 
        function(item){
            toolbar.add(item);
        });
    
        toolbar.doLayout();
        toggle(self.config.showControls);
        function setTitle(text) {
            Ext.get(title.getEl()).update(text)
        };
        function getTitle(text) {
            return title.getEl().innerHTML
        };
        function toggle() {
            visible ? hide() : show()
        };
        function show() {
            visible = true;
            Ext.each(items,
            function(item) {
                if (item.show) item.show()
            })
        };
        function hide() {
            visible = false;
            Ext.each(items,
            function(item) {
                if (item.hide && !item.permanent && AnnoJ.config.settings.display == 0) item.hide();
                if (item.hide && !item.heatmap_permanent && AnnoJ.config.settings.display == 1) item.hide()
            })
        };
        function insert(index, item) {
            if (!item.show || !item.hide) return;
            var oldParent = ext.dom.parentNode;
            ext.appendTo(document.body);
            items.insert(index, item);
            toolbar.insert(index, item);
            toggle(visible);
            toolbar.doLayout();
            ext.appendTo(oldParent)
        };
        function isVisible() {
            return visible
        };
        return {
            ext: ext,
            toolbar: toolbar,
            addItem: addItem,
            setScale: setScale,
            setTitle: setTitle,
            getTitle: getTitle,
            toggle: toggle,
            show: show,
            hide: hide,
            insert: insert,
            isVisible: isVisible
        }
    })();
    this.appendTo = function(d) {
        self.Frame.ext.appendTo(d)
    };
    this.remove = function() {
        self.Frame.ext.remove()
    };
    this.insertBefore = function(d) {
        self.Frame.ext.insertBefore(d)
    };
    this.insertAfter = function(d) {
        self.Frame.ext.insertAfter(d)
    };
    this.mask = function(m) {
        self.Frame.ext.mask(m)
    };
    this.unmask = function() {
        self.Frame.ext.unmask()
    };
    this.isMasked = function() {
        return self.Frame.ext.isMasked()
    };
    this.getTitle = function() {
        return self.Toolbar.getTitle()
    };
    this.setTitle = function(title) {
        self.Toolbar.setTitle(title)
    };
    this.getWidth = function() {
        return self.Frame.ext.getWidth(w)
    };
    this.setWidth = function(w) {
        self.Frame.ext.setWidth(w)
    };
    this.getHeight = function() {
        return self.Frame.ext.getHeight(h)
    };
    this.setHeight = function(h) {
        self.Frame.ext.setHeight(h)
    };
    this.getMinHeight = function() {
        return self.config.minHeight
    };
    this.getMaxHeight = function() {
        return self.config.maxHeight
    };
    this.broadcast = function(type, data) {
        this.fireEvent('generic', type, data)
    };
    this.receive = function(type, data) {};
    this.Frame.ext.appendChild(this.Overflow.ext);
    this.Frame.ext.appendChild(this.Toolbar.ext);
    this.Overflow.ext.appendChild(this.Canvas.ext);
    this.ext = this.Frame.ext
};
Ext.extend(AnnoJ.BaseTrack, Ext.util.Observable);
AnnoJ.DataTrack = function(userConfig) {
    AnnoJ.DataTrack.superclass.constructor.call(this, userConfig);
    var self = this;
    var defaultConfig = {
        datasource: ''
    };
    Ext.applyIf(this.config, defaultConfig);
    this.addEvents({
        'describe': true
    });
    var infoButton = new Ext.Button({
        iconCls: 'silk_information',
        tooltip: 'Show information about the track',
        permanent: true,
        handler: function() {
            self.fireEvent('describe', self.Syndicator.getSyndication())
        }
    });
    this.Toolbar.insert(1, infoButton);
    this.Communicator = (function() {
        var busy = false;
        function isBusy() {
            return busy
        };
        function get(data, options) {
            var options = Ext.apply(options || {},
            {
                method: 'GET'
            });
            return request(data, options)
        };
        function post(data, options) {
            var options = Ext.apply(options || {},
            {
                method: 'POST'
            });
            return request(data, options)
        };
        function request(data, options) {
            if (busy) return false;
            self.setTitle('<span class="waiting">Communicating with server...</span>');
            var options = Ext.apply(options || {},
            {},
            {
                url: self.config.datasource,
                method: 'POST',
                data: data || null,
                success: function() {},
                failure: function() {}
            });
            options.success = function(response) {
                busy = false;
                self.setTitle(self.config.name);
                options.success(response)
            };
            options.failure = function(response) {
                busy = false;
                self.setTitle(self.config.name);
                options.failure(response)
            };
            BaseJS.request(options);
            return true
        };
        return {
            isBusy: isBusy,
            get: get,
            post: post,
            request: request
        }
    })();
    this.Syndicator = (function() {
        var syndication = null;
        var syndicated = false;
        var busy = false;
        function check() {
            return syndicated
        };
        function get() {
            return syndication
        };
        function syndicate(options) {
            if (busy) return;
            busy = true;
            self.setTitle('Syndicating...');
            self.mask("<span class='waiting'>Syndicating datasource...</span>");
            var options = Ext.applyIf(options || {},
            {
                url: self.config.data,
                success: function() {},
                failure: function() {}
            });
            var tempS = options.success;
            var tempF = options.failure;
            options.success = function(response) {
                syndicated = true;
                syndication = response;
                busy = false;
                if (self.config.name == 'Track') {
                    self.config.name = syndication.service.title
                }
                self.setTitle(self.config.name);
                self.unmask();
                tempS(response)
            };
            options.failure = function(string) {
                syndication = {};
                syndicated = false;
                busy = false;
                self.setTitle('Error: syndication failed');
                self.unmask();
                tempF(string)
            };
            BaseJS.syndicate(options)
        };
        return {
            isSyndicated: check,
            getSyndication: get,
            syndicate: syndicate
        }
    })()
};
Ext.extend(AnnoJ.DataTrack, AnnoJ.BaseTrack);
AnnoJ.BrowserTrack = function(userConfig) {
    AnnoJ.BrowserTrack.superclass.constructor.call(this, userConfig);
    var self = this;
    var defaultConfig = {
        autoResize: false,
        autoScroll: true,
        minCache: 100,
        cache: 3 * screen.width,
        maxCache: 20 * screen.width,
        scaler: 0.5,
        dragMode: 'browse'
    };
    Ext.applyIf(self.config, defaultConfig);
    self.config.originalHeight = self.config.height;
    if(!isInfo(self.config.id)){
    this.ContextMenu.addItems([self.config.name, '-', new Ext.menu.Item({
        text: 'Close track',
        iconCls: 'silk_delete',
        handler: function() {
            self.fireEvent('close', self)
        }
    }), new Ext.menu.Item({
        text: 'Track Information',
        iconCls: 'silk_information',
        handler: function() {
            self.fireEvent('describe', self.Syndicator.getSyndication())
        }
    }), new Ext.menu.Item({
        text: 'Toggle Toolbar',
        iconCls: 'silk_application',
        handler: function() {
            self.Toolbar.toggle()
        }
    }), '-', new Ext.menu.Item({
        text: 'Minimize',
        iconCls: 'silk_arrow_down',
        handler: function() {
            self.setHeight(self.config.minHeight);
            self.fireEvent('resize', self.config.minHeight)
        }
    }), new Ext.menu.Item({
        text: 'Maximize',
        iconCls: 'silk_arrow_up',
        handler: function() {
            self.setHeight(self.config.maxHeight);
            self.fireEvent('resize', self.config.maxHeight)
        }
    }), new Ext.menu.Item({
        text: 'Original Size',
        iconCls: 'silk_arrow_undo',
        handler: function() {
            self.setHeight(self.config.originalHeight);
            self.fireEvent('resize', self.config.originalHeight)
        }
    })])
    };
    var Scaler = (function() {
        var value = 0.5;
        function get() {
            return value
        };
        function set(v) {
            var v = parseFloat(v);
            if (v < 0) v = 0;
            if (v > 1) v = 1;
            value = v;
            self.rescale(v);
            return v
        };
        return {
            get: get,
            set: set
        }
    })();
    var DataManager = (function() {
        var defaultView = {
            assembly: '',
            position: 0,
            bases: 10,
            pixels: 1
        };
        var views = {
            loading: defaultView,
            requested: defaultView
        };
        var state = {
            busy: false,
            empty: true,
            ready: false,
            assembly: '',
            frameL: 0,
            frameR: 0
        };
        var policy = {
            frame: 10000,
            bases: 10,
            pixels: 1,
            index: 0
        };
        function getPolicy(view) {
            var p = self.getPolicy(view);
            if (!p) return null;
            if (p.bases == undefined) return null;
            if (p.pixels == undefined) return null;
            if (p.cache == undefined) return null;
            if (p.index == undefined) return null;
            p.bases = parseInt(p.bases) || 0;
            p.pixels = parseInt(p.pixels) || 0;
            p.cache = parseInt(p.cache) || 0;
            p.index = parseInt(p.index) || 0;
            if (p.pixels < 1 || p.bases < 1 || p.cache < 100 * p.bases / p.pixels) {
                return null
            }
            return p
        };
        function pos2frame(pos) {
            if (pos < 0) pos = 0;
            return Math.floor(Math.abs(pos) / policy.cache)
        };
        function frame2pos(frame) {
            return {
                left: Math.abs(frame) * policy.cache,
                right: (Math.abs(frame) + 1) * policy.cache - 1
            }
        };
        function getEdges() {
            var half = Math.round(AnnoJ.pixels2bases(self.Frame.ext.getWidth()) / 2);
            var view = AnnoJ.getLocation();
            var pos = view.position;
            if(isInfo(self.config.id)){ 
              pos = InfoRequest.position;
              half = Math.round(InfoRequest.bases / InfoRequest.pixels * (self.Frame.ext.getWidth()) / 2);
            }
            return {
                g1: pos - half,
                g2: pos + half
            }
        };
        function convertX(x) {
            return Math.round(x * views.current.bases / views.current.pixels)
        };
        function convertG(g) {
            return Math.round(g * views.current.pixels / views.current.bases)
        };
        function clear() {
            state.empty = true;
            self.clearData()
        };
        function prune(frameLeft, frameRight) {
            if (state.empty) return;
            if (frameLeft > state.right || frameRight < state.left) {
                clear();
                return
            }
            if (frameLeft > state.left) {
                state.left = frameLeft
            }
            if (frameRight < state.right) {
                state.right = frameRight
            }
            self.pruneData(frame2pos(frameLeft).left, frame2pos(frameRight).right)
        };
        function parse(data, frame) {
            if (state.empty || frame < state.left) {
                state.left = frame
            }
            if (state.empty || frame > state.right) {
                state.right = frame
            }
            state.empty = false;
            if (!data) return;
            var pos = frame2pos(frame);
            self.parseData(data, pos.left, pos.right)
        };
        function getLocation() {
            return views.current
        };
        function setLocation(requested) {
            Ext.apply(views.requested || {},
            requested || views.requested || {},
            defaultView);
       
            if(isInfo(self.config.id) && !InfoRequest.ready) return;
            if(isInfo(self.config.id)){
                views.requested.position = InfoRequest.position;
                views.requested.bases = InfoRequest.bases;
                views.requested.pixels = InfoRequest.pixels;
            }
            var hic_y,old_y;
            if(self.config.type == 'HiCTrack'){
                var track = AnnoJ.getGUI().Tracks.tracks.find('id', self.config.id);
                old_y = track.config.hic_y;
                hic_y = findConf(self.config.id).assembly;
                if(AnnoJ.config.settings.hic_d) views.requested.assembly = hic_y;
                else views.requested.assembly = AnnoJ.config.location.assembly
            }
            if(self.config.type == 'ModelsTrack'){
                var loc = AnnoJ.getLocation();
                AnnoJ.config.markname = 'chr'+loc.assembly+'-'+loc.position;
            }
            var newPolicy = getPolicy(views.requested);
            if (!newPolicy) {
                self.clearCanvas();
                self.mask('Out of zooming level');
                return
            }
            self.unmask();
            if (views.requested.assembly != state.assembly || policy.index != newPolicy.index || (isInfo(self.config.id) && !state.ready) || (self.config.type == 'HiCTrack' && hic_y != old_y)) {
                clear();
                self.clearCanvas();
                policy = newPolicy
            }
            var bases = self.config.cache * policy.bases / policy.pixels;
            if(isInfo(self.config.id)) bases /= 3;
        
            if(self.config.type == 'HiCTrack'){
                var half = 0;
            }
            var frameL = pos2frame(views.requested.position - bases);
            var frameR = pos2frame(views.requested.position + bases);
            prune(frameL, frameR);
            if (state.empty) {
                loadFrame(frameL)
            } else if (frameL < state.left) {
                loadFrame(state.left - 1)
            } else if (frameR > state.right) {
                loadFrame(state.right + 1)
            }
            if(state.busy == false){
              var edges = getEdges();
              self.paintCanvas(edges.g1, edges.g2, views.requested.bases, views.requested.pixels);
              state.ready = false;
              InfoRequest.ready = false
            }
            var ratio = AnnoJ.bases2pixels(1);
            if(self.config.type == "ModelsTrack" && ratio >= 5 && !isInfo(self.config.id))
            {
              var left = Math.round(views.requested.position - screen.width/(ratio*2));
              var right = Math.round(views.requested.position + screen.width/(ratio*2));
              if(left < 0) left = 0;
              if((!AnnoJ.config.genome_left || !AnnoJ.config.genome_right || AnnoJ.config.genome_left != left || AnnoJ.config.genome_right != right))
              {
                 AnnoJ.config.genome_left = left;
                 AnnoJ.config.genome_right = right;
                 loadGenome(left,right,self.config.name);
              }
           }
        };
        function loadGenome(gleft, gright, name) {
            var gurl;
            if(name.indexOf('hg18') != -1) gurl = "/proxy/http://tabit.ucsd.edu/fetchers/hg18_genome.php";
            if(name.indexOf('hg19') != -1) gurl = "/proxy/http://tabit.ucsd.edu/fetchers/hg19_genome.php";
            state.busy = true;
            self.setTitle('<span class="waiting">Updating genome...</span>');
            views.loading = views.requested;
            BaseJS.request({
                url: gurl,
                method: 'POST',
                requestJSON: false,
                data: {
                    action: 'range',
                    assembly: views.loading.assembly,
                    left: gleft,
                    right: gright,
                    bases: policy.bases,
                    pixels: policy.pixels
                },
                success: function(response) {
                    views.loading = null;
                    state.busy = false;
                    if(response.data[0] == "GENOME"){
                        AnnoJ.config.genome = response.data[2];
                        AnnoJ.config.genome_x = response.data[1];
                    }     
                    self.setTitle(self.config.name);
                    var edges = getEdges();
                    self.paintCanvas(edges.g1, edges.g2, views.requested.bases, views.requested.pixels)
                },
                failure: function(message) {
                    AnnoJ.error('Failed to load genome ' + self.config.name + ' (' + message + ')');
                    views.loading = null;
                    state.busy = false;
                    self.setTitle(self.config.name);
                }
            })
        };
        function loadFrame(frame) {
            if(self.config.id.indexOf("new-") != -1){
                if(policy.bases / policy.pixels < 5) return;
            }
            if (state.busy) return;
            state.busy = true;
            self.setTitle('<span class="waiting">Updating...</span>');
            views.loading = views.requested;
            var request_url = self.config.data;
            var title = self.config.name;
            var pos = frame2pos(frame);
            if(isInfo(self.config.id)) {
                request_url = AnnoJ.config.infoTrack.data;
                title = AnnoJ.config.infoTrack.name;
                var width = self.Frame.ext.getWidth() / 2;
                width = Math.ceil(width / 100 + 0.05) * 100;
                var ratio = width*InfoRequest.bases / InfoRequest.pixels;
                if(ratio < 1) ratio = 1;
                pos.left = InfoRequest.position - width * ratio;
                if(pos.left < 0) pos.left = 0;
                pos.right = InfoRequest.position + width * ratio;
                views.loading.assembly = AnnoJ.config.infoTrack.assembly
            }
            var sources = '';
            var actions = '';
            if(self.config.id == 'trackyyyy-0'){
                sources = AnnoJ.config.infoTrack.urls;
                 actions = AnnoJ.config.infoTrack.action
            }
            var tables = '';
            if(self.config.id != 'trackyyyy-0' && self.config.id != 'trackxxxx-0'){
                var track = findConf(self.config.id);
                sources = track.urls;
                 if(self.config.type == 'HiCTrack') 
                sources = AnnoJ.config.location.assembly + ',' + track.assembly;
                actions = track.action;
                if(!sources) sources = '';
                if(!actions) actions = '';
                tables = track.name
            }
             if(self.config.type == 'HiCTrack'){
                var whole = pos.right - pos.left + 1;
                pos.left -= whole;
                pos.right += whole;
                if(pos.left < 0) pos.left = 0
            }
            var cgi = request_url.indexOf('.cgi?');
            if(cgi >= 0){
               tables = '';
               var p = request_url.substring(cgi + 5);
               var t = p.indexOf('table=');
               if(t >= 0){
                  var tt = p.indexOf('&');
                  if(tt >= 0) tables = p.substring(t+6, tt);
                  else tables = p.substring(t+6);
               }
               request_url = request_url.substring(0,cgi + 4);
            }
 
            BaseJS.request({
                url: request_url,
                method: 'POST',
                requestJSON: false,
                data: {
                    action: 'range',
                    assembly: views.loading.assembly,
                    left: pos.left,
                    right: pos.right,
                    bases: policy.bases,
                    pixels: policy.pixels,
                    action2: actions,
                    urls: sources,
                    tracktype: self.config.type,
                    table: tables
                },
                success: function(response) {
                    if (views.loading && views.loading.assembly != state.assembly) {
                        state.assembly = views.loading.assembly;
                        clear()
                    }
                    if(self.config.id == 'trackyyyy-0'){
                        InfoRequest.corr = response.data;
                        state.empty = false;
                    }
                    else if(self.config.type == 'HiCTrack'){
                        var track = AnnoJ.getGUI().Tracks.tracks.find('id', self.config.id);
                        track.config.hic_y = findConf(self.config.id).assembly;
                        parse(response.data, frame);
                        state.empty = false
                    }
                    else parse(response.data, frame);
                    views.loading = null;
                    state.busy = false;
                    state.ready = true;
                    self.setTitle(title);
                    setLocation(views.requested)
                },
                failure: function(message) {
                    views.loading = null;
                    state.busy = false;
                    state.ready = true;
                    if(self.config.id.indexOf('Peakcall-') >= 0)
                      self.setTitle(self.config.name+"<font color=#FF0000>(in progress...)</font>")
                    //else self.setTitle(self.config.name+"<font color=#FF0000>(no data...)</font>")
                }
            })
        };
        return {
            getLocation: getLocation,
            setLocation: setLocation,
            getEdges: getEdges,
            convertX: convertX,
            convertG: convertG,
            clear: clear
        }
    })();
    this.close = function() {
        self.mask('Track Closed');
        DataManager.clear();
        self.remove()
    };
    this.hide = function() {
        self.mask('Track Closed');
        DataManager.clear();
        self.Frame.ext.setHeight(0);
        self.Frame.ext.setVisible(false)
    };
    this.moveCanvas = function(x) {
        self.Canvas.ext.setLeft(x)
    };
    this.setHeight = function(h) {
        if (h < self.config.minHeight) h = self.config.minHeight;
        if (h > self.config.maxHeight) h = self.config.maxHeight;
        var trackConfig = find(AnnoJ.config.tracks, 'id', self.config.id);
        if(trackConfig) trackConfig.height = h;
        self.Frame.ext.setHeight(h);
        self.refreshCanvas()
    };
    this.setHeatmapHeight = function(h) {
        if (h < 10) h = 10;
        self.Frame.ext.setHeight(h);
        self.refreshCanvas()
    };
    this.getLocation = DataManager.getLocation;
    this.setLocation = DataManager.setLocation;
    this.getEdges = DataManager.getEdges;
    this.convertX = DataManager.convertX;
    this.convertG = DataManager.convertG;
    this.setScale = Scaler.set;
    this.getScale = Scaler.get;
    this.clearCanvas = function() {};
    this.paintCanvas = function(x1, x2, bases, pixels) {};
    this.refreshCanvas = function() {};
    this.clearData = function() {};
    this.pruneData = function(x1, x2) {};
    this.parseData = function(data, x1, x2) {};
    this.getPolicy = function(view) {
        return null
    };
    this.rescale = function(value) {};
    this.setScale(self.config.scaler)
};
Ext.extend(AnnoJ.BrowserTrack, AnnoJ.DataTrack);
AnnoJ.MaskTrack = function(userConfig) {
    AnnoJ.MaskTrack.superclass.constructor.call(this, userConfig);
    var self = this;
    var defaultConfig = {
        single: false,
        clsAbove: 'AJ_above',
        clsBelow: 'AJ_below'
    };
    Ext.apply(self.config, userConfig || {},
    defaultConfig);
    var containerA = new Ext.Element(document.createElement('DIV'));
    var containerB = new Ext.Element(document.createElement('DIV'));
    containerA.addClass(self.config.clsAbove);
    containerB.addClass(self.config.clsBelow);
    containerA.setStyle('position', 'relative');
    containerB.setStyle('position', 'relative');
    containerA.setStyle('width', '100%');
    containerB.setStyle('width', '100%');
    if (self.config.single) {
        containerA.setStyle('height', '100%');
        containerB.setStyle('display', 'none')
    } else {
        containerA.setStyle('height', '49%');
        containerB.setStyle('height', '49%');
        containerA.setStyle('borderBottom', 'dotted black 1px')
    }
    containerA.appendTo(self.Canvas.ext);
    containerB.appendTo(self.Canvas.ext);
    var Models = (function() {
        var dataA = new ModelsList();
        var dataB = new ModelsList();
        function parse(data) {
            dataA.parse(data, true);
            dataB.parse(data, false)
        };
        var canvasA = new MaskCanvas();
        var canvasB = new MaskCanvas();
        canvasA.setContainer(containerA.dom);
        canvasB.setContainer(containerB.dom);
        function paint(left, right, bases, pixels) {
            var subsetA = dataA.subset2canvas(left, right, bases, pixels);
            var subsetB = dataB.subset2canvas(left, right, bases, pixels);
            canvasA.setData(subsetA);
            canvasB.setData(subsetB);
            canvasA.paint();
            canvasB.paint();
            var list = canvasA.groups.getList();
            for (var series in list) {
                self.Toolbar.addItem(series);
                addLabel(series)
            }
            list = canvasB.groups.getList();
            for (var series in list) {
                self.Toolbar.addItem(series);
                addLabel(series)
            }
        };
        return {
            dataA: dataA,
            dataB: dataB,
            canvasA: canvasA,
            canvasB: canvasB,
            parse: parse,
            paint: paint
        }
    })();
    var handler = Models;
    var policies = [{
        index: 0,
        min: 1 / 100,
        max: 10 / 1,
        bases: 1,
        pixels: 1,
        cache: 10000
    },
    {
        index: 1,
        min: 10 / 1,
        max: 100 / 1,
        bases: 10,
        pixels: 1,
        cache: 100000
    },
    {
        index: 2,
        min: 100 / 1,
        max: 1000 / 1,
        bases: 100,
        pixels: 1,
        cache: 1000000
    }];
    var labels = null;
    function addLabel(name) {
        if (!labels) {
            self.ContextMenu.addItems(['-', 'Series']);
            labels = {}
        }
        if (labels[name] == undefined) {
            labels[name] = true;
            self.ContextMenu.addItems([new Ext.menu.CheckItem({
                text: name,
                checked: true,
                handler: function() {
                    handler.canvasA.groups.toggle(name, !this.checked);
                    handler.canvasB.groups.toggle(name, !this.checked);
                    handler.canvasA.refresh();
                    handler.canvasB.refresh()
                }
            })])
        }
    };
    this.getPolicy = function(view) {
        var ratio = view.bases / view.pixels;
        for (var i = 0; i < policies.length; i++) {
            if (ratio >= policies[i].min && ratio < policies[i].max) {
                return policies[i]
            }
        }
        return null
    };
    this.rescale = function() {};
    this.clearCanvas = function() {
        handler.canvasA.clear();
        handler.canvasB.clear()
    };
    this.paintCanvas = function(l, r, b, p) {
        handler.paint(l, r, b, p)
    };
    this.refreshCanvas = function() {
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    };
    this.resizeCanvas = function() {
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    };
    this.clearData = function() {
        handler.dataA.clear();
        handler.dataB.clear()
    };
    this.pruneData = function(a, b) {
        handler.dataA.prune(a, b);
        handler.dataB.prune(a, b)
    };
    this.parseData = function(data) {
        handler.parse(data)
    }
};
Ext.extend(AnnoJ.MaskTrack, AnnoJ.BrowserTrack);
AnnoJ.ModelsTrack = function(userConfig) {
    AnnoJ.ModelsTrack.superclass.constructor.call(this, userConfig);
    var self = this;
    var defaultConfig = {
        single: false,
        searchBox: true,
        searchURL: self.config.data,
        clsAbove: 'AJ_above',
        clsBelow: 'AJ_below',
        slider: 0.5,
        showLabels: true,
        showArrows: true,
        labelPos: 'left',
        boxHeight: 10,
        boxHeightMax: 24,
        boxHeightMin: 1,
        boxBlingLimit: 6
    };
    Ext.applyIf(self.config, defaultConfig);
    var containerA = new Ext.Element(document.createElement('DIV'));
    var containerB = new Ext.Element(document.createElement('DIV'));
    containerA.addClass(self.config.clsAbove);
    containerB.addClass(self.config.clsBelow);
    containerA.setStyle('position', 'relative');
    containerB.setStyle('position', 'relative');
    containerA.setStyle('width', '100%');
    containerB.setStyle('width', '100%');
    if (self.config.single) {
        containerA.setStyle('height', '100%');
        containerB.setStyle('display', 'none')
    } else {
        containerA.setStyle('height', '49%');
        containerB.setStyle('height', '49%');
        containerA.setStyle('borderBottom', 'dotted black 1px')
    }
    containerA.appendTo(self.Canvas.ext);
    containerB.appendTo(self.Canvas.ext);
    if (self.config.searchBox) {
        var ds = new Ext.data.Store({
            url: self.config.searchURL,
            baseParams: {
                action: 'lookup'
            },
            reader: new Ext.data.JsonReader({
                root: 'rows',
                totalProperty: 'count',
                id: 'id'
            },
            [{
                name: 'id',
                mapping: 'id'
            },
            {
                name: 'assembly',
                mapping: 'assembly'
            },
            {
                name: 'start',
                mapping: 'start'
            },
            {
                name: 'end',
                mapping: 'end'
            },
            {
                name: 'description',
                mapping: 'description'
            }])
        });
        var resultTpl = new Ext.XTemplate('<tpl for="."><div class="gi">', '<b>{id}: </b><span>{description}</span>', '</div></tpl>');
        var search = new Ext.form.ComboBox({
            store: ds,
            displayField: 'id',
            typeAhead: false,
            cls: 'promote',
            loadingText: 'Searching...',
            width: 150,
            pageSize: 10,
            hideTrigger: false,
            tpl: resultTpl,
            minChars: 3,
            minListWidth: 400,
            itemSelector: 'div.gi',
            emptyText: 'Search...',
            onSelect: function(record) {
                var loc = AnnoJ.getLocation();
                loc.assembly = record.data.assembly;
                loc.position = parseInt(record.data.start);
                AnnoJ.setLocation(loc);
                self.fireEvent('browse', loc);
                this.collapse()
            }
        });
        search.on('expand',
        function() {
            if (!self.Toolbar.isVisible()) {
                this.collapse()
            }
        });
    if(!isInfo(self.config.id)){
            self.Toolbar.insert(4, new Ext.Toolbar.Spacer());
            self.Toolbar.insert(4, search);
    }
    }
    var Models = (function() {
        var dataA = new ModelsList();
        var dataB = new ModelsList();
        function parse(data) {
            dataA.parse(data, true);
            dataB.parse(data, false)
        };
        var canvasA = new ModelsCanvas({
            strand: '+',
            labels: self.config.showLabels,
            arrows: self.config.showArrows,
            scaler: self.config.slider * 2,
            boxHeight: self.config.boxHeight,
            boxHeightMax: self.config.boxHeightMax,
            boxHeightMin: self.config.boxHeightMin,
            boxBlingLimit: self.config.boxBlingLimit
        });
        var canvasB = new ModelsCanvas({
            strand: '-',
            labels: self.config.showLabels,
            arrows: self.config.showArrows,
            scaler: self.config.slider * 2,
            boxHeight: self.config.boxHeight,
            boxHeightMax: self.config.boxHeightMax,
            boxHeightMin: self.config.boxHeightMin,
            boxBlingLimit: self.config.boxBlingLimit
        });
        canvasA.setContainer(containerA.dom);
        canvasB.setContainer(containerB.dom);
        canvasA.on('modelSelected', lookupModel);
        canvasB.on('modelSelected', lookupModel);
        function paint(left, right, bases, pixels) {
            var subsetA = dataA.subset2canvas(left, right, bases, pixels);
            var subsetB = dataB.subset2canvas(left, right, bases, pixels);
            canvasA.setData(subsetA);
            canvasB.setData(subsetB);
            if(subsetA.length > 0){
                var i = Math.floor(subsetA.length/2);
                AnnoJ.config.markname = subsetA[i].id
            } else if(subsetA.length > 0){
                var i = Math.floor(subsetB.length/2);
                AnnoJ.config.markname = subsetB[i].id
            }
            var id = 'models_' + self.config.id;
            while(1)
            {
                var o = document.getElementById(id);
                if(o == null) break;
                o.parentNode.removeChild(o);
            }

            canvasA.paint();
            canvasB.paint();
            var list = canvasA.groups.getList();
            for (var series in list) {
                self.Toolbar.addItem(series);
                addLabel(series)
            }
            list = canvasB.groups.getList();
            for (var series in list) {
                self.Toolbar.addItem(series);
                addLabel(series)
            }
        };
        return {
            dataA: dataA,
            dataB: dataB,
            canvasA: canvasA,
            canvasB: canvasB,
            parse: parse,
            paint: paint
        }
    })();
    var handler = Models;
    var policies = [{
        index: 0,
        min: 1 / 100,
        max: 1 / 5,
        bases: 1,
        pixels: 100,
        cache: 1000
    },
    {
        index: 1,
        min: 1 / 5,
        max: 1 / 1,
        bases: 1,
        pixels: 10,
        cache: 1000
    },
    {
        index: 2,
        min: 1 / 1,
        max: 10 / 1,
        bases: 1,
        pixels: 1,
        cache: 10000
    },
    {
        index: 3,
        min: 10 / 1,
        max: 100 / 1,
        bases: 10,
        pixels: 1,
        cache: 100000
    },
    {
        index: 4,
        min: 100 / 1,
        max: 1000 / 1,
        bases: 100,
        pixels: 1,
        cache: 1000000
    },
    {
        index: 5,
        min: 1000 / 1,
        max: 10000 / 1,
        bases: 1000,
        pixels: 1,
        cache: 10000000
    }];
    var labels = null;
    function addLabel(name) {
        if (!labels) {
            self.ContextMenu.addItems(['-', 'Series']);
            labels = {}
        }
        if (labels[name] == undefined) {
            labels[name] = true;
            self.ContextMenu.addItems([new Ext.menu.CheckItem({
                text: name,
                checked: true,
                handler: function() {
                    handler.canvasA.groups.toggle(name, !this.checked);
                    handler.canvasB.groups.toggle(name, !this.checked);
                    handler.canvasA.refresh();
                    handler.canvasB.refresh()
                }
            })])
        }
    };
    function lookupModel(id) {
        AnnoJ.getGUI().InfoBox.echo("<div class='waiting'>Loading...</div>");
        BaseJS.request({
            url: self.config.data,
            method: 'GET',
            requestJSON: false,
            data: {
                action: 'describe',
                id: id
            },
            success: function(response) {
                if (response.success) {
                    var d = response.data;
                    var html = "<table>";
                    html += "<tr><td><b>Gene ID: </b></td><td width='100%'>" + d.id + "</td></tr>";
                    html += "<tr><td><b>Assembly: </b></td><td>" + d.assembly + "</td></tr>";
                    html += "<tr><td><b>Position: </b></td><td>" + d.start + '..' + d.end + "</td></tr>";
                    html += "<tr><td colspan='2'>" + d.description + "</td></tr>";
                    html += "</table>";
                    AnnoJ.getGUI().InfoBox.echo(html)
                } else {
                    AnnoJ.getGUI().InfoBox.echo("Error: failed to retrieve gene information. Server says: " + response.message)
                }
            },
            failure: function(message) {
                AnnoJ.getGUI().InfoBox.echo("Error: failed to retrieve gene information from the server")
            }
        })
    }
    this.getPolicy = function(view) {
        var ratio = view.bases / view.pixels;
        for (var i = 0; i < policies.length; i++) {
            if (ratio >= policies[i].min && ratio < policies[i].max) {
                return policies[i]
            }
        }
        return null
    };
    this.rescale = function(f) {
        var f = parseFloat(f * 2) || 0;
        handler.canvasA.setScaler(f);
        handler.canvasB.setScaler(f);
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    };
    this.l_rescale = function(f) {
        handler.canvasA.setScaler(f);
        handler.canvasB.setScaler(f);
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    };
    this.clearCanvas = function() {
        handler.canvasA.clear();
        handler.canvasB.clear()
    };
    this.paintCanvas = function(l, r, b, p) {
        handler.paint(l, r, b, p)
    };
    this.refreshCanvas = function() {
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    };
    this.resizeCanvas = function() {
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    };
    this.clearData = function() {
        handler.dataA.clear();
        handler.dataB.clear()
    };
    this.pruneData = function(a, b) {
        handler.dataA.prune(a, b);
        handler.dataB.prune(a, b)
    };
    this.parseData = function(data) {
        handler.parse(data)
    }
};
Ext.extend(AnnoJ.ModelsTrack, AnnoJ.BrowserTrack);
AnnoJ.MicroarrayTrack = function(userConfig) {
    var self = this;
    AnnoJ.MicroarrayTrack.superclass.constructor.call(self, userConfig);
    var defaultConfig = {
        single: false,
        clsAbove: 'AJ_above',
        clsBelow: 'AJ_below'
    };
    Ext.applyIf(self.config, defaultConfig);
    var containerA = new Ext.Element(document.createElement('DIV'));
    var containerB = new Ext.Element(document.createElement('DIV'));
    containerA.addClass(self.config.clsAbove);
    containerB.addClass(self.config.clsBelow);
    containerA.setStyle('position', 'relative');
    containerB.setStyle('position', 'relative');
    containerA.setStyle('width', '100%');
    containerB.setStyle('width', '100%');
    if (self.config.single) {
        containerA.setStyle('height', '100%');
        containerB.setStyle('display', 'none')
    } else {
        containerA.setStyle('height', '49%');
        containerB.setStyle('height', '49%');
        containerA.setStyle('borderBottom', 'dotted black 1px')
    }
    containerA.appendTo(self.Canvas.ext);
    containerB.appendTo(self.Canvas.ext);
    var Histogram = (function() {
        var dataA = new HistogramData();
        var dataB = new HistogramData();
        function parse(data) {
            for (var series in data) {
                self.Toolbar.addItem(series);
                addLabel(series)
            }
            dataA.parse(data, true);
            dataB.parse(data, false)
        };
        var canvasA = new HistogramCanvas();
        var canvasB = new HistogramCanvas();
        canvasB.flipY();
        canvasA.setContainer(containerA.dom);
        canvasB.setContainer(containerB.dom);
        function paint(left, right, bases, pixels) {
            var subsetA = dataA.subset2canvas(left, right, bases, pixels);
            var subsetB = dataB.subset2canvas(left, right, bases, pixels);
            canvasA.setData(subsetA);
            canvasB.setData(subsetB);
            canvasA.paint();
            canvasB.paint()
        };
        return {
            dataA: dataA,
            dataB: dataB,
            canvasA: canvasA,
            canvasB: canvasB,
            parse: parse,
            paint: paint
        }
    })();
    var handler = Histogram;
    var policies = [{
        index: 0,
        min: 1 / 100,
        max: 10 / 1,
        bases: 1,
        pixels: 1,
        cache: 10000
    },
    {
        index: 1,
        min: 10 / 1,
        max: 100 / 1,
        bases: 10,
        pixels: 1,
        cache: 100000
    },
    {
        index: 2,
        min: 100 / 1,
        max: 1000 / 1,
        bases: 100,
        pixels: 1,
        cache: 1000000
    },
    {
        index: 3,
        min: 1000 / 1,
        max: 10000 / 1,
        bases: 1000,
        pixels: 1,
        cache: 10000000
    },
    {
        index: 4,
        min: 10000 / 1,
        max: 200001 / 1,
        bases: 10000,
        pixels: 1,
        cache: 100000000
    }];

    var labels = null;
    function addLabel(name) {
        if (!labels) {
            self.ContextMenu.addItems(['-', 'Series']);
            labels = {}
        }
        if (labels[name] == undefined) {
            labels[name] = true;
            self.ContextMenu.addItems([new Ext.menu.CheckItem({
                text: name,
                checked: true,
                handler: function() {
                    handler.canvasA.groups.toggle(name, !this.checked);
                    handler.canvasB.groups.toggle(name, !this.checked);
                    handler.canvasA.refresh();
                    handler.canvasB.refresh()
                }
            })])
        }
    };
    this.getPolicy = function(view) {
        var ratio = view.bases / view.pixels;
        handler = Histogram;
        for (var i = 0; i < policies.length; i++) {
            if (ratio >= policies[i].min && ratio < policies[i].max) {
                return policies[i]
            }
        }
        return null
    };
    this.rescale = function(f) {
        var f = Math.pow(f * 2, 4);
        handler.canvasA.setScaler(f);
        handler.canvasB.setScaler(f);
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    };
    this.l_rescale = function(f) {
        handler.canvasA.setScaler(f);
        handler.canvasB.setScaler(f);
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    };
    this.clearCanvas = function() {
        handler.canvasA.clear();
        handler.canvasB.clear()
    };
    this.paintCanvas = function(l, r, b, p) {
        handler.paint(l, r, b, p)
    };
    this.refreshCanvas = function() {
        handler.canvasA.refresh(true);
        handler.canvasB.refresh(true)
    };
    this.resizeCanvas = function() {
        handler.canvasA.refresh(true);
        handler.canvasB.refresh(true)
    };
    this.clearData = function() {
        handler.dataA.clear();
        handler.dataB.clear()
    };
    this.pruneData = function(a, b) {
        handler.dataA.prune(a, b);
        handler.dataB.prune(a, b)
    };
    this.parseData = function(data) {
        handler.parse(data)
    }
};
Ext.extend(AnnoJ.MicroarrayTrack, AnnoJ.BrowserTrack);
AnnoJ.MethTrack = function(userConfig) {
    var self = this;
    AnnoJ.MethTrack.superclass.constructor.call(self, userConfig);
    var defaultConfig = {
        single: false,
        clsAbove: 'AJ_above',
        clsBelow: 'AJ_below'
    };
    Ext.applyIf(self.config, defaultConfig);
    var containerA = new Ext.Element(document.createElement('DIV'));
    var containerB = new Ext.Element(document.createElement('DIV'));
    containerA.addClass(self.config.clsAbove);
    containerB.addClass(self.config.clsBelow);
    containerA.setStyle('position', 'relative');
    containerB.setStyle('position', 'relative');
    containerA.setStyle('width', '100%');
    containerB.setStyle('width', '100%');
    if (self.config.single) {
        containerA.setStyle('height', '100%');
        containerB.setStyle('display', 'none')
    } else {
        containerA.setStyle('height', '49%');
        containerB.setStyle('height', '49%');
        containerA.setStyle('borderBottom', 'dotted black 1px')
    }
    containerA.appendTo(self.Canvas.ext);
    containerB.appendTo(self.Canvas.ext);
    var menuCG = new Ext.menu.CheckItem({
        text: 'CG',
        handler: toggleMeth
    });
    var menuCHG = new Ext.menu.CheckItem({
        text: 'CHG',
        handler: toggleMeth
    });
    var menuCHH = new Ext.menu.CheckItem({
        text: 'CHH',
        handler: toggleMeth
    });
    menuCG.setChecked(true);
    menuCHG.setChecked(true);
    menuCHH.setChecked(true);
    self.ContextMenu.addItems([menuCG, menuCHG, menuCHH]);
    var toolbarCG = new Ext.Toolbar.Button({
        text: 'CG',
        iconCls: 'silk_bullet_orange',
        tooltip: 'Show/hide CG methylation',
        handler: toggleMeth
    });
    var toolbarCHG = new Ext.Toolbar.Button({
        text: 'CHG',
        iconCls: 'silk_bullet_blue',
        tooltip: 'Show/hide CHG methylation',
        handler: toggleMeth
    });
    var toolbarCHH = new Ext.Toolbar.Button({
        text: 'CHH',
        iconCls: 'silk_bullet_pink',
        tooltip: 'Show/hide CHH methylation',
        handler: toggleMeth
    });
    self.Toolbar.insert(4, toolbarCHH);
    self.Toolbar.insert(4, toolbarCHG);
    self.Toolbar.insert(4, toolbarCG);
    function toggleMeth(item) {
        var show = true;
        if (item.iconCls) {
            show = item.iconCls == 'silk_bullet_white'
        } else {
            show = !item.checked
        }
        if (item.text == 'CG') {
            toolbarCG.setIconClass(show ? 'silk_bullet_orange': 'silk_bullet_white');
            if (item.iconCls) menuCG.setChecked(show)
        } else if (item.text == 'CHG') {
            toolbarCHG.setIconClass(show ? 'silk_bullet_blue': 'silk_bullet_white');
            if (item.iconCls) menuCHG.setChecked(show)
        } else if (item.text == 'CHH') {
            toolbarCHH.setIconClass(show ? 'silk_bullet_pink': 'silk_bullet_white');
            if (item.iconCls) menuCHH.setChecked(show)
        } else {
            return
        }
        handler.canvasA.groups.toggle(item.text, show);
        handler.canvasB.groups.toggle(item.text, show);
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    }
    var Histogram = (function() {
        var dataA = new HistogramData();
        var dataB = new HistogramData();
        function parse(data) {
            dataA.parse(data, true);
            dataB.parse(data, false)
        };
        var canvasA = new HistogramCanvas();
        var canvasB = new HistogramCanvas();
        canvasB.flipY();
        canvasA.setContainer(containerA.dom);
        canvasB.setContainer(containerB.dom);
        function paint(left, right, bases, pixels) {
            var subsetA = dataA.subset2canvas(left, right, bases, pixels);
            var subsetB = dataB.subset2canvas(left, right, bases, pixels);
            canvasA.setData(subsetA);
            canvasB.setData(subsetB);
            var max = Math.max(canvasA.getMax() || 0, canvasB.getMax() || 0);
            AnnoJ.config.maxlist[self.config.id] = Math.ceil(max);
            canvasA.paint();
            canvasB.paint()
        };
        return {
            dataA: dataA,
            dataB: dataB,
            canvasA: canvasA,
            canvasB: canvasB,
            parse: parse,
            paint: paint
        }
    })();
    var handler = Histogram;
    var policies = [{
        index: 0,
        min: 1 / 100,
        max: 10 / 1,
        bases: 1,
        pixels: 1,
        cache: 10000
    },
    {
        index: 1,
        min: 10 / 1,
        max: 100 / 1,
        bases: 10,
        pixels: 1,
        cache: 100000
    },
    {
        index: 2,
        min: 100 / 1,
        max: 1000 / 1,
        bases: 100,
        pixels: 1,
        cache: 1000000
    },
    {
        index: 3,
        min: 1000 / 1,
        max: 10000 / 1,
        bases: 1000,
        pixels: 1,
        cache: 10000000
    },
    {
        index: 4,
        min: 10000 / 1,
        max: 200001 / 1,
        bases: 10000,
        pixels: 1,
        cache: 100000000
    }];
    this.getPolicy = function(view) {
        var ratio = view.bases / view.pixels;
        handler = Histogram;
        for (var i = 0; i < policies.length; i++) {
            if (ratio >= policies[i].min && ratio < policies[i].max) {
                return policies[i]
            }
        }
        return null
    };
    this.rescale = function(f) {
        var f = Math.pow(f * 2, 4);
        handler.canvasA.setScaler(f);
        handler.canvasB.setScaler(f);
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    };
    this.l_rescale = function(f) {
        handler.canvasA.setScaler(f);
        handler.canvasB.setScaler(f);
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    };
    this.clearCanvas = function() {
        handler.canvasA.clear();
        handler.canvasB.clear()
    };
    this.paintCanvas = function(l, r, b, p) {
        handler.paint(l, r, b, p)
    };
    this.refreshCanvas = function() {
        handler.canvasA.refresh(true);
        handler.canvasB.refresh(true)
    };
    this.resizeCanvas = function() {
        handler.canvasA.refresh(true);
        handler.canvasB.refresh(true)
    };
    this.clearData = function() {
        handler.dataA.clear();
        handler.dataB.clear()
    };
    this.pruneData = function(a, b) {
        handler.dataA.prune(a, b);
        handler.dataB.prune(a, b)
    };
    this.parseData = function(data) {
        handler.parse(data)
    }
};
Ext.extend(AnnoJ.MethTrack, AnnoJ.BrowserTrack);
AnnoJ.ReadsTrack = function(userConfig) {
    var self = this;
    AnnoJ.ReadsTrack.superclass.constructor.call(self, userConfig);
    var defaultConfig = {
        single: false,
        clsAbove: 'AJ_above',
        clsBelow: 'AJ_below',
        slider: 0.5,
        boxHeight: 10,
        boxHeightMax: 24,
        boxHeightMin: 1,
        boxBlingLimit: 6
    };
    Ext.applyIf(self.config, defaultConfig);
    var containerA = new Ext.Element(document.createElement('DIV'));
    var containerB = new Ext.Element(document.createElement('DIV'));
    containerA.addClass(self.config.clsAbove);
    containerB.addClass(self.config.clsBelow);
    containerA.setStyle('position', 'relative');
    containerB.setStyle('position', 'relative');
    containerA.setStyle('width', '100%');
    containerB.setStyle('width', '100%');
    if (self.config.single) {
        containerA.setStyle('height', '100%');
        containerB.setStyle('display', 'none')
    } else {
        containerA.setStyle('height', '49%');
        containerB.setStyle('height', '49%');
        containerA.setStyle('borderBottom', 'dotted black 1px')
    }
    containerA.appendTo(self.Canvas.ext);
    containerB.appendTo(self.Canvas.ext);
    var Histogram = (function() {
        var dataA = new HistogramData();
        var dataB = new HistogramData();
        function parse(data) {
            for (var series in data) {
                addLabel(series);
                self.Toolbar.addItem(series)
            }
            dataA.parse(data, true);
            dataB.parse(data, false);
            if(Ext.isGecko){
              var cnt = dataA.getCount(data);
              if(cnt <= 0) dataA.parse(data, true);
              cnt = dataB.getCount(data);
              if(cnt <= 0) dataB.parse(data, false)
            }
        };
        var canvasA = new HistogramCanvas();
        var canvasB = new HistogramCanvas();
        canvasB.flipY();
        function paint(left, right, bases, pixels) {
            var subsetA = dataA.subset2canvas(left, right, bases, pixels);
            var subsetB = dataB.subset2canvas(left, right, bases, pixels);
            canvasA.setData(subsetA);
            canvasB.setData(subsetB);
            var max = Math.max(canvasA.getMax() || 0, canvasB.getMax() || 0);
            AnnoJ.config.maxlist[self.config.id] = Math.ceil(max);
            canvasA.paint();
            canvasB.paint()
        };
        return {
            dataA: dataA,
            dataB: dataB,
            canvasA: canvasA,
            canvasB: canvasB,
            parse: parse,
            paint: paint
        }
    })();
    var Reads = (function() {
        var dataA = new ReadsList();
        var dataB = new ReadsList();
        function parse(data) {
            for (var series in data) {
                addLabel(series);
                self.Toolbar.addItem(series)
            }
            dataA.parse(data, true);
            dataB.parse(data, false)
        };
        var canvasA = new ReadsCanvas({
            scaler: self.config.slider,
            oxHeight: self.config.boxHeight,
            boxHeightMax: self.config.boxHeightMax,
            boxHeightMin: self.config.boxHeightMin,
            boxBlingLimit: self.config.boxBlingLimit
        });
        var canvasB = new ReadsCanvas({
            scaler: self.config.slider,
            boxHeight: self.config.boxHeight,
            boxHeightMax: self.config.boxHeightMax,
            boxHeightMin: self.config.boxHeightMin,
            boxBlingLimit: self.config.boxBlingLimit
        });
        canvasB.flipY();
        function paint(left, right, bases, pixels) {
            var subsetA = dataA.subset2canvas(left, right, bases, pixels);
            var subsetB = dataB.subset2canvas(left, right, bases, pixels);
            canvasA.setData(subsetA);
            canvasB.setData(subsetB);
            canvasA.paint();
            canvasB.paint()
        };
        return {
            dataA: dataA,
            dataB: dataB,
            canvasA: canvasA,
            canvasB: canvasB,
            parse: parse,
            paint: paint
        }
    })();
    var handler = Histogram;
    var policies = [{
        index: 0,
        min: 1 / 100,
        max: 1 / 1,
        bases: 1,
        pixels: 100,
        cache: 1000
    },
    {
        index: 1,
        min: 1 / 1,
        max: 10 / 1,
        bases: 1,
        pixels: 1,
        cache: 10000
    },
    {
        index: 2,
        min: 10 / 1,
        max: 100 / 1,
        bases: 10,
        pixels: 1,
        cache: 100000
    },
    {
        index: 3,
        min: 100 / 1,
        max: 1000 / 1,
        bases: 100,
        pixels: 1,
        cache: 1000000
    },
    {
        index: 4,
        min: 1000 / 1,
        max: 10000 / 1,
        bases: 1000,
        pixels: 1,
        cache: 10000000
    },
    {
        index: 5,
        min: 10000 / 1,
        max: 200001 / 1,
        bases: 10000,
        pixels: 1,
        cache: 100000000
    }];
    var labels = null;
    function addLabel(name) {
        if (!labels) {
            self.ContextMenu.addItems(['-', 'Series']);
            labels = {}
        }
        if (labels[name] == undefined) {
            labels[name] = true;
            self.ContextMenu.addItems([new Ext.menu.CheckItem({
                text: name,
                checked: true,
                handler: function() {
                    handler.canvasA.groups.toggle(name, !this.checked);
                    handler.canvasB.groups.toggle(name, !this.checked);
                    handler.canvasA.refresh();
                    handler.canvasB.refresh()
                }
            })])
        }
    };
    this.getPolicy = function(view) {
        var ratio = view.bases / view.pixels;
        handler.canvasA.setContainer(null);
        handler.canvasB.setContainer(null);
        handler = (ratio < 10) ? Reads: Histogram;
        handler.canvasA.setContainer(containerA.dom);
        handler.canvasB.setContainer(containerB.dom);
        for (var i = 0; i < policies.length; i++) {
            if (ratio >= policies[i].min && ratio < policies[i].max) {
                return policies[i]
            }
        }
        return null
    };
    this.rescale = function(f) {
        var f = (handler == Histogram) ? Math.pow(f * 2, 4) : f;
        handler.canvasA.setScaler(f);
        handler.canvasB.setScaler(f);
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    };
    this.l_rescale = function(f) {
        handler.canvasA.setScaler(f);
        handler.canvasB.setScaler(f);
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    };
    this.clearCanvas = function() {
        handler.canvasA.clear();
        handler.canvasB.clear()
    };
    this.paintCanvas = function(l, r, b, p) {
        handler.paint(l, r, b, p)
    };
    this.refreshCanvas = function() {
        handler.canvasA.refresh(true);
        handler.canvasB.refresh(true)
    };
    this.resizeCanvas = function() {
        handler.canvasA.refresh(true);
        handler.canvasB.refresh(true)
    };
    this.clearData = function() {
        handler.dataA.clear();
        handler.dataB.clear()
    };
    this.pruneData = function(a, b) {
        handler.dataA.prune(a, b);
        handler.dataB.prune(a, b)
    };
    this.parseData = function(data) {
        handler.parse(data)
    }
};
Ext.extend(AnnoJ.ReadsTrack, AnnoJ.BrowserTrack);

AnnoJ.PairedEndTrack = function(userConfig) {
    var self = this;
    AnnoJ.PairedEndTrack.superclass.constructor.call(self, userConfig);
    var defaultConfig = {
        single: false,
        clsAbove: 'AJ_above',
        clsBelow: 'AJ_below',
        slider: 0.5,
        boxHeight: 10,
        boxHeightMax: 24,
        boxHeightMin: 1,
        boxBlingLimit: 6
    };
    Ext.applyIf(self.config, defaultConfig);
    var containerA = new Ext.Element(document.createElement('DIV'));
    var containerB = new Ext.Element(document.createElement('DIV'));
    containerA.addClass(self.config.clsAbove);
    containerB.addClass(self.config.clsBelow);
    containerA.setStyle('position', 'relative');
    containerB.setStyle('position', 'relative');
    containerA.setStyle('width', '100%');
    containerB.setStyle('width', '100%');
    if (self.config.single) {
        containerA.setStyle('height', '100%');
        containerB.setStyle('display', 'none')
    } else {
        containerA.setStyle('height', '49%');
        containerB.setStyle('height', '49%');
        containerA.setStyle('borderBottom', 'dotted black 1px')
    }
    containerA.appendTo(self.Canvas.ext);
    containerB.appendTo(self.Canvas.ext);
    var Histogram = (function() {
        var dataA = new HistogramData();
        var dataB = new HistogramData();
        function parse(data) {
            for (var series in data) {
                addLabel(series)
            }
            dataA.parse(data, true);
            dataB.parse(data, false)
        };
        var canvasA = new HistogramCanvas();
        var canvasB = new HistogramCanvas();
        canvasB.flipY();
        function paint(left, right, bases, pixels) {
            var subsetA = dataA.subset2canvas(left, right, bases, pixels);
            var subsetB = dataB.subset2canvas(left, right, bases, pixels);
            canvasA.setData(subsetA);
            canvasB.setData(subsetB);
            var max = Math.max(canvasA.getMax() || 0, canvasB.getMax() || 0);
            AnnoJ.config.maxlist[self.config.id] = Math.ceil(max);
            canvasA.paint();
            canvasB.paint()
        };
        return {
            dataA: dataA,
            dataB: dataB,
            canvasA: canvasA,
            canvasB: canvasB,
            parse: parse,
            paint: paint
        }
    })();
    var Reads = (function() {
        var dataA = new PairedReadsList();
        var dataB = new PairedReadsList();
        function parse(data) {
            for (var series in data) {
                addLabel(series)
            }
            dataA.parse(data, true);
            dataB.parse(data, false)
        };
        var canvasA = new PairedReadsCanvas({
            scaler: self.config.slider,
            boxHeight: self.config.boxHeight,
            boxHeightMax: self.config.boxHeightMax,
            boxHeightMin: self.config.boxHeightMin,
            boxBlingLimit: self.config.boxBlingLimit
        });
        var canvasB = new PairedReadsCanvas({
            scaler: self.config.slider,
            boxHeight: self.config.boxHeight,
            boxHeightMax: self.config.boxHeightMax,
            boxHeightMin: self.config.boxHeightMin,
            boxBlingLimit: self.config.boxBlingLimit
        });
        canvasB.flipY();
        function paint(left, right, bases, pixels) {
            var subsetA = dataA.subset2canvas(left, right, bases, pixels);
            var subsetB = dataB.subset2canvas(left, right, bases, pixels);
            canvasA.setData(subsetA);
            canvasB.setData(subsetB);
            canvasA.paint();
            canvasB.paint()
        };
        return {
            dataA: dataA,
            dataB: dataB,
            canvasA: canvasA,
            canvasB: canvasB,
            parse: parse,
            paint: paint
        }
    })();
    var handler = Histogram;
    var policies = [{
        index: 0,
        min: 1 / 100,
        max: 1 / 1,
        bases: 1,
        pixels: 100,
        cache: 1000
    },
    {
        index: 1,
        min: 1 / 1,
        max: 10 / 1,
        bases: 1,
        pixels: 1,
        cache: 10000
    },
    {
        index: 2,
        min: 10 / 1,
        max: 100 / 1,
        bases: 10,
        pixels: 1,
        cache: 100000
    },
    {
        index: 3,
        min: 100 / 1,
        max: 1000 / 1,
        bases: 100,
        pixels: 1,
        cache: 1000000
    },
    {
        index: 4,
        min: 1000 / 1,
        max: 10000 / 1,
        bases: 1000,
        pixels: 1,
        cache: 10000000
    },
    {
        index: 5,
        min: 10000 / 1,
        max: 200001 / 1,
        bases: 10000,
        pixels: 1,
        cache: 100000000
    }];
    var labels = null;
    function addLabel(name) {
        if (!labels) {
            self.ContextMenu.addItems(['-', 'Series']);
            labels = {}
        }
        if (labels[name] == undefined) {
            labels[name] = true;
            self.ContextMenu.addItems([new Ext.menu.CheckItem({
                text: name,
                checked: true,
                handler: function() {
                    handler.canvasA.groups.toggle(name, !this.checked);
                    handler.canvasB.groups.toggle(name, !this.checked);
                    handler.canvasA.refresh();
                    handler.canvasB.refresh()
                }
            })])
        }
    };
    this.getPolicy = function(view) {
        var ratio = view.bases / view.pixels;
        handler.canvasA.setContainer(null);
        handler.canvasB.setContainer(null);
        handler = (ratio < 10) ? Reads: Histogram;
        handler.canvasA.setContainer(containerA.dom);
        handler.canvasB.setContainer(containerB.dom);
        for (var i = 0; i < policies.length; i++) {
            if (ratio >= policies[i].min && ratio < policies[i].max) {
                return policies[i]
            }
        }
        return null
    };
    this.rescale = function(f) {
        var f = (handler == Histogram) ? Math.pow(f * 2, 4) : f;
        handler.canvasA.setScaler(f);
        handler.canvasB.setScaler(f);
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    };
    this.l_rescale = function(f) {
        handler.canvasA.setScaler(f);
        handler.canvasB.setScaler(f);
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    };
    this.clearCanvas = function() {
        handler.canvasA.clear();
        handler.canvasB.clear()
    };
    this.paintCanvas = function(l, r, b, p) {
        handler.paint(l, r, b, p)
    };
    this.refreshCanvas = function() {
        handler.canvasA.refresh(true);
        handler.canvasB.refresh(true)
    };
    this.resizeCanvas = function() {
        handler.canvasA.refresh(true);
        handler.canvasB.refresh(true)
    };
    this.clearData = function() {
        handler.dataA.clear();
        handler.dataB.clear()
    };
    this.pruneData = function(a, b) {
        handler.dataA.prune(a, b);
        handler.dataB.prune(a, b)
    };
    this.parseData = function(data) {
        handler.parse(data)
    }
};
Ext.extend(AnnoJ.PairedEndTrack, AnnoJ.BrowserTrack);

AnnoJ.SmallReadsTrack = function(userConfig) {
    var self = this;
    AnnoJ.SmallReadsTrack.superclass.constructor.call(self, userConfig);
    var defaultConfig = {
        single: false,
        clsAbove: 'AJ_above',
        clsBelow: 'AJ_below',
        slider: 0.5,
        boxHeight: 10,
        boxHeightMax: 24,
        boxHeightMin: 1,
        boxBlingLimit: 6
    };
    Ext.applyIf(self.config, defaultConfig);
    var containerA = new Ext.Element(document.createElement('DIV'));
    var containerB = new Ext.Element(document.createElement('DIV'));
    containerA.addClass(self.config.clsAbove);
    containerB.addClass(self.config.clsBelow);
    containerA.setStyle('position', 'relative');
    containerB.setStyle('position', 'relative');
    containerA.setStyle('width', '100%');
    containerB.setStyle('width', '100%');
    if (self.config.single) {
        containerA.setStyle('height', '100%');
        containerB.setStyle('display', 'none')
    } else {
        containerA.setStyle('height', '49%');
        containerB.setStyle('height', '49%');
        containerA.setStyle('borderBottom', 'dotted black 1px')
    }
    containerA.appendTo(self.Canvas.ext);
    containerB.appendTo(self.Canvas.ext);
    var menuMultis = new Ext.menu.CheckItem({
        text: 'Show Multi-Mappers',
        handler: toggleMultis
    });
    var menu21mers = new Ext.menu.CheckItem({
        text: '21mers',
        handler: toggleClass
    });
    var menu22mers = new Ext.menu.CheckItem({
        text: '22mers',
        handler: toggleClass
    });
    var menu23mers = new Ext.menu.CheckItem({
        text: '23mers',
        handler: toggleClass
    });
    var menu24mers = new Ext.menu.CheckItem({
        text: '24mers',
        handler: toggleClass
    });
    var menuOthers = new Ext.menu.CheckItem({
        text: 'Others',
        handler: toggleClass
    });
    menuMultis.setChecked(true);
    menu21mers.setChecked(true);
    menu22mers.setChecked(true);
    menu23mers.setChecked(true);
    menu24mers.setChecked(true);
    menuOthers.setChecked(true);
    self.ContextMenu.addItems([menuMultis, menu21mers, menu22mers, menu23mers, menu24mers, menuOthers]);
    var toolbarMultis = new Ext.Toolbar.Button({
        text: 'Multis',
        iconCls: 'silk_bullet_orange',
        tooltip: 'Show or hide read that map to multiple locations',
        handler: toggleMultis
    });
    var toolbar21mers = new Ext.Toolbar.Button({
        text: '21mers',
        iconCls: 'silk_bullet_orange',
        tooltip: 'Show or hide 21mers',
        handler: toggleClass
    });
    var toolbar22mers = new Ext.Toolbar.Button({
        text: '22mers',
        iconCls: 'silk_bullet_orange',
        tooltip: 'Show or hide 22mers',
        handler: toggleClass
    });
    var toolbar23mers = new Ext.Toolbar.Button({
        text: '23mers',
        iconCls: 'silk_bullet_orange',
        tooltip: 'Show or hide 23mers',
        handler: toggleClass
    });
    var toolbar24mers = new Ext.Toolbar.Button({
        text: '24mers',
        iconCls: 'silk_bullet_orange',
        tooltip: 'Show or hide 24mers',
        handler: toggleClass
    });
    var toolbarOthers = new Ext.Toolbar.Button({
        text: 'Others',
        iconCls: 'silk_bullet_orange',
        tooltip: 'Show or hide reads other than 21mers or 24mers',
        handler: toggleClass
    });
    self.Toolbar.insert(4, toolbarMultis);
    self.Toolbar.insert(4, toolbarOthers);
    self.Toolbar.insert(4, toolbar24mers);
    self.Toolbar.insert(4, toolbar23mers);
    self.Toolbar.insert(4, toolbar22mers);
    self.Toolbar.insert(4, toolbar21mers);
    function toggleMultis(item) {
        var show = true;
        if (item.iconCls) {
            show = item.iconCls == 'silk_bullet_white'
        } else {
            show = !item.checked
        }
        toolbarMultis.setIconClass(show ? 'silk_bullet_orange': 'silk_bullet_white');
        if (item.iconCls) menuMultis.setChecked(show);
        handler.canvasA.toggleSpecial('multi_mapper', show);
        handler.canvasB.toggleSpecial('multi_mapper', show);
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    }
    function toggleClass(item) {
        var show = true;
        if (item.iconCls) {
            show = item.iconCls == 'silk_bullet_white'
        } else {
            show = !item.checked
        }
        if (item.text == '21mers') {
            toolbar21mers.setIconClass(show ? 'silk_bullet_orange': 'silk_bullet_white');
            if (item.iconCls) menu21mers.setChecked(show)
        } else if (item.text == '22mers') {
            toolbar22mers.setIconClass(show ? 'silk_bullet_orange': 'silk_bullet_white');
            if (item.iconCls) menu22mers.setChecked(show)
        } else if (item.text == '23mers') {
            toolbar23mers.setIconClass(show ? 'silk_bullet_orange': 'silk_bullet_white');
            if (item.iconCls) menu23mers.setChecked(show)
        } else if (item.text == '24mers') {
            toolbar24mers.setIconClass(show ? 'silk_bullet_orange': 'silk_bullet_white');
            if (item.iconCls) menu24mers.setChecked(show)
        } else if (item.text == 'Others') {
            toolbarOthers.setIconClass(show ? 'silk_bullet_orange': 'silk_bullet_white');
            if (item.iconCls) menuOthers.setChecked(show)
        } else {
            return
        }
        handler.canvasA.toggleSpecial(item.text, show);
        handler.canvasB.toggleSpecial(item.text, show);
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    }
    var Histogram = (function() {
        var dataA = new HistogramData();
        var dataB = new HistogramData();
        function parse(data) {
            for (var series in data) {
                addLabel(series);
                self.Toolbar.addItem(series)
            }
            dataA.parse(data, true);
            dataB.parse(data, false)
        };
        var canvasA = new HistogramCanvas();
        var canvasB = new HistogramCanvas();
        canvasB.flipY();
        function paint(left, right, bases, pixels) {
            var subsetA = dataA.subset2canvas(left, right, bases, pixels);
            var subsetB = dataB.subset2canvas(left, right, bases, pixels);
            canvasA.setData(subsetA);
            canvasB.setData(subsetB);
            var max = Math.max(canvasA.getMax() || 0, canvasB.getMax() || 0);
            AnnoJ.config.maxlist[self.config.id] = Math.ceil(max);
            canvasA.paint();
            canvasB.paint()
        };
        return {
            dataA: dataA,
            dataB: dataB,
            canvasA: canvasA,
            canvasB: canvasB,
            parse: parse,
            paint: paint
        }
    })();
    var Reads = (function() {
        var dataA = new SmallReadsList();
        var dataB = new SmallReadsList();
        function parse(data) {
            for (var series in data) {
                self.Toolbar.addItem(series);
                addLabel(series)
            }
            dataA.parse(data, true);
            dataB.parse(data, false)
        };
        var canvasA = new ReadsCanvas({
            scaler: self.config.slider,
            boxHeight: self.config.boxHeight,
            boxHeightMax: self.config.boxHeightMax,
            boxHeightMin: self.config.boxHeightMin,
            boxBlingLimit: self.config.boxBlingLimit
        });
        var canvasB = new ReadsCanvas({
            scaler: self.config.slider,
            boxHeight: self.config.boxHeight,
            boxHeightMax: self.config.boxHeightMax,
            boxHeightMin: self.config.boxHeightMin,
            boxBlingLimit: self.config.boxBlingLimit
        });
        canvasB.flipY();
        function paint(left, right, bases, pixels) {
            var subsetA = dataA.subset2canvas(left, right, bases, pixels);
            var subsetB = dataB.subset2canvas(left, right, bases, pixels);
            canvasA.setData(subsetA);
            canvasB.setData(subsetB);
            canvasA.paint();
            canvasB.paint()
        };
        return {
            dataA: dataA,
            dataB: dataB,
            canvasA: canvasA,
            canvasB: canvasB,
            parse: parse,
            paint: paint
        }
    })();
    var handler = Histogram;
    var policies = [{
        index: 0,
        min: 1 / 100,
        max: 1 / 1,
        bases: 1,
        pixels: 100,
        cache: 1000
    },
    {
        index: 1,
        min: 1 / 1,
        max: 10 / 1,
        bases: 1,
        pixels: 1,
        cache: 10000
    },
    {
        index: 2,
        min: 10 / 1,
        max: 100 / 1,
        bases: 10,
        pixels: 1,
        cache: 100000
    },
    {
        index: 3,
        min: 100 / 1,
        max: 1000 / 1,
        bases: 100,
        pixels: 1,
        cache: 1000000
    },
    {
        index: 4,
        min: 1000 / 1,
        max: 10000 / 1,
        bases: 1000,
        pixels: 1,
        cache: 10000000
    },
    {
        index: 5,
        min: 10000 / 1,
        max: 200001 / 1,
        bases: 10000,
        pixels: 1,
        cache: 100000000
    }];

    var labels = null;
    function addLabel(name) {
        if (!labels) {
            self.ContextMenu.addItems(['-', 'Series']);
            labels = {}
        }
        if (labels[name] == undefined) {
            labels[name] = true;
            self.ContextMenu.addItems([new Ext.menu.CheckItem({
                text: name,
                checked: true,
                handler: function() {
                    handler.canvasA.groups.toggle(name, !this.checked);
                    handler.canvasB.groups.toggle(name, !this.checked);
                    handler.canvasA.refresh();
                    handler.canvasB.refresh()
                }
            })])
        }
    };
    this.getPolicy = function(view) {
        var ratio = view.bases / view.pixels;
        handler.canvasA.setContainer(null);
        handler.canvasB.setContainer(null);
        handler = (ratio < 10) ? Reads: Histogram;
        handler.canvasA.setContainer(containerA.dom);
        handler.canvasB.setContainer(containerB.dom);
        for (var i = 0; i < policies.length; i++) {
            if (ratio >= policies[i].min && ratio < policies[i].max) {
                return policies[i]
            }
        }
        return null
    };
    this.rescale = function(f) {
        var f = (handler == Histogram) ? Math.pow(f * 2, 4) : f;
        handler.canvasA.setScaler(f);
        handler.canvasB.setScaler(f);
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    };
    this.l_rescale = function(f) {
        handler.canvasA.setScaler(f);
        handler.canvasB.setScaler(f);
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    };
    this.clearCanvas = function() {
        handler.canvasA.clear();
        handler.canvasB.clear()
    };
    this.paintCanvas = function(l, r, b, p) {
        handler.paint(l, r, b, p)
    };
    this.refreshCanvas = function() {
        handler.canvasA.refresh(true);
        handler.canvasB.refresh(true)
    };
    this.resizeCanvas = function() {
        handler.canvasA.refresh(true);
        handler.canvasB.refresh(true)
    };
    this.clearData = function() {
        handler.dataA.clear();
        handler.dataB.clear()
    };
    this.pruneData = function(a, b) {
        handler.dataA.prune(a, b);
        handler.dataB.prune(a, b)
    };
    this.parseData = function(data) {
        handler.parse(data)
    }
};
Ext.extend(AnnoJ.SmallReadsTrack, AnnoJ.BrowserTrack);
AnnoJ.HiCTrack = function(userConfig) {
    var self = this;
    AnnoJ.HiCTrack.superclass.constructor.call(self, userConfig);
    var defaultConfig = {
        single: true,
        clsAbove: 'AJ_above',
        clsBelow: 'AJ_below',
        hic_y : '0', 
        indexy : 0,
        slider: 0.5,
        boxHeight: 10,
        boxHeightMax: 24,
        boxHeightMin: 1,
        boxBlingLimit: 6
    };
    var subsetA = {};
    Ext.applyIf(self.config, defaultConfig);
    var containerA = new Ext.Element(document.createElement('DIV'));
    containerA.addClass(self.config.clsAbove);
    containerA.setStyle('position', 'relative');
    containerA.setStyle('width', '100%');
    containerA.setStyle('height', '100%');
    containerA.appendTo(self.Canvas.ext);
    var Intensity = (function() {
        var canvasA = new HiCInteraction();
        function paint(left, right, bases, pixels) {
            canvasA.setData(subsetA);
            canvasA.paint()
        };
        return {
            canvasA: canvasA,
            paint: paint
        }
    })();
    var handler = Intensity;
    var policies = [{
        index: 0,
        min: 1 / 100,
        max: 1 / 1,
        bases: 1,
        pixels: 100,
        cache: 1000
    },
    {
        index: 1,
        min: 1 / 1,
        max: 10 / 1,
        bases: 1,
        pixels: 1,
        cache: 10000
    },
    {
        index: 2,
        min: 10 / 1,
        max: 100 / 1,
        bases: 10,
        pixels: 1,
        cache: 100000
    },
    {
        index: 3,
        min: 100 / 1,
        max: 1000 / 1,
        bases: 100,
        pixels: 1,
        cache: 1000000
    },
    {
        index: 4,
        min: 1000 / 1,
        max: 10000 / 1,
        bases: 1000,
        pixels: 1,
        cache: 10000000
    },
    {
        index: 5,
        min: 10000 / 1,
        max: 200001 / 1,
        bases: 10000,
        pixels: 1,
        cache: 100000000
    }];
    var labels = null;
    function addLabel(name) {
        if (!labels) {
            self.ContextMenu.addItems(['-', 'Series']);
            labels = {}
        }
        if (labels[name] == undefined) {
            labels[name] = true;
            self.ContextMenu.addItems([new Ext.menu.CheckItem({
                text: name,
                checked: true,
                handler: function() {
                    handler.canvasA.groups.toggle(name, !this.checked);
                    handler.canvasA.refresh()
                }
            })])
        }
    };
    this.getPolicy = function(view) {
        var ratio = view.bases / view.pixels;
        handler.canvasA.setContainer(null);
        handler = Intensity;
        handler.canvasA.setContainer(containerA.dom);
        for (var i = 0; i < policies.length; i++) {
            if (ratio >= policies[i].min && ratio < policies[i].max) {
                return policies[i]
            }
        }
        return null
    };
    this.rescale = function(f) {
        var f = (handler == Intensity) ? Math.pow(f * 2, 4) : f;
        handler.canvasA.setScaler(f);
        handler.canvasA.refresh()
    };
    this.l_rescale = function(f) {
        handler.canvasA.setScaler(f);
        handler.canvasA.refresh()
    };
    this.clearCanvas = function() {
        handler.canvasA.clear()
    };
    this.paintCanvas = function(l, r, b, p) {
        handler.paint(l, r, b, p)
    };
    this.refreshCanvas = function() {
        handler.canvasA.refresh(true)
    };
    this.resizeCanvas = function() {
        handler.canvasA.refresh(true)
    };
    this.clearData = function() {
    };
    this.pruneData = function(a, b) {
    };
    this.getData = function() {
    return subsetA
    };
    this.parseData = function(data) {
    subsetA = data
    }
};
Ext.extend(AnnoJ.HiCTrack, AnnoJ.BrowserTrack);
AnnoJ.IntensityTrack = function(userConfig) {
    var self = this;
    AnnoJ.IntensityTrack.superclass.constructor.call(self, userConfig);
    var defaultConfig = {
        single: false,
        clsAbove: 'AJ_above',
        clsBelow: 'AJ_below',
        slider: 0.5,
        boxHeight: 10,
        boxHeightMax: 24,
        boxHeightMin: 1,
        boxBlingLimit: 6
    };
    Ext.applyIf(self.config, defaultConfig);

    var containerA = new Ext.Element(document.createElement('DIV'));
    var containerB = new Ext.Element(document.createElement('DIV'));
    containerA.addClass(self.config.clsAbove);
    containerB.addClass(self.config.clsBelow);
    containerA.setStyle('position', 'relative');
    containerB.setStyle('position', 'relative');
    containerA.setStyle('width', '100%');
    containerB.setStyle('width', '100%');
    if (self.config.single) {
        containerA.setStyle('height', '100%');
        containerB.setStyle('display', 'none')
    } else {
        containerA.setStyle('height', '49%');
        containerB.setStyle('height', '49%');
        containerA.setStyle('borderBottom', 'dotted black 1px')
    }
    containerA.appendTo(self.Canvas.ext);
    containerB.appendTo(self.Canvas.ext);
    var Histogram = (function() {
        var dataA = new HistogramData();
        var dataB = new HistogramData();
        function parse(data) {
            for (var series in data) {
                self.Toolbar.addItem(series);
                addLabel(series)
            }
            dataA.parse(data, true);
            dataB.parse(data, false);
            if(Ext.isGecko){
              var cnt = dataA.getCount(data);
              if(cnt <= 0) dataA.parse(data, true);
              cnt = dataB.getCount(data);
              if(cnt <= 0) dataB.parse(data, false)
            }
        };
        var canvasA = new HistogramCanvas();
        var canvasB = new HistogramCanvas();
        canvasB.flipY();
        function paint(left, right, bases, pixels) {
            var subsetA = dataA.subset2canvas(left, right, bases, pixels);
            var subsetB = dataB.subset2canvas(left, right, bases, pixels);
            canvasA.setData(subsetA);
            canvasB.setData(subsetB);
            var max = Math.max(canvasA.getMax() || 0, canvasB.getMax() || 0);
            AnnoJ.config.maxlist[self.config.id] = Math.ceil(max);
            canvasA.paint();
            canvasB.paint()
        };
        return {
            dataA: dataA,
            dataB: dataB,
            canvasA: canvasA,
            canvasB: canvasB,
            parse: parse,
            paint: paint
        }
    })();
    var handler = Histogram;
    var policies = [{
        index: 0,
        min: 1 / 100,
        max: 1 / 1,
        bases: 1,
        pixels: 10,
        cache: 1000
    },
    {
        index: 1,
        min: 1 / 1,
        max: 10 / 1,
        bases: 1,
        pixels: 1,
        cache: 10000
    },
    {
        index: 2,
        min: 10 / 1,
        max: 100 / 1,
        bases: 10,
        pixels: 1,
        cache: 100000
    },
    {
        index: 3,
        min: 100 / 1,
        max: 1000 / 1,
        bases: 100,
        pixels: 1,
        cache: 1000000
    },
    {
        index: 4,
        min: 1000 / 1,
        max: 10000 / 1,
        bases: 1000,
        pixels: 1,
        cache: 10000000
    },
    {
        index: 5,
        min: 10000 / 1,
        max: 200001 / 1,
        bases: 10000,
        pixels: 1,
        cache: 100000000
    }];

    var labels = null;
    function addLabel(name) {
        if (!labels) {
            self.ContextMenu.addItems(['-', 'Series']);
            labels = {}
        }
        if (labels[name] == undefined) {
            labels[name] = true;
            self.ContextMenu.addItems([new Ext.menu.CheckItem({
                text: name,
                checked: true,
                handler: function() {
                    handler.canvasA.groups.toggle(name, !this.checked);
                    handler.canvasB.groups.toggle(name, !this.checked);
                    handler.canvasA.refresh();
                    handler.canvasB.refresh()
                }
            })])
        }
    };
    this.getPolicy = function(view) {
        var ratio = view.bases / view.pixels;
        handler.canvasA.setContainer(null);
        handler.canvasB.setContainer(null);
        handler = Histogram;
        handler.canvasA.setContainer(containerA.dom);
        handler.canvasB.setContainer(containerB.dom);
        for (var i = 0; i < policies.length; i++) {
            if (ratio >= policies[i].min && ratio < policies[i].max) {
                return policies[i]
            }
        }
        return null
    };
    this.rescale = function(f) {
        var f = (handler == Histogram) ? Math.pow(f * 2, 4) : f;
        handler.canvasA.setScaler(f);
        handler.canvasB.setScaler(f);
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    };
    this.l_rescale = function(f) {
        handler.canvasA.setScaler(f);
        handler.canvasB.setScaler(f);
        handler.canvasA.refresh();
        handler.canvasB.refresh()
    };
    this.clearCanvas = function() {
        handler.canvasA.clear();
        handler.canvasB.clear()
    };
    this.paintCanvas = function(l, r, b, p) {
        handler.paint(l, r, b, p)
    };
    this.refreshCanvas = function() {
        handler.canvasA.refresh(true);
        handler.canvasB.refresh(true)
    };
    this.resizeCanvas = function() {
        handler.canvasA.refresh(true);
        handler.canvasB.refresh(true)
    };
    this.clearData = function() {
        handler.dataA.clear();
        handler.dataB.clear()
    };
    this.pruneData = function(a, b) {
        handler.dataA.prune(a, b);
        handler.dataB.prune(a, b)
    };
    this.parseData = function(data) {
        handler.parse(data)
    }
};
Ext.extend(AnnoJ.IntensityTrack, AnnoJ.BrowserTrack);
