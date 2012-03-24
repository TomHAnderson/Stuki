<?php

namespace Favorites;

use Stuki\Plugin\Plugin;

class Favorites implements Plugin {

    /**
     * This is the only function from the implementation
     */
    public function run(\Entities\Entities $entity) {
        $locator = \Zend\Registry::get('locator');

        $modelFavorites = $locator->get('modelFavorites');
        $view = $locator->get('view');

        $view->setVars(
            array(
                'favorite' => $modelFavorites->findBy(
                    array(
                        'entity' => $entity
                    )
                ),
                'entity' => $entity
            )
        );

        return $view->render('favorites/view.phtml');
    }
}