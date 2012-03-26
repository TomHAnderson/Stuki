<?php
/**
 */
namespace Stuki\Model;

use Stuki\Model\Model,
    Stuki\Theme\Exception as Exception,
    Stuki\Plugin\Plugin,
    Entities\Themes as EntityThemes
    ;

class Themes extends Model {

    /**
     * Add a new theme
     */
    public function insert($alias, $template) {

        // Verify template exists
        $view = $this->getLocator()->get('view');
        #if (!$view->resolver()->resolve($template))
#FIXME: verify path resolves commented because it's broke in zf2-b3
#        try {
#            $view->resolver()->getScriptPath($template);
#        } catch (\Exception $e) {
#            throw new Exception\InvalidArgumentException('Theme file does not exist');
#        }

        // Is renderer already installed?
        if ($this->findOneBy(array(
            'template' => $template
        )))
            throw new Exception\InvalidArgumentException('Theme is already installed');

        // Is alias unique?
        if ($this->findOneBy(array(
            'alias' => $alias
        )))
            throw new Exception\InvalidArgumentException('Alias is not unique');

        // Add the renderer
        $em = $this->getEm();
        $theme = new EntityThemes();
        $theme->setAlias($alias);
        $theme->setTemplate($template);
        $em->persist($theme);
        $em->flush();

        return $theme;
    }

    /**
     * Delete a renderer
     */
    public function delete(\Entities\Themes $theme) {
        if (count($theme->getAttributeSets()))
            throw new Exception\InvalidArgumentException('The specified theme is in use by one or more attribute sets');

        $this->getEm()->remove($theme);
        $this->getEm()->flush();
    }

    /**
     * Retrieve one renderer
     */
    public function find($plugin_key) {
        if (!is_int($plugin_key)) throw new \Stuki\Exception('Theme key must be an integer');
        return $this->getEm()->getRepository('Entities\Themes')->find($plugin_key);
    }

    /**
     * Retrieve all Plugins
     */
    public function findAll($sort = null) {
        if (!$sort) $sort = array('alias' => 'asc');
        return $this->getEm()->getRepository('Entities\Themes')->findBy(array(), $sort);
    }

    /**
     * Retrieve a subset of Plugins
     */
    public function findBy($search, $sort = null) {
        if (!$sort) $sort = array('alias' => 'asc');
        return $this->getEm()->getRepository('Entities\Themes')->findBy($search, $sort);
    }

    /**
     * Retrieve one using an assoc array of params
     */
    public function findOneBy($search) {
        return $this->getEm()->getRepository('Entities\Themes')->findOneBy($search);
    }
}

