------------------------
-- Plugin information --
------------------------

// TESTING! This plugin is far from being finished!

// put this on the head:
    <?php
        $createjs = new CreateJS();
        $createjs->setView($this);
        $createjs->addXmlNamespace('sioc', "http://rdfs.org/sioc/ns#");
        $createjs->addXmlNamespace('dcterms', "http://purl.org/dc/terms/");

        echo $createjs->getBoilerplate();
    ?>


class DefaultController extends Website_Controller_Action {

	public function defaultAction () {

        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(
            new CreateJS_Controller_Plugin(), 700
        );
	}
}

// this to make stuff editable:

    <div xmlns:sioc="http://rdfs.org/sioc/ns#" xmlns:dcterms="http://purl.org/dc/terms/" about="http://pimcore/document/1">
        <h1 property="dcterms:title">sample head</h1>
        <p property="dcterms:content">
            sample content
        </p>
    </div>
