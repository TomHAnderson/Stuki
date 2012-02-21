<?php

namespace StuQL\Controller;

use Zend\Mvc\Controller\ActionController,
    Zend\Search\Lucene
;

class IndexController extends ActionController
{
    public function indexAction() {
        $result = array();
        $error = '';

        $statement = $this->getRequest()->query()->get('statement');

        $stuql = $this->getLocator()->get('modelStuQL');
        $attributeSets = $this->getLocator()->get('modelAttributeSets');

        if ($statement) {
            try {
                $result = $stuql->query($statement);
            } catch (\StuQL\Exception $exception) {
                $error = $exception->getMessage();
            }

        }

        $sets = $attributeSets->findAll();

        return array(
            'attributeSets' => $sets,
            'statement' => $statement,
            'result' => $result,
            'error' => $error,
        );
    }
}
