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
 * @ORM\Table(name="attribute_set_plugins_xref")
 */
class AttributeSetPlugins extends Entity
{

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="AttributeSets", inversedBy="plugins")
     * @ORM\JoinColumn(name="ref_attribute_set", referencedColumnName="attribute_set_key")
     */
    protected $attributeSet;

    public function getAttributeSet() {
        return $this->attributeSet;
    }

    public function setAttributeSet($value) {
        $this->attributeSet = $value;
    }

    /**
     * @ORM\Id
     * @ORM\ManyToOne(targetEntity="Plugins", inversedBy="attributeSets")
     * @ORM\JoinColumn(name="ref_plugin", referencedColumnName="plugin_key")
     */
    protected $plugin;

    public function getPlugin() {
        return $this->plugin;
    }

    public function setPlugin($value) {
        $this->plugin = $value;
    }

}