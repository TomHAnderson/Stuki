<?php

namespace Stuki\Controller;

use Zend\Mvc\Controller\ActionController,
    Stuki\Form\AttributeSets\Insert as InsertForm,
    Stuki\Form\AttributeSets\Update as UpdateForm
    ;

class AttributeSetsController extends ActionController
{
    public function indexAction()
    {
        $modelAttributeSets = $this->getLocator()->get('modelAttributeSets');

        return array(
            'attributeSets' => $modelAttributeSets->findAll()
        );
    }

    public function insertAction() {

        $request = $this->getRequest();
        $form = new InsertForm();
        $attributeSets = $this->getLocator()->get('modelAttributeSets');
        $themes = $this->getLocator()->get('modelThemes');

        $elementTheme = $form->getElement('ref_theme');
        foreach ($themes->findAll() as $theme) {
            $elementTheme->addMultiOption($theme->getKey(), $theme->getAlias());
        }

        if ($request->isPost() and $form->isValid($request->post()->toArray())) {
            $attributeSet = $attributeSets->insert($form->getValues());

            // Redirect to /renderers
            return $this->plugin('redirect')->toUrl('/attributesets/view?attribute_set_key=' . $attributeSet->getKey());
        } elseif (!$request->isPost()) {
            $form->setDefaults(array(
                'tabs' => 1
            ));
        } else {
            // Form failed, add global message
            $form->setDescription('There were errors reported');
            $form->addDecorator('Description');
        }

        $this->events()->trigger('insert', $this, array());

        return array(
            'form' => $form
        );
    }

    public function updateAction() {
        $request = $this->getRequest();
        $attributeSets = $this->getLocator()->get('modelAttributeSets');
        $themes = $this->getLocator()->get('modelThemes');
        $form = new UpdateForm();

        if (!$key = $request->query()->get('attribute_set_key'))
            return $this->plugin('redirect')->toUrl('/attributesets');

        if (!$attributeSet = $attributeSets->find((int)$key))
            throw new \Stuki\Exception("Attribute set not found matching $key");

        $elementTheme = $form->getElement('ref_theme');
        foreach ($themes->findAll() as $theme) {
            $elementTheme->addMultiOption($theme->getKey(), $theme->getAlias());
        }

        if ($request->isPost() and $form->isValid($request->post()->toArray())) {
            $attributeSets->update($attributeSet, $form->getValues());

            // Redirect back
            return $this->plugin('redirect')->toUrl('/attributesets/view?attribute_set_key=' . $attributeSet->getKey());
        } elseif (!$request->isPost()) {
            $form->setDefaults(array(
                'name' => $attributeSet->getName(),
                'code' => $attributeSet->getCode(),
                'description' => $attributeSet->getDescription(),
                'title' => $attributeSet->getTitle(),
                'ref_theme' => $attributeSet->getTheme()->getKey(),
                'tabs' => $attributeSet->getTabs()
            ));
        } else {
            // Form failed, add global message
            $form->setDescription('There were errors reported');
            $form->addDecorator('Description');
        }

        $this->events()->trigger('update', $this, array('attributeSet' => $attributeSet));

        return array(
            'form' => $form
        );
    }

    public function deleteAction() {
        $attributeSets = $this->getLocator()->get('modelAttributeSets');
        $attributeSet = $attributeSets->find((int)$this->getRequest()->query()->get('attribute_set_key'));
        $attributeSets->delete($attributeSet);

        // Redirect to /renderers
        $this->plugin('redirect')->toUrl('/attributesets');
    }

    public function viewAction() {
        $attributeSets = $this->getLocator()->get('modelAttributeSets');
        $set = $attributeSets->find((int)$this->getRequest()->query()->get('attribute_set_key'));

        return array(
            'attributeSet' => $set
        );
    }

    public function relationsAction() {
        $request = $this->getRequest();
        $modelAttributeSets = $this->getLocator()->get('modelAttributeSets');
        if (!$attributeSet = $modelAttributeSets->find((int)$this->getRequest()->query()->get('attribute_set_key')))
            throw new \Stuki\Exception('Attribute set not found');

        $allSets = $modelAttributeSets->findAll();

        if ($request->isPost()) {
            $modelAttributeSets->updateRelations($attributeSet, $request->post()->get('children'));

            // Redirect to attribute set
            return $this->plugin('redirect')->toUrl('/attributesets/view?attribute_set_key=' . $attributeSet->getKey());
        }

        return array(
            'allSets' => $allSets,
            'attributeSet' => $attributeSet
        );
    }

    public function orderingAction() {
        $request = $this->getRequest();
        $modelAttributeSets = $this->getLocator()->get('modelAttributeSets');
        if (!$attributeSet = $modelAttributeSets->find((int)$request->query()->get('attribute_set_key')))
            throw new \Stuki\Exception('Attribute set not found');

        if ($request->query()->get('reorder')) {
            // Gather form data and save form
            $modelAttributeSets->updateOrder($attributeSet, $request->query()->get('reorder'));
            $modelAttributeSets->updateTabs($attributeSet, $request->query()->get('tabs'));
            $modelAttributeSets->updateTabTitles($attributeSet, $request->query()->get('tab_titles'));

            // Redirect to attribute set
            return $this->plugin('redirect')->toUrl('/attributesets/view?attribute_set_key=' . $attributeSet->getKey());
        }

        return array(
            'attributeSet' => $attributeSet
        );
    }

    public function pluginsAction() {
        $request = $this->getRequest();
        $modelAttributeSets = $this->getLocator()->get('modelAttributeSets');
        $modelPlugins = $this->getLocator()->get('modelPlugins');

        if (!$attributeSet = $modelAttributeSets->find((int)$request->query()->get('attribute_set_key')))
            throw new \Stuki\Exception('Attribute set not found');

        if ($request->isPost()) {
            $modelAttributeSets->updatePlugins($attributeSet, $request->post()->get('plugins'));

            // Redirect to attribute set
            return $this->plugin('redirect')->toUrl('/attributesets/view?attribute_set_key=' . $attributeSet->getKey());
        }

        return array(
            'attributeSet' => $attributeSet,
            'allPlugins' => $modelPlugins->findAll()
        );
    }

    public function addpluginAction() {
        $request = $this->getRequest();
        $modelAttributeSets = $this->getLocator()->get('modelAttributeSets');
        $modelPlugins = $this->getLocator()->get('modelPlugins');

        if (!$attributeSet = $modelAttributeSets->find((int)$request->post()->get('attribute_set_key')))
            throw new \Stuki\Exception('Attribute set not found');

        if (!$plugin = $modelPlugins->find((int)$request->post()->get('plugin_key')))
            throw new \Stuki\Exception('Plugin not found');

        $modelAttributeSets->addPlugin($attributeSet, $plugin);

        // Redirect to attribute set
        return $this->plugin('redirect')->toUrl('/attributesets/plugins?attribute_set_key=' . $attributeSet->getKey());
    }

    public function removepluginAction() {
        $request = $this->getRequest();
        $modelAttributeSets = $this->getLocator()->get('modelAttributeSets');

        if (!$attribute_set_plugin_key = (int)$request->query()->get('attribute_set_plugin_key'))
            throw new \Stuki\Exception('Attribute set plugin key not supplied');

        if (!$xref = $modelAttributeSets->findPlugin((int)$attribute_set_plugin_key)) {
            throw new \Stuki\Exception('Attribute set plugin not found');
        }

        $attribute_set_key = $xref->getAttributeSet()->getKey();

        $modelAttributeSets->removePlugin($xref);

        // Redirect to attribute set
        return $this->plugin('redirect')->toUrl('/attributesets/plugins?attribute_set_key=' . $attribute_set_key);
    }

    public function pluginparametersAction() {
        $request = $this->getRequest();
        $modelAttributeSets = $this->getLocator()->get('modelAttributeSets');
        $modelPlugins = $this->getLocator()->get('modelPlugins');

        if (!$attributeSet = $modelAttributeSets->find((int)$request->query()->get('attribute_set_key')))
            throw new \Stuki\Exception('Attribute set not found');

        if (!$plugin = $modelAttributeSets->findPlugin((int)$request->query()->get('attribute_set_plugin_key')))
            throw new \Stuki\Exception('Attribute set Plugin not found');

        $pluginClass = $plugin->getPlugin()->getClassObject();
        if (!$pluginClass instanceof \Stuki\Plugin\Parameters)
            throw new \Stuki\Exception('This plugin does not support parameters');

        $form = $pluginClass->getParametersForm();

        if ($request->isPost() and $form->isValid($request->post()->toArray())) {
            $modelAttributeSets->updatePluginParameters($plugin, $form->getValues());

            return $this->plugin('redirect')->
                toUrl('/attributesets/view?attribute_set_key=' .
                    $attributeSet->getKey());

        } else if (!$request->isPost()) {
            $form->setDefaults((array)$plugin->getParameters());
        }

        return array(
            'attributeSet' => $attributeSet,
            'plugin' => $plugin,
            'form' => $form
        );
    }
}