<?php

class ElasticSearch_Indexer
{

    private $db;

    /**
     *
     */
    public function __construct()
    {

        $db = Pimcore_Resource_Mysql::get();
        $this->db = $db;
        //$db->query("DROP TABLE IF EXISTS `plugin_searchphp_contents_temp`;");
    }

    /**
     * @param  string[] $urls
     * @return void
     */
    public function findLinks($urls)
    {

        /*
        $manager = Schedule_Manager_Factory::getManager("searchphpcrawlermanager.pid");
        for ($i = 1; $i <= $this->maxThreads; $i++) {
            $manager->registerJob(new Schedule_Maintenance_Job("crawler-" . $i, $this, "continueWithFoundLinks", array()));
        }
        $manager->registerJob(new Schedule_Maintenance_Job("crawler-indexer", $this, "doIndex", array()));
        $manager->run();
        */
    }

    /**
     *
     */
    public function doIndex()
    {

        $elasticaClient = new Elastica_Client(
            array(
                'servers' => array(
                    array(
                        'host' => 'localhost',
                        'port' => 9200
                    )
                )
            )
        );

        $elasticaIndex = $elasticaClient->getIndex('pimcore');

        // Create the index new
        $elasticaIndex->create(array(
            'number_of_shards' => 1,
            'number_of_replicas' => 0,
            'analysis' => array(
                'analyzer' => array(
                    'indexAnalyzer' => array(
                        'type' => 'custom',
                        'tokenizer' => 'standard',
                        'filter' => array('lowercase' )
                    ),
                    'searchAnalyzer' => array(
                        'type' => 'custom',
                        'tokenizer' => 'standard',
                        'filter' => array('standard', 'lowercase',)
                    )
                )
            )
        ), true);

        $elasticaType = $elasticaIndex->getType('tweet');

        // Define mapping
        $mapping = new Elastica_Type_Mapping();
        $mapping->setType($elasticaType);
        $mapping->setParam('index_analyzer', 'indexAnalyzer');
        $mapping->setParam('search_analyzer', 'searchAnalyzer');

        // Define boost field
        $mapping->setParam('_boost', array('name' => '_boost', 'null_value' => 1.0));

        // Set mapping
        $mapping->setProperties(array(
            'id'      => array('type' => 'integer', 'include_in_all' => FALSE),
            'user'    => array(
                'type' => 'object',
                'properties' => array(
                    'name'      => array('type' => 'string', 'include_in_all' => TRUE),
                    'fullName'  => array('type' => 'string', 'include_in_all' => TRUE)
                ),
            ),
            'msg'     => array('type' => 'string', 'include_in_all' => TRUE),
            'tstamp'  => array('type' => 'date', 'include_in_all' => FALSE),
            'location'=> array('type' => 'geo_point', 'include_in_all' => FALSE),
            '_boost'  => array('type' => 'float', 'include_in_all' => FALSE)
        ));

        // Send mapping to type
        $mapping->send();

        // The Id of the document
        $id = 1;

        // Create a document
        $tweet = array(
            'id'      => $id,
            'user'    => array(
                'name'      => 'mewantcookie',
                'fullName'  => 'Cookie Monster'
            ),
            'msg'     => 'Me wish there were expression for cookies like there is for apples. "A cookie a day make the doctor diagnose you with diabetes" not catchy.',
            'tstamp'  => '1238081389',
            'location'=> '41.12,-71.34',
            '_boost'  => 1.0
        );
        $tweetDocument = new Elastica_Document($id, $tweet);

        // Add tweet to type
        $elasticaType->addDocument($tweetDocument);

        // Refresh Index
        $elasticaType->getIndex()->refresh();

        echo "done\n";
    }

    /**
     *
     */
    public function doSearch()
    {

        $elasticaClient = new Elastica_Client(
            array(
                'servers' => array(
                    array(
                        'host' => 'localhost',
                        'port' => 9200
                    )
                )
            )
        );

        $elasticaIndex = $elasticaClient->getIndex('pimcore');

        $elasticaType = $elasticaIndex->getType('tweet');

        // Define mapping
        $mapping = new Elastica_Type_Mapping();
        $mapping->setType($elasticaType);
        $mapping->setParam('index_analyzer', 'indexAnalyzer');
        $mapping->setParam('search_analyzer', 'searchAnalyzer');

        // Define boost field
        $mapping->setParam('_boost', array('name' => '_boost', 'null_value' => 1.0));

        // Set mapping
        $mapping->setProperties(array(
            'id'      => array('type' => 'integer', 'include_in_all' => FALSE),
            'user'    => array(
                'type' => 'object',
                'properties' => array(
                    'name'      => array('type' => 'string', 'include_in_all' => TRUE),
                    'fullName'  => array('type' => 'string', 'include_in_all' => TRUE)
                ),
            ),
            'msg'     => array('type' => 'string', 'include_in_all' => TRUE),
            'tstamp'  => array('type' => 'date', 'include_in_all' => FALSE),
            'location'=> array('type' => 'geo_point', 'include_in_all' => FALSE),
            '_boost'  => array('type' => 'float', 'include_in_all' => FALSE)
        ));

        // Send mapping to type
        $mapping->send();


        $elasticaQueryString 	= new Elastica_Query_QueryString();
        $elasticaQueryString->setDefaultOperator('AND');
        $elasticaQueryString->setQuery('cookies');

        // Create the actual search object with some data.
        $elasticaQuery 		= new Elastica_Query();
        $elasticaQuery->setQuery($elasticaQueryString);
        //$elasticaQuery->setFrom(1);
        $elasticaQuery->setLimit(4);

        //Search on the index.
        $elasticaResultSet 	= $elasticaIndex->search($elasticaQuery);

        $elasticaResults 	= $elasticaResultSet->getResults();
        $totalResults 		= $elasticaResultSet->getTotalHits();

        foreach ($elasticaResults as $elasticaResult) {
            var_dump($elasticaResult->getData());
        }
    }

}
