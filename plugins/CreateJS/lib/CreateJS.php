<?php

class CreateJS
{
    /**
     * @var Pimcore_View
     */
    private $view;

    /**
     * @var array
     */
    private $namespaces = array();

    /**
     * @param $key string
     * @param $url string
     */
    public function addXmlNamespace($key, $url)
    {
        $this->namespaces[$key] = $url;
    }

    public function getDocumentContainerDiv()
    {
        $namespaceElementList = array();

        foreach ($this->namespaces as $namespaceKey => $namespaceUrl) {

            $namespaceElementList[] =
                'xmlns:' . $namespaceKey . '="' . $namespaceUrl . '"';
        }

        $containerDiv =
            '<div '
            . implode(' ', $namespaceElementList)
            . ' about="'.$this->getDocumentAbout() . '"'
            . '>';

        return $containerDiv;
    }

    /**
     * @return string
     */
    public function getDocumentAbout()
    {
        return "http://pimcore/document/1";
    }

    public function getTag($tag, $propertyKey)
    {

        /** @var $doc Document_Page */
        $doc = $this->view->document;

        list($dummy, $key) = explode(':',$propertyKey);

        return
            '<' . $tag . ' property="' . $propertyKey . '">'
            . $doc->getElement($key)->getValue()
            .'</'.$tag.'>';
    }

    /**
     * @return string
     */
    public function getBoilerplate()
    {

        $data = '';

        if ($this->getView()->editmode) {

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
        }
        return $data;
    }

    /**
     * @return \Pimcore_View
     */
    public function getView()
    {
        return $this->view;
    }

    /**
     * @param \Pimcore_View $view
     */
    public function setView($view)
    {
        $this->view = $view;
    }
}