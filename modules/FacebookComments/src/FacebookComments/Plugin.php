<?php

namespace FacebookComments;

use Stuki\Plugin\Plugin as StukiPlugin;

class Plugin implements StukiPlugin {

    /**
     * This is the only function from the implementation
     */
    public function run(\Entities\Entities $entity) {
        $locator = \Zend\Registry::get('locator');
        $view = $locator->get('view');

        return $view->render('facebookcomments/facebook.phtml');
    }
}