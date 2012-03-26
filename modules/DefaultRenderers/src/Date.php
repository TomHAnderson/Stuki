<?php

namespace DefaultRenderers;

use Stuki\Renderer\Renderer,
    Zend\Form\Element\Text;

class Date extends Text implements Renderer
{
    private $datatype = 'datetime';

    // Anytime this renderer is created we want to include JS
    public function __construct($spec, $options = null) {
        parent::__construct($spec, $options);

        // Get the view object
        $locator = \Zend\Registry::get('locator');
        $view = $locator->get('view');
        $view->plugin('headScript')->appendFile('/assets/defaultrenderers/Date.js');
    }

    public function getDataType() {
        return $this->datatype;
    }

    public function postCreate() {
        $this->addValidator('Date');
        $this->size = 10;

        $this->class = "date";

        return $this;
    }

    // For display
    public function formatValue($value) {
        if (!$value) return '';
        return $value->format('Y-m-d');
    }

    // For editing
    public function formatEditValue($value) {
        if (!$value) return '';
        return $value->format('Y-m-d');
    }

    // Format for search
    public function formatSearchValue($value) {
        if (!$value) return false;
        return $value->format('Ymd');
    }

    /**
     * Return an about section of the ini
     */
    public function getAbout($field) {
        $ini = new \Zend_Config_Ini(__DIR__ . '/plugin.ini', 'about');
        return $ini->$field;
    }

}
