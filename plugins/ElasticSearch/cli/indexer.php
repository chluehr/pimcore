<?php
        include_once("../../../pimcore/config/startup.php");
Pimcore::initAutoloader();
Pimcore::initConfiguration();
Pimcore::initLogger();
Pimcore::initPlugins();

ini_set('memory_limit', '2048M');
ini_set("max_execution_time", "-1");


logger::log("ElasticSearch_Plugin: Starting crawl", Zend_Log::DEBUG);

$indexer = new ElasticSearch_Indexer();
//$indexer->doIndex();
$indexer->doSearch();

logger::log("ElasticSearch_Plugin: Finished crawl", Zend_Log::DEBUG);

       
