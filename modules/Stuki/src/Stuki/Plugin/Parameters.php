<?php
/**
 * The renderer interface
 */

namespace Stuki\Plugin;

interface Parameters
{
    /**
     * Return a form for saving parameters to an attribute
     *
     * @returns Zend\Form
     */
    public function getParametersForm();

    /**
     * Set the attribute parameters so this can render appropriatly
     */
    public function setParameters($params);

    /**
     * getParameters is on the attribute entity
     */
}