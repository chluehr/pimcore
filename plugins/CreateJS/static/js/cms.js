pimcore.registerNS("pimcore.plugin.packManager");

pimcore.plugin.packManager = Class.create(pimcore.plugin.admin, {

    /* These values needs to match the values in AdminController.php! */
    PAGE_SIZE: 10,
    ITEM_STATE_PENDING: 0,
    ITEM_STATE_ACCEPTED: 1,
    ITEM_STATE_GALLERY: 2,
    ITEM_STATE_UNCERTAIN: 3,
    ITEM_STATE_REJECTED: 4,
    ITEM_STATE_ALERTED: 10,

    /**
     * global store for all data
     */
    store: null,
    dataview: null,

    getClassName: function() {
        return "pimcore.plugin.packManager";
    },

    initialize: function() {
        pimcore.plugin.broker.registerPlugin(this);
    },

    /**
     * Displays sub menu item in "Extras".
     *
     * @param params
     * @param broker
     */
    pimcoreReady: function (params,broker){
        var user = pimcore.globalmanager.get("user");

        if (user.isAllowed("plugin_pack_manager")) {
            // Get the global toolbar.
            var toolbar = Ext.getCmp("pimcore_panel_toolbar");
            // Create new menu action for the pack manager.
            var action = new Ext.Action({
                id: "pack_manager_menu_item",
                text: "Pack Manager",
                iconCls:"pimcore_icon_seo",
                handler: this.showTab
            });
            // add a sub-menu item under "Extras" in the main menu
            toolbar.items.items[1].menu.add(action);
        }
    },

    /**
     * Shows tab and activates it.
     */
    showTab: function() {

        // var grid = packManager.getGrid();

        var grid = packManager.getDataView();

         this.panel = new Ext.Panel({
            id:         "pack_manager_check_panel",
            title:      "Pack Manager",
            iconCls:    "pimcore_icon_seo",
            border:     false,
            layout:     "border",
            closable:   true,
            items:      [grid]

        });

        /**
         * Adds manager tab panel to the global tab bar
         */
        var tabPanel = Ext.getCmp("pimcore_panel_tabs");
        tabPanel.add(this.panel);
        tabPanel.activate("pack_manager_check_panel");

        pimcore.layout.refresh();
    },

    getDataView: function() {

        this.store = new Ext.data.JsonStore({
            url: '/plugin/CreateJS/admin/dataview',
            totalProperty: 'totalCount',
            idProperty: 'id',
            remoteSort: true,
            root: 'packs',
            fields: [
                'id','url','key','viewCount', 'message', 'filter'
            ]
        });
        this.store.load();

        var tpl = new Ext.XTemplate(
            '<tpl for=".">',
            '<div class="thumb-wrap" rel="{id}">',
                '<div class="thumb f{filter}">' +
                    '<img src="{url}" title="{url}">' +
                    '<div>ID: {id} / Views: {viewCount}</div>',
                    '<div class="message">{message}</div>',
                '</div>',
            '</div>',
            '</tpl>',
            '<div class="x-clear"></div>'
        );

        this.dataview = new Ext.DataView({
            id: 'images-view',
            autoScroll: true,
            //region: 'center',
            store: this.store,
            tpl: tpl,
            autoHeight: true,
            autoWidth: true,
            emptyText: '<div style="padding: 100px;">No data available.</div>',
            //style: 'border:1px solid #99BBE8; border-top-width: 0',
            overItemCls: 'x-item-over',
            itemSelector: 'div.thumb-wrap',
            multiSelect: true,
            simpleSelect:true,

            plugins: [
                //new Ext.DataView.DragSelector()
            ],

            prepareData: function(data){
                //data.shortName = Ext.util.Format.ellipsis(data.name, 15);
                return data;
            },

            listeners: {
                selectionchange: {
                    fn: function(dv,nodes){
                        var l = nodes.length;
                        var s = l != 1 ? 's' : '';
                        //panelLeft.setTitle('Simple DataView Gallery ('+l+' image'+s+' selected)');
                    }
                },
                click: {
                    fn: function() {
                        var selNode = packManager.dataview.getSelectedRecords();
                        //tplDetail.overwrite(panelRightBottom.body, selNode[0].data);
                    }
                }
            }
        });

        var toptoolbar = new Ext.Toolbar({
            items:[
                // select all
                {
                    id: "panel_selectall_button",
                    text: 'all',
                    iconCls: 'icon_selall',
                    handler: function(btn, pressed){
                        packManager.dataview.selectRange(0);
                    }
                },
                {
                    id: "panel_selectnone_button",
                    text: 'none',
                    iconCls: 'icon_selnone',
                    handler: function(btn, pressed){
                        packManager.dataview.clearSelections(true);
                    }
                },
                '-',
                // Gallery-Button
                {
                    id: "panel_gallery_button",
                    text: 'gallery',
                    iconCls: 'icon_award',
                    handler: function(btn, pressed){
                        packManager.updateStatusOfSelectedPacks(packManager.ITEM_STATE_GALLERY);
                    }
                },
                {
                    id: "panel_accept_button",
                    text: 'accept',
                    iconCls: 'pimcore_icon_add',
                    handler: function(btn, pressed){
                        packManager.updateStatusOfSelectedPacks(packManager.ITEM_STATE_ACCEPTED);
                    }
                },
                // Publishing-Button
                {
                    id: "panel_uncertain_button",
                    text: 'uncertain',
                    iconCls: 'icon_exclamation',
                    handler: function(btn, pressed){
                        packManager.updateStatusOfSelectedPacks(packManager.ITEM_STATE_UNCERTAIN);
                    }
                },
                '-',
                // Reject-Button
                {
                    id: "panel_reject_button",
                    text: 'reject',
                    iconCls: 'pimcore_icon_delete',
                    handler: function(btn, pressed){
                        packManager.updateStatusOfSelectedPacks(packManager.ITEM_STATE_REJECTED);

                    }
                },
                // schiebt die Filterbox nach rechts.
                '->',
                {
                    id: "listFilter",
                    xtype: "combo",
                    allowBlank: false,
                    editable: false,
                    triggerAction: 'all',
                    typeAhead: false,
                    disableKeyFilter: true,
                    mode: 'local',
                    store: new Ext.data.ArrayStore({
                        id: 0,
                        fields: [
                            'value',
                            'displayText'
                        ],
                        data: [
                            [packManager.ITEM_STATE_ALERTED, 'Alerts'],
                            [packManager.ITEM_STATE_PENDING, 'Pending'],
                            [packManager.ITEM_STATE_GALLERY, 'Gallery'],
                            [packManager.ITEM_STATE_ACCEPTED, 'Accepted'],
                            [packManager.ITEM_STATE_UNCERTAIN, 'Uncertain'],
                            [packManager.ITEM_STATE_REJECTED, 'Rejected']
                        ]
                    }),
                    valueField: 'value',
                    displayField: 'displayText',
                    value: packManager.ITEM_STATE_ALERTED,
                    listeners:{
                        scope: this,
                        'select': function(btn, pressed){
                            // Load the new list on first page.
                            packManager.store.setBaseParam("listFilter", this.getFilterValue());
                            Ext.getCmp("tabPagingToolbar").moveFirst();
                        }
                    }
                }
            ]
        });

        var pagingbar = new Ext.PagingToolbar({
            id: "tabPagingToolbar",
            pageSize: packManager.PAGE_SIZE,
            store: this.store,
            displayInfo: true,
            displayMsg: 'Displaying entries {0} - {1} of {2}',
            emptyMsg: "No entries to display"
        });

        var panelMain = new Ext.Panel({
            id: 'images-view',
            frame: true,
            autoHeight: true,
            layout: 'auto',
            region: 'center',

            items: [toptoolbar,this.dataview,pagingbar]
        });

        return panelMain;

    },

    reloadPanel: function(){
        // on succcess:
        var filter = this.getFilterValue();
        // set filter selection

        if (filter != '') {
            packManager.store.setBaseParam("listFilter", filter);
        }
        else {
            packManager.store.setBaseParam("listFilter", packManager.ITEM_STATE_ALERTED);
        }

        packManager.store.reload();
        Ext.getCmp('packGrid').render();
    },

    getFilterValue: function(){
        return Ext.getCmp("listFilter").value;
    },

    updateStatusOfSelectedPacks: function(status) {

        var idList = packManager.getSelectedListItems();

        $.ajax({
            url: "/plugin/CreateJS/admin/updateStatus",
            async: true,
            data: {
                status: status,
                idList: idList,
                filter: this.getFilterValue()
            },
            type: "post",
            success: function(data){
                if (data != undefined && data.success != undefined && data.success == true) {
                    packManager.reloadPanel();
                }
            },
            fail: function(data) {
                alert('API Call error [E892984845]');
            }
        })
    },

    getSelectedListItems: function() {

        var idList = [];

        var nodes = packManager.dataview.getSelectedNodes();

        Ext.each(nodes, function(item){
            idList[idList.length] = item.getAttribute('rel');
        });

        return idList;
    }
});

var packManager = new pimcore.plugin.packManager();
