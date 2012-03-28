<?
namespace Entities;

use Stuki\Entity\Entity,
    ZfcUserDoctrineORM\Entity\User as ZfcUser,
    Doctrine\ORM\Mapping AS ORM;

class AttributeSetPlugins extends Entity
{
    protected $attribute_set_plugin_key;

    public function getKey() {
        return $this->attribute_set_plugin_key;
    }


    protected $plugin;

    public function getPlugin() {
        return $this->plugin;
    }

    public function setPlugin($value) {
        $this->plugin = $value;
        return $this;
    }


    protected $attributeSet;

    public function getAttributeSet() {
        return $this->attributeSet;
    }

    public function setAttributeSet($value) {
        $this->attributeSet = $value;
        return $this;
    }

    protected $alias;

    public function getAlias() {
        return $this->alias;
    }

    public function setAlias($value) {
        $this->alias = $value;
        return $this;
    }


    protected $parameters;

    public function getParameters() {
        return $this->parameters;
    }

    public function setParameters($value) {
        $this->parameters = $value;
        return $this;
    }


    protected $sort;

    public function getSort() {
        return $this->sort;
    }

    public function setSort($value) {
        $this->sort = $value;
        return $this;
    }


}