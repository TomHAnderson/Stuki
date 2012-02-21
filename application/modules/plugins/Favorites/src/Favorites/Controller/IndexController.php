<?php

namespace Favorites\Controller;

use Zend\Mvc\Controller\ActionController,
    Attachments\Form\Insert as InsertForm
    ;

class IndexController extends ActionController
{
    public function insertAction() {
        $entity_key = $this->getRequest()->query()->get('entity_key');

        $modelEntities = $this->getLocator()->get('modelEntities');
        $modelFavorites = $this->getLocator()->get('modelFavorites');
        $modelAuthentication = $this->getLocator()->get('modelAuthentication');

        $modelFavorites->insert($modelEntities->find($entity_key), $modelAuthentication->getEntity());

        return $this->plugin('redirect')->toUrl('/entities/view?entity_key=' . $entity_key);
    }

    public function deleteAction() {
        $entity_key = $this->getRequest()->query()->get('entity_key');

        $modelEntities = $this->getLocator()->get('modelEntities');
        $modelFavorites = $this->getLocator()->get('modelFavorites');
        $modelAuthentication = $this->getLocator()->get('modelAuthentication');

        $modelFavorites->delete($modelEntities->find($entity_key), $modelAuthentication->getEntity());

        return $this->plugin('redirect')->toUrl('/entities/view?entity_key=' . $entity_key);
    }
}
