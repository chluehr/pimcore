<?php

class CreateJS_AdminController extends Pimcore_Controller_Action_Admin
{
    /**
     * Updates state of single items.
     *
     * @return array
     */
    public function syncAction()
    {
        $request = $this->getRequest();

        $model = $request->getParam('model');

        $subject = $model['@subject'];
        $type = $model['@type'];
        $title = $model['<http://purl.org/dc/terms/title>'];


        $result = array(
            'error' => null,
            'result' => array(
                'subject' => $subject,
                'type' => $type,
                'title' => $title,
            )
        );
        return $this->_helper->json($result);

    }
}

