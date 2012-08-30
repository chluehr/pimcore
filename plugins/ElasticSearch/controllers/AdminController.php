<?php
/**
 * Pimcore
 *
 * LICENSE
 *
 * This source file is subject to the new BSD license that is bundled
 * with this package in the file LICENSE.txt.
 * It is also available through the world-wide-web at this URL:
 * http://www.pimcore.org/license
 *
 * @copyright  Copyright (c) 2009-2010 elements.at New Media Solutions GmbH (http://www.elements.at)
 * @license    http://www.pimcore.org/license     New BSD License
 */

class ElasticSearch_AdminController extends Pimcore_Controller_Action_Admin {

    protected $config;

    public function init() {
        parent::init();
    }

    public function settingsAction() {

        $this->view->config = $this->config;
    }

    public function startIndexerAction() {

        $es = new ElasticSearch();

        for ($t=1;$t<=10;$t++) {

            // Create a document
            $document = array(
                'id'      => $t,
                'title'     => "Foo bar # $t baz",
                'text'     => 'Me wish there were expression for cookies like there is for apples. "A cookie a day make the doctor diagnose you with diabetes" not catchy.',
            );

            // Add doc to type
            $es->addDocument($document, 'document');
        }

        $this->_helper->json(array("success" => true));
    }

    public function createIndexAction() {

        $es = new ElasticSearch();
        $es->createIndex();

        $this->_helper->json(array("success" => true));
    }

}