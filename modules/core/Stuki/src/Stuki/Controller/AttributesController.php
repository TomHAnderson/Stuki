<?php

namespace Stuki\Controller;

use Zend\Mvc\Controller\ActionController,
    Stuki\Renderer\Parameters,
    Stuki\Form\Attributes\Insert as InsertForm,
    Stuki\Form\Attributes\Update as UpdateForm
    ;

class AttributesController extends ActionController
{

    public function insertAction() {
        $request = $this->getRequest();
        $form = new InsertForm();

        $modelAttributes = $this->getLocator()->get('modelAttributes');
        $modelAttributeSets = $this->getLocator()->get('modelAttributeSets');
        $modelRenderers = $this->getLocator()->get('modelRenderers');


        $attribute_set_key = $request->query()->get('attribute_set_key');

        // Add renderers to form
        // This is done here so we have fine grained control over which renderers are displayed.
        // If editing an attribute we don't want renderers with a different datatype selectable.
        $elementRenderer = $form->getElement('ref_renderer');
        foreach ($modelRenderers->findAll() as $renderer) {
            $elementRenderer->addMultiOption($renderer->getKey(), $renderer->getAlias());
        }


        // If the post is valid, continue
        if ($request->isPost() and $form->isValid($request->post()->toArray())) {
            $attributeSet = $modelAttributeSets->find((int)$attribute_set_key);
            $modelAttributes->insert($attributeSet, $form->getValues());

            // Redirect to /renderers
            return $this->plugin('redirect')->toUrl('/attributesets/view?attribute_set_key=' . $attribute_set_key);
        }

        return array(
            'form' => $form,
            'attribute_set_key' => $attribute_set_key
        );
    }

    public function updateAction() {
        $request = $this->getRequest();
        $attributes = $this->getLocator()->get('modelAttributes');
        $modelRenderers = $this->getLocator()->get('modelRenderers');
        $form = new UpdateForm();

        if (!$key = $request->query()->get('attribute_key'))
            return $this->plugin('redirect')->toUrl('/attributesets');

        if (!$attribute = $attributes->find((int)$key))
            throw new \Stuki\Exception("Attribute set not found matching $key");

        // Add renderers to form
        // If the attribute has values only allow to change to renderers with
        // identical data types
        $elementRenderer = $form->getElement('ref_renderer');
        if ($attribute->getValueCount()) {
            // Don't allow changes to the renderer
            $form->removeElement('ref_renderer');
        } else {
            // Not used yet, allow all renderers
            foreach ($modelRenderers->findAll() as $renderer) {
                $elementRenderer->addMultiOption($renderer->getKey(), $renderer->getAlias());
            }
        }


        if ($request->isPost() and $form->isValid($request->post()->toArray())) {
            $attributes->update($attribute, $form->getValues());

            // Redirect back
            return $this->plugin('redirect')->toUrl('/attributesets/view?attribute_set_key=' .
                                                    $attribute->getAttributeSet()->getKey());
        } elseif (!$request->isPost()) {
            $form->setDefaults(array(
                'label' => $attribute->getLabel(),
                'code' => $attribute->getCode(),
                'description' => $attribute->getDescription(),
                'ref_renderer' => $attribute->getRenderer()->getKey(),
                'options' => implode("\n", $attribute->getOptions()),
                'regex' => $attribute->getRegex(),
                'error' => $attribute->getError(),
                'isRequired' => $attribute->getIsRequired(),
                'isUnique' => $attribute->getIsUnique(),
                'isIncludedInSummary' => $attribute->getIsIncludedInSummary()
            ));
        }

        return array(
            'form' => $form,
            'attribute' => $attribute
        );
    }

    public function deleteAction() {
        $modelAttributes = $this->getLocator()->get('modelAttributes');
        $attribute = $modelAttributes->find((int)$this->getRequest()->query()->get('attribute_key'));
        $modelAttributes->delete($attribute);

        // Redirect to /renderers
        $this->plugin('redirect')->toUrl('/attributesets/view?attribute_set_key=' .
            $attribute->getOriginalAttributeSet()->getKey());
    }

    public function undeleteAction() {
        $modelAttributes = $this->getLocator()->get('modelAttributes');
        $attribute = $modelAttributes->find((int)$this->getRequest()->query()->get('attribute_key'));
        $modelAttributes->undelete($attribute);

        // Redirect to /renderers
        $this->plugin('redirect')->toUrl('/attributesets/view?attribute_set_key=' .
            $attribute->getAttributeSet()->getKey());
    }

    /**
     * This renders a form supplied by a renderer
     * and saves the results to the attribute parameters
     */
    public function parametersAction() {
        $request = $this->getRequest();
        $modelAttributes = $this->getLocator()->get('modelAttributes');

        // Get attribute
        $attribute_key = $request->query()->get('attribute_key');
        if (!$attribute = $modelAttributes->find((int)$attribute_key))
            throw new \Stuki\Exception('Attribute not found');

        // Verfiy attribute has parameters
        if (!$attribute->getRenderer()->getClassObject('parameters') instanceof Parameters)
            throw new \Stuki\Exception('Attribute renderer does not require parameters');

        // Get the form from the renderer class
        $form = $attribute->getRenderer()->
                            getClassObject('parameters')->
                            getParametersForm();

        if ($request->isPost() and $form->isValid($request->post()->toArray())) {
            $modelAttributes->updateParameters($attribute, $form->getValues());

            // Redirect to /renderers
            return $this->plugin('redirect')->
                toUrl('/attributesets/view?attribute_set_key=' .
                    $attribute->getAttributeSet()->getKey());
        } else if (!$request->isPost()) {
            $form->setDefaults($attribute->getParameters());
        }


        return array(
            'attribute' => $attribute,
            'form' => $form
        );
    }
}
