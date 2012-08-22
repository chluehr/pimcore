<?php

class CreateJS_AdminController extends Pimcore_Controller_Action_Admin
{
    /**
     * Perform a backbone sync for CreateJS
     * @return array
     * @throws Exception
     */
    public function syncAction()
    {
        try {

            $request = $this->getRequest();

            $model = $request->getParam('model', false);

            if (!is_array($model)) {

                throw new Exception('No valid model parameter found.');
            }

            if (!array_key_exists('@subject', $model)) {

                throw new Exception('No valid subject in model found.');
            }

            $subject = $model['@subject'];

            // @todo handle types
            // $type = $model['@type'];

            // @todo proper "about" attribute handling needed!
            // (just trying to detect a pageId for now ..)
            if (!preg_match('/([0-9]+)>$/', $subject, $match)) {

                throw new Exception('PageId not found in "about".');
            }

            /** @var $page Document_Page */
            $page = Document::getById((int)$match[1]);

            foreach ($model as $elementKey => $elementValue) {

                // skip links
                if (substr($elementKey, 0, 1) == '@') {
                    continue;
                }

                $page->setRawElement($elementKey, 'input', $elementValue);
            }

            $page->save();

            $result = array(
                'error' => null,
                'result' => array(
                    'success' => true,
                )
            );

        } catch (Exception $exception) {

            $result = array(
                'error' => array(
                    'code' => $exception->getCode(),
                    'message' => $exception->getMessage(),
                ),
                'result' => null
            );
        }

        return $this->_helper->json($result);
    }
}

