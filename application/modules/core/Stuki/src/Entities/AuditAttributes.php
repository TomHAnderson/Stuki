<?

namespace Entities;

use Stuki\Entity\Entity,
    Doctrine\ORM\Mapping AS ORM;

/**
 * @ORM\Entity
 * @ORM\Table(name="audit_attributes", indexes={@ORM\index(name="attribute_key_idx", columns={"attribute_key"})})
 */
class AuditAttributes extends Entity
{

    /**
     * @ORM\Column(type="integer")
     * @ORM\Id
     * @ORM\GeneratedValue
     */
    protected $audit_key;

    /**
     * @ORM\Column(type="integer")
     */
    protected $attribute_key;

    /**
     * What renderer does this attribute implement?
     *
     * @ORM\ManyToOne(targetEntity="AuditRenderers")
     * @ORM\JoinColumn(name="ref_renderer", referencedColumnName="renderer_key")
     */
    protected $renderer;

    /**
     * What attribute set does this attribute belong to?
     *
     * @ORM\ManyToOne(targetEntity="AuditAttributeSets", inversedBy="attributes")
     * @ORM\JoinColumn(name="ref_attribute_set", referencedColumnName="attribute_set_key")
     */
    protected $attributeSet;

    /**
     * A reference to the original attribute set to be used when deleted.
     *
     * @ORM\ManyToOne(targetEntity="AuditAttributeSets")
     * @ORM\JoinColumn(name="ref_original_attribute_set", referencedColumnName="attribute_set_key")
     */
    protected $originalAttributeSet;

    /**
     * The code for the attribute.  This is used for
     * searching e.g. "code:value".  This is essentially
     * a pseudo-database field name.
     *
     * @ORM\Column(type="string")
     */
    protected $code;

    /**
     * The label for the attribute
     *
     * @ORM\Column(type="string")
     */
    protected $label;

    /**
     * The description of the attribute
     *
     * @ORM\Column(type="text")
     */
    protected $description;

    /**
     * An array of options if this
     * field is a select or multiple select element
     *
     * @ORM\Column(type="array")
     */
    protected $options;

    /**
     * A regular expression to evaluate the field against
     *
     * Ignored if empty
     *
     * @ORM\Column(type="text")
     */
    protected $regex;

    /**
     * A custom error message.  Usually used in conjunction
     * with $regex
     *
     * @ORM\Column(type="text")
     */
    protected $error;

    /**
     * The sort order of this attribute within an attribute set
     *
     * @ORM\Column(type="integer")
     */
    protected $sort;

    /**
     * The tab this attribute is displayed on
     *
     * @ORM\Column(type="integer")
     */
    protected $tab;

    /**
     * Is this a required column?
     *
     * @ORM\Column(name="is_required", type="boolean")
     */
    protected $isRequired;

    /**
     * Is this a unique column value?
     *
     * @ORM\Column(name="is_unique", type="boolean")
     */
    protected $isUnique;

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
     * @ORM\Column(name="audit_ref_user", type="integer")
     */
    protected $auditUser;

}