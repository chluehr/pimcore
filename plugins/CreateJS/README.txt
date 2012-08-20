------------------------
-- Plugin information --
------------------------

// TESTING! This plugin is far from being finished!

// put this on the head:
    <?php
        $createjs = new CreateJS();
        $createjs->setView($this);
        echo $createjs->getBoilerplate();
    ?>

// this to make stuff editable:
    <div xmlns:sioc="http://rdfs.org/sioc/ns#" xmlns:dcterms="http://purl.org/dc/terms/"
        about="http://pimcore/document/1" rel="dcterms:hasPart" rev="dcterms:partOf" >
	<h1 property="dcterms:title">Hello World!</h1>
	<p property="dcterms:content">
		This is just a simple example page.
		<br />
		To learn how to create templates with pimcore, please visit our <a href="http://www.pimcore.org/wiki/" target="_blank">documentation</a> or install the example data package.
	</p>
    </div>

