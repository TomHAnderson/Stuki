<?
/**
 * Attribute Sets are EAV tables.  They are a collection of attributes and
 * entities reference them.
 */
namespace Entities;

use Stuki\Entity\Entity,
    Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="attribute_sets")
 */
class AttributeSets extends Entity
{
    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $attribute_set_key;

    public function getKey() {
        return $this->attribute_set_key;
    }

    /**
     * @ORM\ManyToOne(targetEntity="Users")
     * @ORM\JoinColumn(name="ref_user", referencedColumnName="user_key")
     */
    protected $user;

    public function getUser() {
        return $this->user;
    }

    public function setUser(Users $value) {
        $this->user = $value;
    }

    /**
     * @ORM\OneToMany(targetEntity="Attributes", mappedBy="attributeSet")
     * @ORM\OrderBy({"sort" = "ASC"})
     */
    protected $attributes;

    public function getAttributes() {
        return $this->attributes;
    }

    /**
     * @ORM\OneToMany(targetEntity="Attributes", mappedBy="originalAttributeSet")
     */
    protected $originalAttributes;

    public function getOriginalAttributes() {
        return $this->originalAttributes;
    }

    /**
     * Relations limit which attribute sets may be linked to which
     * @ORM\OneToMany(targetEntity="AttributeSetRelations", mappedBy="parent")
     */
    protected $relations;

    public function getRelations() {
        return $this->relations;
    }

    /**
     * @ORM\OneToMany(targetEntity="AttributeSetRelations", mappedBy="child")
     */
    protected $inverseRelations;

    public function getInverseRelations() {
        return $this->inverseRelations;
    }


    /**
     * @ORM\OneToMany(targetEntity="Entities", mappedBy="attributeSet")
     */
    protected $entities;

    public function getEntities() {
        return $this->entities;
    }

    /**
     * @ORM\OneToMany(targetEntity="AttributeSetPlugins", mappedBy="attributeSet")
     */
    protected $plugins;

    public function getPlugins() {
        return $this->plugins;
    }

    /**
     * What theme set does this belong to?
     *
     * @ORM\ManyToOne(targetEntity="Themes", inversedBy="attributeSets")
     * @ORM\JoinColumn(name="ref_theme", referencedColumnName="theme_key")
     */
    protected $theme;

    public function getTheme() {
        return $this->theme;
    }

    public function setTheme($value) {
        $this->theme = $value;
    }

    /**
     * @ORM\Column(type="string", nullable=true)
     */
    protected $name;

    public function getName() {
        return $this->name;
    }

    public function setName($value) {
        $this->name = $value;
    }

    /**
     * @ORM\Column(type="string")
     */
    protected $code;

    public function getCode() {
        return $this->code;
    }

    public function setCode($value) {
        $this->code = $value;
    }

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $title;

    public function getTitle() {
        return $this->title;
    }

    public function setTitle($value) {
        $this->title = $value;
    }

    /**
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description;

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($value) {
        $this->description = $value;
    }

    /**
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $tabs;

    public function getTabs() {
        return $this->tabs;
    }

    public function setTabs($value) {
        $this->tabs = $value;
    }

    /**
     * @ORM\Column(name="tab_titles", type="array", nullable=true)
     */
    protected $tabTitles;

    public function getTabTitles() {
        return $this->tabTitles;
    }

    public function setTabTitles($value) {
        $this->tabTitles = $value;
    }

    /**
     * @ORM\PostPersist
     */
    function insert() {
        $this->auditInsert();
    }

    /**
     * @ORM\PostUpdate
     */
    function update() {
        $this->auditUpdate();
    }

    /**
     * @ORM\PreRemove
     */
    function delete() {
        $this->auditDelete();
    }
}