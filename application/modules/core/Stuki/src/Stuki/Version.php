<?php
namespace Stuki;

class Version
{
    const VERSION = '2.1.2';

    /**
     * Return the current version number
     */
    public function getVersion() {
        return self::VERSION;
    }

    /**
     * Does the current version fall between the given version numbers
     * Used for plugin version from & to
     */
    public function isBetween($start, $end) {
        if (version_compare($this->getVersion(), $start, '>=') AND version_compare($this->getVersion(), $end, '<='))
            return true;

        return false;
    }
}
