<?
/**
 * Renderers are the heart of Stuki EAV.  They are the core component all other
 * EAV entities rely on.
 */
namespace Entities;

use Stuki\Entity\Entity,
    Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="renderers", uniqueConstraints={@ORM\UniqueConstraint(name="class_idx", columns={"class"})})
 */
class Renderers extends Entity {

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $renderer_key;

    public function getKey() {
        return $this->renderer_key;
    }

    /**
     * @ORM\Column(name="alias", type="string")
     */
    protected $alias;

    public function getAlias() {
        return $this->alias;
    }

    public function setAlias($value) {
        $this->alias = $value;
    }

    /**
     * @ORM\Column(name="class", type="string")
     */
    protected $class;

    public function getClass() {
        return $this->class;
    }

    public function getClassObject($fieldName) {

        if (!is_string($fieldName))
            throw new \Stuki\Exception('The field name to create a renderer must be a string');

        $renderer = new $this->class($fieldName);

        return $renderer;
    }

    public function setClass($value) {
        $this->class = $value;
    }

    /**
     * @ORM\OneToMany(targetEntity="Attributes", mappedBy="renderer")
     */
    protected $attributes;

    public function getAttributes() {
        return $this->attributes;
    }

}