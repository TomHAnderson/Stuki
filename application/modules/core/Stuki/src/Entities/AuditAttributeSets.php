<?
/**
 * Attribute Sets are EAV tables.  They are a collection of attributes and
 * entities reference them.
 */
namespace Entities;

use Stuki\Entity\Entity,
    Stuki\Entity\Audit,
    Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="audit_attribute_sets", indexes={@ORM\index(name="attribute_set_key_idx", columns={"attribute_set_key"})}))
 */
class AuditAttributeSets extends Entity implements Audit
{
    public function getGetterSetterMap() {
        return array (
            'getKey' => 'setKey',
            'getUser' => 'setUser',
            'getTheme' => 'setTheme',
            'getName' => 'setName',
            'getCode' => 'setCode',
            'getTitle' => 'setTitle',
            'getDescription' => 'setDescription',
            'getTabs' => 'setTabs',
            'getTabTitles' => 'setTabTitles'
        );
    }

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $audit_key;

    /**
     * @ORM\Column(name="audit_start", type="datetime")
     */
    protected $auditStart;

    /**
     * @ORM\Column(name="audit_start_usec", type="integer")
     */
    protected $auditStartUsec;

    /**
     * @ORM\Column(name="audit_stop", type="datetime")
     */
    protected $auditStop;

    /**
     * @ORM\Column(name="audit_stop_usec", type="integer")
     */
    protected $auditStopUsec;

    /**
     * @ORM\ManyToOne(targetEntity="AuditUsers")
     * @ORM\JoinColumn(name="audit_ref_user", referencedColumnName="user_key")
     */
    protected $auditUser;

    /**
     * @ORM\Column(type="integer")
     * @ORM\GeneratedValue
     */
    protected $attribute_set_key;

    public function getKey() {
        return $this->attribute_set_key;
    }

    public function setKey($value) {
        $this->attribute_set_key = $value;
    }

    /**
     * @ORM\ManyToOne(targetEntity="AuditUsers")
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
     * @ORM\OneToMany(targetEntity="AuditAttributes", mappedBy="attributeSet")
     * @ORM\OrderBy({"sort" = "ASC"})
     */
    protected $attributes;

    public function getAttributes() {
        return $this->attributes;
    }

    /**
     * @ORM\OneToMany(targetEntity="AuditAttributes", mappedBy="originalAttributeSet")
     */
    protected $originalAttributes;

    public function getOriginalAttributes() {
        return $this->originalAttributes;
    }

    /**
     * Relations limit which attribute sets may be linked to which
     * @ORM\OneToMany(targetEntity="AuditAttributeSetRelations", mappedBy="parent")
     */
    protected $relations;

    public function getRelations() {
        return $this->relations;
    }

    /**
     * @ORM\OneToMany(targetEntity="AuditAttributeSetRelations", mappedBy="child")
     */
    protected $inverseRelations;

    public function getInverseRelations() {
        return $this->inverseRelations;
    }


    /**
     * @ORM\OneToMany(targetEntity="AuditEntities", mappedBy="attributeSet")
     */
    protected $entities;

    public function getEntities() {
        return $this->entities;
    }

    /**
     * @ORM\OneToMany(targetEntity="AuditAttributeSetPlugins", mappedBy="attributeSet")
     */
    protected $plugins;

    public function getPlugins() {
        return $this->plugins;
    }

    /**
     * What theme set does this belong to?
     *
     * @ORM\ManyToOne(targetEntity="AuditThemes", inversedBy="attributeSets")
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
}