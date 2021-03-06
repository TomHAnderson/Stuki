<?php

namespace Stuki\Controller;

use Zend\Mvc\Controller\ActionController;

class IndexController extends ActionController
{
    public function indexAction()
    {
        $modelAttributeSets = $this->getLocator()->get('modelAttributeSets');
        $modelEntities = $this->getLocator()->get('modelEntities');

        if (!$root = $modelEntities->getRoot()) {
            $root = new \Entities\Entities;
        }

        return array(
            'rootEntity' => $root->getKey(),
        );
    }

    public function adminAction()
    {
        $modelAttributeSets = $this->getLocator()->get('modelAttributeSets');
        $modelEntities = $this->getLocator()->get('modelEntities');

        $attributeSets = $modelAttributeSets->findAll();
        if (!$root = $modelEntities->getRoot()) {
            $root = new \Entities\Entities;
        }

        return array(
            'rootEntity' => $root->getKey(),
            'attributeSets' => $attributeSets
        );
    }
}
