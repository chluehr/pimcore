<?php
/**
 * based on @see Pimcore_Controller_Plugin_CssMinify
 */
class CreateJS_Controller_Plugin extends Zend_Controller_Plugin_Abstract {

    /**
     * @var array
     */
    private $namespaces = array();

    /**
     * @return string
     */
    private function getBoilerplate()
    {

        $data = <<<EOF
        <script src="/plugins/CreateJS/static/js/jquery-1.7.1.min.js"></script>
        <script src="/plugins/CreateJS/static/js/jquery-ui-1.8.18.custom.min.js"></script>
        <script src="/plugins/CreateJS/static/js/modernizr.custom.80485.js"></script>
        <script src="/plugins/CreateJS/static/js/underscore-min.js"></script>
        <script src="/plugins/CreateJS/static/js/backbone-min.js"></script>
        <script src="/plugins/CreateJS/static/js/vie-min.js"></script>
        <script src="/plugins/CreateJS/static/js/jquery.rdfquery.min.js"></script>
        <script src="/plugins/CreateJS/static/js/annotate-min.js"></script>
        <script src="/plugins/CreateJS/static/js/create.js"></script>
        <script src="/plugins/CreateJS/static/js/hallo.js"></script>
        <script src="/plugins/CreateJS/static/js/create-hallo.js"></script>

        <link rel="stylesheet" href="/plugins/CreateJS/static/css/create-ui.css" />
        <link rel="stylesheet" href="/plugins/CreateJS/static/css/midgardnotif.css" />
EOF;
        return $data;
    }

    /**
     *
     */
    public function dispatchLoopShutdown() {

        if(!Pimcore_Tool::isHtmlResponse($this->getResponse())) {
            return;
        }

        $view = Zend_Controller_Action_HelperBroker::getExistingHelper('ViewRenderer')->view;
        $document =  $view->document;

        include_once("simple_html_dom.php");

        $body = $this->getResponse()->getBody();

        $html = str_get_html($body);

        if ($view->editmode) {

            $headTag = $html->find('head', 0);
            $headTag->innertext .= $this->getBoilerplate();
        }

        $bodyTag = $html->find('body', 0);
        foreach ($bodyTag->attr as $attrKey => $attrValue) {

            if (preg_match('/^xmlns:(.+)$/', $attrKey, $match)) {

                $this->namespaces[$match[1]] = $attrValue;
            }
        }

        foreach ($html->find('[about]') as $element) {

            if ($element->about == '@document') {
                $element->about = 'http://pimcore/document/' .$document->getId();
            }
        }

        foreach ($html->find('[property]') as $element) {

            $propertyName = $element->property;
            list($namespaceKey, $propertyKey) = explode(':', $propertyName);
            if (array_key_exists($namespaceKey, $this->namespaces)) {
                $propertyName = $this->namespaces[$namespaceKey] . $propertyKey;
            }

            $innerText = '[['.$propertyName.']]';
            $documentElement = $document->getElement('<'.$propertyName.'>');
            if (is_object($documentElement)) {
                $innerText = $documentElement->getValue();
            }
            $element->innertext = $innerText;
        }

        $body = $html->save();
        $this->getResponse()->setBody($body);

    }
}

