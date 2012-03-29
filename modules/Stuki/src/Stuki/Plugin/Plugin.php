<?php

namespace Stuki\Plugin;

interface Plugin {
    public function run(\Entities\AttributeSetPlugins $pluginXref, \Entities\Entities $entity);
}