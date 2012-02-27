<?
/**
 */
namespace Entities;

use Stuki\Entity\Entity,
    Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="themes")
 */
class Themes extends Entity
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $theme_key;

    public function getKey() {
        return $this->theme_key;
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
    protected $template;

    public function getTemplate() {
        return $this->template;
    }

    public function setTemplate($value) {
        $this->template = $value;
    }

    /**
     * @ORM\OneToMany(targetEntity="AttributeSets", mappedBy="theme")
     */
    protected $attributeSets;

    public function getAttributeSets() {
        return $this->attributeSets;
    }

}