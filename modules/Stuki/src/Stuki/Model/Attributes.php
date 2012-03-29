<?php

namespace Stuki\Model;

use Stuki\Model\Model as StukiModel;

class Attributes extends StukiModel {

    /**
     * Retrieve all
     */
    public function findAll($sort = null) {
        if (!$sort) $sort = array('alias' => 'asc');
        return $this->getEm()->getRepository('Entities\Attributes')->findBy(array(), $sort);
    }

    /**
     * Retrieve a subset
     */
    public function findBy($search, $sort = null) {
        if (!$sort) $sort = array('alias' => 'asc');
        return $this->getEm()->getRepository('Entities\Attributes')->findBy($search, $sort);
    }

    /**
     * Find one
     */
    public function find($key) {
        return $this->getEm()->getRepository('Entities\Attributes')->find($key);
    }

    public function findOneBy($params) {
        return $this->getEm()->getRepository('Entities\Attributes')->findOneBy($params);
    }


    public function updateParameters(\Entities\Attributes $attribute, $parameters) {
        $attribute->setParameters($parameters);
        $this->getEm()->flush();
    }

    public function delete(\Entities\Attributes $attribute)  {
        $attribute->setAttributeSet(null);
        $this->getEm()->flush();

        return $attribute;
    }

    public function undelete(\Entities\Attributes $attribute)  {
        $attribute->setAttributeSet($attribute->getOriginalAttributeSet());
        $this->getEm()->flush();

        return $attribute;
    }

    public function insert(\Entities\AttributeSets $attributeSet, $values) {
        $renderers = $this->getLocator()->get('modelRenderers');

        // Find renderer
        if (!$renderer = $renderers->find((int)$values['ref_renderer']))
            throw new \Stuki\Exception('The renderer cannot be found');

        // Verfiy code is unique for this attribute set
        foreach ($attributeSet->getAttributes() as $att) {
            if ($att->getCode() == $values['code'])
                throw new \Stuki\Exception('Code is not unique for this attribute set');
        }

        // Verfiy label is unique for this attribute set
        foreach ($attributeSet->getAttributes() as $att) {
            if ($att->getLabel() == $values['label'])
                throw new \Stuki\Exception('Label is not unique for this attribute set');
        }

        $attribute = new \Entities\Attributes;

        $attribute->setAttributeSet($attributeSet);
        $attribute->setOriginalAttributeSet($attributeSet);
        $attribute->setRenderer($renderer);
        $attribute->setCode($values['code']);
        $attribute->setLabel($values['label']);
        $attribute->setDescription($values['description']);
        $attribute->setOptions(explode("\n", $values['options']));
        $attribute->setRegex($values['regex']);
        $attribute->setError($values['error']);
        // Sort & Tab are not set from this page
        $attribute->setIsRequired($values['isRequired']);
        $attribute->setIsUnique($values['isUnique']);
        $attribute->setIsIncludedInSummary($values['isIncludedInSummary']);

        $this->getEm()->persist($attribute);
        $this->getEm()->flush();

        return true;
    }

    public function update(\Entities\Attributes $attribute, $values) {
        $renderer = null;
        $renderers = $this->getLocator()->get('modelRenderers');

        // Find renderer
        if (isset($values['ref_renderer']))
            if (!$renderer = $renderers->find((int)$values['ref_renderer']))
                throw new \Stuki\Exception('Renderer cannot be found');

        // If values exist you cannot change the renderer
        if ($attribute->getValueCount()) {
            if ($renderer AND $attribute->getRenderer() != $renderer) {
                // Don't allow changes to the renderer
                throw new \Stuki\Exception('You cannot change the renderer because values already exist');
            }
        }

        if ($renderer) $attribute->setRenderer($renderer);

        $attribute->setCode($values['code']);
        $attribute->setLabel($values['label']);
        $attribute->setDescription($values['description']);
        $attribute->setOptions(explode("\n", $values['options']));
        $attribute->setRegex($values['regex']);
        $attribute->setError($values['error']);
        // Sort & Tab are not set from this page
        $attribute->setIsRequired($values['isRequired']);
        $attribute->setIsUnique($values['isUnique']);
        $attribute->setIsIncludedInSummary($values['isIncludedInSummary']);

        $this->getEm()->flush();

        return true;
    }
}