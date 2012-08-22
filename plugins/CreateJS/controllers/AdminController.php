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

        preg_match('/([0-9]+)>$/', $subject, $match);

        /** @var $page Document_Page */
        $page = Document::getById((int)$match[1]);

        foreach ($model as $elementKey => $elementValue) {

            if (substr($elementKey,0,1) != '@') {

                    $page->setRawElement($elementKey, 'input', $elementValue);
            }

        }

        $page->save();

        $result = array(
            'error' => null,
            'result' => array(
                'success' => true,
            )
        );
        return $this->_helper->json($result);

    }
}

