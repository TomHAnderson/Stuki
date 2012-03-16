<?php
/**
 * Renderers are the glue which binds the database to the code.
 * Renderers are the core component of the the Stuki design.
 */
namespace Stuki\Model;

use Stuki\Model\Model,
    Stuki\Renderer\Exception as Exception,
    Stuki\Renderer\Renderer,
    Entities\Renderers as EntityRenderers
    ;

class Renderers extends Model {

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
        if (!$test instanceof Renderer)
            throw new Exception\InvalidArgumentException('Renderer class does not implement Stuki\Renderer');

        // Is renderer already installed?
        if ($this->findOneBy(array(
            'class' => $class
        )))
            throw new Exception\InvalidArgumentException('Renderer is already installed');

        // Is alias unique?
        if ($this->findOneBy(array(
            'alias' => $alias
        )))
            throw new Exception\InvalidArgumentException('Alias is not unique');

        // Add the renderer
        $em = $this->getEm();
        $renderer = new EntityRenderers();
        $renderer->setAlias($alias);
        $renderer->setClass($class);
        $em->persist($renderer);
        $em->flush();

        return true;
    }

    /**
     * Delete a renderer
     */
    public function delete(\Entities\Renderers $renderer) {
        if (count($renderer->getAttributes()))
            throw new Exception\InvalidArgumentException('The specified renderer is in use by one or more attributes');

        $em = $this->getEm();
        $em->remove($renderer);
        $em->flush();
    }

    /**
     * Retrieve one renderer
     */
    public function find($renderer_key) {
        if (!is_int($renderer_key)) throw new \Stuki\Exception('Renderer key must be an integer');
        return $this->getEm()->getRepository('Entities\Renderers')->find($renderer_key);
    }

    /**
     * Retrieve all renderers
     */
    public function findAll($sort = null) {
        if (!$sort) $sort = array('alias' => 'asc');
        return $this->getEm()->getRepository('Entities\Renderers')->findBy(array(), $sort);
    }

    /**
     * Retrieve a subset of renderers
     */
    public function findBy($search, $sort = null) {
        if (!$sort) $sort = array('alias' => 'asc');
        return $this->getEm()->getRepository('Entities\Renderers')->findBy($search, $sort);
    }

    /**
     * Retrieve one using an assoc array of params
     */
    public function findOneBy($search) {
        return $this->getEm()->getRepository('Entities\Renderers')->findOneBy($search);
    }
}

