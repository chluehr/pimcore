------------------------
-- Plugin information --
------------------------

// TESTING! This plugin is far from being finished!

// 1) modify your Pimcore controller(s) to register the new CreateJS output
//    plugin and unregister the default Pimcore editmode plugin:

class DefaultController extends Website_Controller_Action {

	public function defaultAction () {

        $front = Zend_Controller_Front::getInstance();
        $front->registerPlugin(
            new CreateJS_Controller_Plugin(), 1000
        );
        $front->unregisterPlugin(
            'Pimcore_Controller_Plugin_Frontend_Editmode'
        );
	}
}

// 3) change your templates like this to make stuff editable:

<!-- // add namespaces to body(!): -->
<body xmlns:dcterms="http://purl.org/dc/terms/">

    <!-- // add document about link -->
    <div about="@document">
        <h1 property="dcterms:title">sample head</h1>
        <p property="dcterms:content">
            sample content
        </p>
    </div>

// NOTES:

- only simple string fields for now
- pimcore preview works only after save & publish
- save & publish is still needed after createjs save!
- what about nested about sections?