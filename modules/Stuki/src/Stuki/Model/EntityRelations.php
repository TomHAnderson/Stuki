<?php
/**
 * Insert, update, and delete entities.
 * This also builds entity titles
 */
namespace Stuki\Model;

use Stuki\Model\Model as StukiModel,
    Stuki\Renderer\Parameters;

class EntityRelations extends StukiModel {

    /**
     * Find one
     */
    public function find($key) {
        return $this->getEm()->getRepository('Entities\EntityRelations')->find($key);
    }

    /**
     * Retrieve all
     */
    public function findAll($sort = null) {
        if (!$sort) $sort = array('sort' => 'asc');
        return $this->getEm()->getRepository('Entities\EntityRelations')->findBy(array(), $sort);
    }

    /**
     * Retrieve a subset
     */
    public function findBy($search, $sort = null) {
        if (!$sort) $sort = array('sort' => 'asc');
        return $this->getEm()->getRepository('Entities\EntityRelations')->findBy($search, $sort);
    }

    public function findByAttributeSet(\Entities\AttributeSets $attributeSet, $search, $sort = null) {
#        if (!$sort) $sort = array('sort' => 'asc');

        # FIXME:  This could be improved with a dql query instead

        // Fetch all xrefs for this entity
        $xrefs = $this->findBy($search);

        // Filter xrefs by attribute set
        $return = array();
        foreach ($xrefs as $x) {
            if ($x->getChild()->getAttributeSet() == $attributeSet) {
                $return[] = $x;
            }
        }

        return $return;
    }

    public function insert(\Entities\Entities $parent, \Entities\Entities $child) {
        $xref = new \Entities\EntityRelations();

        $xref->setParent($parent);
        $xref->setChild($child);
        $xref->setSort(0);

        $this->getEm()->persist($xref);

        $this->getEm()->flush();
    }
}