<?php
/**
 * The renderer interface
 */

namespace Stuki\Renderer;

interface Renderer
{
    /**
     * Return the datatype for this renderer
     */
     public function getDataType();

    /**
     * The postCreate function is called after the underlying
     * html element has been created.
     */
     #FIXME: can we turn this into a listener?
    public function postCreate();

    /**
     * formatValue takes the value of a renderer'd value
     * and formats it for presentation
     */
    public function formatValue($value);

    /**
     * formatEditValue formats a value prior to it's editing.
     * This is primarily done for date and datetime values
     */
    public function formatEditValue($value);

    /**
     * A formatted value to be inserted into the seach index
     */
    public function formatSearchValue($value);
}