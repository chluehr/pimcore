<!DOCTYPE html PUBLIC "-//W3C//DTD XHTML 1.0 Transitional//EN"
        "http://www.w3.org/TR/xhtml1/DTD/xhtml1-transitional.dtd">
<html xmlns="http://www.w3.org/1999/xhtml">
<head>
<meta http-equiv="Content-Type" content="text/html; charset=utf-8"/>
<title>Pimcore ElasticSearch Plugin :: Settings</title>

<link href="/pimcore/static/js/lib/ext/resources/css/ext-all.css" media="screen" rel="Stylesheet" type="text/css"/>
<?php
   $conf = Zend_Registry::get("pimcore_config_system");

$themeUrl = "/pimcore/static/js/lib/ext/resources/css/xtheme-blue.css";
if ($conf->general->theme) {
    $themeUrl = $conf->general->theme;
}

?>
<link href="<?php echo  $themeUrl ?>" media="screen" rel="Stylesheet" type="text/css"/>
<script src="/pimcore/static/js/lib/ext/adapter/ext/ext-base.js" type="text/javascript"></script>

<script src="/pimcore/static/js/lib/ext/ext-all-debug.js" type="text/javascript"></script>

<script type="text/javascript" src="/pimcore/static/js/lib/ext-plugins/SuperBoxSelect/SuperBoxSelect.js"></script>

<link href="/pimcore/static/js/lib/ext-plugins/SuperBoxSelect/superboxselect.css" media="screen" rel="Stylesheet"
      type="text/css"/>
<link href="/plugins/SearchPhp/static/css/admin.css" media="screen" rel="Stylesheet" type="text/css"/>


<script type="text/javascript">




Ext.onReady(function() {

    Ext.QuickTips.init();

    var form1 = new Ext.form.FormPanel({
        id:'f1Form',
        renderTo: 'elasticsearch_form',
        autoScroll: true,
        border:false,
        width: 600,
        autoHeight:true ,
        items:[
            {   xtype: 'buttongroup',
                fieldLabel: 'Indexer',
                hideLabel: false,
                hidden: false,
                columns:2,
                bodyBorder:false,
                border: false,
                frame:false,  
                items: [
                    {
                        xtype:'button',
                        hideLabel: true,
                        text: 'Create Index',
                        id: 'createIndex',
                        disabled: false,
                        listeners: {
                            click: function(button, event) {
                                Ext.Ajax.request({
                                    url: "/plugin/ElasticSearch/admin/create-index",
                                    method: "get"
                                });
                            }
                        }
                    },
                    {
                        xtype:'button',
                        hideLabel: true,
                        text: 'Start Indexer',
                        id: 'startIndexer',
                        disabled: false,
                        listeners: {
                            click: function(button, event) {
                                Ext.Ajax.request({
                                    url: "/plugin/ElasticSearch/admin/start-indexer",
                                    method: "get"
                                });
                            }
                        }
                    }
                ]
            }
        ]
    });
});


</script>

</head>
<body>
<div id="page">
    <div id="elasticsearch_form" class="exForm"></div>
</div>
</body>
</html>

