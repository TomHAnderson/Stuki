<?php
/**
 * The renderer interface
 */

namespace Stuki\Renderer;

interface Select
{
    /**
     * Return an array of key value pairs for select options
     */
    public function getRendererMultiOptions(\Entities\Attributes $attribute);
}