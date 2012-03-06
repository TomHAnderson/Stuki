<?
/**
 * These are plugins associated with an attribute set.  So these plugins are displayed/ran
 * when an associated entity of this attribute set type is displayed.
 */
namespace Entities;

use Stuki\Entity\Entity,
    Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="attribute_set_relations_xref")
 */
class AttributeSetRelations extends Entity
{
    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="AttributeSets", inversedBy="relations")
     * @ORM\JoinColumn(name="ref_attribute_set_parent", referencedColumnName="attribute_set_key")
     */
    protected $parent;

    public function getParent() {
        return $this->parent;
    }

    public function setParent(\Entities\AttributeSets $value) {
        $this->parent = $value;
    }

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="AttributeSets", inversedBy="inverseRelations")
     * @ORM\JoinColumn(name="ref_attribute_set_child", referencedColumnName="attribute_set_key")
     */
    protected $child;

    public function getChild() {
        return $this->child;
    }

    public function setChild(\Entities\AttributeSets $value) {
        $this->child = $value;
    }

}