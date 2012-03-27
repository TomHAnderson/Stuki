<?php
/**
 * Use this renderer interface when the value this renderer populates
 * is a key to an(other) entity.  When an entity is deleted a check
 * will be made against renderers which implement ForeignKey
 * vs the current identifier and if foreign keys exist you
 * will not be allowed to delete the entity.
 */

namespace Stuki\Renderer;

interface ForeignKey
{
}