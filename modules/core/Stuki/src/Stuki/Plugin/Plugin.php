<?php

namespace Stuki\Plugin;

interface Plugin {
    public function run(\Entities\Entities $entity);
}