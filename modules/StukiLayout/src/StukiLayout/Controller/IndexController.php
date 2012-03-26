<?php
/**
 */
namespace StukiLayout\Controller;

use Zend\Mvc\Controller\ActionController,
    Stuki\Form\Entities\Insert as InsertForm,
    Stuki\Form\Entities\Update as UpdateForm,
    Zend\View\Model\ViewModel,
    Zend\Layout\Layout as LayoutManager
    ;

class IndexController extends ActionController
{
    public function entityinsertjsAction() {
        $request = $this->getRequest();

        $modelAttributeSets = $this->getLocator()->get('modelAttributeSets');

        if (!$attribute_set_key = $request->query()->get('attribute_set_key'))
            throw new \Stuki\Exception('An attribute set key is required');

        if (!$attributeSet = $modelAttributeSets->find((int)$attribute_set_key))
            throw new \Stuki\Exception('Attribute set not found');

        // Turn off the layout. i.e. only render the view script.
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);

        $viewModel->setVariables(array(
            'attributeSet' => $attributeSet
        ));

        $response = $this->getResponse();
        $response->headers()->addHeaderLine('Content-Type: text/javascript');

        return $viewModel;

    }

    public function entityupdatejsAction() {
        $request = $this->getRequest();

        $modelEntities = $this->getLocator()->get('modelEntities');

        if (!$entity_key = $request->query()->get('entity_key'))
            throw new \Stuki\Exception('An entity key is required');

        if (!$entity = $modelEntities->find((int)$entity_key))
            throw new \Stuki\Exception('Entity not found');

        // Turn off the layout. i.e. only render the view script.
        $viewModel = new ViewModel();
        $viewModel->setTerminal(true);

        $viewModel->setVariables(array(
            'entity' => $entity
        ));

        $response = $this->getResponse();
        $response->headers()->addHeaderLine('Content-Type: text/javascript');

        return $viewModel;

    }
}
