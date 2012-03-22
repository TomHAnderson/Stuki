<?php
/**
 * Renderers are the glue which binds the database to the code.
 * Renderers are the core component of the the Stuki design.
 */
namespace Stuki\Model;

use Stuki\Model\Model,
    Entities\AttributeSets as EntityAttributeSets,
    Stuki\AttributeSets\Exception as Exception
    ;

class AttributeSets extends Model {

    /**
     * Add a new renderer
     *
     * All we require is a class name.
     */
    public function insert($values) {
        $themes = $this->getLocator()->get('modelThemes');
        // Verify code is unique
        if ($this->findBy(array('code' => $values['code'])))
            throw new \Stuki\Exception('Attribute set code is not unique');

        // Verify theme is valid
        if (!$theme = $themes->find((int)$values['ref_theme']))
            throw new \Stuki\Exception("Invalid theme selected");

#print_r($values);die();
        // Add the attribute set
        $em = $this->getEm();
        $attributeSet = new EntityAttributeSets();
        $attributeSet->setName($values['name']);
        $attributeSet->setCode($values['code']);
        $attributeSet->setDescription($values['description']);
        $attributeSet->setTitle($values['title']);
        $attributeSet->setTheme($theme);
        $attributeSet->setTabs((int)$values['tabs']);
        $attributeSet->setUser($this->getAuthentication()->getEntity());
        $em->persist($attributeSet);
        $em->flush();

        return $attributeSet;
    }

    public function update(\Entities\AttributeSets $attributeSet, $values) {
        $themes = $this->getLocator()->get('modelThemes');

        // Verify code is unique
        if ($test = $this->findOneBy(array('code' => $values['code'])) AND $test != $attributeSet)
            throw new \Stuki\Exception('Attribute set code is not unique');

        // Verify theme is valid
        if (!$theme = $themes->find((int)$values['ref_theme']))
            throw new \Stuki\Exception("Invalid theme selected");

        $em = $this->getEm();
        $attributeSet->setName($values['name']);
        $attributeSet->setCode($values['code']);
        $attributeSet->setDescription($values['description']);
        $attributeSet->setTitle($values['title']);
        $attributeSet->setTheme($theme);
        $attributeSet->setTabs((int)$values['tabs']);
        $em->persist($attributeSet);
        $em->flush();

        // When an attribute set is updated all entities of that attribute set must be reindexed and retitled
        $queue = $this->getQueue();
        $queue->send('ATTRIBUTESET_RETITLE_ALL:' . $attributeSet->getKey());

        return true;
    }

    /**
     * Delete a renderer
     */
    public function delete(\Entities\AttributeSets $attributeSet) {
        if (count($attributeSet->getEntities()))
            throw new \Stuki\Exception('The specified attribute set is in use by one or more entities');

        $em = $this->getEm();
        $em->remove($attributeSet);
        $em->flush();
    }

    /**
     * Retrieve one
     */
    public function find($renderer_key) {
        if (!is_int($renderer_key)) throw new \Stuki\Exception('Attribute set key must be an integer');
        return $this->getEm()->getRepository('Entities\AttributeSets')->find($renderer_key);
    }

    /**
     * Retrieve all
     */
    public function findAll($sort = null) {
        if (!$sort) $sort = array('name' => 'asc');
        return $this->getEm()->getRepository('Entities\AttributeSets')->findBy(array(), $sort);
    }

    /**
     * Retrieve a subset
     */
    public function findBy($search, $sort = null) {
        if (!$sort) $sort = array('name' => 'asc');
        return $this->getEm()->getRepository('Entities\AttributeSets')->findBy($search, $sort);
    }

    /**
     * Retrieve one using an assoc array of params
     */
    public function findOneBy($search) {
        return $this->getEm()->getRepository('Entities\AttributeSets')->findOneBy($search);
    }

    /**
     * A many to many relationship with itself, this defines which attribute sets may be
     * added to a given attribute set
     */
    public function updateRelations(\Entities\AttributeSets $attributeSet, $children) {
        // Truncate existing relations
        $query = $this->getEm()->createQuery("
            DELETE FROM \Entities\AttributeSetRelations a
            WHERE a.parent = " . $attributeSet->getKey()
        );
        $query->getResult();

        // Insert new relations
        foreach ((array)$children as $ref_child) {
            $relation = new \Entities\AttributeSetRelations();
            $relation->setParent($attributeSet);
            $relation->setChild($this->find((int)$ref_child));

            $this->getEm()->persist($relation);
        }

        $this->getEm()->flush();
    }

    public function updatePlugins(\Entities\AttributeSets $attributeSet, $plugins) {
        $modelPlugins = $this->getLocator()->get('modelPlugins');

        // Truncate existing plugins
        $query = $this->getEm()->createQuery("
            DELETE FROM \Entities\AttributeSetPlugins a
            WHERE a.attributeSet = " . $attributeSet->getKey()
        );
        $query->getResult();

        foreach ((array)$plugins as $plugin_key) {
            $newPlugin = new \Entities\AttributeSetPlugins();
            $newPlugin->setAttributeSet($attributeSet);
            $newPlugin->setPlugin($modelPlugins->find((int)$plugin_key));

            $this->getEm()->persist($newPlugin);
        }

        $this->getEm()->flush();
    }

    /**
     * Reorder the attributes attached to an attribute set
     */
    public function updateOrder(\Entities\AttributeSets $attributeSet, $reorder) {
        $order = 0;
        foreach ($reorder as $key) {
            $order ++;
            foreach ($attributeSet->getAttributes() as $a) {
                if ($a->getKey() == $key) $a->setSort($order);
#                if ($tabs[$a->getKey()]) $a->setTab($tabs[$a->getKey()]);
            }
        }

        $this->getEm()->flush();
        return $attributeSet;
    }

    /**
     * Reorder the attributes attached to an attribute set
     */
    public function updateTabs(\Entities\AttributeSets $attributeSet, $tabs) {
        foreach ($attributeSet->getAttributes() as $a) {
#                if ($a->getKey() == $key) $a->setSort($order);
            if ($tabs[$a->getKey()]) $a->setTab($tabs[$a->getKey()]);
        }

        $this->getEm()->flush();
        return $attributeSet;
    }

    public function updateTabTitles(\Entities\AttributeSets $attributeSet, $titles) {
        $attributeSet->setTabTitles($titles);

        $this->getEm()->flush();
        return $attributeSet;
    }

}

