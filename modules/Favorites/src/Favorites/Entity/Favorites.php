<?
namespace Entities;

use Stuki\Entity\Entity as StukiEntity,
    ZfcUserDoctrineORM\Entity as ZfcEntity,
    Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="favorites")
 */
class Favorites extends StukiEntity
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $favorite_key;

    public function getKey() {
        return $this->favorite_key;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Entities")
     * @ORM\JoinColumn(name="ref_entity", referencedColumnName="entity_key")
     */
    protected $entity;

    public function getEntity() {
        return $this->entity;
    }

    public function setEntity(\Entities\Entities $value) {
        $this->entity = $value;
    }

    /**
     * @ORM\ManyToOne(targetEntity="\ZfcUserDoctrineORM\Entity\User")
     * @ORM\JoinColumn(name="ref_user", referencedColumnName="user_id")
     */
    protected $user;

    public function getUser() {
        return $this->user;
    }

    public function setUser(\ZfcUserDoctrineORM\Entity\User $value) {
        $this->user = $value;
    }
}