<?php

class ElasticSearch
{

    private $elasticaClient;

    private $elasticaIndex;

    public function __construct()
    {
        $this->elasticaClient = $this->getElasticaClient();
        $this->elasticaIndex = $this->elasticaClient->getIndex('pimcore');
    }

    /**
     * @return Elastica_Client
     */
    private function getElasticaClient()
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
        return $elasticaClient;
    }


    public function search($terms, $limit=10, $start=0)
    {

        $elasticaQueryString 	= new Elastica_Query_QueryString();
        $elasticaQueryString->setDefaultOperator('AND');
        $elasticaQueryString->setQuery($terms);

        // Create the actual search object with some data.
        $elasticaQuery 		= new Elastica_Query();
        $elasticaQuery->setQuery($elasticaQueryString);
        $elasticaQuery->setFrom($start);
        $elasticaQuery->setLimit($limit);

        //Search on the index.
        $elasticaResultSet 	= $this->elasticaIndex->search($elasticaQuery);

        $elasticaResults 	= $elasticaResultSet->getResults();
        $totalResults 		= $elasticaResultSet->getTotalHits();

        $resultArray = array();

        foreach ($elasticaResults as $elasticaResult) {
            $resultArray[] = $elasticaResult->getData();
        }

        return $resultArray;
    }

    public function addDocument($document, $type)
    {
        $elasticaType = $this->elasticaIndex->getType($type);

        $elasticaDocument = new Elastica_Document($document['id'], $document);

        // Add doc to type
        $elasticaType->addDocument($elasticaDocument);

        // Refresh Index
        $elasticaType->getIndex()->refresh();

    }

    /**
     *
     */
    public function createIndex()
    {
        // Create the index new
        $this->elasticaIndex->create(array(
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

        $elasticaType = $this->elasticaIndex->getType('document');

        // Define mapping
        $mapping = new Elastica_Type_Mapping();
        $mapping->setType($elasticaType);
        $mapping->setParam('index_analyzer', 'indexAnalyzer');
        $mapping->setParam('search_analyzer', 'searchAnalyzer');

        // Define boost field
        //$mapping->setParam('_boost', array('name' => '_boost', 'null_value' => 1.0));

        // Set mapping
        $mapping->setProperties(array(
            'id'      => array('type' => 'integer', 'include_in_all' => FALSE),
            'title'   => array('type' => 'string', 'include_in_all' => TRUE),
            'text'    => array('type' => 'string', 'include_in_all' => TRUE),
        ));

        // Send mapping to type
        $mapping->send();

    }

}
