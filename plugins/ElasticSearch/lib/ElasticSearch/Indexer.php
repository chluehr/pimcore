<?php

class ElasticSearch_Indexer
{

    private $db;

    private $elasticSearch;


    /**
     *
     */
    public function __construct()
    {

        $db = Pimcore_Resource_Mysql::get();
        $this->db = $db;

        $this->elasticSearch = new ElasticSearch();

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
    public function createIndex()
    {
        $this->elasticSearch->createIndex();
    }

    /**
     *
     */
    public function doIndex()
    {

        // Create a document
        $document = array(
            'id'      => 1,
            'title'     => 'Foo bar baz',
            'text'     => 'Me wish there were expression for cookies like there is for apples. "A cookie a day make the doctor diagnose you with diabetes" not catchy.',
        );

        // Add doc to type
        $this->elasticSearch->addDocument($document, 'document');

        echo "done\n";
    }

}
