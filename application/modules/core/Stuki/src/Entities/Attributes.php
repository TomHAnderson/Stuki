<?
/**
 * The attributes table stores EAV column information.  There is
 * a row in this table for each 'field' in the EAV entity.
 */
namespace Entities;

use Stuki\Entity\Entity,
    Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\HasLifecycleCallbacks
 * @ORM\Table(name="attributes")
 */
class Attributes extends Entity
{

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $attribute_key;

    public function getKey() {
        return $this->attribute_key;
    }

    /**
     * What renderer does this attribute implement?
     *
     * @ORM\ManyToOne(targetEntity="Renderers", inversedBy="attributes")
     * @ORM\JoinColumn(name="ref_renderer", referencedColumnName="renderer_key")
     */
    protected $renderer;

    public function getRenderer() {
        return $this->renderer;
    }

    public function setRenderer($value) {
        $this->renderer = $value;
    }

    /**
     * What attribute set does this attribute belong to?
     *
     * @ORM\ManyToOne(targetEntity="AttributeSets", inversedBy="attributes")
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
     * A reference to the original attribute set to be used when deleted.
     *
     * @ORM\ManyToOne(targetEntity="AttributeSets", inversedBy="originalAttributes")
     * @ORM\JoinColumn(name="ref_original_attribute_set", referencedColumnName="attribute_set_key")
     */
    protected $originalAttributeSet;

    public function getOriginalAttributeSet() {
        return $this->originalAttributeSet;
    }

    public function setOriginalAttributeSet($value) {
        $this->originalAttributeSet = $value;
    }

    /**
     * The code for the attribute.  This is used for
     * searching e.g. "code:value".  This is essentially
     * a pseudo-database field name.
     *
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
     * The label for the attribute
     *
     * @ORM\Column(type="string")
     */
    protected $label;

    public function getLabel() {
        return $this->label;
    }

    public function setLabel($value) {
        $this->label = $value;
    }

    /**
     * The description of the attribute
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $description = '';

    public function getDescription() {
        return $this->description;
    }

    public function setDescription($value) {
        $this->description = $value;
    }

    /**
     * An array of options if this
     * field is a select or multiple select element
     *
     * @ORM\Column(type="array", nullable=true)
     */
    protected $options;

    public function getOptions() {
        return array_combine($this->options, $this->options);
    }


    public function setOptions($value) {
        $this->options = $value;
    }

    /**
     * An array of parameters for the renderer
     *
     * @ORM\Column(type="array", nullable=true)
     */
    protected $parameters;

    public function getParameters() {
        return (array)$this->parameters;
    }

    public function setParameters($value) {
        if (!is_array($value))
            throw new \Stuki\Exception('setParameters requires an array');
        $this->parameters = $value;
    }

    /**
     * A regular expression to evaluate the field against
     *
     * Ignored if empty
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $regex = '';

    public function getRegex() {
        return $this->regex;
    }

    public function setRegex($value) {
        # FIXME:  evaluate if regular expression is valid?
        $this->regex = $value;
    }

    /**
     * A custom error message.  Usually used in conjunction
     * with $regex
     *
     * @ORM\Column(type="text", nullable=true)
     */
    protected $error = '';

    public function getError() {
        return $this->error;
    }

    public function setError($value) {
        $this->error = $value;
    }

    /**
     * The sort order of this attribute within an attribute set
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $sort = 0;

    public function getSort() {
        return $this->sort;
    }

    public function setSort($value) {
        $this->sort = $value;
    }

    /**
     * The tab this attribute is displayed on
     *
     * @ORM\Column(type="integer", nullable=true)
     */
    protected $tab = 1;

    public function getTab() {
        return $this->tab;
    }

    public function setTab($value) {
        $this->tab = $value;
    }


    /**
     * Is this a required column?
     *
     * @ORM\Column(name="is_required", type="boolean")
     */
    protected $isRequired = false;

    public function getIsRequired() {
        return $this->isRequired;
    }

    public function setIsRequired($value) {
        $this->isRequired = $value;
    }

    /**
     * Is this a unique column value?
     *
     * @ORM\Column(name="is_unique", type="boolean")
     */
    protected $isUnique = false;

    public function getIsUnique() {
        return $this->isUnique;
    }

    public function setIsUnique($value) {
        $this->isUnique = $value;
    }

    /**
     * Is this a unique column value?
     *
     * @ORM\Column(name="is_included_in_summary", type="boolean")
     */
    protected $isIncludedInSummary = false;

    public function getIsIncludedInSummary() {
        return $this->isIncludedInSummary;
    }

    public function setIsIncludedInSummary($value) {
        $this->isIncludedInSummary = $value;
    }

    /**
     * Get counts of all values for this attribute
     */
    function getValueCount() {
        $renderer = $this->getRenderer()->getClassObject('getValueCount');
        $datatype = $renderer->getDataType();

        $entity = '\Entities\Values' . ucfirst($datatype);

        $query = $this->getEm()->createQuery("
            SELECT count(v)
            FROM $entity v
            WHERE v.attribute = :attribute"
        );
        $query->setParameter('attribute', $this->getKey());
        $count = $query->getSingleScalarResult();

        return $count;
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