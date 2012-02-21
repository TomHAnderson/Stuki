<?php
namespace Stuki\Tests\Stuki\Src\Stuki;

use Stuki\Test\TestCase,
    Stuki\Version as Version;

class VersionTest extends TestCase {

    public function setUp() {
    }

    public function tearDown() {
    }

    public function testVersion() {
        $versionModel = new Version();
        $this->assertStringStartsWith('2.', $versionModel->getVersion());
    }

    public function testVersionBetween() {
        $versionModel = new Version();
        $this->assertTrue($versionModel->isBetween('1.0.0', '21.1.0'));
    }

    public function testVersionNotBetween() {
        $versionModel = new Version();
        $this->assertFalse($versionModel->isBetween('21.0.0', '1.0.0'));
    }
}
