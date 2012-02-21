<?php

namespace Stuki\Controller;

use Zend\Mvc\Controller\ActionController,
    Stuki\Plugin\Exception as Exception,
    Stuki\Form\Plugins\Insert as InsertForm
    ;

class PluginsController extends ActionController
{
    public function indexAction()
    {
        $plugins = $this->getLocator()->get('modelPlugins');

        return array(
            'allPlugins' => $plugins->findAll()
        );
    }

    /**
     * Add a new renderer.  Since renderers must be tested for a valid
     * class this is it's own action (instead of at the bottom of index)
     * and it makes adding a new one more deliberate.
     */
    public function insertAction() {
        $form = new InsertForm();
        $request = $this->getRequest();
        $modelPlugins = $this->getLocator()->get('modelPlugins');

        if ($request->isPost() and $form->isValid($request->post()->toArray())) {
            try {
                // Add renderer
                $values = $form->getValues();
                $modelPlugins->insert($values['alias'], $values['class']);

                // Redirect to /renderers
                $this->plugin('redirect')->toUrl('/plugins');

            } catch (Exception\InvalidArgumentException $e) {
                $form->getElement('class')->addError($e->getMessage());
            } catch (\Exception $e) {
                throw $e;
            }
        }

        return array(
            'form' => $form
        );
    }

    public function deleteAction() {
        $renderers = $this->getLocator()->get('modelRenderers');
        $renderer = $renderers->find((int)$this->getRequest()->query()->get('renderer_key'));
        $renderers->delete($renderer);

        // Redirect to /renderers
        $this->plugin('redirect')->toUrl('/renderers');
    }

    public function attributesetsAction() {
        // FIXME: Show all attribute sets for the given plugin
    }
}
