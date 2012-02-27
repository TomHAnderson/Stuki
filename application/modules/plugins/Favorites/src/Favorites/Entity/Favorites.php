<?
namespace Entities;

use Stuki\Entity\Entity,
    Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="favorites")
 */
class Favorites extends Entity
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
     * @ORM\ManyToOne(targetEntity="Users", inversedBy="entities")
     * @ORM\JoinColumn(name="ref_user", referencedColumnName="user_key")
     */
    protected $user;

    public function getUser() {
        return $this->user;
    }

    public function setUser(\Entities\Users $value) {
        $this->user = $value;
    }
}