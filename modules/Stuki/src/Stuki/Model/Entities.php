<?php
/**
 * Insert, update, and delete entities.
 * This also builds entity titles
 */
namespace Stuki\Model;

use Stuki\Model\Model as StukiModel,
    Stuki\Renderer\Parameters;

class Entities extends StukiModel {

    /**
     * Find one
     */
    public function find($key) {
        return $this->getEm()->getRepository('Entities\Entities')->find($key);
    }

    /**
     * Retrieve all
     */
    public function findAll($sort = null) {
        if (!$sort) $sort = array('title' => 'asc');
        return $this->getEm()->getRepository('Entities\Entities')->findBy(array(), $sort);
    }

    /**
     * Retrieve a subset
     */
    public function findBy($search, $sort = null) {
        if (!$sort) $sort = array('title' => 'asc');
        return $this->getEm()->getRepository('Entities\Entities')->findBy($search, $sort);
    }

    /**
     * Update an entity
     */
    public function update(\Entities\Entities $entity, $update) {
        foreach ($entity->getAttributeSet()->getAttributes() as $a) {
            // Find the existing value
            $value = $entity->findOneValueBy(array('attribute' => $a));

            // Update the value
            $newValue = $update['eav_' . $a->getKey()];

            // Check for special need datatypes
            switch ($a->getRenderer()->getClassObject('update')->getDataType()) {
                case 'datetime':
                    $newValue = ($newValue) ? new \Datetime($newValue): null;
                    break;
                default:
                    break;
            }

            if ($value) {
                // If the new value is emtpy remove the old value
                if ($newValue !== null and $newValue !== '') {
                    $value->setValue($newValue);
                } else {
                    $this->getEm()->remove($value);
                }
            } else {
                // No value exists, add it
                $class = '\Entities\Values' . ucfirst($a->getRenderer()->getClassObject('update')->getDataType());
                $value = new $class();
                $value->setAttribute($a);
                $value->setEntity($entity);
                $value->setValue($newValue);
                $this->getEm()->persist($value);
            }
        }

        $this->getEm()->flush();

        // Run buildTitle after em flush.  buildTitle will do another flush.
        $entity = $this->buildTitle($entity);

        // Run events
        $this->events()->trigger('update', $this, array('entity' => $entity));

        return $entity;
    }

    /**
     * Add a new entity
     */
    public function insert(
        \Entities\AttributeSets $attributeSet,
        \Entities\Entities $parentEntity,
        $values
        ) {

        $attributes = $this->getLocator()->get('modelAttributes');
        $entities = $this->getLocator()->get('modelEntities');


        $entity = new \Entities\Entities;

        // Is this the first entity in the system?
        if (!$parentEntity->getKey()) {
            $entity->setParent($entity);
            $parentEntity = $entity;
        } else {

            // Verify parent entity may attach the given attribute set
            $found = false;
            foreach ($parentEntity->getAttributeSet()->getRelations() as $att) {
                if ($att->getChild() == $attributeSet) {
                    $found = true;
                    break;
                }
            }
            if (!$found) throw new \Stuki\Exception("
                You cannot attach this attribute set to the parent entity's
                attribute set.  See Relations to enable this.
            ");
        }


        $entity->setUser($this->getAuthentication()->getEntity());
        $entity->setAttributeSet($attributeSet);
        $entity->setParent($parentEntity);

        $this->getEm()->persist($entity);

        foreach ($values as $col => $val) {
            if (substr($col, 0, 4) != 'eav_') continue;
            $attribute_key = (int)substr($col, 4);

            if (!$attribute = $attributes->find((int)$attribute_key))
                throw new \Stuki\Exception('Cannot find attribute ' . $attribute_key);

            $datatype = $attribute->getRenderer()->getClassObject('insert')->getDataType();
            $valueModel = "\Entities\Values{$datatype}";
            $value = new $valueModel;

            // Format values based on datatype
            $renderer = $attribute->getRenderer()->getClassObject('insert');
            switch ($datatype) {
                case 'datetime':
                    $val = ($val) ? new \Datetime($val): null;
                    break;
            }

            $value->setValue($val);
            $value->setAttribute($attribute);
            $value->setEntity($entity);

            // Only store non empty values
            if ($val !== '' AND $val !== null)
                $this->getEm()->persist($value);
        }

        $this->getEm()->flush();

        // Run buildTitle after em flush.  buildTitle will do another flush.
        $entity = $this->buildTitle($entity);

        // Run events
        $this->events()->trigger('insert', $this, array('entity' => $entity));

        return $entity;
    }

    /**
     * Delete an entity and it's values
     */
    public function delete(\Entities\Entities $entity) {
        if (sizeof($entity->getChildren()))
            throw new \Stuki\Exception("You cannot delete this entity because it has children");

        foreach ($entity->getValues() as $val) {
            $this->getEm()->remove($val);
        }

        // Run events before entity is removed
        $this->events()->trigger('delete', $this, array('entity' => $entity));

        $this->getEm()->remove($entity);
        $this->getEm()->flush();

        return true;
    }

    /**
     * Get count of all entities
     */
    public function getCount() {
        $entity = '\Entities\Entities';

        $query = $this->getEm()->createQuery("
            SELECT count(v)
            FROM $entity v
            "
        );

        $count = $query->getSingleScalarResult();

        return $count;
    }

    /**
     * Return the root entity
     */
    public function getRoot() {
        $entity = '\Entities\Entities';

        $query = $this->getEm()->createQuery("
            SELECT v
            FROM $entity v
            WHERE v.parent = v.entity_key
            "
        );

        try {
            $root = $query->getSingleResult();
        } catch (\Exception $e) {
            // An exception is thrown when no result is found
            // - on install
            return;
        }

        return $root;
    }

    /**
     * Can an entity be deleted?
     */
    public function canDelete(\Entities\Entities $entity) {
        if ($entity->children AND $entity->children->count()) return new \Stuki\Model\Result(true, false);;
        if ($entity->parent->entity_key == $entity->entity_key) return new \Stuki\Model\Result(true, false);;

        return new \Stuki\Model\Result(true, true);
    }

    /**
     * Find the system's root element
     */
    public function findRoot() {
        $query = $this->em->createQuery("
            SELECT e
            FROM \Entities\Entities e
            WHERE e.parent = e.entity_key");
        $res = $query->getResult();

        return new \Stuki\Model\Result(true, $res[0]);
    }

    /**
     * Build a dynamic title for the given entity
     */
    public function buildTitle(\Entities\Entities $entity) {

        $title = $entity->getAttributeSet()->getTitle();
        # FIXME: what about title elements with no code?
        # this used to loop attributes
        foreach ($entity->getAttributeSet()->getAttributes() as $att) {
            if ($value = $entity->findOneValueBy(array('attribute' => $att))) {

                // Build renderer
                $renderer = $value->getAttribute()->getRenderer()->getClassObject('buildtitle');
                if ($renderer instanceof Parameters)
                    $renderer->setParameters($value->getAttribute()->getParameters());

                $title = str_replace(
                   '{' . $value->getAttribute()->getCode() . '}',
                   $renderer->formatValue($value->getValue()),
                   $title
                );
            } else {
                $title = str_replace('{' . $att->getCode() . '}', '', $title);
            }
        }

        $entity->setTitle($title);
        $this->getEm()->flush();

        return $entity;
    }

}