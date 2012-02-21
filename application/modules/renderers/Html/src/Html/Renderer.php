<?php

namespace Html;

use Stuki\Renderer\Renderer as StukiRenderer,
    Zend\Form\Element\Textarea;

class Renderer extends Textarea implements StukiRenderer
{
    private $datatype = 'text';

    // Anytime this renderer is created we want to include JS
    public function __construct($spec, $options = null) {
        parent::__construct($spec, $options);

        // Get the view object
        $locator = \Zend\Registry::get('locator');
        $view = $locator->get('view');
        $view->plugin('headScript')->appendFile('/assets/html/ckeditor/ckeditor.js');
    }


    public function getDataType() {
        return $this->datatype;
    }

    public function postCreate() {
        $this->rows = 8;
        $this->class .= ' ckeditor';
        return $this;
    }

    // For display
    public function formatValue($value) {
        return $this->purify($value);
    }

    // For editing
    public function formatEditValue($value) {
        return $value;
    }

    // Format for search
    public function formatSearchValue($value) {
        return strip_tags($this->purify($value));
    }

    public function purify($value) {
        require_once __DIR__ . '/../../vendor/htmlpurifier-4.4.0-lite/library/HTMLPurifier.auto.php';
        $hp = new \HTMLPurifier();
        return $hp->purify($value);
    }

}
