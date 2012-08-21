<?php
/**
 * based on @see Pimcore_Controller_Plugin_CssMinify
 */
class CreateJS_Controller_Plugin extends Zend_Controller_Plugin_Abstract {

    public function dispatchLoopShutdown() {

        if(!Pimcore_Tool::isHtmlResponse($this->getResponse())) {
            return;
        }

        include_once("simple_html_dom.php");

        $body = $this->getResponse()->getBody();

        $html = str_get_html($body);

        foreach($html->find('[property]') as $element) {
            $element->innertext = "contents!";
        }

        $body = $html->save();
        $this->getResponse()->setBody($body);

    }
}

