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

// this to make stuff editable:

    <?php echo $createjs->getDocumentContainerDiv() ?>
    <?php echo $createjs->getTag('h1', "dcterms:title"); ?>
    <?php echo $createjs->getTag('p', "dcterms:content"); ?>

