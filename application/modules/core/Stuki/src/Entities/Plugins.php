<?
/**
 * This entity manages plugin configurations
 * Plugins were used in Stuki 2.0 extensively.  They will probably be transformed with ZF2
 */
namespace Entities;

use Stuki\Entity\Entity,
    Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="plugins")
 */
class Plugins extends Entity
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $plugin_key;

    public function getKey() {
        return $this->plugin_key;
    }

    /**
     * @ORM\Column(type="string")
     */
    protected $alias;

    public function getAlias() {
        return $this->alias;
    }

    public function setAlias($value) {
        $this->alias = $value;
    }

    /**
     * @ORM\Column(type="string")
     */
    protected $class;

    public function getClass() {
        return $this->class;
    }

    public function setClass($value) {
        $this->class = $value;
    }

    /**
     * Return the plugin class
     */
    public function getClassObject() {
        return new $this->class;
    }

    /**
     * @ORM\OneToMany(targetEntity="AttributeSetPlugins", mappedBy="plugin")
     */
    protected $attributeSets;

    public function getAttributeSets() {
        return $this->attributeSets;
    }

}