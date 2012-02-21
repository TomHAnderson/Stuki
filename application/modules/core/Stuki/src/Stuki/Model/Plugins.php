<?php
/**
 * Renderers are the glue which binds the database to the code.
 * Renderers are the core component of the the Stuki design.
 */
namespace Stuki\Model;

use Stuki\Model\Model,
    Stuki\Plugin\Exception as Exception,
    Stuki\Plugin\Plugin,
    Entities\Plugins as EntityPlugins
    ;

class Plugins extends Model {

    /**
     * Add a new renderer
     *
     * All we require is a class name.
     */
    public function insert($alias, $class) {
        // Test whether the class is valid
        if (!class_exists($class))
            throw new Exception\InvalidArgumentException('Invalid class name');

        // Verify it's an instance of renderer
        $test = new $class('test');
        if (!$test instanceof Plugin)
            throw new Exception\InvalidArgumentException('Plugin class does not implement Stuki\Plugin\Plugin');

        // Is renderer already installed?
        if ($this->findOneBy(array(
            'class' => $class
        )))
            throw new Exception\InvalidArgumentException('Plugin is already installed');

        // Is alias unique?
        if ($this->findOneBy(array(
            'alias' => $alias
        )))
            throw new Exception\InvalidArgumentException('Alias is not unique');

        // Add the renderer
        $em = $this->getEm();
        $plugin = new EntityPlugins();
        $plugin->setAlias($alias);
        $plugin->setClass($class);
        $em->persist($plugin);
        $em->flush();

        return $plugin;
    }

    /**
     * Delete a renderer
     */
    public function delete(\Entities\Plugins $plugin) {
        if (count($plugin->getAttributeSets()))
            throw new Exception\InvalidArgumentException('The specified plugin is in use by one or more attribute sets');

        $this->getEm()->remove($plugin);
        $this->getEm()->flush();
    }

    /**
     * Retrieve one renderer
     */
    public function find($plugin_key) {
        if (!is_int($plugin_key)) throw new \Stuki\Exception('Renderer key must be an integer');
        return $this->getEm()->getRepository('Entities\Plugins')->find($plugin_key);
    }

    /**
     * Retrieve all Plugins
     */
    public function findAll($sort = null) {
        if (!$sort) $sort = array('alias' => 'asc');
        return $this->getEm()->getRepository('Entities\Plugins')->findBy(array(), $sort);
    }

    /**
     * Retrieve a subset of Plugins
     */
    public function findBy($search, $sort = null) {
        if (!$sort) $sort = array('alias' => 'asc');
        return $this->getEm()->getRepository('Entities\Plugins')->findBy($search, $sort);
    }

    /**
     * Retrieve one using an assoc array of params
     */
    public function findOneBy($search) {
        return $this->getEm()->getRepository('Entities\Plugins')->findOneBy($search);
    }
}

