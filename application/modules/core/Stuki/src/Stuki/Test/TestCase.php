<?php

namespace Stuki\Test;

abstract class TestCase extends \PHPUnit_Framework_TestCase {
    protected function getEm() {
        return $this->getLocator()->get('doctrine_em');
    }

    protected function getLocator() {
        return \Zend\Registry::get('locator');
    }
}