<?
/**
 * These are EAV rows.  Entities are dual-named in Stuki as both Doctrine Entities (this class)
 * and EAV entities.
 */
namespace Entities;

use Stuki\Entity\Entity,
    ZfcUserDoctrineORM\Entity\User,
    Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="entities")
 */
class EntityRelations extends Entity
{

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $entity_relation_key;

    public function getKey() {
        return $this->entity_relation_key;
    }


    protected $parent;

    public function getParent() {
        return $this->parent;
    }

    public function setParent(\Entities\Entities $value) {
        $this->parent = $value;
    }


    protected $child;

    public function getChild() {
        return $this->child;
    }

    public function setChild(\Entities\Entities $value) {
        $this->child = $value;
        return $this;
    }


    protected $sort;

    public function getSort() {
        return $this->sort;
    }

    public function setSort($value) {
        $this->sort = $value;
    }

}