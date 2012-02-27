<?
/**
 * These are EAV rows.  Entities are dual-named in Stuki as both Doctrine Entities (this class)
 * and EAV entities.
 */
namespace Entities;

use Stuki\Entity\Entity,
    Stuki\Entity\Audit,
    Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="audit_entities", indexes={@ORM\index(name="entity_key_idx", columns={"entity_key"})}))
 */
class AuditEntities extends Entity implements Audit
{

    public function getGetterSetterMap() {
        return array (
            'getKey' => 'setKey',
            'getUser' => 'setUser',
            'getAttributeSet' => 'setAttributeSet',
            'getParent' => 'setParent',
            'getTitle' => 'setTitle',
            'getIsLocked' => 'setIsLocked',
        );
    }

    public $valueEntities = array(
        'Entities\ValuesArray',
        'Entities\ValuesDatetime',
        'Entities\ValuesDecimal',
        'Entities\ValuesInteger',
        'Entities\ValuesText',
        'Entities\ValuesVarchar'
    );

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
    protected $entity_key;

    public function getKey() {
        return $this->entity_key;
    }

    public function setKey($value) {
        $this->entity_key = $value;
        return $this;
    }

    /**
     * @ORM\ManyToOne(targetEntity="AuditUsers", inversedBy="entities")
     * @ORM\JoinColumn(name="ref_user", referencedColumnName="user_key")
     */
    protected $user;

    public function getUser() {
        return $this->user;
    }

    public function setUser($value) {
        $this->user = $value;
    }

    /**
     * @ORM\ManyToOne(targetEntity="AuditAttributeSets", inversedBy="entities")
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
     * @ORM\ManyToOne(targetEntity="AuditEntities", inversedBy="children")
     * @ORM\JoinColumn(name="ref_entity", referencedColumnName="entity_key")
     */
    protected $parent;

    public function getParent() {
        return $this->parent;
    }

    public function setParent($value) {
        $this->parent = $value;
    }

    /**
     * @ORM\OneToMany(targetEntity="AuditEntities", mappedBy="parent")
     * @ORM\OrderBy({"title" = "ASC"})
     */
    protected $children;

    public function getChildren() {
        return $this->children;
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
     * @ORM\Column(name="is_locked", type="boolean", nullable=true)
     */
    protected $isLocked;

    public function getIsLocked() {
        return $this->isLocked;
    }

    public function setIsLocked($value) {
        $this->isLocked = $value;
    }

    /**
     * Because values are stored in 6 tables this aggregates entity's values.
     */
    public function getValues() {
        $search = array(
            'entity' => $this,
        );

        $res = array();
        foreach ($this->valueEntities as $ve) {
            $res = array_merge($res, $this->getEm()->getRepository($ve)->findBy($search));
        }

        uasort($res, array($this, 'sortValues'));

        // Remove values no longer associated with this entity (decoupled from attribute set)
        foreach ($res as $key => $val) {
            if ($val->getAttribute()->getAttributeSet() != $this->getAttributeSet())
                unset($res[$key]);
        }

        return $res;
    }

    /**
     * Return a value based on the params
     */
    public function findOneValueBy($params) {
        $params['entity'] = $this;

        foreach ($this->valueEntities as $repository) {
            switch ($repository) {
                case 'Entities\ValuesDatetime-remove':
                    // Only search datetimes if a datetime is specified for the value
                    if (isset($params['value']) AND $params['value'] instanceof \Datetime)
                        if ($entity = $this->getEm()->getRepository($repository)->findOneBy($params))
                            return $entity;
                    break;
                default:
                    if ($entity = $this->getEm()->getRepository($repository)->findOneBy($params))
                        return $entity;
                    break;
            }
        }
    }

    /**
     * A function to sort values by their attribute's sort
     */
    private function sortValues($a, $b) {
        $aSort = $a->getAttribute()->getSort();
        $bSort = $b->getAttribute()->getSort();

        if ($aSort == $bSort) return 0;
        return ($aSort < $bSort) ? -1: 1;
    }

    /**
     * A function to sort entities by attribute set name and title
     */
    private function sortChildren($a, $b) {
        $aSort = $a->getAttributeSet()->getName() . '--' . $a->getTitle();
        $bSort = $b->getAttributeSet()->getName() . '--' . $b->getTitle();

        if ($aSort == $bSort) return 0;
        return ($aSort < $bSort) ? -1: 1;
    }

}