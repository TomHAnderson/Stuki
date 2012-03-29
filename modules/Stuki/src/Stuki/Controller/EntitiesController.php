<?php
/**
 */
namespace Stuki\Controller;

use Zend\Mvc\Controller\ActionController,
    Stuki\Form\Entities\Insert as InsertForm,
    Stuki\Form\Entities\Update as UpdateForm,
    Zend\View\Model\ViewModel
    ;

class EntitiesController extends ActionController
{
    public function viewAction() {
        $request = $this->getRequest();

        $modelEntities = $this->getLocator()->get('modelEntities');

        return array(
            'entity' => $modelEntities->find($request->query()->get('entity_key'))
        );
    }

    public function insertAction() {
        $request = $this->getRequest();

        $modelEntities = $this->getLocator()->get('modelEntities');
        $modelAttributeSets = $this->getLocator()->get('modelAttributeSets');

        if (!$attribute_set_key = $request->query()->get('attribute_set_key'))
            throw new \Stuki\Exception('An attribute set key is required');

        if (!$attributeSet = $modelAttributeSets->find((int)$attribute_set_key))
            throw new \Stuki\Exception('Attribute set not found');

        if (!$parent_key = $request->query()->get('parent_key'))
            throw new \Stuki\Exception('A parent is required');

        $form = new InsertForm($attributeSet);

        // If the post is valid, continue
        if ($request->isPost() and $form->isValid($request->post()->toArray())) {
            if (!$parent = $modelEntities->find($parent_key)) {
                // Is this the system's first entity?
                if (!$modelEntities->getCount()) {
                    $parent = new \Entities\Entities;
                }
            }

            $entity = $modelEntities->insert(
                $attributeSet,
                $parent,
                $form->getValues()
            );

            return $this->plugin('redirect')->toUrl('/entities/view?entity_key=' . $entity->getKey());
        } elseif ($request->isPost()) {
            // Form failed, add global message
            $form->setDescription('There were errors reported');
            $form->addDecorator('Description');
        }

        $this->events()->trigger('insert', $this, array('attributeSet' => $attributeSet));

        return array(
            'form' => $form,
            'attributeSet' => $attributeSet,
            'parent' => $modelEntities->find($parent_key)
        );
    }

    public function updateAction() {

        $request = $this->getRequest();

        $modelEntities = $this->getLocator()->get('modelEntities');

        if (!$entity_key = $request->query()->get('entity_key'))
            throw new \Stuki\Exception('An entity key is required');

        if (!$entity = $modelEntities->find((int)$entity_key))
            throw new \Stuki\Exception('Entity not found');

        $form = new UpdateForm($entity);

        // If the post is valid, continue
        if ($request->isPost() and $form->isValid($request->post()->toArray())) {
            $entity = $modelEntities->update($entity, $form->getValues());

            return $this->plugin('redirect')->toUrl('/entities/view?entity_key=' . $entity->getKey());
        } else if (!$request->isPost()) {
            // Set default values
            foreach ($entity->getAttributeSet()->getAttributes() as $a) {
                $element = $a->getRenderer()->getClassObject('eav_' . $a->getKey());

                // Set renderer parameters
                if ($element instanceof Parameters)
                    $element->setParameters($a->getParameters());

                // Find and set value
                $value = $entity->findOneValueBy(array('attribute' => $a));
                if ($value) $form->setDefault('eav_' . $a->getKey(), $element->formatEditValue($value->getValue()));
            }
        } else {
            // Form failed, add global message
            $form->setDescription('There were errors reported');
            $form->addDecorator('Description');
        }

        $this->events()->trigger('update', $this, array('entity' => $entity));

        return array(
            'form' => $form,
            'entity' => $entity
        );
    }

    public function deleteAction() {
        $request = $this->getRequest();

        $modelEntities = $this->getLocator()->get('modelEntities');

        if (!$entity_key = $request->query()->get('entity_key'))
            throw new \Stuki\Exception('An entity key is required');

        if (!$entity = $modelEntities->find((int)$entity_key))
            throw new \Stuki\Exception('Entity not found');

        $parent = $entity->getParent();
        $modelEntities->delete($entity);

        return $this->plugin('redirect')->toUrl('/entities/view?entity_key=' . $parent->getKey());
    }

    public function insertrelationAction() {
        $request = $this->getRequest();

        $modelEntities = $this->getLocator()->get('modelEntities');
        $modelEntityRelations = $this->getLocator()->get('modelEntityRelations');

        if (!$parent_key = $request->post()->get('parent_key'))
            throw new \Stuki\Exception('A parent entity key is required');

        if (!$child_key = $request->post()->get('child_key'))
            throw new \Stuki\Exception('A child entity key is required');

        if (!$parent = $modelEntities->find((int)$parent_key))
            throw new \Stuki\Exception('Parent entity not found');

        if (!$child = $modelEntities->find((int)$child_key))
            throw new \Stuki\Exception('Child entity not found');

        $modelEntityRelations->insert($parent, $child);

        return $this->plugin('redirect')->toUrl('/entities/view?entity_key=' . $parent->getKey());

    }
}
