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
     * Add a new plugin.
     */
    public function insertAction() {
        $form = new InsertForm();
        $request = $this->getRequest();
        $modelPlugins = $this->getLocator()->get('modelPlugins');

        if ($request->isPost() and $form->isValid($request->post()->toArray())) {
            try {
                // Add
                $values = $form->getValues();
                $modelPlugins->insert($values['alias'], $values['class']);

                // Redirect
                return $this->plugin('redirect')->toUrl('/plugins');

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
        $plugins = $this->getLocator()->get('modelPlugins');
        $plugin = $plugins->find((int)$this->getRequest()->query()->get('plugin_key'));
        $plugins->delete($plugin);

        // Redirect
        return $this->plugin('redirect')->toUrl('/plugins');
    }

    public function attributesetsAction() {
        // FIXME: Show all attribute sets for the given plugin
    }
}
