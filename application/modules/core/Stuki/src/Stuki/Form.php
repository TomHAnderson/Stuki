<?php

namespace Stuki;

use Zend\Form\Form as ZFForm;

class Form extends ZFForm
{
    function __construct($options = null)
    {
        $this->setElementDecorators(array(
            'ViewHelper'
        ));

        $this->setMethod("post");

#        $view = new \Zend_View();
#        $this->setView($view);

#        $view->addHelperPath(APPLICATION_PATH . '/../Library/Stuki/View/Helper/', 'Stuki_View_Helper');
#        $this->addPrefixPath('Stuki_Form', APPLICATION_PATH . '/../Library/Stuki/Form/', null);

        parent::__construct($options);

    }
}
